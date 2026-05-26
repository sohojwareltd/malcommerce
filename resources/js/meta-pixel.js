const CURRENCY = 'BDT';

function isEnabled() {
    return typeof window.fbq === 'function';
}

function productParams(product, quantity = 1) {
    if (!product) {
        return {};
    }

    const id = String(product.id);
    const itemPrice = Number(product.price) || 0;
    const qty = Math.max(1, Number(quantity) || 1);

    return {
        content_name: product.name || '',
        content_ids: [id],
        content_type: 'product',
        content_category: product.category || '',
        contents: [{ id, quantity: qty, item_price: itemPrice }],
        currency: product.currency || CURRENCY,
        value: itemPrice * qty,
        num_items: qty,
    };
}

function track(event, params, options) {
    if (!isEnabled()) {
        return;
    }

    if (options && Object.keys(options).length > 0) {
        window.fbq('track', event, params, options);
    } else {
        window.fbq('track', event, params);
    }
}

function sessionKey(productId, suffix) {
    return `meta_pixel_${suffix}_${productId}`;
}

function trackViewContent(product, quantity = 1) {
    track('ViewContent', productParams(product, quantity));
}

/** Pre-purchase intent: user engaged with the order form or order CTA. */
function trackLead(product, extra = {}) {
    track('Lead', {
        ...productParams(product, 1),
        content_slug: product.slug || '',
        ...extra,
    });
}

function trackInitiateCheckout(product, { quantity = 1, value, deliveryCharge = 0 } = {}) {
    const id = String(product.id);
    const itemPrice = Number(product.price) || 0;
    const qty = Math.max(1, Number(quantity) || 1);
    const total = value != null
        ? Number(value)
        : itemPrice * qty + Number(deliveryCharge || 0);

    track('InitiateCheckout', {
        ...productParams(product, qty),
        value: total,
    });
}

function trackAddPaymentInfo(product, { paymentMethod, quantity = 1, value } = {}) {
    const itemPrice = Number(product.price) || 0;
    const qty = Math.max(1, Number(quantity) || 1);
    const total = value != null ? Number(value) : itemPrice * qty;

    track('AddPaymentInfo', {
        ...productParams(product, qty),
        value: total,
        payment_method: paymentMethod || '',
    });
}

function trackPurchase(payload) {
    if (!payload?.product) {
        return;
    }

    const product = payload.product;
    const id = String(product.id);
    const qty = Math.max(1, Number(payload.num_items) || 1);
    const unitPrice = Number(product.price) || 0;

    const params = {
        value: Number(payload.value) || 0,
        currency: payload.currency || CURRENCY,
        content_ids: [id],
        content_type: 'product',
        content_name: product.name,
        content_category: product.category || '',
        contents: [{ id, quantity: qty, item_price: unitPrice }],
        num_items: qty,
    };

    const options = payload.order_id ? { eventID: String(payload.order_id) } : {};

    track('Purchase', params, options);
}

function bindOrderForm(form, product, getState = () => ({})) {
    if (!form || !product) {
        return;
    }

    let leadSent = false;

    try {
        leadSent = sessionStorage.getItem(sessionKey(product.id, 'lead')) === '1';
    } catch (_) {
        /* ignore */
    }

    const sendLead = () => {
        if (leadSent) {
            return;
        }
        leadSent = true;
        try {
            sessionStorage.setItem(sessionKey(product.id, 'lead'), '1');
        } catch (_) {
            /* ignore */
        }
        trackLead(product);
    };

    form.querySelectorAll('input[name="customer_name"], input[name="customer_phone"], textarea[name="address"]').forEach((el) => {
        el.addEventListener('focus', sendLead);
        el.addEventListener('input', sendLead, { once: true });
    });

    form.querySelectorAll('input[name="payment_method"]').forEach((el) => {
        el.addEventListener('change', () => {
            const state = getState();
            trackAddPaymentInfo(product, {
                paymentMethod: el.value,
                quantity: state.quantity,
                value: state.value,
            });
        });
    });

    let checkoutSent = false;

    form.addEventListener('submit', () => {
        if (checkoutSent) {
            return;
        }
        checkoutSent = true;
        const state = getState();
        trackInitiateCheckout(product, {
            quantity: state.quantity,
            value: state.value,
            deliveryCharge: state.deliveryCharge,
        });
    });
}

function checkoutStateFromForm(form) {
    const quantity = parseInt(form.querySelector('input[name="quantity"]')?.value, 10) || 1;
    const price = parseFloat(form.dataset.productPrice) || 0;
    let deliveryCharge = 0;

    try {
        const options = JSON.parse(form.dataset.deliveryOptions || '[]');
        const selected = form.querySelector('input[name="delivery_option"]:checked');
        if (selected && options[selected.value]) {
            deliveryCharge = parseFloat(options[selected.value].charge) || 0;
        }
    } catch (_) {
        /* ignore */
    }

    return {
        quantity,
        deliveryCharge,
        value: price * quantity + deliveryCharge,
    };
}

export const MetaPixel = {
    isEnabled,
    trackViewContent,
    trackLead,
    trackInitiateCheckout,
    trackAddPaymentInfo,
    trackPurchase,
    bindOrderForm,
    checkoutStateFromForm,
};

if (typeof window !== 'undefined') {
    window.MetaPixel = MetaPixel;
}
