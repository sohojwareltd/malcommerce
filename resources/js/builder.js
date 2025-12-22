import { createRoot } from 'react-dom/client';
import ThemeBuilder from './components/ThemeBuilder.jsx';
import React from 'react';

// Wait for DOM to be ready
function initBuilder() {
    const container = document.getElementById('theme-builder-container');
    if (!container) {
        console.error('Theme builder container not found');
        return;
    }

    try {
        const initialLayout = JSON.parse(container.dataset.layout || '[]');
        const productId = container.dataset.productId;
        
        if (!productId) {
            console.error('Product ID not found');
            return;
        }
        
        const handleSave = (layout) => {
            const csrfToken = document.querySelector('meta[name="csrf-token"]');
            if (!csrfToken) {
                alert('CSRF token not found. Please refresh the page.');
                return;
            }
            
            // Save via AJAX
            fetch(`/admin/products/${productId}/builder`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken.content,
                    'Accept': 'application/json',
                },
                body: JSON.stringify({
                    page_layout: layout
                })
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Show success message
                    const successDiv = document.createElement('div');
                    successDiv.className = 'fixed top-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg z-50';
                    successDiv.textContent = data.message || 'Layout saved successfully!';
                    document.body.appendChild(successDiv);
                    setTimeout(() => {
                        successDiv.remove();
                    }, 3000);
                }
            })
            .catch(error => {
                console.error('Error saving layout:', error);
                alert('Error saving layout. Please try again.');
            });
        };
        
        // Get product data from data attributes
        const productSlug = container.dataset.productSlug || '';
        const productData = {
            name: container.dataset.productName || '',
            description: container.dataset.productDescription || '',
            shortDescription: container.dataset.productShortDescription || '',
            price: container.dataset.productPrice || '',
            comparePrice: container.dataset.productComparePrice || '',
            images: JSON.parse(container.dataset.productImages || '[]'),
        };
        
        const root = createRoot(container);
        root.render(React.createElement(ThemeBuilder, {
            initialLayout,
            onSave: handleSave,
            productSlug: productSlug,
            productData: productData
        }));
    } catch (error) {
        console.error('Error initializing theme builder:', error);
        container.innerHTML = '<div class="p-8 text-center text-red-600">Error loading page builder. Please refresh the page.</div>';
    }
}

// Try to initialize immediately, or wait for DOM
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initBuilder);
} else {
    // DOM is already ready
    initBuilder();
}

