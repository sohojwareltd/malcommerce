import { createRoot } from 'react-dom/client';
import PageBuilder from './components/PageBuilder.jsx';
import React from 'react';

// Wait for DOM to be ready
function initPageBuilder() {
    const container = document.getElementById('page-builder-editor');
    if (container) {
        try {
            const scriptEl = document.getElementById('page-builder-initial-sections');
            const initialSections = scriptEl && scriptEl.textContent
                ? JSON.parse(scriptEl.textContent)
                : [];
            const entityType = container.dataset.entityType || 'product';
            const productId = container.dataset.productId || null;
            const productName = container.dataset.productName || '';
            const productImage = container.dataset.productImage || '';
            const productPrice = container.dataset.productPrice || null;
            const productComparePrice = container.dataset.productComparePrice || null;
            const productInStock = container.dataset.productInStock === '1';
            const productStockQuantity = container.dataset.productStockQuantity || null;
            const courseSlug = container.dataset.courseSlug || '';
            const orderSettings = (() => {
                if (entityType === 'course') {
                    try {
                        const checkout = JSON.parse(container.dataset.checkoutSettings || '{}');
                        return {
                            title: checkout.title || 'কোর্স কিনুন',
                            buttonText: checkout.buttonText || 'bKash দিয়ে কিনুন',
                            hideSummary: true,
                            hideQuantity: true,
                            deliveryOptions: [],
                            minQuantity: 1,
                            maxQuantity: 1,
                            paymentOptions: ['bkash'],
                        };
                    } catch (_) {
                        return {
                            title: 'কোর্স কিনুন',
                            buttonText: 'bKash দিয়ে কিনুন',
                            hideSummary: true,
                            hideQuantity: true,
                            deliveryOptions: [],
                            minQuantity: 1,
                            maxQuantity: 1,
                            paymentOptions: ['bkash'],
                        };
                    }
                }
                try {
                    return JSON.parse(container.dataset.orderSettings || '{}');
                } catch (_) {
                    return {};
                }
            })();
            
            const root = createRoot(container);
            root.render(React.createElement(PageBuilder, {
                initialSections: initialSections,
                entityType: entityType,
                productId: productId,
                productName: productName,
                productImage: productImage,
                productPrice: productPrice,
                productComparePrice: productComparePrice,
                productInStock: productInStock,
                productStockQuantity: productStockQuantity,
                courseSlug: courseSlug,
                orderSettings: orderSettings
            }));
        } catch (error) {
            console.error('Error rendering page builder:', error);
            container.innerHTML = '<div class="p-8 text-center text-red-600">Error loading page builder. Please refresh the page.</div>';
        }
    }
}

// Try to initialize immediately, or wait for DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initPageBuilder);
} else {
    // DOM is already ready
    initPageBuilder();
}

