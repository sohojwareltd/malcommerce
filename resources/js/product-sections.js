import { createRoot } from 'react-dom/client';
import ProductSections from './components/ProductSections.jsx';
import React from 'react';

// Wait for DOM to be ready
function initProductSections() {
    const container = document.getElementById('custom-sections');
    if (container) {
        try {
            const layout = JSON.parse(container.dataset.layout || '[]');
            const entityType = container.dataset.entityType || 'product';
            const productId = container.dataset.productId;
            const productName = container.dataset.productName || '';
            const productImage = container.dataset.productImage || '';
            const productShortDescription = container.dataset.productShortDescription || '';
            const productPrice = container.dataset.productPrice;
            const productComparePrice = container.dataset.productComparePrice;
            const productInStock = container.dataset.productInStock === '1';
            const productStockQuantity = container.dataset.productStockQuantity;
            const productCategory = container.dataset.productCategory || '';
            const productSlug = container.dataset.productSlug || '';
            const courseSlug = container.dataset.courseSlug || '';
            const courseEnrolled = container.dataset.courseEnrolled === '1';
            const myCourseUrl = container.dataset.myCourseUrl || '';
            const authName = container.dataset.authName || '';
            const authPhone = container.dataset.authPhone || '';
            const orderSettings = entityType === 'course'
                ? JSON.parse(container.dataset.checkoutSettings || '{}')
                : JSON.parse(container.dataset.orderSettings || '{}');
            
            if (!layout || !Array.isArray(layout) || layout.length === 0) {
                container.innerHTML = '<div class="p-8 text-center text-gray-600"><p>No page content available.</p><p class="text-sm mt-2">Please create a page layout in the admin panel.</p></div>';
                return;
            }
            
            const root = createRoot(container);
            root.render(React.createElement(ProductSections, { 
                layout,
                entityType,
                productId,
                productName,
                productImage,
                productShortDescription,
                productPrice,
                productComparePrice,
                productInStock,
                productStockQuantity,
                productCategory,
                productSlug,
                courseSlug,
                courseEnrolled,
                myCourseUrl,
                authName,
                authPhone,
                orderSettings: entityType === 'course' ? {
                    title: orderSettings.title || 'কোর্স কিনুন',
                    buttonText: orderSettings.buttonText || 'bKash দিয়ে কিনুন',
                    hideSummary: true,
                    hideQuantity: true,
                    deliveryOptions: [],
                    minQuantity: 1,
                    maxQuantity: 1,
                    paymentOptions: ['bkash'],
                } : orderSettings,
            }));
        } catch (error) {
            console.error('Error rendering product sections:', error);
            container.innerHTML = '<div class="p-8 text-center text-red-600">Error loading page content. Please refresh the page.</div>';
        }
    }
}

// Try to initialize immediately, or wait for DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initProductSections);
} else {
    // DOM is already ready
    initProductSections();
}

