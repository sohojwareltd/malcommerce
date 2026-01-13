import React, { useEffect, useState } from 'react';

// Helper function to check if string contains HTML tags
const containsHTML = (str) => {
    if (!str || typeof str !== 'string') return false;
    return /<[a-z][\s\S]*>/i.test(str);
};

// Helper component to render text that may contain HTML
const RenderText = ({ content, className = '', style = {}, tag = 'div' }) => {
    if (!content) return null;
    
    const Tag = tag;
    const hasHTML = containsHTML(content);
    
    if (hasHTML) {
        return <Tag className={className} style={style} dangerouslySetInnerHTML={{ __html: content }} />;
    } else {
        return <Tag className={className} style={style}>{content}</Tag>;
    }
};

const ProductSections = ({ layout, productId, productName, productImage, productShortDescription, productPrice, productComparePrice, productInStock, productStockQuantity, orderSettings = {} }) => {
    useEffect(() => {
        // Initialize countdown timers for pricing sections
        layout?.forEach((section, index) => {
            if (section.type === 'pricing' && section.countdown_date) {
                const countdownElement = document.getElementById(`countdown-${index}`);
                if (countdownElement) {
                    const updateCountdown = () => {
                        const now = new Date().getTime();
                        const endDate = new Date(section.countdown_date).getTime();
                        const distance = endDate - now;

                        if (distance < 0) {
                            countdownElement.textContent = 'Offer Expired';
                            return;
                        }

                        const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                        const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                        const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                        const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                        countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    };

                    updateCountdown();
                    const interval = setInterval(updateCountdown, 1000);

                    return () => clearInterval(interval);
                }
            }
        });

        // Initialize Slick carousels for image and video sliders
        const initSliders = () => {
            if (typeof window.$ !== 'undefined' && window.$.fn.slick) {
                layout?.forEach((section, index) => {
                    if (section.type === 'image_slider' || section.type === 'video_slider') {
                        const sliderId = `slider-${index}`;
                        const sliderElement = document.getElementById(sliderId);
                        if (sliderElement && !window.$(sliderElement).hasClass('slick-initialized')) {
                            // Show the slider container
                            sliderElement.style.display = 'block';
                            
                            window.$(sliderElement).slick({
                                slidesToShow: 1,
                                slidesToScroll: 1,
                                autoplay: section.autoplay !== false,
                                autoplaySpeed: section.autoplaySpeed || 5000,
                                dots: section.dots !== false,
                                arrows: section.arrows !== false,
                                fade: false,
                                infinite: true,
                                speed: 600,
                                prevArrow: '<button type="button" class="slick-prev"><i class="fas fa-chevron-left"></i></button>',
                                nextArrow: '<button type="button" class="slick-next"><i class="fas fa-chevron-right"></i></button>',
                                responsive: [
                                    {
                                        breakpoint: 768,
                                        settings: {
                                            arrows: false,
                                            slidesToShow: 1,
                                            slidesToScroll: 1
                                        }
                                    }
                                ]
                            });
                        }
                    }
                });
            }
        };

        // Wait for DOM and jQuery to be ready
        const initSlidersWithDelay = () => {
            // Use setTimeout to ensure DOM is fully rendered
            setTimeout(() => {
                initSliders();
            }, 200);
        };

        if (typeof window.$ !== 'undefined' && window.$.fn.slick) {
            initSlidersWithDelay();
        } else if (typeof window.$ !== 'undefined') {
            // jQuery loaded but Slick might not be ready
            window.$(document).ready(initSlidersWithDelay);
        } else {
            // Wait for jQuery to load
            const checkJQuery = setInterval(() => {
                if (typeof window.$ !== 'undefined' && window.$.fn.slick) {
                    clearInterval(checkJQuery);
                    initSlidersWithDelay();
                }
            }, 100);
            
            // Clear interval after 5 seconds if jQuery still not loaded
            setTimeout(() => clearInterval(checkJQuery), 5000);
        }

        // Cleanup function
        return () => {
            if (typeof window.$ !== 'undefined' && window.$.fn.slick) {
                layout?.forEach((section, index) => {
                    if (section.type === 'image_slider' || section.type === 'video_slider') {
                        const sliderId = `slider-${index}`;
                        const sliderElement = document.getElementById(sliderId);
                        if (sliderElement && window.$(sliderElement).hasClass('slick-initialized')) {
                            window.$(sliderElement).slick('unslick');
                        }
                    }
                });
            }
        };
    }, [layout]);

    if (!layout || !Array.isArray(layout)) {
        return null;
    }

    // Determine the first section that should render the order form
    // Priority: order_form first, then call_to_action with order keywords
    const firstOrderFormIndex = layout.findIndex((section) => section.type === 'order_form');
    const firstOrderCTIndex = layout.findIndex((section) =>
        section.type === 'call_to_action' &&
        (
            section.button_link === '#order' ||
            section.button_text?.toLowerCase().includes('order') ||
            section.button_text?.toLowerCase().includes('অর্ডার')
        )
    );
    const firstOrderIndex = firstOrderFormIndex !== -1 ? firstOrderFormIndex : firstOrderCTIndex;

    // Order Form Component with React State (single primary form per page)
    const OrderForm = ({ index, section, isPrimary }) => {
        if (!isPrimary) return null;

        const [quantity, setQuantity] = useState(1);
        const deliveryOptions = orderSettings.deliveryOptions || [];
        const [selectedDelivery, setSelectedDelivery] = useState(deliveryOptions.length > 0 ? 0 : null);
        const price = parseFloat(productPrice) || 0;
        const minQuantity = parseInt(orderSettings.minQuantity || 0);
        const maxQuantitySetting = parseInt(orderSettings.maxQuantity || 0);
        const hideSummary = orderSettings.hideSummary || false;
        const hideQuantity = orderSettings.hideQuantity || false;
        const orderFormTitle = orderSettings.title || 'অর্ডার করুন';
        const orderButtonText = orderSettings.buttonText || 'অর্ডার নিশ্চিত করুন';
        
        const deliveryCharge = selectedDelivery !== null && deliveryOptions[selectedDelivery] 
            ? parseFloat(deliveryOptions[selectedDelivery].charge || 0) 
            : 0;
        const totalPrice = (quantity * price) + deliveryCharge;
        const stockQuantity = parseInt(productStockQuantity) || 999;
        const maxQuantity = maxQuantitySetting > 0 ? Math.min(stockQuantity, maxQuantitySetting) : stockQuantity;
        const effectiveMinQuantity = Math.max(1, minQuantity);

        // Initialize first delivery option if available (handled in useState initialization above)

        const handleQuantityChange = (delta) => {
            const newQuantity = quantity + delta;
            if (newQuantity >= effectiveMinQuantity && newQuantity <= maxQuantity) {
                setQuantity(newQuantity);
            }
        };

        return (
            <div className="w-full py-8 theme-section" id="page-order-form">
                {(section.title || section.content) && (
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 text-center">
                        {section.title && (
                            <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                        )}
                        {section.content && (
                            <RenderText content={section.content} tag="p" className="text-lg font-bangla" style={{ lineHeight: '1.8' }} />
                        )}
                    </div>
                )}
                {productId && productInStock && (
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                        {/* Order Form */}
                        <div className="card overflow-hidden" style={section.background_color ? { backgroundColor: section.background_color } : {}}>
                            <h2 className="text-xl md:text-2xl font-bold mb-6 text-gray-900 font-bangla break-words">{orderFormTitle}</h2>
                            <form action="/orders" method="POST" id={`product-order-form-${index}`} className="space-y-4">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content || ''} />
                                <input type="hidden" name="product_id" value={productId} />
                                
                                {/* Quantity */}
                                {!hideQuantity && (
                                <div className="mb-6">
                                    <label className="block text-sm font-medium text-gray-700 mb-3 font-bangla">পরিমাণ</label>
                                    <div className="flex items-center gap-2 md:gap-4 flex-wrap">
                                        {/* Product Image and Title */}
                                        <div className="flex items-center gap-2 md:gap-3 flex-1 min-w-0">
                                            {productImage ? (
                                                <img 
                                                    src={productImage} 
                                                    alt={productName} 
                                                    className="w-14 h-14 md:w-20 md:h-20 rounded-lg object-cover border border-gray-200 shadow-sm flex-shrink-0"
                                                />
                                            ) : (
                                                <div className="w-14 h-14 md:w-20 md:h-20 rounded-lg bg-gray-100 border border-gray-200 flex items-center justify-center flex-shrink-0">
                                                    <svg className="w-7 h-7 md:w-10 md:h-10 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                </div>
                                            )}
                                            <div className="flex-1 min-w-0 overflow-hidden">
                                                <h3 className="font-semibold text-gray-900 font-bangla text-sm md:text-base truncate">{productName}</h3>
                                                <p className="text-xs md:text-sm text-gray-600 font-bangla whitespace-nowrap">৳{price.toLocaleString('bn-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}</p>
                                            </div>
                                        </div>
                                        
                                        {/* Quantity Controls */}
                                        <div className="flex items-center gap-2 md:gap-3 flex-shrink-0">
                                            <button 
                                                type="button"
                                                onClick={() => handleQuantityChange(-1)}
                                                className="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                                            >
                                                -
                                            </button>
                                            <input 
                                                type="number"
                                                name="quantity"
                                                value={quantity}
                                                onChange={(e) => {
                                                    const val = parseInt(e.target.value) || effectiveMinQuantity;
                                                    if (val >= effectiveMinQuantity && val <= maxQuantity) setQuantity(val);
                                                }}
                                                min={effectiveMinQuantity}
                                                max={maxQuantity}
                                                className="w-16 md:w-20 text-center border border-gray-300 rounded-lg px-1 md:px-2 py-2 font-bold focus:ring-2 focus:ring-primary focus:border-transparent text-sm md:text-base"
                                                required
                                            />
                                            <button 
                                                type="button"
                                                onClick={() => handleQuantityChange(1)}
                                                className="w-9 h-9 md:w-10 md:h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                                            >
                                                +
                                            </button>
                                        </div>
                                        <span className="text-gray-600 font-bangla text-xs md:text-sm hidden md:inline whitespace-nowrap">(স্টকে: {maxQuantity === 999 ? '∞' : maxQuantity} টি)</span>
                                    </div>
                                    <div className="mt-2 md:hidden">
                                        <span className="text-gray-600 font-bangla text-sm">স্টকে: {maxQuantity === 999 ? '∞' : maxQuantity} টি</span>
                                    </div>
                                </div>
                                )}
                                {hideQuantity && <input type="hidden" name="quantity" value={quantity} />}
                                
                                {/* Customer Information */}
                                <div className="space-y-4 mb-6">
                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                            আপনার নাম <span className="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="text"
                                            name="customer_name"
                                            required
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                                            placeholder="আপনার সম্পূর্ণ নাম"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                            মোবাইল নাম্বার <span className="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="tel"
                                            name="customer_phone"
                                            required
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="01XXXXXXXXX"
                                            pattern="[0-9]{11}"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                            পূর্ণ ঠিকানা <span className="text-red-500">*</span>
                                        </label>
                                        <textarea 
                                            name="address"
                                            rows="4"
                                            required
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla resize-none"
                                            placeholder=""
                                        ></textarea>
                                    </div>
                                </div>

                                {/* Delivery Options */}
                                {deliveryOptions.length > 0 && (
                                <div className="mb-6">
                                    <label className="block text-sm font-medium text-gray-700 mb-3 font-bangla">
                                        ডেলিভারি অপশন <span className="text-red-500">*</span>
                                    </label>
                                    <div className="space-y-2">
                                        {deliveryOptions.map((option, optIndex) => (
                                            <label key={optIndex} className="flex items-center gap-3 p-3 border border-gray-300 rounded-lg hover:bg-gray-50 cursor-pointer">
                                                <input 
                                                    type="radio" 
                                                    name="delivery_option" 
                                                    value={optIndex}
                                                    checked={selectedDelivery === optIndex}
                                                    onChange={() => setSelectedDelivery(optIndex)}
                                                    className="w-4 h-4 text-primary border-gray-300 focus:ring-primary"
                                                    required
                                                />
                                                <div className="flex-1">
                                                    <div className="font-semibold text-gray-900 font-bangla">{option.name || 'Standard'}</div>
                                                    <div className="text-sm text-gray-600 font-bangla">
                                                        ৳{parseFloat(option.charge || 0).toLocaleString('bn-BD', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}
                                                        {option.days && ` - ${option.days} দিন`}
                                                    </div>
                                                </div>
                                            </label>
                                        ))}
                                    </div>
                                </div>
                                )}

                                {/* Order Summary */}
                                {!hideSummary && (
                                <div className="bg-gray-50 rounded-xl p-6 mb-6 border border-gray-200">
                                    <h3 className="font-semibold text-gray-900 mb-4 font-bangla">অর্ডার সারাংশ</h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-700 font-bangla">পণ্যের মূল্য</span>
                                            <span className="font-semibold text-gray-900">৳{(price * quantity).toLocaleString('bn-BD')}</span>
                                        </div>
                                        {!hideQuantity && (
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-700 font-bangla">পরিমাণ</span>
                                            <span className="font-semibold text-gray-900">{quantity} টি</span>
                                        </div>
                                        )}
                                        <div className="flex justify-between items-center text-sm text-gray-600">
                                            <span className="font-bangla">ডেলিভারি চার্জ</span>
                                            <span className="font-bangla">{deliveryCharge > 0 ? `৳${deliveryCharge.toLocaleString('bn-BD')}` : 'ফ্রি'}</span>
                                        </div>
                                        <div className="border-t border-gray-300 pt-3 mt-3">
                                            <div className="flex justify-between items-center">
                                                <span className="text-lg font-semibold text-gray-900 font-bangla">মোট</span>
                                                <span className="text-2xl font-bold" style={{ color: 'var(--color-primary)' }}>৳{totalPrice.toLocaleString('bn-BD')}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p className="text-xs text-gray-600 mt-4 font-bangla">
                                        💳 ক্যাশ অন ডেলিভারি - অগ্রীম কোন টাকা ছাড়াই অর্ডার করুন
                                    </p>
                                </div>
                                )}

                                {effectiveMinQuantity > 0 && quantity < effectiveMinQuantity && (
                                <div className="mb-4 p-3 bg-yellow-50 border border-yellow-200 rounded-lg">
                                    <p className="text-sm text-yellow-800 font-bangla">
                                        সর্বনিম্ন অর্ডার পরিমাণ: {effectiveMinQuantity} টি
                                    </p>
                                </div>
                                )}
                                {maxQuantity > 0 && quantity > maxQuantity && (
                                <div className="mb-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                    <p className="text-sm text-red-800 font-bangla">
                                        সর্বোচ্চ অর্ডার পরিমাণ: {maxQuantity} টি
                                    </p>
                                </div>
                                )}

                                <button 
                                    type="submit"
                                    className={`w-full btn-primary font-bangla text-base md:text-lg py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 break-words ${
                                        (effectiveMinQuantity > 0 && quantity < effectiveMinQuantity) || 
                                        (maxQuantity > 0 && quantity > maxQuantity) ||
                                        (deliveryOptions.length > 0 && (selectedDelivery === null || selectedDelivery === undefined))
                                            ? 'opacity-50 cursor-not-allowed' : ''
                                    }`}
                                    style={{ backgroundColor: 'var(--color-primary)' }}
                                    disabled={
                                        (effectiveMinQuantity > 0 && quantity < effectiveMinQuantity) || 
                                        (maxQuantity > 0 && quantity > maxQuantity) ||
                                        (deliveryOptions.length > 0 && (selectedDelivery === null || selectedDelivery === undefined))
                                    }
                                >
                                    <span className="whitespace-normal">{orderButtonText} - ৳{totalPrice.toLocaleString('bn-BD')}</span>
                                </button>
                            </form>
                        </div>
                    </div>
                )}
            </div>
        );
    };

    const renderSection = (section, index) => {
        switch (section.type) {
            case 'rich_text':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center font-bangla" style={{ lineHeight: '1.3' }}>
                                    {section.title}
                                </h2>
                            )}
                            <div 
                                className="prose prose-lg max-w-none font-bangla" 
                                style={{ 
                                    lineHeight: '1.8',
                                    fontSize: '1.125rem',
                                    color: '#374151'
                                }} 
                                dangerouslySetInnerHTML={{ __html: section.content }} 
                            />
                        </div>
                    </section>
                );
            
            case 'image_gallery':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {section.images?.map((image, imgIndex) => (
                                    <div key={imgIndex} className="theme-card">
                                        <img src={image} alt={section.title || 'Gallery'} className="w-full h-auto rounded-lg" />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                );
            
            case 'image_slider':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            {section.images && section.images.length > 0 ? (
                                <>
                                    <div id={`slider-${index}`} className="image-slider" style={{ display: 'none' }}>
                                        {section.images.map((image, imgIndex) => (
                                            <div key={imgIndex} className="slider-slide">
                                                <img src={image} alt={`${section.title || 'Slide'} ${imgIndex + 1}`} className="w-full h-auto object-contain rounded-lg" />
                                            </div>
                                        ))}
                                    </div>
                                    <style>{`
                                        #slider-${index}.image-slider:not(.slick-initialized) .slider-slide {
                                            display: none;
                                        }
                                        #slider-${index}.image-slider:not(.slick-initialized) .slider-slide:first-child {
                                            display: block;
                                        }
                                        #slider-${index}.image-slider .slick-slide {
                                            outline: none;
                                        }
                                        #slider-${index}.image-slider .slick-prev,
                                        #slider-${index}.image-slider .slick-next {
                                            z-index: 10;
                                            width: 50px;
                                            height: 50px;
                                            background: rgba(255, 255, 255, 0.9);
                                            border-radius: 50%;
                                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                                            transition: all 0.2s ease;
                                            color: var(--color-primary);
                                            border: none;
                                            cursor: pointer;
                                        }
                                        #slider-${index}.image-slider .slick-prev:hover,
                                        #slider-${index}.image-slider .slick-next:hover {
                                            background: white;
                                            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                                        }
                                        #slider-${index}.image-slider .slick-prev {
                                            left: 15px;
                                        }
                                        #slider-${index}.image-slider .slick-next {
                                            right: 15px;
                                        }
                                        #slider-${index}.image-slider .slick-dots {
                                            bottom: 15px;
                                            z-index: 10;
                                        }
                                        #slider-${index}.image-slider .slick-dots li button {
                                            width: 12px;
                                            height: 12px;
                                            border-radius: 50%;
                                            background: var(--color-primary);
                                            opacity: 0.5;
                                            border: none;
                                            cursor: pointer;
                                        }
                                        #slider-${index}.image-slider .slick-dots li.slick-active button {
                                            opacity: 1;
                                        }
                                    `}</style>
                                </>
                            ) : (
                                <p className="text-center text-gray-500 font-bangla py-8">No images added to slider</p>
                            )}
                        </div>
                    </section>
                );
            
            case 'video_slider':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            {section.videos && section.videos.length > 0 ? (
                                <>
                                    <div id={`slider-${index}`} className="video-slider" style={{ display: 'none' }}>
                                        {section.videos.map((video, vidIndex) => (
                                            <div key={vidIndex} className="slider-slide">
                                                <div className="aspect-video rounded-lg overflow-hidden shadow-lg">
                                                    <iframe
                                                        src={video.url}
                                                        className="w-full h-full"
                                                        allowFullScreen
                                                        title={video.title || `Video ${vidIndex + 1}`}
                                                    />
                                                </div>
                                                {video.title && (
                                                    <h3 className="text-lg font-semibold text-gray-900 font-bangla mt-4 text-center">{video.title}</h3>
                                                )}
                                            </div>
                                        ))}
                                    </div>
                                    <style>{`
                                        #slider-${index}.video-slider:not(.slick-initialized) .slider-slide {
                                            display: none;
                                        }
                                        #slider-${index}.video-slider:not(.slick-initialized) .slider-slide:first-child {
                                            display: block;
                                        }
                                        #slider-${index}.video-slider .slick-slide {
                                            outline: none;
                                        }
                                        #slider-${index}.video-slider .slick-prev,
                                        #slider-${index}.video-slider .slick-next {
                                            z-index: 10;
                                            width: 50px;
                                            height: 50px;
                                            background: rgba(255, 255, 255, 0.9);
                                            border-radius: 50%;
                                            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
                                            transition: all 0.2s ease;
                                            color: var(--color-primary);
                                            border: none;
                                            cursor: pointer;
                                        }
                                        #slider-${index}.video-slider .slick-prev:hover,
                                        #slider-${index}.video-slider .slick-next:hover {
                                            background: white;
                                            box-shadow: 0 6px 12px rgba(0, 0, 0, 0.15);
                                        }
                                        #slider-${index}.video-slider .slick-prev {
                                            left: 15px;
                                        }
                                        #slider-${index}.video-slider .slick-next {
                                            right: 15px;
                                        }
                                        #slider-${index}.video-slider .slick-dots {
                                            bottom: 15px;
                                            z-index: 10;
                                        }
                                        #slider-${index}.video-slider .slick-dots li button {
                                            width: 12px;
                                            height: 12px;
                                            border-radius: 50%;
                                            background: var(--color-primary);
                                            opacity: 0.5;
                                            border: none;
                                            cursor: pointer;
                                        }
                                        #slider-${index}.video-slider .slick-dots li.slick-active button {
                                            opacity: 1;
                                        }
                                    `}</style>
                                </>
                            ) : (
                                <p className="text-center text-gray-500 font-bangla py-8">No videos added to slider</p>
                            )}
                        </div>
                    </section>
                );
            
            case 'banner':
                const getBannerBackgroundStyle = () => {
                    const baseStyle = { color: section.text_color || '#000000' };
                    const bgType = section.background_type || 'color';
                    
                    if (bgType === 'gradient') {
                        return {
                            ...baseStyle,
                            background: `linear-gradient(to right, ${section.gradient_start || '#FFD700'}, ${section.gradient_end || '#FFA500'})`
                        };
                    } else if (bgType === 'image' && section.background_image) {
                        return {
                            ...baseStyle,
                            backgroundImage: `url(${section.background_image})`,
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            backgroundRepeat: 'no-repeat'
                        };
                    } else {
                        return {
                            ...baseStyle,
                            backgroundColor: section.background_color || '#FFD700'
                        };
                    }
                };
                
                return (
                    <div key={index} className="w-full py-8 md:py-12 my-6 md:my-8" style={getBannerBackgroundStyle()}>
                        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            {section.title && (
                                <RenderText 
                                    content={section.title}
                                    tag="h2"
                                    className="text-2xl md:text-3xl lg:text-4xl font-bold mb-3 md:mb-4 font-bangla" 
                                    style={{ lineHeight: '1.3' }}
                                />
                            )}
                            {section.content && (
                                <RenderText 
                                    content={section.content}
                                    tag="p"
                                    className="text-lg md:text-xl font-bangla" 
                                    style={{ lineHeight: '1.6' }}
                                />
                            )}
                            {section.images && section.images[0] && (
                                <div className="mt-6 md:mt-8">
                                    <img src={section.images[0]} alt={section.title} className="mx-auto max-w-full rounded-lg" />
                                </div>
                            )}
                        </div>
                    </div>
                );
            
            case 'faq':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center font-bangla" style={{ lineHeight: '1.3' }} />
                            )}
                            <div className="space-y-5 md:space-y-6">
                                {section.items?.map((faq, faqIndex) => (
                                    <div key={faqIndex} className="pb-5 md:pb-6 border-b border-gray-200 last:border-b-0">
                                        <RenderText content={faq.question} tag="h3" className="font-semibold text-lg md:text-xl mb-3 font-bangla text-gray-900" style={{ lineHeight: '1.5' }} />
                                        <RenderText content={faq.answer} tag="p" className="text-gray-600 text-base md:text-lg font-bangla" style={{ lineHeight: '1.7' }} />
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                );
            
            case 'testimonials':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center font-bangla" style={{ lineHeight: '1.3' }}>
                                    {section.title}
                                </h2>
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 md:gap-8">
                                {section.items?.map((testimonial, testIndex) => (
                                    <div key={testIndex} className="text-center">
                                        <p className="text-gray-700 mb-4 text-base md:text-lg font-bangla" style={{ lineHeight: '1.8' }}>
                                            "{testimonial.text}"
                                        </p>
                                        <p className="font-semibold text-gray-900 font-bangla">- {testimonial.author}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                );
            
            case 'video':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="aspect-video rounded-lg overflow-hidden shadow-lg">
                                <iframe
                                    src={section.url}
                                    className="w-full h-full"
                                    allowFullScreen
                                    title={section.title || 'Video'}
                                />
                            </div>
                        </div>
                    </div>
                );
            
            case 'specifications':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="theme-card overflow-hidden">
                                <table className="w-full">
                                    <tbody>
                                        {section.items?.map((spec, specIndex) => (
                                            <tr key={specIndex} className="border-b last:border-b-0">
                                                <td className="px-6 py-4 font-semibold bg-gray-50 w-1/3 font-bangla">{spec.label}</td>
                                                <td className="px-6 py-4 font-bangla">{spec.value}</td>
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                );
            
            case 'comparison':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="theme-card overflow-x-auto">
                                <table className="w-full">
                                    <thead style={{ backgroundColor: 'var(--color-primary)', color: 'white' }}>
                                        <tr>
                                            <th className="px-6 py-4 text-left font-bangla">Feature</th>
                                            {section.items?.map((item, itemIndex) => (
                                                <th key={itemIndex} className="px-6 py-4 text-center font-bangla">{item.name || `Option ${itemIndex + 1}`}</th>
                                            ))}
                                        </tr>
                                    </thead>
                                    <tbody>
                                        {section.items?.[0]?.features?.map((feature, featIndex) => (
                                            <tr key={featIndex} className="border-b">
                                                <td className="px-6 py-4 font-semibold font-bangla">{feature.label}</td>
                                                {section.items.map((item, itemIndex) => (
                                                    <td key={itemIndex} className="px-6 py-4 text-center font-bangla">
                                                        {item.features?.[featIndex]?.value || '-'}
                                                    </td>
                                                ))}
                                            </tr>
                                        ))}
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                );
            
            case 'order_form': {
                // Only render the first order form as primary
                if (index === firstOrderIndex && productInStock && productId) {
                    return <OrderForm key={index} index={index} section={section} isPrimary={true} />;
                }
                
                // Other order forms are ignored (only one allowed)
                return null;
            }
            
            case 'call_to_action': {
                const isOrderCTA =
                    section.button_link === '#order' ||
                    section.button_text?.toLowerCase().includes('order') ||
                    section.button_text?.toLowerCase().includes('অর্ডার');

                // Primary order CTA renders the single shared order form
                if (isOrderCTA && index === firstOrderIndex && productInStock && productId) {
                    return <OrderForm key={index} index={index} section={section} isPrimary={true} />;
                }

                // Secondary CTAs just scroll to the primary order form
                const handleOrderScroll = (e) => {
                    e.preventDefault();
                    const target = document.getElementById('page-order-form');
                    if (target) {
                        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
                    }
                };

                const ctaStyle = {
                    backgroundImage: section.background_image ? `url(${section.background_image})` : undefined,
                    backgroundColor: section.background_image ? undefined : (section.background_color || '#008060'),
                    backgroundSize: section.background_image ? 'cover' : undefined,
                    backgroundPosition: section.background_image ? 'center' : undefined,
                    color: section.text_color || '#FFFFFF',
                };
                return (
                    <section
                        key={index}
                        className="py-12 md:py-16"
                        style={ctaStyle}
                    >
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="text-2xl md:text-3xl lg:text-4xl font-bold mb-4 md:mb-6 font-bangla" style={{ lineHeight: '1.3' }} />
                            )}
                            {section.content && (
                                <RenderText
                                    content={section.content}
                                    tag="p"
                                    className="text-lg md:text-xl mb-6 md:mb-8 font-bangla"
                                    style={{ lineHeight: '1.6' }}
                                />
                            )}
                            {section.button_text && (
                                <a
                                    href={isOrderCTA ? '#page-order-form' : section.button_link || '#'}
                                    onClick={isOrderCTA ? handleOrderScroll : undefined}
                                    className="inline-block font-bangla text-lg md:text-xl font-semibold px-8 md:px-12 py-4 md:py-5 rounded-lg transition transform hover:scale-105 shadow-lg hover:shadow-xl"
                                    style={{
                                        backgroundColor: section.button_color || '#FFFFFF',
                                        color: section.background_color || '#008060',
                                    }}
                                >
                                    {section.button_text}
                                </a>
                            )}
                        </div>
                    </section>
                );
            }
            
            case 'tabs':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="theme-card">
                                <TabsComponent items={section.items} />
                            </div>
                        </div>
                    </div>
                );
            
            case 'grid':
                const gridGap = section.gap === 'none' ? '0' : section.gap === 'small' ? '0.5rem' : section.gap === 'large' ? '2rem' : '1rem';
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="grid gap-4" style={{ gridTemplateColumns: `repeat(${section.columns || 2}, 1fr)`, gap: gridGap }}>
                                {(section.items || []).slice(0, section.columns || 2).map((item, i) => (
                                    <div key={i} className="theme-card">
                                        {item.type === 'text' && <RenderText content={item.content} className="prose max-w-none font-bangla" />}
                                        {item.type === 'image' && item.image && <img src={item.image} alt="" className="w-full rounded-lg" />}
                                        {item.type === 'html' && <RenderText content={item.content} className="font-bangla" />}
                                        {item.type === 'video' && item.content && (
                                            <div className="aspect-video">
                                                <iframe src={item.content} className="w-full h-full rounded-lg" allowFullScreen />
                                            </div>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                );
            
            case 'flex':
                const flexDirection = section.direction || 'row';
                const flexAlign = section.align || 'start';
                const flexJustify = section.justify || 'start';
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />}
                            <div className="flex gap-4 flex-wrap" style={{ 
                                flexDirection,
                                alignItems: flexAlign === 'start' ? 'flex-start' : flexAlign === 'end' ? 'flex-end' : flexAlign === 'center' ? 'center' : 'stretch',
                                justifyContent: flexJustify === 'start' ? 'flex-start' : flexJustify === 'end' ? 'flex-end' : flexJustify === 'center' ? 'center' : flexJustify === 'between' ? 'space-between' : 'space-around'
                            }}>
                                {(section.items || []).map((item, i) => (
                                    <div key={i} className="theme-card flex-1 min-w-[200px]">
                                        {item.type === 'text' && <RenderText content={item.content} className="prose max-w-none font-bangla" />}
                                        {item.type === 'image' && item.image && <img src={item.image} alt="" className="w-full rounded-lg" />}
                                        {item.type === 'html' && <RenderText content={item.content} className="font-bangla" />}
                                        {item.type === 'button' && (
                                            <a href={item.buttonLink || '#'} className="btn-primary font-bangla inline-block">
                                                {item.buttonText || 'Button'}
                                            </a>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                );
            
            case 'spacer':
                return (
                    <div key={index} style={{ height: `${section.height || 50}px` }} className="my-4"></div>
                );
            
            case 'container':
                const maxWidthMap = {
                    'full': '100%',
                    '7xl': '1280px',
                    '6xl': '1152px',
                    '5xl': '1024px',
                    '4xl': '896px',
                    '3xl': '768px'
                };
                const paddingMap = {
                    'none': '0',
                    'small': '1rem',
                    'medium': '2rem',
                    'large': '4rem'
                };
                return (
                    <div key={index} className="my-8" style={{
                        maxWidth: maxWidthMap[section.maxWidth || 'full'],
                        margin: '0 auto',
                        padding: paddingMap[section.padding || 'medium'],
                        backgroundColor: section.backgroundColor || 'var(--color-background)',
                        borderRadius: 'var(--radius-xl)'
                    }}>
                        <RenderText content={section.content} className="font-bangla" />
                    </div>
                );
            
            case 'hero':
                // Determine background style based on background_type
                const getHeroBackgroundStyle = () => {
                    const baseStyle = { color: section.text_color || '#FFFFFF' };
                    const bgType = section.background_type || 'color';
                    
                    if (bgType === 'gradient') {
                        return {
                            ...baseStyle,
                            background: `linear-gradient(to right, ${section.gradient_start || '#008060'}, ${section.gradient_end || '#006E52'})`
                        };
                    } else if (bgType === 'image' && section.background_image) {
                        return {
                            ...baseStyle,
                            backgroundImage: `url(${section.background_image})`,
                            backgroundSize: 'cover',
                            backgroundPosition: 'center',
                            backgroundRepeat: 'no-repeat'
                        };
                    } else {
                        return {
                            ...baseStyle,
                            backgroundColor: section.background_color || '#008060'
                        };
                    }
                };

                return (
                    <section key={index} className="py-8 md:py-12"
                             style={getHeroBackgroundStyle()}>
                        <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div className="text-center">
                                {section.title && (
                                    <RenderText 
                                        content={section.title}
                                        tag="h1"
                                        className="text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold mb-4 md:mb-6 font-bangla" 
                                        style={{ lineHeight: '1.2' }}
                                    />
                                )}
                                {section.subtitle && (
                                    <RenderText 
                                        content={section.subtitle}
                                        tag="p"
                                        className="text-lg md:text-xl lg:text-2xl mb-4 md:mb-6 font-bangla max-w-4xl mx-auto" 
                                        style={{ lineHeight: '1.5' }}
                                    />
                                )}
                                {section.discount_text && (
                                    <RenderText 
                                        content={section.discount_text}
                                        tag="p"
                                        className="text-xl md:text-2xl font-bold mb-4 md:mb-6 font-bangla"
                                    />
                                )}
                                {section.html_content && (
                                    <div 
                                        className="mb-4 md:mb-6 font-bangla text-lg md:text-xl" 
                                        style={{ lineHeight: '1.6' }}
                                        dangerouslySetInnerHTML={{ __html: section.html_content }} 
                                    />
                                )}
                                {section.video_url && (
                                    <div className="mb-8 md:mb-12 max-w-4xl mx-auto space-y-4">
                                        <div className="aspect-video rounded-lg overflow-hidden shadow-xl">
                                            <iframe
                                                src={section.video_url}
                                                className="w-full h-full"
                                                allowFullScreen
                                                title={section.video_title || section.title || 'Video'}
                                            />
                                        </div>
                                        {(section.video_title || section.title) && (
                                            <h3 className="text-center text-3xl md:text-4xl lg:text-5xl xl:text-6xl font-bold text-gray-900 font-bangla" style={{ lineHeight: '1.2' }}>
                                                {section.video_title || section.title}
                                            </h3>
                                        )}
                                    </div>
                                )}
                                {section.images && section.images[0] && !section.video_url && (
                                    <div className="mb-8 md:mb-12">
                                        <img src={section.images[0]} alt={section.title} className="mx-auto max-w-full rounded-lg" />
                                    </div>
                                )}
                                {(section.button_text || section.button2_text) && (
                                    <div className="flex flex-col md:flex-row flex-wrap items-stretch md:items-center justify-center gap-4 md:gap-6 w-full md:w-auto">
                                        {section.button_text && (
                                            <a href={section.button_link || '#'} 
                                               className="w-full md:w-auto block md:inline-block text-center font-bangla text-base md:text-lg lg:text-xl font-semibold px-6 md:px-8 lg:px-12 py-3 md:py-4 lg:py-5 rounded-lg transition transform hover:scale-105 shadow-lg hover:shadow-xl"
                                               style={{
                                                   backgroundColor: section.button_bg_color || '#FFFFFF',
                                                   color: section.button_text_color || '#008060'
                                               }}
                                               dangerouslySetInnerHTML={containsHTML(section.button_text) ? { __html: section.button_text } : undefined}>
                                                {!containsHTML(section.button_text) && section.button_text}
                                            </a>
                                        )}
                                        {section.button2_text && (
                                            <a href={section.button2_link || '#'} 
                                               className="w-full md:w-auto block md:inline-block text-center font-bangla text-base md:text-lg lg:text-xl font-semibold px-6 md:px-8 lg:px-12 py-3 md:py-4 lg:py-5 rounded-lg transition transform hover:scale-105 shadow-lg hover:shadow-xl"
                                               style={{
                                                   backgroundColor: section.button2_bg_color || '#008060',
                                                   color: section.button2_text_color || '#FFFFFF'
                                               }}
                                               dangerouslySetInnerHTML={containsHTML(section.button2_text) ? { __html: section.button2_text } : undefined}>
                                                {!containsHTML(section.button2_text) && section.button2_text}
                                            </a>
                                        )}
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                );
            
            case 'benefits':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center font-bangla" style={{ lineHeight: '1.3' }} />
                            )}
                            <div className="space-y-3 md:space-y-4">
                                {section.items?.map((benefit, i) => (
                                    <div key={i} className="flex items-start gap-3 md:gap-4 py-2">
                                        <div className="flex-shrink-0 mt-1">
                                            <svg className="w-5 h-5 md:w-6 md:h-6 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                                                <path fillRule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clipRule="evenodd" />
                                            </svg>
                                        </div>
                                        <div className="flex-1">
                                            {benefit.title && (
                                                <RenderText content={benefit.title} tag="h3" className="text-lg md:text-xl font-semibold font-bangla text-gray-900" style={{ lineHeight: '1.6' }} />
                                            )}
                                            {benefit.description && (
                                                <RenderText content={benefit.description} tag="p" className="text-gray-600 text-base md:text-lg font-bangla mt-1" style={{ lineHeight: '1.7' }} />
                                            )}
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                );
            
            case 'pricing':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="text-3xl md:text-4xl font-bold mb-8 md:mb-12 text-center font-bangla" style={{ lineHeight: '1.3' }}>
                                    {section.title}
                                </h2>
                            )}
                            <div className="text-center">
                                {section.original_price && (
                                    <div className="mb-4">
                                        <span className="text-xl md:text-2xl text-gray-500 line-through font-bangla">{section.original_price}</span>
                                    </div>
                                )}
                                {section.offer_price && (
                                    <div className="mb-4">
                                        <span className="text-4xl md:text-5xl lg:text-6xl font-bold font-bangla" style={{ color: '#DC2626' }}>
                                            {section.offer_price}
                                        </span>
                                    </div>
                                )}
                                {section.discount_text && (
                                    <p className="font-bold text-lg md:text-xl mb-6 md:mb-8 font-bangla" style={{ color: '#059669' }}>
                                        {section.discount_text}
                                    </p>
                                )}
                                {section.countdown_date && (
                                    <div className="text-center mb-6 md:mb-8">
                                        <p className="text-sm md:text-base text-gray-600 mb-3 font-bangla">অফার শেষ হতে বাকি:</p>
                                        <div className="inline-flex items-center justify-center px-6 py-3 rounded-lg bg-red-50 border-2 border-red-300">
                                            <span
                                                id={`countdown-${index}`}
                                                className="font-mono text-xl md:text-2xl tracking-wider font-bold font-bangla text-red-600"
                                            >
                                                Loading...
                                            </span>
                                        </div>
                                        <p className="text-xs md:text-sm text-gray-500 mt-3 font-bangla">
                                            সময় শেষ হলে অফার পরিবর্তন হতে পারে।
                                        </p>
                                    </div>
                                )}
                            </div>
                        </div>
                    </section>
                );
            
            case 'steps':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            <div className="space-y-6">
                                {section.items?.map((step, i) => (
                                    <div key={i} className="theme-card border-l-4" style={{ borderLeftColor: 'var(--color-accent)' }}>
                                        <div className="flex items-start gap-4">
                                            <div className="flex-shrink-0 w-12 h-12 rounded-full flex items-center justify-center font-bold text-xl text-white" style={{ backgroundColor: 'var(--color-accent)' }}>
                                                {i + 1}
                                            </div>
                                            <div className="flex-1">
                                                {step.title && (
                                                    <RenderText content={step.title} tag="h3" className="text-xl md:text-2xl font-bold mb-2 font-bangla text-gray-800" />
                                                )}
                                                {step.description && (
                                                    <RenderText content={step.description} tag="p" className="text-gray-700 text-lg font-bangla" style={{ lineHeight: '1.8' }} />
                                                )}
                                            </div>
                                        </div>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                );
            
            case 'social_links':
                return (
                    <section key={index} className="theme-section bg-gray-50">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            <div className="flex flex-wrap justify-center gap-4">
                                {section.items?.map((link, i) => {
                                    const platformColors = {
                                        'Facebook': '#1877F2',
                                        'WhatsApp': '#25D366',
                                        'YouTube': '#FF0000',
                                        'Instagram': 'linear-gradient(to right, #833AB4, #FD1D1D, #FCB045)',
                                    };
                                    const platformIcons = {
                                        'Facebook': 'fab fa-facebook-f',
                                        'WhatsApp': 'fab fa-whatsapp',
                                        'YouTube': 'fab fa-youtube',
                                        'Instagram': 'fab fa-instagram',
                                    };
                                    const bgColor = platformColors[link.platform] || 'var(--color-primary)';
                                    const iconClass = platformIcons[link.platform] || 'fas fa-link';
                                    
                                    return (
                                        <a key={i} 
                                           href={link.url || '#'} 
                                           target="_blank" 
                                           rel="noopener noreferrer"
                                           className="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg transition shadow-md hover:shadow-lg font-bangla font-semibold"
                                           style={{ background: bgColor }}>
                                            <i className={`${iconClass} text-lg`}></i>
                                            <span>{link.platform || 'Link'}</span>
                                        </a>
                                    );
                                })}
                            </div>
                        </div>
                    </section>
                );
            
            case 'contact_info':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <RenderText content={section.title} tag="h2" className="theme-section-title font-bangla" />
                            )}
                            <div className="theme-card">
                                {section.phone && (
                                    <div className="flex items-center justify-center gap-3 mb-4">
                                        <i className="fas fa-phone text-xl" style={{ color: 'var(--color-accent)' }}></i>
                                        <a href={`tel:${section.phone}`} className="text-xl font-bold font-bangla hover:underline" style={{ color: 'var(--color-accent)' }}>{section.phone}</a>
                                    </div>
                                )}
                                {section.email && (
                                    <div className="flex items-center justify-center gap-3 mb-4">
                                        <i className="fas fa-envelope text-xl" style={{ color: 'var(--color-accent)' }}></i>
                                        <a href={`mailto:${section.email}`} className="text-lg font-bangla hover:underline" style={{ color: 'var(--color-accent)' }}>{section.email}</a>
                                    </div>
                                )}
                                {section.address && (
                                    <div className="flex items-start justify-center gap-3">
                                        <i className="fas fa-map-marker-alt text-xl mt-1" style={{ color: 'var(--color-accent)' }}></i>
                                        <p className="text-gray-700 text-lg font-bangla">{section.address}</p>
                                    </div>
                                )}
                                {section.content && (
                                    <RenderText content={section.content} className="mt-4 font-bangla text-lg" />
                                )}
                            </div>
                        </div>
                    </section>
                );
            
            default:
                return null;
        }
    };

    // Tabs Component
    const TabsComponent = ({ items }) => {
        const [activeTab, setActiveTab] = useState(0);
        
        return (
            <>
                <div className="border-b border-gray-200">
                    <div className="flex overflow-x-auto">
                        {items?.map((tab, tabIndex) => (
                            <button
                                key={tabIndex}
                                onClick={() => setActiveTab(tabIndex)}
                                className={`px-6 py-4 font-semibold border-b-2 transition whitespace-nowrap font-bangla ${
                                    activeTab === tabIndex 
                                        ? 'border-primary text-gray-900' 
                                        : 'border-transparent text-gray-600 hover:text-gray-900'
                                }`}
                                style={activeTab === tabIndex ? { borderBottomColor: 'var(--color-primary)' } : {}}
                            >
                                {tab.title || `Tab ${tabIndex + 1}`}
                            </button>
                        ))}
                    </div>
                </div>
                <div className="p-6">
                    {items?.map((tab, tabIndex) => (
                        <div key={tabIndex} style={{ display: activeTab === tabIndex ? 'block' : 'none' }}>
                            <RenderText content={tab.content} className="font-bangla" />
                        </div>
                    ))}
                </div>
            </>
        );
    };

    return (
        <div className="w-full">
            {layout.map((section, index) => renderSection(section, index))}
        </div>
    );
};

export default ProductSections;