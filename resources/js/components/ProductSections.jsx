import React, { useEffect, useState } from 'react';

const ProductSections = ({ layout, productId, productPrice, productComparePrice, productInStock, productStockQuantity }) => {
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
        const price = parseFloat(productPrice) || 0;
        const totalPrice = quantity * price;
        const maxQuantity = parseInt(productStockQuantity) || 999;

        const handleQuantityChange = (delta) => {
            const newQuantity = quantity + delta;
            if (newQuantity >= 1 && newQuantity <= maxQuantity) {
                setQuantity(newQuantity);
            }
        };

        return (
            <div className="w-full py-8 theme-section" id="page-order-form">
                {(section.title || section.content) && (
                    <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 mb-8 text-center">
                        {section.title && (
                            <h2 className="theme-section-title font-bangla">{section.title}</h2>
                        )}
                        {section.content && (
                            <p className="text-lg font-bangla" style={{ lineHeight: '1.8' }}>{section.content}</p>
                        )}
                    </div>
                )}
                {productId && productInStock && (
                    <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                        <div className="card">
                            <h2 className="text-2xl font-bold mb-6 text-gray-900 font-bangla">অর্ডার করুন</h2>
                            <form action="/orders" method="POST" id={`product-order-form-${index}`} className="space-y-4">
                                <input type="hidden" name="_token" value={document.querySelector('meta[name="csrf-token"]')?.content || ''} />
                                <input type="hidden" name="product_id" value={productId} />
                                
                                {/* Quantity */}
                                <div className="mb-6">
                                    <label className="block text-sm font-medium text-gray-700 mb-3 font-bangla">পরিমাণ</label>
                                    <div className="flex items-center gap-3">
                                        <button 
                                            type="button"
                                            onClick={() => handleQuantityChange(-1)}
                                            className="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                                        >
                                            -
                                        </button>
                                        <input 
                                            type="number"
                                            name="quantity"
                                            value={quantity}
                                            onChange={(e) => {
                                                const val = parseInt(e.target.value) || 1;
                                                if (val >= 1 && val <= maxQuantity) setQuantity(val);
                                            }}
                                            min="1"
                                            max={maxQuantity}
                                            className="w-20 text-center border border-gray-300 rounded-lg px-2 py-2 font-bold focus:ring-2 focus:ring-primary focus:border-transparent"
                                            required
                                        />
                                        <button 
                                            type="button"
                                            onClick={() => handleQuantityChange(1)}
                                            className="w-10 h-10 flex items-center justify-center border border-gray-300 rounded-lg hover:bg-gray-50 transition font-bold text-gray-700"
                                        >
                                            +
                                        </button>
                                        <span className="text-gray-600 font-bangla text-sm">(স্টকে: {maxQuantity === 999 ? '∞' : maxQuantity} টি)</span>
                                    </div>
                                </div>
                                
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
                                            ইমেইল <span className="text-red-500">*</span>
                                        </label>
                                        <input 
                                            type="email"
                                            name="customer_email"
                                            required
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                            placeholder="your@email.com"
                                        />
                                    </div>

                                    <div>
                                        <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                            পূর্ণ ঠিকানা <span className="text-red-500">*</span>
                                        </label>
                                        <textarea 
                                            name="address"
                                            rows="3"
                                            required
                                            className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla resize-none"
                                            placeholder="বাড়ি নম্বর, রোড, এলাকা"
                                        ></textarea>
                                    </div>
                                </div>

                                {/* BD Address Details */}
                                <div className="space-y-4 mb-6 border-t pt-6">
                                    <h3 className="font-semibold text-gray-900 font-bangla mb-4">ডেলিভারির ঠিকানা বিস্তারিত</h3>

                                    <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                                জেলা <span className="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text"
                                                name="district"
                                                required
                                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                                                placeholder="ঢাকা"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                                উপজেলা <span className="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text"
                                                name="upazila"
                                                required
                                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                                                placeholder="গুলশান"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                                শহর / গ্রাম <span className="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text"
                                                name="city_village"
                                                required
                                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent font-bangla"
                                                placeholder="ধানমন্ডি"
                                            />
                                        </div>

                                        <div>
                                            <label className="block text-sm font-medium text-gray-700 mb-2 font-bangla">
                                                পোস্ট কোড <span className="text-red-500">*</span>
                                            </label>
                                            <input 
                                                type="text"
                                                name="post_code"
                                                required
                                                className="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-primary focus:border-transparent"
                                                placeholder="1205"
                                            />
                                        </div>
                                    </div>
                                </div>

                                {/* Order Summary */}
                                <div className="bg-gray-50 rounded-xl p-6 mb-6 border border-gray-200">
                                    <h3 className="font-semibold text-gray-900 mb-4 font-bangla">অর্ডার সারাংশ</h3>
                                    <div className="space-y-3">
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-700 font-bangla">পণ্যের মূল্য</span>
                                            <span className="font-semibold text-gray-900">৳{price.toLocaleString('en-IN')}</span>
                                        </div>
                                        <div className="flex justify-between items-center">
                                            <span className="text-gray-700 font-bangla">পরিমাণ</span>
                                            <span className="font-semibold text-gray-900">{quantity} টি</span>
                                        </div>
                                        <div className="flex justify-between items-center text-sm text-gray-600">
                                            <span className="font-bangla">ডেলিভারি চার্জ</span>
                                            <span className="font-bangla text-green-600">ফ্রি</span>
                                        </div>
                                        <div className="border-t border-gray-300 pt-3 mt-3">
                                            <div className="flex justify-between items-center">
                                                <span className="text-lg font-semibold text-gray-900 font-bangla">মোট</span>
                                                <span className="text-2xl font-bold" style={{ color: 'var(--color-primary)' }}>৳{totalPrice.toLocaleString('en-IN')}</span>
                                            </div>
                                        </div>
                                    </div>
                                    <p className="text-xs text-gray-600 mt-4 font-bangla">
                                        💳 ক্যাশ অন ডেলিভারি - অগ্রীম কোন টাকা ছাড়াই অর্ডার করুন
                                    </p>
                                </div>

                                <button 
                                    type="submit"
                                    className="w-full btn-primary font-bangla text-lg py-4 rounded-xl shadow-lg hover:shadow-xl transition-all duration-200"
                                    style={{ backgroundColor: 'var(--color-primary)' }}
                                >
                                    অর্ডার নিশ্চিত করুন - ৳{totalPrice.toLocaleString('en-IN')}
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
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
                            )}
                            <div className="prose max-w-none font-bangla text-lg" style={{ lineHeight: '1.8' }} dangerouslySetInnerHTML={{ __html: section.content }} />
                        </div>
                    </section>
                );
            
            case 'image_gallery':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
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
            
            case 'banner':
                const bannerStyle = {
                    backgroundImage: section.background_image ? `url(${section.background_image})` : undefined,
                    backgroundColor: section.background_image ? undefined : (section.background_color || 'var(--color-primary)'),
                    backgroundSize: section.background_image ? 'cover' : undefined,
                    backgroundPosition: section.background_image ? 'center' : undefined,
                    color: section.text_color || '#FFFFFF'
                };
                return (
                    <div key={index} className="my-8 theme-card overflow-hidden"
                         style={bannerStyle}>
                        <div className="p-8 md:p-12 text-center">
                            {section.title && <h2 className="text-3xl md:text-4xl font-bold mb-4 font-bangla">{section.title}</h2>}
                            {section.content && <p className="text-lg mb-6 font-bangla">{section.content}</p>}
                            {section.images && section.images[0] && (
                                <img src={section.images[0]} alt={section.title} className="mx-auto max-w-full rounded-lg shadow-lg" />
                            )}
                        </div>
                    </div>
                );
            
            case 'faq':
                return (
                    <div key={index} className="theme-section">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
                            <div className="space-y-4">
                                {section.items?.map((faq, faqIndex) => (
                                    <div key={faqIndex} className="theme-card border-l-4" style={{ borderLeftColor: 'var(--color-primary)' }}>
                                        <h3 className="font-semibold text-lg mb-2 font-bangla">{faq.question}</h3>
                                        <p className="text-gray-700 font-bangla">{faq.answer}</p>
                                    </div>
                                ))}
                            </div>
                        </div>
                    </div>
                );
            
            case 'testimonials':
                return (
                    <section key={index} className="theme-section bg-gray-50">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
                            )}
                            <div className="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                                {section.items?.map((testimonial, testIndex) => (
                                    <div key={testIndex} className="theme-card border-l-4" style={{ borderLeftColor: 'var(--color-accent)' }}>
                                        <p className="text-gray-700 mb-4 text-lg font-bangla" style={{ lineHeight: '1.8' }}>
                                            "{testimonial.text}"
                                        </p>
                                        <p className="font-semibold font-bangla" style={{ color: 'var(--color-accent)' }}>- {testimonial.author}</p>
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
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
                    backgroundColor: section.background_image ? undefined : (section.background_color || 'var(--color-primary)'),
                    backgroundSize: section.background_image ? 'cover' : undefined,
                    backgroundPosition: section.background_image ? 'center' : undefined,
                    color: section.text_color || '#FFFFFF',
                };
                return (
                    <section
                        key={index}
                        className="theme-section"
                        style={ctaStyle}
                    >
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            {section.title && (
                                <h2 className="text-2xl md:text-3xl font-bold mb-4 font-bangla">{section.title}</h2>
                            )}
                            {section.content && (
                                <p
                                    className="text-lg mb-6 font-bangla"
                                    style={{ lineHeight: '1.8' }}
                                >
                                    {section.content}
                                </p>
                            )}
                            {section.button_text && (
                                <a
                                    href={isOrderCTA ? '#page-order-form' : section.button_link || '#'}
                                    onClick={isOrderCTA ? handleOrderScroll : undefined}
                                    className="btn-primary font-bangla text-lg px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:scale-105"
                                    style={{
                                        backgroundColor: section.button_color || 'white',
                                        color: section.background_color || 'var(--color-primary)',
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
                            <div className="grid gap-4" style={{ gridTemplateColumns: `repeat(${section.columns || 2}, 1fr)`, gap: gridGap }}>
                                {(section.items || []).slice(0, section.columns || 2).map((item, i) => (
                                    <div key={i} className="theme-card">
                                        {item.type === 'text' && <div className="prose max-w-none font-bangla">{item.content}</div>}
                                        {item.type === 'image' && item.image && <img src={item.image} alt="" className="w-full rounded-lg" />}
                                        {item.type === 'html' && <div className="font-bangla" dangerouslySetInnerHTML={{ __html: item.content || '' }} />}
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
                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
                            <div className="flex gap-4 flex-wrap" style={{ 
                                flexDirection,
                                alignItems: flexAlign === 'start' ? 'flex-start' : flexAlign === 'end' ? 'flex-end' : flexAlign === 'center' ? 'center' : 'stretch',
                                justifyContent: flexJustify === 'start' ? 'flex-start' : flexJustify === 'end' ? 'flex-end' : flexJustify === 'center' ? 'center' : flexJustify === 'between' ? 'space-between' : 'space-around'
                            }}>
                                {(section.items || []).map((item, i) => (
                                    <div key={i} className="theme-card flex-1 min-w-[200px]">
                                        {item.type === 'text' && <div className="prose max-w-none font-bangla">{item.content}</div>}
                                        {item.type === 'image' && item.image && <img src={item.image} alt="" className="w-full rounded-lg" />}
                                        {item.type === 'html' && <div className="font-bangla" dangerouslySetInnerHTML={{ __html: item.content || '' }} />}
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
                        <div className="font-bangla" dangerouslySetInnerHTML={{ __html: section.content || '' }} />
                    </div>
                );
            
            case 'hero':
                const heroStyle = {
                    backgroundImage: section.background_image ? `url(${section.background_image})` : undefined,
                    backgroundColor: section.background_image ? undefined : (section.background_color || 'var(--color-background)'),
                    backgroundSize: section.background_image ? 'cover' : undefined,
                    backgroundPosition: section.background_image ? 'center' : undefined,
                    color: section.text_color || 'var(--color-text)'
                };
                return (
                    <section key={index} className="theme-section"
                             style={heroStyle}>
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 md:py-16">
                            <div className="text-center">
                                {section.title && (
                                    <h1 className="text-3xl md:text-5xl lg:text-6xl font-bold mb-6 font-bangla" style={{ lineHeight: '1.2' }}>
                                        {section.title}
                                    </h1>
                                )}
                                {section.subtitle && (
                                    <p className="text-lg md:text-xl mb-8 font-bangla max-w-3xl mx-auto" style={{ lineHeight: '1.6' }}>
                                        {section.subtitle}
                                    </p>
                                )}
                                {section.images && section.images[0] && (
                                    <div className="mb-8">
                                        <img src={section.images[0]} alt={section.title} className="mx-auto max-w-full rounded-lg shadow-xl" />
                                    </div>
                                )}
                                {section.button_text && (
                                    <a href={section.button_link || '#'} 
                                       className="font-bangla text-lg md:text-xl px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:scale-105"
                                       style={{
                                           backgroundColor: section.button_bg_color || 'var(--color-primary)',
                                           color: section.button_text_color || '#FFFFFF'
                                       }}>
                                        {section.button_text}
                                    </a>
                                )}
                            </div>
                        </div>
                    </section>
                );
            
            case 'benefits':
                return (
                    <section key={index} className="theme-section">
                        <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
                            )}
                            <div className="space-y-4">
                                {section.items?.map((benefit, i) => (
                                    <div key={i} className="theme-card border-l-4 hover:bg-gray-50 transition" style={{ borderLeftColor: 'var(--color-accent)' }}>
                                        {benefit.title && (
                                            <h3 className="text-xl md:text-2xl font-bold mb-2 font-bangla text-gray-800">{benefit.title}</h3>
                                        )}
                                        {benefit.description && (
                                            <p className="text-gray-700 text-lg font-bangla" style={{ lineHeight: '1.8' }}>
                                                {benefit.description}
                                            </p>
                                        )}
                                    </div>
                                ))}
                            </div>
                        </div>
                    </section>
                );
            
            case 'pricing':
                return (
                    <section key={index} className="theme-section bg-gray-50">
                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                            {section.title && (
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
                            )}
                            <div className="theme-card text-center">
                                {section.original_price && (
                                    <div className="mb-4">
                                        <p className="text-lg text-gray-500 mb-2 font-bangla">Previous Price</p>
                                        <span className="text-2xl text-gray-500 line-through font-bangla">{section.original_price}</span>
                                    </div>
                                )}
                                {section.offer_price && (
                                    <div className="mb-4">
                                        <p className="text-lg text-gray-700 mb-2 font-bangla">Offer Price</p>
                                        <span className="text-5xl font-bold font-bangla" style={{ color: 'var(--color-error)' }}>{section.offer_price}</span>
                                    </div>
                                )}
                                {section.discount_text && (
                                    <p className="text-center font-bold text-xl mb-6 font-bangla" style={{ color: 'var(--color-accent)' }}>{section.discount_text}</p>
                                )}
                                {section.countdown_date && (
                                    <div className="text-center mb-6">
                                        <p className="text-sm text-gray-600 mb-3 font-bangla">অফার শেষ হতে বাকি:</p>
                                        <div className="inline-flex items-center justify-center px-4 py-2 rounded-full bg-red-50 border border-red-200 shadow-sm">
                                            <span
                                                id={`countdown-${index}`}
                                                className="font-mono text-xl md:text-2xl tracking-widest font-bold font-bangla text-red-600"
                                            >
                                                Loading...
                                            </span>
                                        </div>
                                        <p className="text-xs text-gray-500 mt-2 font-bangla">
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
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
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
                                                    <h3 className="text-xl md:text-2xl font-bold mb-2 font-bangla text-gray-800">
                                                        {step.title}
                                                    </h3>
                                                )}
                                                {step.description && (
                                                    <p className="text-gray-700 text-lg font-bangla" style={{ lineHeight: '1.8' }}>
                                                        {step.description}
                                                    </p>
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
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
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
                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
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
                                    <div className="mt-4 font-bangla text-lg" dangerouslySetInnerHTML={{ __html: section.content }} />
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
                            <div className="font-bangla" dangerouslySetInnerHTML={{ __html: tab.content || '' }} />
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