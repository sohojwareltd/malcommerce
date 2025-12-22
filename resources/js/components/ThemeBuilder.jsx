import React, { useState, useEffect } from 'react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';
import { GridSection, FlexSection, SpacerSection, ContainerSection } from './LayoutComponents.jsx';

const ThemeBuilder = ({ initialLayout = [], onSave, productSlug, productData = {} }) => {
    const [sections, setSections] = useState(initialLayout);
    const [showPreview, setShowPreview] = useState(true);
    const [previewDevice, setPreviewDevice] = useState('desktop'); // 'mobile', 'tablet', 'desktop'
    const [uploading, setUploading] = useState({});

    const sectionTypes = [
        // Layout Components
        { value: 'grid', label: 'Grid', icon: '⊞', category: 'layout' },
        { value: 'flex', label: 'Flex/Row', icon: '⇄', category: 'layout' },
        { value: 'container', label: 'Container', icon: '📦', category: 'layout' },
        { value: 'spacer', label: 'Spacer', icon: '↕️', category: 'layout' },
        // Content Components
        { value: 'hero', label: 'Hero Section', icon: '⭐', category: 'content' },
        { value: 'rich_text', label: 'Rich Text', icon: '📝', category: 'content' },
        { value: 'image_gallery', label: 'Image Gallery', icon: '🖼️', category: 'content' },
        { value: 'banner', label: 'Banner', icon: '🎨', category: 'content' },
        { value: 'benefits', label: 'Benefits/Features', icon: '✨', category: 'content' },
        { value: 'pricing', label: 'Pricing & Offer', icon: '💰', category: 'content' },
        { value: 'steps', label: 'Steps/Instructions', icon: '📋', category: 'content' },
        { value: 'faq', label: 'FAQs', icon: '❓', category: 'content' },
        { value: 'testimonials', label: 'Testimonials', icon: '💬', category: 'content' },
        { value: 'video', label: 'Video Embed', icon: '🎥', category: 'content' },
        { value: 'specifications', label: 'Specifications', icon: '📋', category: 'content' },
        { value: 'comparison', label: 'Product Comparison', icon: '⚖️', category: 'content' },
        { value: 'call_to_action', label: 'Call to Action', icon: '🎯', category: 'content' },
        { value: 'order_form', label: 'Order Form', icon: '🛒', category: 'content' },
        { value: 'social_links', label: 'Social Links', icon: '🔗', category: 'content' },
        { value: 'contact_info', label: 'Contact Info', icon: '📞', category: 'content' },
        { value: 'tabs', label: 'Tabs', icon: '📑', category: 'content' },
    ];

    const addSection = (type) => {
        const newSection = {
            type,
            title: '',
            content: ['rich_text', 'container', 'hero', 'contact_info'].includes(type) ? '' : null,
            subtitle: type === 'hero' ? '' : null,
            images: ['image_gallery', 'banner', 'hero'].includes(type) ? [] : null,
            items: ['faq', 'testimonials', 'specifications', 'comparison', 'tabs', 'grid', 'flex', 'benefits', 'steps', 'social_links'].includes(type) ? [] : null,
            url: type === 'video' ? '' : null,
            button_text: ['call_to_action', 'hero'].includes(type) ? 'Order Now' : null,
            button_link: ['call_to_action', 'hero'].includes(type) ? '#' : null,
            button_bg_color: ['call_to_action', 'hero'].includes(type) ? '#2563EB' : null,
            button_text_color: ['call_to_action', 'hero'].includes(type) ? '#FFFFFF' : null,
            background_color: ['banner', 'call_to_action', 'hero'].includes(type) ? '#4F46E5' : null,
            background_image: ['banner', 'call_to_action', 'hero'].includes(type) ? '' : null,
            text_color: ['banner', 'call_to_action', 'hero'].includes(type) ? '#FFFFFF' : null,
            // Pricing properties
            original_price: type === 'pricing' ? '' : null,
            offer_price: type === 'pricing' ? '' : null,
            discount_text: type === 'pricing' ? '' : null,
            countdown_date: type === 'pricing' ? '' : null,
            // Contact properties
            phone: type === 'contact_info' ? '' : null,
            email: type === 'contact_info' ? '' : null,
            address: type === 'contact_info' ? '' : null,
            // Grid properties
            columns: type === 'grid' ? 2 : null,
            gap: type === 'grid' ? 'medium' : null,
            // Flex properties
            direction: type === 'flex' ? 'row' : null,
            align: type === 'flex' ? 'start' : null,
            justify: type === 'flex' ? 'start' : null,
            // Spacer properties
            height: type === 'spacer' ? 50 : null,
            // Container properties
            maxWidth: type === 'container' ? 'full' : null,
            padding: type === 'container' ? 'medium' : null,
            backgroundColor: type === 'container' ? '#FFFFFF' : null,
        };
        setSections([...sections, newSection]);
    };

    const updateSection = (index, field, value) => {
        const updated = [...sections];
        updated[index] = { ...updated[index], [field]: value };
        setSections(updated);
    };

    const removeSection = (index) => {
        setSections(sections.filter((_, i) => i !== index));
    };

    const handleDragEnd = (result) => {
        if (!result.destination) return;
        
        const items = Array.from(sections);
        const [reorderedItem] = items.splice(result.source.index, 1);
        items.splice(result.destination.index, 0, reorderedItem);
        
        setSections(items);
    };

    const handleSave = () => {
        if (onSave) {
            onSave(sections);
        }
    };

    const generateTemplate = () => {
        if (!confirm('This will replace your current layout with a Tinibee-style template. Continue?')) {
            return;
        }

        const template = [
            // Hero Section - Tinibee Style
            {
                type: 'hero',
                title: productData.name || 'Product Name',
                subtitle: productData.shortDescription || productData.description?.substring(0, 200) || 'Amazing product that will change your life',
                images: productData.images && productData.images.length > 0 ? [productData.images[0]] : [],
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#FFFFFF',
                text_color: '#1F2937',
            },
            // CTA Button Section
            {
                type: 'call_to_action',
                title: '',
                content: '',
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#10B981',
                text_color: '#FFFFFF',
            },
            // Benefits Section - Tinibee Style (Numbered)
            {
                type: 'benefits',
                title: 'Product Benefits',
                items: [
                    { title: '1. Premium Quality', description: 'Made with the finest materials and craftsmanship' },
                    { title: '2. Fast Delivery', description: 'Quick and reliable shipping to your doorstep' },
                    { title: '3. Money Back Guarantee', description: '30-day money-back guarantee if not satisfied' },
                    { title: '4. 24/7 Support', description: 'Round-the-clock customer support available' },
                    { title: '5. Easy to Use', description: 'Simple and intuitive design for everyone' },
                    { title: '6. Best Value', description: 'Great quality at an affordable price' },
                    { title: '7. Safe & Tested', description: 'Certified safe for use, lab tested' },
                ],
            },
            // CTA Button
            {
                type: 'call_to_action',
                title: '',
                content: '',
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#10B981',
                text_color: '#FFFFFF',
            },
            // Usage Instructions - Tinibee Style
            {
                type: 'steps',
                title: 'How to Use',
                items: [
                    { title: 'Step 1', description: 'Take 5-6 drops of oil in your hand' },
                    { title: 'Step 2', description: 'Massage on chest, back, and feet' },
                    { title: 'Step 3', description: 'Use 2-3 times daily for 5-10 minutes' },
                ],
            },
            // CTA Button
            {
                type: 'call_to_action',
                title: '',
                content: '',
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#10B981',
                text_color: '#FFFFFF',
            },
            // Contact Info
            {
                type: 'contact_info',
                title: 'Contact Customer Support',
                phone: '+880 1805-417192',
                email: '',
                address: '',
            },
            // Customer Reviews
            {
                type: 'testimonials',
                title: 'Customer Reviews',
                items: [
                    { text: 'This product exceeded my expectations! Highly recommended.', author: 'Sarah Ahmed' },
                    { text: 'Great quality and fast delivery. Very satisfied with my purchase.', author: 'Mohammad Rahman' },
                    { text: 'Best product I\'ve bought this year. Worth every penny!', author: 'Fatima Khan' },
                ],
            },
            // Video/Testimonial Section
            {
                type: 'call_to_action',
                title: 'Listen to why thousands of mothers are choosing this product',
                content: '',
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#4F46E5',
                text_color: '#FFFFFF',
            },
            // Pricing Section - Tinibee Style
            {
                type: 'pricing',
                title: '',
                original_price: productData.comparePrice ? `৳${parseFloat(productData.comparePrice).toFixed(2)}` : '৳1290.00',
                offer_price: productData.price ? `৳${parseFloat(productData.price).toFixed(2)}` : '৳890.00',
                discount_text: productData.comparePrice ? `Save ৳${(parseFloat(productData.comparePrice) - parseFloat(productData.price || 0)).toFixed(2)}` : 'Save ৳400',
                countdown_date: new Date(Date.now() + 7 * 24 * 60 * 60 * 1000).toISOString().slice(0, 16),
            },
            // Additional Offer Text
            {
                type: 'rich_text',
                title: '',
                content: '<div class="text-center py-4"><p class="text-lg font-semibold">Order 2 items and get them for only ৳1650</p><p class="text-sm text-gray-600">Free home delivery on orders today!</p></div>',
            },
            // Safety/Certification
            {
                type: 'rich_text',
                title: '',
                content: '<div class="text-center py-6 bg-green-50 rounded-lg"><p class="text-lg font-bold text-green-800">Lab Tested by BCSIR</p><p class="text-sm text-green-700">100% safe for use, certified quality</p></div>',
            },
            // CTA Button
            {
                type: 'call_to_action',
                title: '',
                content: '',
                button_text: 'অর্ডার করুন',
                button_link: '#order',
                background_color: '#10B981',
                text_color: '#FFFFFF',
            },
            // Product Images
            ...(productData.images && productData.images.length > 0 ? [{
                type: 'image_gallery',
                title: 'Product Images',
                images: productData.images,
            }] : []),
            // Order Form Section (CTA with #order)
            {
                type: 'call_to_action',
                title: 'Place Your Order',
                content: 'Fill in your name, full address, and mobile number below, then click Confirm Order',
                button_text: 'Confirm Order',
                button_link: '#order',
                background_color: '#FFFFFF',
                text_color: '#1F2937',
            },
            // Social Links
            {
                type: 'social_links',
                title: 'Stay Connected',
                items: [
                    { platform: 'Facebook', url: 'https://facebook.com/yourpage' },
                    { platform: 'WhatsApp', url: 'https://wa.me/8801805417192' },
                    { platform: 'YouTube', url: 'https://youtube.com/yourchannel' },
                    { platform: 'Instagram', url: 'https://instagram.com/yourpage' },
                ],
            },
            // Final Contact
            {
                type: 'contact_info',
                title: 'Contact Customer Support',
                phone: '+880 1805-417192',
                email: '',
                address: '',
            },
        ];

        setSections(template);
    };

    const uploadImage = async (sectionIndex, identifier) => {
        return new Promise((resolve) => {
            const input = document.createElement('input');
            input.type = 'file';
            input.accept = 'image/*';
            input.onchange = async (e) => {
                const file = e.target.files[0];
                if (!file) {
                    resolve(null);
                    return;
                }

                const uploadKey = `${sectionIndex}-${identifier || 'new'}`;
                setUploading(prev => ({ ...prev, [uploadKey]: true }));

                const formData = new FormData();
                formData.append('image', file);

                try {
                    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;
                    if (!csrfToken) {
                        alert('CSRF token not found. Please refresh the page.');
                        resolve(null);
                        setUploading(prev => ({ ...prev, [uploadKey]: false }));
                        return;
                    }

                    const response = await fetch('/admin/upload-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    if (!response.ok) {
                        const errorText = await response.text();
                        console.error('Upload failed:', errorText);
                        alert('Failed to upload image. Please try again.');
                        resolve(null);
                        return;
                    }

                    const data = await response.json();
                    if (data.success && data.url) {
                        resolve(data.url);
                    } else {
                        alert(data.message || 'Failed to upload image');
                        resolve(null);
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    alert('Error uploading image: ' + error.message);
                    resolve(null);
                } finally {
                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                }
            };
            input.click();
        });
    };

    const removeImage = (sectionIndex, imageIndex) => {
        const updated = [...sections];
        updated[sectionIndex].images.splice(imageIndex, 1);
        setSections(updated);
    };

    // Auto-save preview (debounced)
    useEffect(() => {
        const timer = setTimeout(() => {
            if (onSave && sections.length > 0) {
                // Auto-save every 3 seconds if there are changes
            }
        }, 3000);
        return () => clearTimeout(timer);
    }, [sections]);

    const renderPreview = () => {
        const deviceStyles = {
            mobile: { width: '375px', maxWidth: '100%' },
            tablet: { width: '768px', maxWidth: '100%' },
            desktop: { width: '100%' }
        };

        const previewStyle = deviceStyles[previewDevice] || deviceStyles.desktop;

        return (
            <div className="w-full">
                {/* Device Selector - Shopify Style */}
                <div className="mb-4 flex items-center justify-center gap-1 bg-white border border-[#E1E3E5] rounded-lg p-1 inline-flex shadow-sm">
                    <button
                        onClick={() => setPreviewDevice('mobile')}
                        className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                            previewDevice === 'mobile' 
                                ? 'bg-[#008060] text-white' 
                                : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                        }`}
                        title="Mobile (375px)"
                    >
                        Mobile
                    </button>
                    <button
                        onClick={() => setPreviewDevice('tablet')}
                        className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                            previewDevice === 'tablet' 
                                ? 'bg-[#008060] text-white' 
                                : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                        }`}
                        title="Tablet (768px)"
                    >
                        Tablet
                    </button>
                    <button
                        onClick={() => setPreviewDevice('desktop')}
                        className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                            previewDevice === 'desktop' 
                                ? 'bg-[#008060] text-white' 
                                : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                        }`}
                        title="Desktop (Full Width)"
                    >
                        Desktop
                    </button>
                </div>

                {/* Preview Container - Shopify Style */}
                <div className="flex justify-center" style={{ 
                    backgroundColor: previewDevice !== 'desktop' ? '#E1E3E5' : 'transparent',
                    padding: previewDevice !== 'desktop' ? '2rem' : '0',
                    minHeight: previewDevice === 'mobile' ? '667px' : previewDevice === 'tablet' ? '1024px' : 'auto'
                }}>
                    <div 
                        className="bg-white shadow-lg rounded overflow-hidden transition-all duration-200 border border-[#E1E3E5]"
                        style={previewStyle}
                    >
                        <div className="space-y-6 p-4" style={{ 
                            minHeight: previewDevice === 'mobile' ? '600px' : 'auto'
                        }}>
                    {sections.map((section, index) => {
                        switch (section.type) {
                            case 'rich_text':
                                return (
                                    <section key={index} className="theme-section">
                                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                                            {section.title && (
                                                <h2 className="theme-section-title font-bangla">{section.title}</h2>
                                            )}
                                            <div className="prose max-w-none font-bangla text-lg" style={{ lineHeight: '1.8' }} dangerouslySetInnerHTML={{ __html: section.content || '' }} />
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
                                                {(section.images || []).map((image, imgIndex) => (
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
                                                {(section.items || []).map((faq, faqIndex) => (
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
                                                {(section.items || []).map((testimonial, testIndex) => (
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
                                                <iframe src={section.url} className="w-full h-full" allowFullScreen title={section.title || 'Video'} />
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
                                                        {(section.items || []).map((spec, specIndex) => (
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
                                                {(section.items || []).map((benefit, i) => (
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
                                                            <span className="font-mono text-xl md:text-2xl tracking-widest font-bold font-bangla text-red-600">
                                                                {new Date(section.countdown_date).toLocaleDateString()}
                                                            </span>
                                                        </div>
                                                        <p className="text-xs text-gray-500 mt-2 font-bangla">
                                                            সময় শেষ হলে অফার পরিবর্তন হতে পারে।
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
                                                {(section.items || []).map((step, i) => (
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
                                                {(section.items || []).map((link, i) => {
                                                    const platformColors = {
                                                        'Facebook': '#1877F2',
                                                        'WhatsApp': '#25D366',
                                                        'YouTube': '#FF0000',
                                                        'Instagram': 'linear-gradient(to right, #833AB4, #FD1D1D, #FCB045)',
                                                    };
                                                    const bgColor = platformColors[link.platform] || 'var(--color-primary)';
                                                    const iconMap = {
                                                        'Facebook': 'fab fa-facebook-f',
                                                        'WhatsApp': 'fab fa-whatsapp',
                                                        'YouTube': 'fab fa-youtube',
                                                        'Instagram': 'fab fa-instagram',
                                                    };
                                                    const iconClass = iconMap[link.platform] || 'fas fa-link';
                                                    
                                                    return (
                                                        <a key={i} 
                                                           href={link.url || '#'} 
                                                           target="_blank" 
                                                           rel="noopener noreferrer"
                                                           className="inline-flex items-center gap-2 px-6 py-3 text-white rounded-lg transition shadow-md hover:shadow-lg font-bangla font-semibold"
                                                           style={{ background: bgColor }}>
                                                            <i className={iconClass}></i>
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
                            case 'order_form':
                                return (
                                    <div key={index} className="w-full py-8 theme-section">
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
                                        <div className="max-w-2xl mx-auto px-4 sm:px-6 lg:px-8">
                                            <div className="card">
                                                <h2 className="text-2xl font-bold mb-6 text-gray-900 font-bangla">অর্ডার করুন</h2>
                                                <p className="text-gray-600 text-center font-bangla mb-4">Order form will appear here on the actual page</p>
                                                <div className="bg-gray-50 rounded-lg p-4 border border-gray-200">
                                                    <div className="space-y-2">
                                                        <div className="h-10 bg-gray-200 rounded"></div>
                                                        <div className="h-10 bg-gray-200 rounded"></div>
                                                        <div className="h-24 bg-gray-200 rounded"></div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                );
                                
                            case 'call_to_action':
                                const ctaStyle = {
                                    backgroundImage: section.background_image ? `url(${section.background_image})` : undefined,
                                    backgroundColor: section.background_image ? undefined : (section.background_color || 'var(--color-primary)'),
                                    backgroundSize: section.background_image ? 'cover' : undefined,
                                    backgroundPosition: section.background_image ? 'center' : undefined,
                                    color: section.text_color || '#FFFFFF'
                                };
                                return (
                                    <section key={index} className="theme-section"
                                             style={ctaStyle}>
                                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                                            {section.title && (
                                                <h2 className="text-2xl md:text-3xl font-bold mb-4 font-bangla">{section.title}</h2>
                                            )}
                                            {section.content && (
                                                <p className="text-lg mb-6 font-bangla" style={{ lineHeight: '1.8' }}>{section.content}</p>
                                            )}
                                            {section.button_text && (
                                                <a href={section.button_link || '#'} 
                                                   className="btn-primary font-bangla text-lg px-8 py-4 rounded-xl shadow-lg hover:shadow-xl transition transform hover:scale-105"
                                                   style={{ backgroundColor: section.button_color || 'white', color: section.background_color || 'var(--color-primary)' }}>
                                                    {section.button_text}
                                                </a>
                                            )}
                                        </div>
                                    </section>
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
                            case 'tabs':
                                const TabsPreview = ({ items }) => {
                                    return (
                                        <>
                                            <div className="border-b border-gray-200">
                                                <div className="flex overflow-x-auto">
                                                    {items?.slice(0, 3).map((tab, tabIndex) => (
                                                        <button
                                                            key={tabIndex}
                                                            className={`px-6 py-4 font-semibold border-b-2 transition whitespace-nowrap font-bangla ${
                                                                tabIndex === 0
                                                                    ? 'border-primary text-gray-900'
                                                                    : 'border-transparent text-gray-600 hover:text-gray-900'
                                                            }`}
                                                            style={tabIndex === 0 ? { borderBottomColor: 'var(--color-primary)' } : {}}
                                                        >
                                                            {tab.title || `Tab ${tabIndex + 1}`}
                                                        </button>
                                                    ))}
                                                </div>
                                            </div>
                                            <div className="p-6">
                                                <div className="font-bangla" dangerouslySetInnerHTML={{ __html: items?.[0]?.content || '' }} />
                                            </div>
                                        </>
                                    );
                                };
                                return (
                                    <div key={index} className="theme-section">
                                        <div className="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
                                            {section.title && <h2 className="theme-section-title font-bangla">{section.title}</h2>}
                                            <div className="theme-card">
                                                <TabsPreview items={section.items} />
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
                            default:
                                return null;
                        }
                    })}
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    return (
        <div className="h-full flex relative bg-[#F6F6F7]">
            {/* Offcanvas Sidebar - Shopify Style */}
            <div className={`fixed left-0 top-0 h-full bg-white border-r border-[#E1E3E5] z-50 transform transition-transform duration-200 ease-in-out ${
                showPreview ? 'translate-x-0' : '-translate-x-full'
            }`} style={{ width: '360px', maxWidth: '90vw', boxShadow: showPreview ? '2px 0 8px rgba(0,0,0,0.08)' : 'none' }}>
                <div className="h-full flex flex-col">
                    {/* Sidebar Header - Shopify Style */}
                    <div className="bg-white border-b border-[#E1E3E5] px-4 py-3">
                        <div className="flex items-center justify-between mb-2">
                            <h2 className="text-sm font-semibold text-[#202223]">Sections</h2>
                            <button
                                onClick={() => setShowPreview(false)}
                                className="text-[#637381] hover:text-[#202223] p-1.5 rounded hover:bg-[#F6F6F7] transition"
                                title="Close Builder"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                            </button>
                        </div>
                        {sections.length === 0 && (
                            <button
                                onClick={generateTemplate}
                                className="w-full bg-[#008060] text-white px-3 py-2 rounded text-xs font-medium hover:bg-[#006E52] transition shadow-sm flex items-center justify-center gap-2"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path>
                                </svg>
                                Generate Demo Template
                            </button>
                        )}
                    </div>
                    
                    {/* Sidebar Content */}
                    <div className="flex-1 overflow-y-auto">
                        {/* Add Section Buttons - Shopify Style */}
                        <div className="p-3 border-b border-[#E1E3E5] bg-[#FAFAFA]">
                            <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2 px-1">Add section</h3>
                    
                            {/* Layout Components */}
                            <div className="mb-3">
                                <div className="grid grid-cols-2 gap-1.5">
                                    {sectionTypes.filter(t => t.category === 'layout').map((type) => (
                                        <button
                                            key={type.value}
                                            onClick={() => addSection(type.value)}
                                            className="bg-white border border-[#E1E3E5] px-3 py-2.5 rounded text-left hover:border-[#008060] hover:bg-[#F0FDF4] transition group"
                                            title={type.label}
                                        >
                                            <div className="flex items-center gap-2">
                                                <span className="text-base group-hover:scale-110 transition-transform">{type.icon}</span>
                                                <span className="text-xs font-medium text-[#202223]">{type.label}</span>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            </div>
                            
                            {/* Content Components */}
                            <div>
                                <div className="grid grid-cols-2 gap-1.5">
                                    {sectionTypes.filter(t => t.category === 'content').map((type) => (
                                        <button
                                            key={type.value}
                                            onClick={() => addSection(type.value)}
                                            className="bg-white border border-[#E1E3E5] px-3 py-2.5 rounded text-left hover:border-[#008060] hover:bg-[#F0FDF4] transition group"
                                            title={type.label}
                                        >
                                            <div className="flex items-center gap-2">
                                                <span className="text-base group-hover:scale-110 transition-transform">{type.icon}</span>
                                                <span className="text-xs font-medium text-[#202223]">{type.label}</span>
                                            </div>
                                        </button>
                                    ))}
                                </div>
                            </div>
                        </div>

                        {/* Sections List - Shopify Style */}
                        <div className="p-3">
                            <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2 px-1">Page sections</h3>
                            <DragDropContext onDragEnd={handleDragEnd}>
                                <Droppable droppableId="sections">
                                    {(provided) => (
                                        <div {...provided.droppableProps} ref={provided.innerRef} className="space-y-2">
                                            {sections.map((section, index) => (
                                                <Draggable key={index} draggableId={`section-${index}`} index={index}>
                                                    {(provided, snapshot) => (
                                                        <div
                                                            ref={provided.innerRef}
                                                            {...provided.draggableProps}
                                                            className={`bg-white border rounded ${
                                                                snapshot.isDragging 
                                                                    ? 'border-[#008060] shadow-lg' 
                                                                    : 'border-[#E1E3E5] hover:border-[#C9CCCF]'
                                                            } transition-all`}
                                                        >
                                                            {/* Section Header */}
                                                            <div className="px-3 py-2.5 border-b border-[#E1E3E5] flex items-center justify-between bg-[#FAFAFA]">
                                                                <div {...provided.dragHandleProps} className="flex items-center gap-2 cursor-move flex-1">
                                                                    <svg className="w-4 h-4 text-[#637381]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 8h16M4 16h16"></path>
                                                                    </svg>
                                                                    <span className="text-sm font-medium text-[#202223]">
                                                                        {sectionTypes.find(t => t.value === section.type)?.label || section.type}
                                                                    </span>
                                                                </div>
                                                                <button
                                                                    onClick={() => removeSection(index)}
                                                                    className="text-[#637381] hover:text-[#BF0711] p-1 rounded hover:bg-[#F6F6F7] transition"
                                                                    title="Remove section"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                            
                                                            {/* Section Editor */}
                                                            <div className="p-3 space-y-3">

                                                                <input
                                                                    type="text"
                                                                    placeholder="Section title (optional)"
                                                                    value={section.title || ''}
                                                                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                                                                    className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />

                                                    {section.type === 'rich_text' && (
                                                        <textarea
                                                            placeholder="Enter HTML content"
                                                            value={section.content || ''}
                                                            onChange={(e) => updateSection(index, 'content', e.target.value)}
                                                            rows={4}
                                                            className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                        />
                                                    )}

                                                    {section.type === 'image_gallery' && (
                                                        <div className="space-y-2">
                                                            <div className="flex gap-2 flex-wrap">
                                                                {(section.images || []).map((img, imgIndex) => (
                                                                    <div key={imgIndex} className="relative">
                                                                        <img src={img} alt="" className="w-20 h-20 object-cover rounded border" />
                                                                        <button
                                                                            onClick={() => removeImage(index, imgIndex)}
                                                                            className="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-5 h-5 text-xs"
                                                        >
                                                                            ×
                                                                        </button>
                                                                    </div>
                                                                ))}
                                                            </div>
                                                            <button
                                                                onClick={async () => {
                                                                    const url = await uploadImage(index, 'new');
                                                                    if (url) {
                                                                        const updated = [...sections];
                                                                        if (!updated[index].images) updated[index].images = [];
                                                                        updated[index].images.push(url);
                                                                        setSections(updated);
                                                                    }
                                                                }}
                                                                disabled={uploading[`${index}-new`]}
                                                                className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition"
                                                            >
                                                                {uploading[`${index}-new`] ? 'Uploading...' : '+ Upload Image'}
                                                            </button>
                                                            <input
                                                                type="text"
                                                                placeholder="Or paste image URL"
                                                                onBlur={(e) => {
                                                                    if (e.target.value) {
                                                                        const updated = [...sections];
                                                                        if (!updated[index].images) updated[index].images = [];
                                                                        updated[index].images.push(e.target.value);
                                                                        setSections(updated);
                                                                        e.target.value = '';
                                                                    }
                                                                }}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                        </div>
                                                    )}

                                                    {section.type === 'banner' && (
                                                        <div className="space-y-2">
                                                            <textarea
                                                                placeholder="Banner content"
                                                                value={section.content || ''}
                                                                onChange={(e) => updateSection(index, 'content', e.target.value)}
                                                                rows={2}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <div className="text-xs text-gray-600 mb-1">Background:</div>
                                                            <div className="flex gap-2">
                                                                <input
                                                                    type="color"
                                                                    value={section.background_color || '#4F46E5'}
                                                                    onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                                                                    className="w-16 h-10"
                                                                    title="Background Color"
                                                                />
                                                                <input
                                                                    type="color"
                                                                    value={section.text_color || '#FFFFFF'}
                                                                    onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                                                                    className="w-16 h-10"
                                                                    title="Text Color"
                                                                />
                                                            </div>
                                                            <div className="text-xs text-gray-600 mb-1">Background Image:</div>
                                                            <div className="flex gap-2 items-start">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Background image URL"
                                                                    value={section.background_image || ''}
                                                                    onChange={(e) => updateSection(index, 'background_image', e.target.value)}
                                                                    className="flex-1 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                                <button
                                                                    onClick={async () => {
                                                                        const url = await uploadImage(index, 'bg');
                                                                        if (url) {
                                                                            updateSection(index, 'background_image', url);
                                                                        }
                                                                    }}
                                                                    disabled={uploading[`${index}-bg`]}
                                                                    className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition whitespace-nowrap"
                                                                >
                                                                    {uploading[`${index}-bg`] ? 'Uploading...' : 'Upload'}
                                                                </button>
                                                            </div>
                                                            <button
                                                                onClick={async () => {
                                                                    const url = await uploadImage(index, 'new');
                                                                    if (url) {
                                                                        const updated = [...sections];
                                                                        if (!updated[index].images) updated[index].images = [];
                                                                        updated[index].images[0] = url;
                                                                        setSections(updated);
                                                                    }
                                                                }}
                                                                disabled={uploading[`${index}-new`]}
                                                                className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition w-full"
                                                            >
                                                                {uploading[`${index}-new`] ? 'Uploading...' : '+ Upload Banner Content Image'}
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'faq' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((faq, faqIndex) => (
                                                                <div key={faqIndex} className="bg-white p-3 rounded border">
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Question"
                                                                        value={faq.question || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[faqIndex] = { ...items[faqIndex], question: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="w-full mb-2 px-3 py-2 border border-neutral-300 rounded-lg text-sm"
                                                                    />
                                                                    <textarea
                                                                        placeholder="Answer"
                                                                        value={faq.answer || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[faqIndex] = { ...items[faqIndex], answer: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        rows={2}
                                                                        className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { question: '', answer: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add FAQ
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'testimonials' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((testimonial, testIndex) => (
                                                                <div key={testIndex} className="bg-white p-3 rounded border">
                                                                    <textarea
                                                                        placeholder="Testimonial text"
                                                                        value={testimonial.text || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[testIndex] = { ...items[testIndex], text: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        rows={2}
                                                                        className="w-full mb-2 px-3 py-2 border border-neutral-300 rounded-lg text-sm"
                                                                    />
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Author name"
                                                                        value={testimonial.author || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[testIndex] = { ...items[testIndex], author: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { text: '', author: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add Testimonial
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'video' && (
                                                        <input
                                                            type="url"
                                                            placeholder="Video embed URL (YouTube, Vimeo, etc.)"
                                                            value={section.url || ''}
                                                            onChange={(e) => updateSection(index, 'url', e.target.value)}
                                                            className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                        />
                                                    )}

                                                    {section.type === 'specifications' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((spec, specIndex) => (
                                                                <div key={specIndex} className="bg-white p-3 rounded border grid grid-cols-2 gap-2">
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Label"
                                                                        value={spec.label || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[specIndex] = { ...items[specIndex], label: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Value"
                                                                        value={spec.value || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[specIndex] = { ...items[specIndex], value: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { label: '', value: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add Specification
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'hero' && (
                                                        <div className="space-y-2">
                                                            <input
                                                                type="text"
                                                                placeholder="Main headline"
                                                                value={section.title || ''}
                                                                onChange={(e) => updateSection(index, 'title', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <textarea
                                                                placeholder="Subtitle/Description"
                                                                value={section.subtitle || ''}
                                                                onChange={(e) => updateSection(index, 'subtitle', e.target.value)}
                                                                rows={2}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <div className="grid grid-cols-2 gap-2">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Button Text"
                                                                    value={section.button_text || ''}
                                                                    onChange={(e) => updateSection(index, 'button_text', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                                <input
                                                                    type="url"
                                                                    placeholder="Button Link"
                                                                    value={section.button_link || ''}
                                                                    onChange={(e) => updateSection(index, 'button_link', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                            </div>
                                                            <button
                                                                onClick={async () => {
                                                                    const url = await uploadImage(index, 'new');
                                                                    if (url) {
                                                                        const updated = [...sections];
                                                                        if (!updated[index].images) updated[index].images = [];
                                                                        updated[index].images[0] = url;
                                                                        setSections(updated);
                                                                    }
                                                                }}
                                                                disabled={uploading[`${index}-new`]}
                                                                className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition w-full"
                                                            >
                                                                {uploading[`${index}-new`] ? 'Uploading...' : '+ Upload Hero Image'}
                                                            </button>
                                                            <div className="space-y-2">
                                                                <div className="text-xs text-gray-600 mb-1">Background:</div>
                                                                <div className="flex gap-2">
                                                                    <input
                                                                        type="color"
                                                                        value={section.background_color || '#4F46E5'}
                                                                        onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                                                                        className="w-16 h-10"
                                                                        title="Background Color"
                                                                    />
                                                                    <input
                                                                        type="color"
                                                                        value={section.text_color || '#FFFFFF'}
                                                                        onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                                                                        className="w-16 h-10"
                                                                        title="Text Color"
                                                                    />
                                                                </div>
                                                                <div className="text-xs text-gray-600 mb-1">Background Image:</div>
                                                                <div className="flex gap-2 items-start">
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Background image URL"
                                                                        value={section.background_image || ''}
                                                                        onChange={(e) => updateSection(index, 'background_image', e.target.value)}
                                                                        className="flex-1 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                    <button
                                                                        onClick={async () => {
                                                                            const url = await uploadImage(index, 'bg');
                                                                            if (url) {
                                                                                updateSection(index, 'background_image', url);
                                                                            }
                                                                        }}
                                                                        disabled={uploading[`${index}-bg`]}
                                                                        className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition whitespace-nowrap"
                                                                    >
                                                                        {uploading[`${index}-bg`] ? 'Uploading...' : 'Upload'}
                                                                    </button>
                                                                </div>
                                                                <div className="text-xs text-gray-600 mb-1">Button Colors:</div>
                                                                <div className="flex gap-2">
                                                                    <input
                                                                        type="color"
                                                                        value={section.button_bg_color || '#2563EB'}
                                                                        onChange={(e) => updateSection(index, 'button_bg_color', e.target.value)}
                                                                        className="w-16 h-10"
                                                                        title="Button Background Color"
                                                                    />
                                                                    <input
                                                                        type="color"
                                                                        value={section.button_text_color || '#FFFFFF'}
                                                                        onChange={(e) => updateSection(index, 'button_text_color', e.target.value)}
                                                                        className="w-16 h-10"
                                                                        title="Button Text Color"
                                                                    />
                                                                </div>
                                                            </div>
                                                        </div>
                                                    )}

                                                    {section.type === 'order_form' && (
                                                        <div className="space-y-2">
                                                            <input
                                                                type="text"
                                                                placeholder="Order Form Title (optional)"
                                                                value={section.title || ''}
                                                                onChange={(e) => updateSection(index, 'title', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <textarea
                                                                placeholder="Order Form Description (optional)"
                                                                value={section.content || ''}
                                                                onChange={(e) => updateSection(index, 'content', e.target.value)}
                                                                rows={2}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <p className="text-xs text-gray-500">
                                                                This form will automatically use the product details from the page.
                                                            </p>
                                                        </div>
                                                    )}

                                                    {section.type === 'benefits' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((benefit, benefitIndex) => (
                                                                <div key={benefitIndex} className="bg-white p-3 rounded border">
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Benefit title"
                                                                        value={benefit.title || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[benefitIndex] = { ...items[benefitIndex], title: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="w-full mb-2 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                    <textarea
                                                                        placeholder="Benefit description"
                                                                        value={benefit.description || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[benefitIndex] = { ...items[benefitIndex], description: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        rows={2}
                                                                        className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { title: '', description: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add Benefit
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'pricing' && (
                                                        <div className="space-y-2">
                                                            <div className="grid grid-cols-2 gap-2">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Original Price"
                                                                    value={section.original_price || ''}
                                                                    onChange={(e) => updateSection(index, 'original_price', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                                <input
                                                                    type="text"
                                                                    placeholder="Offer Price"
                                                                    value={section.offer_price || ''}
                                                                    onChange={(e) => updateSection(index, 'offer_price', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                            </div>
                                                            <input
                                                                type="text"
                                                                placeholder="Discount text (e.g., 'Save 30%')"
                                                                value={section.discount_text || ''}
                                                                onChange={(e) => updateSection(index, 'discount_text', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <input
                                                                type="datetime-local"
                                                                placeholder="Countdown end date"
                                                                value={section.countdown_date || ''}
                                                                onChange={(e) => updateSection(index, 'countdown_date', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                        </div>
                                                    )}

                                                    {section.type === 'steps' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((step, stepIndex) => (
                                                                <div key={stepIndex} className="bg-white p-3 rounded border">
                                                                    <input
                                                                        type="text"
                                                                        placeholder={`Step ${stepIndex + 1} title`}
                                                                        value={step.title || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[stepIndex] = { ...items[stepIndex], title: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="w-full mb-2 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                    <textarea
                                                                        placeholder="Step description"
                                                                        value={step.description || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[stepIndex] = { ...items[stepIndex], description: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        rows={2}
                                                                        className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { title: '', description: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add Step
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'social_links' && (
                                                        <div className="space-y-2">
                                                            {(section.items || []).map((link, linkIndex) => (
                                                                <div key={linkIndex} className="bg-white p-3 rounded border grid grid-cols-2 gap-2">
                                                                    <input
                                                                        type="text"
                                                                        placeholder="Platform (Facebook, WhatsApp, etc.)"
                                                                        value={link.platform || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[linkIndex] = { ...items[linkIndex], platform: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                    <input
                                                                        type="url"
                                                                        placeholder="Link URL"
                                                                        value={link.url || ''}
                                                                        onChange={(e) => {
                                                                            const items = [...(section.items || [])];
                                                                            items[linkIndex] = { ...items[linkIndex], url: e.target.value };
                                                                            updateSection(index, 'items', items);
                                                                        }}
                                                                        className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                    />
                                                                </div>
                                                            ))}
                                                            <button
                                                                onClick={() => {
                                                                    const items = [...(section.items || []), { platform: '', url: '' }];
                                                                    updateSection(index, 'items', items);
                                                                }}
                                                                className="text-[#008060] hover:text-[#006E52] text-sm font-medium transition"
                                                            >
                                                                + Add Social Link
                                                            </button>
                                                        </div>
                                                    )}

                                                    {section.type === 'contact_info' && (
                                                        <div className="space-y-2">
                                                            <input
                                                                type="tel"
                                                                placeholder="Phone number"
                                                                value={section.phone || ''}
                                                                onChange={(e) => updateSection(index, 'phone', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <input
                                                                type="email"
                                                                placeholder="Email address"
                                                                value={section.email || ''}
                                                                onChange={(e) => updateSection(index, 'email', e.target.value)}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <textarea
                                                                placeholder="Address or additional contact info"
                                                                value={section.address || ''}
                                                                onChange={(e) => updateSection(index, 'address', e.target.value)}
                                                                rows={2}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                        </div>
                                                    )}

                                                    {section.type === 'call_to_action' && (
                                                        <div className="space-y-2">
                                                            <textarea
                                                                placeholder="CTA content"
                                                                value={section.content || ''}
                                                                onChange={(e) => updateSection(index, 'content', e.target.value)}
                                                                rows={2}
                                                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                            />
                                                            <div className="grid grid-cols-2 gap-2">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Button Text"
                                                                    value={section.button_text || ''}
                                                                    onChange={(e) => updateSection(index, 'button_text', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                                <input
                                                                    type="url"
                                                                    placeholder="Button Link"
                                                                    value={section.button_link || ''}
                                                                    onChange={(e) => updateSection(index, 'button_link', e.target.value)}
                                                                    className="px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                            </div>
                                                            <div className="text-xs text-gray-600 mb-1">Background:</div>
                                                            <div className="flex gap-2">
                                                                <input
                                                                    type="color"
                                                                    value={section.background_color || '#4F46E5'}
                                                                    onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                                                                    className="w-16 h-10"
                                                                    title="Background Color"
                                                                />
                                                                <input
                                                                    type="color"
                                                                    value={section.text_color || '#FFFFFF'}
                                                                    onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                                                                    className="w-16 h-10"
                                                                    title="Text Color"
                                                                />
                                                            </div>
                                                            <div className="text-xs text-gray-600 mb-1">Background Image:</div>
                                                            <div className="flex gap-2 items-start">
                                                                <input
                                                                    type="text"
                                                                    placeholder="Background image URL"
                                                                    value={section.background_image || ''}
                                                                    onChange={(e) => updateSection(index, 'background_image', e.target.value)}
                                                                    className="flex-1 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                                                />
                                                                <button
                                                                    onClick={async () => {
                                                                        const url = await uploadImage(index, 'bg');
                                                                        if (url) {
                                                                            updateSection(index, 'background_image', url);
                                                                        }
                                                                    }}
                                                                    disabled={uploading[`${index}-bg`]}
                                                                    className="bg-[#008060] text-white px-3 py-1.5 rounded text-sm font-medium hover:bg-[#006E52] disabled:opacity-50 disabled:cursor-not-allowed transition whitespace-nowrap"
                                                                >
                                                                    {uploading[`${index}-bg`] ? 'Uploading...' : 'Upload'}
                                                                </button>
                                                            </div>
                                                        </div>
                                                    )}

                                                    {/* Layout Components */}
                                                    {section.type === 'grid' && (
                                                        <GridSection
                                                            section={section}
                                                            index={index}
                                                            updateSection={updateSection}
                                                            removeImage={removeImage}
                                                            uploadImage={async (sectionIdx, identifier) => {
                                                                const uploadKey = `${sectionIdx}-${identifier}`;
                                                                setUploading(prev => ({ ...prev, [uploadKey]: true }));
                                                                try {
                                                                    const url = await uploadImage(sectionIdx, identifier);
                                                                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                                                                    return url;
                                                                } catch (error) {
                                                                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                                                                    throw error;
                                                                }
                                                            }}
                                                            uploading={uploading}
                                                        />
                                                    )}

                                                    {section.type === 'flex' && (
                                                        <FlexSection
                                                            section={section}
                                                            index={index}
                                                            updateSection={updateSection}
                                                            removeImage={removeImage}
                                                            uploadImage={async (sectionIdx, identifier) => {
                                                                const uploadKey = `${sectionIdx}-${identifier}`;
                                                                setUploading(prev => ({ ...prev, [uploadKey]: true }));
                                                                try {
                                                                    const url = await uploadImage(sectionIdx, identifier);
                                                                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                                                                    return url;
                                                                } catch (error) {
                                                                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                                                                    throw error;
                                                                }
                                                            }}
                                                            uploading={uploading}
                                                        />
                                                    )}

                                                    {section.type === 'spacer' && (
                                                        <SpacerSection
                                                            section={section}
                                                            index={index}
                                                            updateSection={updateSection}
                                                        />
                                                    )}

                                                    {section.type === 'container' && (
                                                        <ContainerSection
                                                            section={section}
                                                            index={index}
                                                            updateSection={updateSection}
                                                        />
                                                    )}
                                                </div>
                                            </div>
                                        )}
                                    </Draggable>
                                ))}
                                {provided.placeholder}
                                {sections.length === 0 && (
                                    <div className="text-center py-8 px-4">
                                        <div className="text-[#8C9196] mb-2">
                                            <svg className="w-12 h-12 mx-auto opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="1.5" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </div>
                                        <p className="text-sm text-[#637381]">No sections yet</p>
                                        <p className="text-xs text-[#8C9196] mt-1">Add a section to get started</p>
                                    </div>
                                )}
                            </div>
                        )}
                    </Droppable>
                </DragDropContext>
                        </div>
                        
                        {/* Save Button - Shopify Style */}
                        <div className="sticky bottom-0 bg-white border-t border-[#E1E3E5] p-3 mt-auto">
                            <button
                                onClick={handleSave}
                                className="w-full bg-[#008060] text-white px-4 py-2 rounded text-sm font-medium hover:bg-[#006E52] transition shadow-sm"
                            >
                                Save
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            {/* Main Preview Area - Shopify Style */}
            <div className="flex-1 h-full overflow-y-auto transition-all duration-200 bg-white" style={{ marginLeft: showPreview ? '360px' : '0' }}>
                <div className="h-full">
                    {/* Preview Header - Shopify Style */}
                    <div className="sticky top-0 bg-white border-b border-[#E1E3E5] z-40 px-4 py-2.5 flex items-center justify-between">
                        <div className="flex items-center gap-2">
                            <button
                                onClick={() => setShowPreview(!showPreview)}
                                className="text-[#637381] hover:text-[#202223] p-1.5 rounded hover:bg-[#F6F6F7] transition"
                                title="Toggle sidebar"
                            >
                                <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16"></path>
                                </svg>
                            </button>
                            <div className="h-4 w-px bg-[#E1E3E5]"></div>
                            <span className="text-sm font-medium text-[#202223]">Preview</span>
                        </div>
                        {productSlug && (
                            <a 
                                href={`/products/${productSlug}`}
                                target="_blank"
                                className="inline-flex items-center gap-1.5 text-[#008060] hover:text-[#006E52] px-3 py-1.5 text-sm font-medium hover:bg-[#F0FDF4] rounded transition"
                            >
                                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                </svg>
                                Open in new tab
                            </a>
                        )}
                    </div>
                    
                    {/* Preview Content */}
                    <div className="p-6 bg-[#F6F6F7] min-h-full">
                        {renderPreview()}
                    </div>
                </div>
            </div>
            
            {/* Overlay when sidebar is open on mobile */}
            {showPreview && (
                <div 
                    className="fixed inset-0 bg-black bg-opacity-50 z-40 lg:hidden"
                    onClick={() => setShowPreview(false)}
                ></div>
            )}
        </div>
    );
};

export default ThemeBuilder;
