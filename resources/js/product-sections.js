import { createRoot } from 'react-dom/client';
import ProductSections from './components/ProductSections.jsx';
import React from 'react';

// Wait for DOM to be ready
function initProductSections() {
    const container = document.getElementById('custom-sections');
    if (container) {
        try {
            const layout = JSON.parse(container.dataset.layout || '[]');
            const productId = container.dataset.productId;
            const productPrice = container.dataset.productPrice;
            const productComparePrice = container.dataset.productComparePrice;
            const productInStock = container.dataset.productInStock === '1';
            const productStockQuantity = container.dataset.productStockQuantity;
            
            if (!layout || !Array.isArray(layout) || layout.length === 0) {
                container.innerHTML = '<div class="p-8 text-center text-gray-600"><p>No page content available.</p><p class="text-sm mt-2">Please create a page layout in the admin panel.</p></div>';
                return;
            }
            
            const root = createRoot(container);
            root.render(React.createElement(ProductSections, { 
                layout,
                productId,
                productPrice,
                productComparePrice,
                productInStock,
                productStockQuantity
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

