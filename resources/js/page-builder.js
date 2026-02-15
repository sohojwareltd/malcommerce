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
            const productId = container.dataset.productId || null;
            const productName = container.dataset.productName || '';
            const productImage = container.dataset.productImage || '';
            const productPrice = container.dataset.productPrice || null;
            const productComparePrice = container.dataset.productComparePrice || null;
            const productInStock = container.dataset.productInStock === '1';
            const productStockQuantity = container.dataset.productStockQuantity || null;
            const orderSettings = (() => {
                try {
                    return JSON.parse(container.dataset.orderSettings || '{}');
                } catch (_) {
                    return {};
                }
            })();
            
            const root = createRoot(container);
            root.render(React.createElement(PageBuilder, {
                initialSections: initialSections,
                productId: productId,
                productName: productName,
                productImage: productImage,
                productPrice: productPrice,
                productComparePrice: productComparePrice,
                productInStock: productInStock,
                productStockQuantity: productStockQuantity,
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

