import React, { useState, useEffect } from 'react';
import { GridSection, FlexSection, SpacerSection, ContainerSection } from './LayoutComponents';
import ProductSections from './ProductSections';
import prefabLayouts from './prefab/prefabLayouts';

const PageBuilder = ({ initialSections = [], productId = null, productPrice = null, productComparePrice = null, productInStock = false, productStockQuantity = null }) => {
    const [sections, setSections] = useState(initialSections);
    const [selectedSectionIndex, setSelectedSectionIndex] = useState(null);
    const [uploading, setUploading] = useState({});
    const [draggedIndex, setDraggedIndex] = useState(null);
    const [viewMode, setViewMode] = useState('edit'); // 'edit' or 'preview'

    // Update global sections for save function
    useEffect(() => {
        window.currentSections = sections;
    }, [sections]);

    // Image upload handler
    const uploadImage = async (sectionIndex, identifier) => {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        
        return new Promise((resolve) => {
            input.onchange = async (e) => {
                const file = e.target.files[0];
                if (!file) {
                    resolve(null);
                    return;
                }

                const uploadKey = `${sectionIndex}-${identifier}`;
                setUploading(prev => ({ ...prev, [uploadKey]: true }));

                const formData = new FormData();
                formData.append('image', file);

                const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

                try {
                    const response = await fetch('/admin/upload-image', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': csrfToken,
                            'Accept': 'application/json',
                        },
                        body: formData,
                    });

                    const data = await response.json();
                    setUploading(prev => ({ ...prev, [uploadKey]: false }));

                    if (data.success) {
                        resolve(data.url);
                    } else {
                        alert('Failed to upload image');
                        resolve(null);
                    }
                } catch (error) {
                    console.error('Upload error:', error);
                    setUploading(prev => ({ ...prev, [uploadKey]: false }));
                    alert('Error uploading image');
                    resolve(null);
                }
            };
            input.click();
        });
    };

    // Section management
    const addSection = (type) => {
        const newSection = createDefaultSection(type);
        const newSections = [...sections, newSection];
        setSections(newSections);
        setSelectedSectionIndex(newSections.length - 1);
    };

    const addPrefabLayout = (layoutKey) => {
        const layout = prefabLayouts[layoutKey];
        if (!layout) return;
        
        // Deep clone the sections to avoid reference issues
        const newSections = layout.sections.map(section => JSON.parse(JSON.stringify(section)));
        
        // Add to existing sections
        const updatedSections = [...sections, ...newSections];
        setSections(updatedSections);
        
        // Select the first section of the new layout
        if (newSections.length > 0) {
            setSelectedSectionIndex(sections.length);
        }
    };

    const removeSection = (index) => {
        const newSections = sections.filter((_, i) => i !== index);
        setSections(newSections);
        if (selectedSectionIndex === index) {
            setSelectedSectionIndex(null);
        } else if (selectedSectionIndex > index) {
            setSelectedSectionIndex(selectedSectionIndex - 1);
        }
    };

    const updateSection = (index, field, value) => {
        const newSections = [...sections];
        newSections[index] = { ...newSections[index], [field]: value };
        setSections(newSections);
    };

    const duplicateSection = (index) => {
        const newSections = [...sections];
        const duplicated = JSON.parse(JSON.stringify(sections[index]));
        newSections.splice(index + 1, 0, duplicated);
        setSections(newSections);
        setSelectedSectionIndex(index + 1);
    };

    // Drag and drop
    const handleDragStart = (index) => {
        setDraggedIndex(index);
    };

    const handleDragOver = (e, index) => {
        e.preventDefault();
    };

    const handleDrop = (e, dropIndex) => {
        e.preventDefault();
        if (draggedIndex === null || draggedIndex === dropIndex) return;

        const newSections = [...sections];
        const [dragged] = newSections.splice(draggedIndex, 1);
        newSections.splice(dropIndex, 0, dragged);
        setSections(newSections);
        setDraggedIndex(null);
        
        if (selectedSectionIndex === draggedIndex) {
            setSelectedSectionIndex(dropIndex);
        } else if (selectedSectionIndex === dropIndex) {
            setSelectedSectionIndex(draggedIndex);
        }
    };

    // Get section type label
    const getSectionLabel = (section) => {
        const labels = {
            rich_text: 'Rich Text',
            image_gallery: 'Image Gallery',
            image_slider: 'Image Slider',
            video_slider: 'Video Slider',
            banner: 'Banner',
            faq: 'FAQs',
            testimonials: 'Testimonials',
            video: 'Video',
            specifications: 'Specifications',
            comparison: 'Comparison',
            order_form: 'Order Form',
            call_to_action: 'Call to Action',
            tabs: 'Tabs',
            grid: 'Grid',
            flex: 'Flex',
            spacer: 'Spacer',
            container: 'Container',
            hero: 'Hero',
            benefits: 'Benefits',
            pricing: 'Pricing',
            steps: 'Steps',
            social_links: 'Social Links',
            contact_info: 'Contact Info',
        };
        return labels[section.type] || section.type;
    };

    // Render section editor
    const renderSectionEditor = (section, index) => {
        if (selectedSectionIndex !== index) return null;

        switch (section.type) {
            case 'grid':
                return <GridSection section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'flex':
                return <FlexSection section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'spacer':
                return <SpacerSection section={section} index={index} updateSection={updateSection} />;
            case 'container':
                return <ContainerSection section={section} index={index} updateSection={updateSection} />;
            default:
                return <SectionEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
        }
    };

    return (
        <div className="h-full flex overflow-hidden">
            {/* Left Sidebar - Section List */}
            <div className="w-80 bg-white border-r border-[#E1E3E5] flex flex-col overflow-hidden">
                <div className="flex-1 overflow-y-auto p-4 min-h-0">
                    <h2 className="text-sm font-semibold text-[#202223] mb-3">Sections</h2>
                    <div className="space-y-2">
                        {sections.length === 0 ? (
                            <p className="text-xs text-[#637381] py-4 text-center">No sections yet</p>
                        ) : (
                            sections.map((section, index) => (
                                <div
                                    key={index}
                                    draggable
                                    onDragStart={() => handleDragStart(index)}
                                    onDragOver={(e) => handleDragOver(e, index)}
                                    onDrop={(e) => handleDrop(e, index)}
                                    className={`p-3 rounded border cursor-move transition ${
                                        selectedSectionIndex === index
                                            ? 'border-[#008060] bg-[#F0FDF4]'
                                            : 'border-[#E1E3E5] hover:border-[#C9CCCF]'
                                    }`}
                                    onClick={() => setSelectedSectionIndex(index)}
                                >
                                    <div className="flex items-center gap-2">
                                        <svg className="w-4 h-4 text-[#637381]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 6h16M4 12h16M4 18h16" />
                                        </svg>
                                        <span className="text-sm font-medium text-[#202223] flex-1">{getSectionLabel(section)}</span>
                                        <button
                                            onClick={(e) => {
                                                e.stopPropagation();
                                                removeSection(index);
                                            }}
                                            className="text-[#637381] hover:text-red-600 p-1"
                                        >
                                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" />
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                            ))
                        )}
                    </div>
                    <div className="mt-4 pt-4 border-t border-[#E1E3E5]">
                        <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2">Prefab Layouts</h3>
                        <div className="space-y-2 mb-4">
                            <PrefabLayoutButtons onAdd={addPrefabLayout} />
                        </div>
                        <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2 mt-4">Add Section</h3>
                        <div className="space-y-2">
                            <SectionAddButtons onAdd={addSection} />
                        </div>
                    </div>
                </div>
            </div>

            {/* Main Content - Editor/Preview */}
            <div className="flex-1 flex flex-col overflow-hidden bg-[#F6F6F7]">
                {/* View Mode Toggle */}
                <div className="bg-white border-b border-[#E1E3E5] px-6 py-3 flex items-center justify-between flex-shrink-0">
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => setViewMode('edit')}
                            className={`px-4 py-1.5 rounded text-sm font-medium transition ${
                                viewMode === 'edit'
                                    ? 'bg-[#008060] text-white'
                                    : 'bg-transparent text-[#637381] hover:bg-[#F6F6F7]'
                            }`}
                        >
                            Edit
                        </button>
                        <button
                            onClick={() => setViewMode('preview')}
                            className={`px-4 py-1.5 rounded text-sm font-medium transition ${
                                viewMode === 'preview'
                                    ? 'bg-[#008060] text-white'
                                    : 'bg-transparent text-[#637381] hover:bg-[#F6F6F7]'
                            }`}
                        >
                            Preview
                        </button>
                    </div>
                </div>

                <div className={`flex-1 overflow-y-auto min-h-0 ${viewMode === 'preview' ? '' : 'p-6'}`}>
                    {viewMode === 'preview' ? (
                        // Preview Mode
                        <div className="bg-white min-h-full">
                            {sections.length === 0 ? (
                                <div className="text-center py-12 text-[#637381]">
                                    <p>No sections to preview. Add sections to see the preview.</p>
                                </div>
                            ) : (
                                <ProductSections
                                    layout={sections}
                                    productId={productId}
                                    productPrice={productPrice}
                                    productComparePrice={productComparePrice}
                                    productInStock={productInStock}
                                    productStockQuantity={productStockQuantity}
                                />
                            )}
                        </div>
                    ) : (
                        // Edit Mode
                        <>
                            {selectedSectionIndex !== null && sections[selectedSectionIndex] ? (
                                <div className="bg-white rounded-lg border border-[#E1E3E5] p-6">
                                    <div className="flex items-center justify-between mb-4">
                                        <h3 className="text-lg font-semibold text-[#202223]">
                                            Edit: {getSectionLabel(sections[selectedSectionIndex])}
                                        </h3>
                                        <button
                                            onClick={() => duplicateSection(selectedSectionIndex)}
                                            className="text-sm text-[#637381] hover:text-[#202223] px-3 py-1.5 rounded hover:bg-[#F6F6F7] transition"
                                        >
                                            Duplicate
                                        </button>
                                    </div>
                                    {renderSectionEditor(sections[selectedSectionIndex], selectedSectionIndex)}
                                </div>
                            ) : (
                                <div className="text-center py-12 text-[#637381]">
                                    <p>Select a section from the sidebar to edit, or add a new section to get started.</p>
                                </div>
                            )}
                        </>
                    )}
                </div>
            </div>
        </div>
    );
};

// Prefab Layout Buttons Component
const PrefabLayoutButtons = ({ onAdd }) => {
    const layouts = Object.entries(prefabLayouts).map(([key, layout]) => ({
        key,
        ...layout
    }));

    return (
        <>
            {layouts.map((layout) => (
                <div
                    key={layout.key}
                    className="border border-[#E1E3E5] rounded p-2 mb-2 hover:border-[#008060] hover:bg-[#F0FDF4] transition cursor-pointer"
                    onClick={() => onAdd(layout.key)}
                >
                    <div className="font-medium text-sm text-[#202223] mb-1">
                        {layout.name}
                    </div>
                    <div className="text-xs text-[#637381] line-clamp-2">
                        {layout.description}
                    </div>
                    <div className="text-xs text-[#008060] mt-1">
                        {layout.sections.length} sections
                    </div>
                </div>
            ))}
        </>
    );
};

// Section Add Buttons Component
const SectionAddButtons = ({ onAdd }) => {
    const sectionTypes = [
        { type: 'rich_text', label: 'Rich Text' },
        { type: 'image_gallery', label: 'Image Gallery' },
        { type: 'image_slider', label: 'Image Slider' },
        { type: 'video_slider', label: 'Video Slider' },
        { type: 'banner', label: 'Banner' },
        { type: 'faq', label: 'FAQs' },
        { type: 'testimonials', label: 'Testimonials' },
        { type: 'video', label: 'Video' },
        { type: 'specifications', label: 'Specifications' },
        { type: 'comparison', label: 'Comparison' },
        { type: 'order_form', label: 'Order Form' },
        { type: 'call_to_action', label: 'Call to Action' },
        { type: 'tabs', label: 'Tabs' },
        { type: 'grid', label: 'Grid' },
        { type: 'flex', label: 'Flex' },
        { type: 'spacer', label: 'Spacer' },
        { type: 'container', label: 'Container' },
        { type: 'hero', label: 'Hero' },
        { type: 'benefits', label: 'Benefits' },
        { type: 'pricing', label: 'Pricing' },
        { type: 'steps', label: 'Steps' },
        { type: 'social_links', label: 'Social Links' },
        { type: 'contact_info', label: 'Contact Info' },
    ];

    return (
        <>
            {sectionTypes.map(({ type, label }) => (
                <button
                    key={type}
                    onClick={() => onAdd(type)}
                    className="w-full text-left px-3 py-2 text-sm text-[#202223] hover:bg-[#F6F6F7] rounded transition"
                >
                    {label}
                </button>
            ))}
        </>
    );
};

// Generic Section Editor Component
const SectionEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const renderEditor = () => {
        switch (section.type) {
            case 'rich_text':
                return <RichTextEditor section={section} index={index} updateSection={updateSection} />;
            case 'image_gallery':
                return <ImageGalleryEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'banner':
                return <BannerEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'faq':
                return <FAQEditor section={section} index={index} updateSection={updateSection} />;
            case 'testimonials':
                return <TestimonialsEditor section={section} index={index} updateSection={updateSection} />;
            case 'video':
                return <VideoEditor section={section} index={index} updateSection={updateSection} />;
            case 'specifications':
                return <SpecificationsEditor section={section} index={index} updateSection={updateSection} />;
            case 'comparison':
                return <ComparisonEditor section={section} index={index} updateSection={updateSection} />;
            case 'order_form':
                return <OrderFormEditor section={section} index={index} updateSection={updateSection} />;
            case 'call_to_action':
                return <CallToActionEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'tabs':
                return <TabsEditor section={section} index={index} updateSection={updateSection} />;
            case 'hero':
                return <HeroEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'benefits':
                return <BenefitsEditor section={section} index={index} updateSection={updateSection} />;
            case 'pricing':
                return <PricingEditor section={section} index={index} updateSection={updateSection} />;
            case 'steps':
                return <StepsEditor section={section} index={index} updateSection={updateSection} />;
            case 'social_links':
                return <SocialLinksEditor section={section} index={index} updateSection={updateSection} />;
            case 'contact_info':
                return <ContactInfoEditor section={section} index={index} updateSection={updateSection} />;
            case 'image_slider':
                return <ImageSliderEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
            case 'video_slider':
                return <VideoSliderEditor section={section} index={index} updateSection={updateSection} />;
            default:
                return <div className="text-sm text-[#637381]">No editor available for this section type.</div>;
        }
    };

    return <div className="space-y-4">{renderEditor()}</div>;
};

// Helper function to create default sections
const createDefaultSection = (type) => {
    const defaults = {
        rich_text: { type: 'rich_text', title: '', content: '' },
        image_gallery: { type: 'image_gallery', title: '', images: [] },
        image_slider: { type: 'image_slider', title: '', images: [], autoplay: true, autoplaySpeed: 5000, dots: true, arrows: true },
        video_slider: { type: 'video_slider', title: '', videos: [], autoplay: true, autoplaySpeed: 5000, dots: true, arrows: true },
        banner: { 
            type: 'banner', 
            title: '', 
            content: '', 
            background_type: 'color',
            background_color: '#FFD700', 
            gradient_start: '#FFD700',
            gradient_end: '#FFA500',
            background_image: '', 
            text_color: '#000000' 
        },
        faq: { type: 'faq', title: '', items: [] },
        testimonials: { type: 'testimonials', title: '', items: [] },
        video: { type: 'video', title: '', url: '' },
        specifications: { type: 'specifications', title: '', items: [] },
        comparison: { type: 'comparison', title: '', items: [] },
        order_form: { type: 'order_form', title: '', content: '', background_color: '' },
        call_to_action: { type: 'call_to_action', title: '', content: '', button_text: '', button_link: '#', background_color: '#008060', text_color: '#FFFFFF', button_color: '#FFFFFF', background_image: '' },
        tabs: { type: 'tabs', title: '', items: [] },
        grid: { type: 'grid', title: '', columns: 2, gap: 'medium', items: [] },
        flex: { type: 'flex', title: '', direction: 'row', align: 'start', justify: 'start', items: [] },
        spacer: { type: 'spacer', height: 50 },
        container: { type: 'container', maxWidth: 'full', padding: 'medium', backgroundColor: '#FFFFFF', content: '' },
        hero: { 
            type: 'hero', 
            title: '', 
            subtitle: '', 
            discount_text: '',
            html_content: '',
            video_url: '',
            video_title: '',
            button_text: '', 
            button_link: '#',
            button2_text: '',
            button2_link: '#',
            background_type: 'color', // 'color', 'gradient', 'image'
            background_color: '#008060',
            gradient_start: '#008060',
            gradient_end: '#006E52',
            background_image: '', 
            text_color: '#FFFFFF', 
            button_bg_color: '#FFFFFF', 
            button_text_color: '#008060',
            button2_bg_color: '#008060',
            button2_text_color: '#FFFFFF',
            images: [] 
        },
        benefits: { type: 'benefits', title: '', items: [] },
        pricing: { type: 'pricing', title: '', original_price: '', offer_price: '', discount_text: '', countdown_date: '' },
        steps: { type: 'steps', title: '', items: [] },
        social_links: { type: 'social_links', title: '', items: [] },
        contact_info: { type: 'contact_info', title: '', phone: '', email: '', address: '', content: '' },
    };
    return defaults[type] || { type };
};

// Individual Section Editors
const RichTextEditor = ({ section, index, updateSection }) => (
    <div className="space-y-3">
        <div>
            <label className="text-sm font-medium block mb-1">Title (optional)</label>
            <input
                type="text"
                value={section.title || ''}
                onChange={(e) => updateSection(index, 'title', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="Section Title"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Content (HTML)</label>
            <textarea
                value={section.content || ''}
                onChange={(e) => updateSection(index, 'content', e.target.value)}
                rows={10}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm font-mono"
                placeholder="Enter HTML content"
            />
        </div>
    </div>
);

const ImageGalleryEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const addImage = async () => {
        const url = await uploadImage(index, 'gallery');
        if (url) {
            const images = [...(section.images || []), url];
            updateSection(index, 'images', images);
        }
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Images</label>
                <div className="space-y-2">
                    {(section.images || []).map((image, imgIndex) => (
                        <div key={imgIndex} className="flex items-center gap-2 border border-neutral-300 rounded p-2">
                            <img src={image} alt="" className="w-16 h-16 object-cover rounded" />
                            <input
                                type="text"
                                value={image}
                                onChange={(e) => {
                                    const images = [...(section.images || [])];
                                    images[imgIndex] = e.target.value;
                                    updateSection(index, 'images', images);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                            />
                            <button
                                onClick={() => {
                                    const images = section.images.filter((_, i) => i !== imgIndex);
                                    updateSection(index, 'images', images);
                                }}
                                className="text-red-500 px-2 py-1 text-sm"
                            >
                                Remove
                            </button>
                        </div>
                    ))}
                    <button
                        onClick={addImage}
                        disabled={uploading[`${index}-gallery`]}
                        className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50"
                    >
                        {uploading[`${index}-gallery`] ? 'Uploading...' : '+ Add Image'}
                    </button>
                </div>
            </div>
        </div>
    );
};

const BannerEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const handleImageUpload = async () => {
        const url = await uploadImage(index, 'background');
        if (url) {
            updateSection(index, 'background_image', url);
        }
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Content</label>
                <textarea
                    value={section.content || ''}
                    onChange={(e) => updateSection(index, 'content', e.target.value)}
                    rows={3}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div className="border-t pt-3 mt-3">
                <label className="text-sm font-medium block mb-2">Background Type</label>
                <select
                    value={section.background_type || 'color'}
                    onChange={(e) => updateSection(index, 'background_type', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                >
                    <option value="color">Solid Color</option>
                    <option value="gradient">Gradient</option>
                    <option value="image">Image</option>
                </select>
            </div>

            {(!section.background_type || section.background_type === 'color') && (
                <div>
                    <label className="text-sm font-medium block mb-1">Background Color</label>
                    <input
                        type="color"
                        value={section.background_color || '#FFD700'}
                        onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                        className="w-full h-10"
                    />
                </div>
            )}

            {section.background_type === 'gradient' && (
                <div className="grid grid-cols-2 gap-2">
                    <div>
                        <label className="text-sm font-medium block mb-1">Gradient Start</label>
                        <input
                            type="color"
                            value={section.gradient_start || '#FFD700'}
                            onChange={(e) => updateSection(index, 'gradient_start', e.target.value)}
                            className="w-full h-10"
                        />
                    </div>
                    <div>
                        <label className="text-sm font-medium block mb-1">Gradient End</label>
                        <input
                            type="color"
                            value={section.gradient_end || '#FFA500'}
                            onChange={(e) => updateSection(index, 'gradient_end', e.target.value)}
                            className="w-full h-10"
                        />
                    </div>
                </div>
            )}

            {section.background_type === 'image' && (
                <div>
                    <label className="text-sm font-medium block mb-1">Background Image</label>
                    {section.background_image && (
                        <div className="mb-2">
                            <img src={section.background_image} alt="" className="w-full h-32 object-cover rounded mb-2" />
                            <button
                                onClick={() => updateSection(index, 'background_image', '')}
                                className="text-red-500 text-sm"
                            >
                                Remove Image
                            </button>
                        </div>
                    )}
                    <button
                        onClick={handleImageUpload}
                        disabled={uploading[`${index}-background`]}
                        className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50 mb-2"
                    >
                        {uploading[`${index}-background`] ? 'Uploading...' : '+ Upload Background Image'}
                    </button>
                </div>
            )}

            <div>
                <label className="text-sm font-medium block mb-1">Text Color</label>
                <input
                    type="color"
                    value={section.text_color || '#000000'}
                    onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                    className="w-full h-10"
                />
            </div>
        </div>
    );
};

const FAQEditor = ({ section, index, updateSection }) => {
    const addFAQ = () => {
        const items = [...(section.items || []), { question: '', answer: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">FAQs</label>
                <div className="space-y-3">
                    {(section.items || []).map((faq, faqIndex) => (
                        <div key={faqIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-xs text-neutral-500">FAQ {faqIndex + 1}</span>
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== faqIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 text-xs"
                                >
                                    Remove
                                </button>
                            </div>
                            <input
                                type="text"
                                value={faq.question || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[faqIndex] = { ...items[faqIndex], question: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm mb-2"
                                placeholder="Question"
                            />
                            <textarea
                                value={faq.answer || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[faqIndex] = { ...items[faqIndex], answer: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={2}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Answer"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addFAQ}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add FAQ
                    </button>
                </div>
            </div>
        </div>
    );
};

const TestimonialsEditor = ({ section, index, updateSection }) => {
    const addTestimonial = () => {
        const items = [...(section.items || []), { text: '', author: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Testimonials</label>
                <div className="space-y-3">
                    {(section.items || []).map((testimonial, testIndex) => (
                        <div key={testIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-xs text-neutral-500">Testimonial {testIndex + 1}</span>
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== testIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 text-xs"
                                >
                                    Remove
                                </button>
                            </div>
                            <textarea
                                value={testimonial.text || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[testIndex] = { ...items[testIndex], text: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={3}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm mb-2"
                                placeholder="Testimonial text"
                            />
                            <input
                                type="text"
                                value={testimonial.author || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[testIndex] = { ...items[testIndex], author: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Author name"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addTestimonial}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Testimonial
                    </button>
                </div>
            </div>
        </div>
    );
};

const VideoEditor = ({ section, index, updateSection }) => (
    <div className="space-y-3">
        <div>
            <label className="text-sm font-medium block mb-1">Title (optional)</label>
            <input
                type="text"
                value={section.title || ''}
                onChange={(e) => updateSection(index, 'title', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Video Embed URL</label>
            <input
                type="url"
                value={section.url || ''}
                onChange={(e) => updateSection(index, 'url', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="https://www.youtube.com/embed/VIDEO_ID"
            />
        </div>
    </div>
);

const SpecificationsEditor = ({ section, index, updateSection }) => {
    const addSpec = () => {
        const items = [...(section.items || []), { label: '', value: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Specifications</label>
                <div className="space-y-2">
                    {(section.items || []).map((spec, specIndex) => (
                        <div key={specIndex} className="flex gap-2 border border-neutral-300 rounded p-2">
                            <input
                                type="text"
                                value={spec.label || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[specIndex] = { ...items[specIndex], label: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Label"
                            />
                            <input
                                type="text"
                                value={spec.value || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[specIndex] = { ...items[specIndex], value: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Value"
                            />
                            <button
                                onClick={() => {
                                    const items = section.items.filter((_, i) => i !== specIndex);
                                    updateSection(index, 'items', items);
                                }}
                                className="text-red-500 px-2 text-sm"
                            >
                                ×
                            </button>
                        </div>
                    ))}
                    <button
                        onClick={addSpec}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Specification
                    </button>
                </div>
            </div>
        </div>
    );
};

const ComparisonEditor = ({ section, index, updateSection }) => {
    const addOption = () => {
        const items = [...(section.items || []), { name: '', features: [] }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Comparison Options</label>
                <div className="space-y-3">
                    {(section.items || []).map((item, itemIndex) => (
                        <div key={itemIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <input
                                    type="text"
                                    value={item.name || ''}
                                    onChange={(e) => {
                                        const items = [...(section.items || [])];
                                        items[itemIndex] = { ...items[itemIndex], name: e.target.value };
                                        updateSection(index, 'items', items);
                                    }}
                                    className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                    placeholder="Option Name"
                                />
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== itemIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 px-2 text-sm ml-2"
                                >
                                    Remove
                                </button>
                            </div>
                            <div className="text-xs text-neutral-500 mb-2">Features (label: value format, one per line)</div>
                            <textarea
                                value={(item.features || []).map(f => `${f.label}: ${f.value}`).join('\n')}
                                onChange={(e) => {
                                    const features = e.target.value.split('\n').filter(l => l.trim()).map(line => {
                                        const [label, ...valueParts] = line.split(':');
                                        return { label: label.trim(), value: valueParts.join(':').trim() };
                                    });
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], features };
                                    updateSection(index, 'items', items);
                                }}
                                rows={4}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm font-mono text-xs"
                                placeholder="Feature 1: Value 1&#10;Feature 2: Value 2"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addOption}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Option
                    </button>
                </div>
            </div>
        </div>
    );
};

const OrderFormEditor = ({ section, index, updateSection }) => (
    <div className="space-y-3">
        <div>
            <label className="text-sm font-medium block mb-1">Title (optional)</label>
            <input
                type="text"
                value={section.title || ''}
                onChange={(e) => updateSection(index, 'title', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Content (optional)</label>
            <textarea
                value={section.content || ''}
                onChange={(e) => updateSection(index, 'content', e.target.value)}
                rows={3}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="Text to display above the order form"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Background Color (optional)</label>
            <div className="flex items-center gap-3">
                <input
                    type="color"
                    value={section.background_color || '#FFFFFF'}
                    onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                    className="w-16 h-10 border border-neutral-300 rounded cursor-pointer"
                />
                <input
                    type="text"
                    value={section.background_color || ''}
                    onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                    className="flex-1 px-3 py-2 border border-neutral-300 rounded text-sm font-mono"
                    placeholder="#FFFFFF or leave empty for default"
                />
                {section.background_color && (
                    <button
                        onClick={() => updateSection(index, 'background_color', '')}
                        className="text-red-500 text-sm px-2 py-1 hover:bg-red-50 rounded"
                        type="button"
                    >
                        Clear
                    </button>
                )}
            </div>
        </div>
        <p className="text-xs text-neutral-500">Note: The order form will automatically use the product's price and stock information.</p>
    </div>
);

const CallToActionEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const handleImageUpload = async () => {
        const url = await uploadImage(index, 'background');
        if (url) {
            updateSection(index, 'background_image', url);
        }
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Content</label>
                <textarea
                    value={section.content || ''}
                    onChange={(e) => updateSection(index, 'content', e.target.value)}
                    rows={3}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Button Text</label>
                <input
                    type="text"
                    value={section.button_text || ''}
                    onChange={(e) => updateSection(index, 'button_text', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Button Link</label>
                <input
                    type="text"
                    value={section.button_link || '#'}
                    onChange={(e) => updateSection(index, 'button_link', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    placeholder="#order or URL"
                />
            </div>
            <div className="grid grid-cols-2 gap-2">
                <div>
                    <label className="text-sm font-medium block mb-1">Background Color</label>
                    <input
                        type="color"
                        value={section.background_color || '#008060'}
                        onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                        className="w-full h-10"
                    />
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Text Color</label>
                    <input
                        type="color"
                        value={section.text_color || '#FFFFFF'}
                        onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                        className="w-full h-10"
                    />
                </div>
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Button Color</label>
                <input
                    type="color"
                    value={section.button_color || '#FFFFFF'}
                    onChange={(e) => updateSection(index, 'button_color', e.target.value)}
                    className="w-full h-10"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Background Image (optional)</label>
                {section.background_image && (
                    <div className="mb-2">
                        <img src={section.background_image} alt="" className="w-full h-32 object-cover rounded mb-2" />
                        <button
                            onClick={() => updateSection(index, 'background_image', '')}
                            className="text-red-500 text-sm"
                        >
                            Remove Image
                        </button>
                    </div>
                )}
                <button
                    onClick={handleImageUpload}
                    disabled={uploading[`${index}-background`]}
                    className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50"
                >
                    {uploading[`${index}-background`] ? 'Uploading...' : '+ Upload Background Image'}
                </button>
            </div>
        </div>
    );
};

const TabsEditor = ({ section, index, updateSection }) => {
    const addTab = () => {
        const items = [...(section.items || []), { title: '', content: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Tabs</label>
                <div className="space-y-3">
                    {(section.items || []).map((tab, tabIndex) => (
                        <div key={tabIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <input
                                    type="text"
                                    value={tab.title || ''}
                                    onChange={(e) => {
                                        const items = [...(section.items || [])];
                                        items[tabIndex] = { ...items[tabIndex], title: e.target.value };
                                        updateSection(index, 'items', items);
                                    }}
                                    className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                    placeholder="Tab Title"
                                />
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== tabIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 px-2 text-sm ml-2"
                                >
                                    Remove
                                </button>
                            </div>
                            <textarea
                                value={tab.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[tabIndex] = { ...items[tabIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={4}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm font-mono text-xs"
                                placeholder="Tab content (HTML)"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addTab}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Tab
                    </button>
                </div>
            </div>
        </div>
    );
};

const HeroEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const handleImageUpload = async () => {
        const url = await uploadImage(index, 'hero');
        if (url) {
            const images = [url];
            updateSection(index, 'images', images);
        }
    };

    const handleBackgroundUpload = async () => {
        const url = await uploadImage(index, 'background');
        if (url) {
            updateSection(index, 'background_image', url);
        }
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Subtitle</label>
                <textarea
                    value={section.subtitle || ''}
                    onChange={(e) => updateSection(index, 'subtitle', e.target.value)}
                    rows={2}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    placeholder="Supporting text"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Discount Text (optional)</label>
                <input
                    type="text"
                    value={section.discount_text || ''}
                    onChange={(e) => updateSection(index, 'discount_text', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    placeholder="e.g., প্রথম ১০০ জন ৯০% ছাড়ে"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">HTML Content (optional)</label>
                <textarea
                    value={section.html_content || ''}
                    onChange={(e) => updateSection(index, 'html_content', e.target.value)}
                    rows={4}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm font-mono text-xs"
                    placeholder="Custom HTML content"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Video Embed URL (optional)</label>
                <input
                    type="url"
                    value={section.video_url || ''}
                    onChange={(e) => updateSection(index, 'video_url', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    placeholder="https://www.youtube.com/embed/VIDEO_ID"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Video Title (optional - shown underneath video)</label>
                <input
                    type="text"
                    value={section.video_title || ''}
                    onChange={(e) => updateSection(index, 'video_title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    placeholder="Title to display underneath the video"
                />
            </div>
            
            <div className="border-t pt-3 mt-3">
                <label className="text-sm font-semibold block mb-2">Button 1 (Primary)</label>
                <div className="mb-2">
                    <label className="text-xs text-neutral-500 block mb-1">Button Text (HTML supported)</label>
                    <textarea
                        value={section.button_text || ''}
                        onChange={(e) => updateSection(index, 'button_text', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm font-mono text-xs mb-2"
                        placeholder="Button Text (HTML supported)"
                        rows={2}
                    />
                    <input
                        type="text"
                        value={section.button_link || '#'}
                        onChange={(e) => updateSection(index, 'button_link', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                        placeholder="Button Link"
                    />
                </div>
                <div className="grid grid-cols-2 gap-2">
                    <div>
                        <label className="text-xs text-neutral-500 block mb-1">BG Color</label>
                        <input
                            type="color"
                            value={section.button_bg_color || '#FFFFFF'}
                            onChange={(e) => updateSection(index, 'button_bg_color', e.target.value)}
                            className="w-full h-8"
                        />
                    </div>
                    <div>
                        <label className="text-xs text-neutral-500 block mb-1">Text Color</label>
                        <input
                            type="color"
                            value={section.button_text_color || '#008060'}
                            onChange={(e) => updateSection(index, 'button_text_color', e.target.value)}
                            className="w-full h-8"
                        />
                    </div>
                </div>
            </div>

            <div className="border-t pt-3 mt-3">
                <label className="text-sm font-semibold block mb-2">Button 2 (Secondary)</label>
                <div className="mb-2">
                    <label className="text-xs text-neutral-500 block mb-1">Button Text (HTML supported)</label>
                    <textarea
                        value={section.button2_text || ''}
                        onChange={(e) => updateSection(index, 'button2_text', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm font-mono text-xs mb-2"
                        placeholder="Button Text (HTML supported)"
                        rows={2}
                    />
                    <input
                        type="text"
                        value={section.button2_link || '#'}
                        onChange={(e) => updateSection(index, 'button2_link', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                        placeholder="Button Link"
                    />
                </div>
                <div className="grid grid-cols-2 gap-2">
                    <div>
                        <label className="text-xs text-neutral-500 block mb-1">BG Color</label>
                        <input
                            type="color"
                            value={section.button2_bg_color || '#008060'}
                            onChange={(e) => updateSection(index, 'button2_bg_color', e.target.value)}
                            className="w-full h-8"
                        />
                    </div>
                    <div>
                        <label className="text-xs text-neutral-500 block mb-1">Text Color</label>
                        <input
                            type="color"
                            value={section.button2_text_color || '#FFFFFF'}
                            onChange={(e) => updateSection(index, 'button2_text_color', e.target.value)}
                            className="w-full h-8"
                        />
                    </div>
                </div>
            </div>

            <div className="border-t pt-3 mt-3">
                <label className="text-sm font-medium block mb-2">Background Type</label>
                <select
                    value={section.background_type || 'color'}
                    onChange={(e) => updateSection(index, 'background_type', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                >
                    <option value="color">Solid Color</option>
                    <option value="gradient">Gradient</option>
                    <option value="image">Image</option>
                </select>
            </div>

            {(!section.background_type || section.background_type === 'color') && (
                <div>
                    <label className="text-sm font-medium block mb-1">Background Color</label>
                    <input
                        type="color"
                        value={section.background_color || '#008060'}
                        onChange={(e) => updateSection(index, 'background_color', e.target.value)}
                        className="w-full h-10"
                    />
                </div>
            )}

            {section.background_type === 'gradient' && (
                <div className="grid grid-cols-2 gap-2">
                    <div>
                        <label className="text-sm font-medium block mb-1">Gradient Start</label>
                        <input
                            type="color"
                            value={section.gradient_start || '#008060'}
                            onChange={(e) => updateSection(index, 'gradient_start', e.target.value)}
                            className="w-full h-10"
                        />
                    </div>
                    <div>
                        <label className="text-sm font-medium block mb-1">Gradient End</label>
                        <input
                            type="color"
                            value={section.gradient_end || '#006E52'}
                            onChange={(e) => updateSection(index, 'gradient_end', e.target.value)}
                            className="w-full h-10"
                        />
                    </div>
                </div>
            )}

            {section.background_type === 'image' && (
                <div>
                    <label className="text-sm font-medium block mb-1">Background Image</label>
                    {section.background_image && (
                        <div className="mb-2">
                            <img src={section.background_image} alt="" className="w-full h-32 object-cover rounded mb-2" />
                            <button
                                onClick={() => updateSection(index, 'background_image', '')}
                                className="text-red-500 text-sm"
                            >
                                Remove Image
                            </button>
                        </div>
                    )}
                    <button
                        onClick={handleBackgroundUpload}
                        disabled={uploading[`${index}-background`]}
                        className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50 mb-2"
                    >
                        {uploading[`${index}-background`] ? 'Uploading...' : '+ Upload Background Image'}
                    </button>
                </div>
            )}

            <div>
                <label className="text-sm font-medium block mb-1">Text Color</label>
                <input
                    type="color"
                    value={section.text_color || '#FFFFFF'}
                    onChange={(e) => updateSection(index, 'text_color', e.target.value)}
                    className="w-full h-10"
                />
            </div>
            {section.background_type !== 'image' && (
                <div>
                    <label className="text-sm font-medium block mb-1">Hero Image (optional)</label>
                    {section.images && section.images[0] && (
                        <div className="mb-2">
                            <img src={section.images[0]} alt="" className="w-full h-32 object-cover rounded mb-2" />
                            <button
                                onClick={() => updateSection(index, 'images', [])}
                                className="text-red-500 text-sm"
                            >
                                Remove Image
                            </button>
                        </div>
                    )}
                    <button
                        onClick={handleImageUpload}
                        disabled={uploading[`${index}-hero`]}
                        className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50"
                    >
                        {uploading[`${index}-hero`] ? 'Uploading...' : '+ Upload Hero Image'}
                    </button>
                </div>
            )}
        </div>
    );
};

const BenefitsEditor = ({ section, index, updateSection }) => {
    const addBenefit = () => {
        const items = [...(section.items || []), { title: '', description: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Benefits</label>
                <div className="space-y-3">
                    {(section.items || []).map((benefit, benefitIndex) => (
                        <div key={benefitIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-xs text-neutral-500">Benefit {benefitIndex + 1}</span>
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== benefitIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 text-xs"
                                >
                                    Remove
                                </button>
                            </div>
                            <input
                                type="text"
                                value={benefit.title || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[benefitIndex] = { ...items[benefitIndex], title: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm mb-2"
                                placeholder="Benefit Title"
                            />
                            <textarea
                                value={benefit.description || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[benefitIndex] = { ...items[benefitIndex], description: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={3}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Benefit Description"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addBenefit}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Benefit
                    </button>
                </div>
            </div>
        </div>
    );
};

const PricingEditor = ({ section, index, updateSection }) => (
    <div className="space-y-3">
        <div>
            <label className="text-sm font-medium block mb-1">Title (optional)</label>
            <input
                type="text"
                value={section.title || ''}
                onChange={(e) => updateSection(index, 'title', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Original Price</label>
            <input
                type="text"
                value={section.original_price || ''}
                onChange={(e) => updateSection(index, 'original_price', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="৳1,000"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Offer Price</label>
            <input
                type="text"
                value={section.offer_price || ''}
                onChange={(e) => updateSection(index, 'offer_price', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="৳800"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Discount Text</label>
            <input
                type="text"
                value={section.discount_text || ''}
                onChange={(e) => updateSection(index, 'discount_text', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                placeholder="20% OFF"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Countdown Date (optional)</label>
            <input
                type="datetime-local"
                value={section.countdown_date || ''}
                onChange={(e) => updateSection(index, 'countdown_date', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
    </div>
);

const StepsEditor = ({ section, index, updateSection }) => {
    const addStep = () => {
        const items = [...(section.items || []), { title: '', description: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Steps</label>
                <div className="space-y-3">
                    {(section.items || []).map((step, stepIndex) => (
                        <div key={stepIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-xs text-neutral-500">Step {stepIndex + 1}</span>
                                <button
                                    onClick={() => {
                                        const items = section.items.filter((_, i) => i !== stepIndex);
                                        updateSection(index, 'items', items);
                                    }}
                                    className="text-red-500 text-xs"
                                >
                                    Remove
                                </button>
                            </div>
                            <input
                                type="text"
                                value={step.title || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[stepIndex] = { ...items[stepIndex], title: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm mb-2"
                                placeholder="Step Title"
                            />
                            <textarea
                                value={step.description || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[stepIndex] = { ...items[stepIndex], description: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={2}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Step Description"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addStep}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Step
                    </button>
                </div>
            </div>
        </div>
    );
};

const SocialLinksEditor = ({ section, index, updateSection }) => {
    const addLink = () => {
        const items = [...(section.items || []), { platform: '', url: '' }];
        updateSection(index, 'items', items);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Social Links</label>
                <div className="space-y-2">
                    {(section.items || []).map((link, linkIndex) => (
                        <div key={linkIndex} className="flex gap-2 border border-neutral-300 rounded p-2">
                            <input
                                type="text"
                                value={link.platform || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[linkIndex] = { ...items[linkIndex], platform: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Platform (e.g., Facebook)"
                            />
                            <input
                                type="url"
                                value={link.url || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[linkIndex] = { ...items[linkIndex], url: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="URL"
                            />
                            <button
                                onClick={() => {
                                    const items = section.items.filter((_, i) => i !== linkIndex);
                                    updateSection(index, 'items', items);
                                }}
                                className="text-red-500 px-2 text-sm"
                            >
                                ×
                            </button>
                        </div>
                    ))}
                    <button
                        onClick={addLink}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Social Link
                    </button>
                </div>
            </div>
        </div>
    );
};

const ContactInfoEditor = ({ section, index, updateSection }) => (
    <div className="space-y-3">
        <div>
            <label className="text-sm font-medium block mb-1">Title (optional)</label>
            <input
                type="text"
                value={section.title || ''}
                onChange={(e) => updateSection(index, 'title', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Phone</label>
            <input
                type="tel"
                value={section.phone || ''}
                onChange={(e) => updateSection(index, 'phone', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Email</label>
            <input
                type="email"
                value={section.email || ''}
                onChange={(e) => updateSection(index, 'email', e.target.value)}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Address</label>
            <textarea
                value={section.address || ''}
                onChange={(e) => updateSection(index, 'address', e.target.value)}
                rows={2}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
            />
        </div>
        <div>
            <label className="text-sm font-medium block mb-1">Additional Content (HTML, optional)</label>
            <textarea
                value={section.content || ''}
                onChange={(e) => updateSection(index, 'content', e.target.value)}
                rows={4}
                className="w-full px-3 py-2 border border-neutral-300 rounded text-sm font-mono"
            />
        </div>
    </div>
);

const ImageSliderEditor = ({ section, index, updateSection, uploadImage, uploading }) => {
    const addImage = async () => {
        const url = await uploadImage(index, 'slider');
        if (url) {
            const images = [...(section.images || []), url];
            updateSection(index, 'images', images);
        }
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Slider Images</label>
                <div className="space-y-2">
                    {(section.images || []).map((image, imgIndex) => (
                        <div key={imgIndex} className="flex items-center gap-2 border border-neutral-300 rounded p-2">
                            <img src={image} alt="" className="w-16 h-16 object-cover rounded" />
                            <input
                                type="text"
                                value={image}
                                onChange={(e) => {
                                    const images = [...(section.images || [])];
                                    images[imgIndex] = e.target.value;
                                    updateSection(index, 'images', images);
                                }}
                                className="flex-1 px-2 py-1 border border-neutral-300 rounded text-sm"
                            />
                            <button
                                onClick={() => {
                                    const images = section.images.filter((_, i) => i !== imgIndex);
                                    updateSection(index, 'images', images);
                                }}
                                className="text-red-500 px-2 py-1 text-sm"
                            >
                                Remove
                            </button>
                        </div>
                    ))}
                    <button
                        onClick={addImage}
                        disabled={uploading[`${index}-slider`]}
                        className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm hover:bg-[#006E52] disabled:opacity-50"
                    >
                        {uploading[`${index}-slider`] ? 'Uploading...' : '+ Add Image'}
                    </button>
                </div>
            </div>
            <div className="grid grid-cols-2 gap-3 border-t pt-3 mt-3">
                <div>
                    <label className="text-sm font-medium block mb-1">Autoplay</label>
                    <select
                        value={section.autoplay ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'autoplay', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Autoplay Speed (ms)</label>
                    <input
                        type="number"
                        value={section.autoplaySpeed || 5000}
                        onChange={(e) => updateSection(index, 'autoplaySpeed', parseInt(e.target.value))}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                        min="1000"
                        step="500"
                    />
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Show Dots</label>
                    <select
                        value={section.dots ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'dots', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Show Arrows</label>
                    <select
                        value={section.arrows ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'arrows', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
            </div>
        </div>
    );
};

const VideoSliderEditor = ({ section, index, updateSection }) => {
    const addVideo = () => {
        const videos = [...(section.videos || []), { url: '', title: '' }];
        updateSection(index, 'videos', videos);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-2">Slider Videos</label>
                <div className="space-y-3">
                    {(section.videos || []).map((video, vidIndex) => (
                        <div key={vidIndex} className="border border-neutral-300 rounded p-3">
                            <div className="flex justify-between items-center mb-2">
                                <span className="text-xs text-neutral-500">Video {vidIndex + 1}</span>
                                <button
                                    onClick={() => {
                                        const videos = section.videos.filter((_, i) => i !== vidIndex);
                                        updateSection(index, 'videos', videos);
                                    }}
                                    className="text-red-500 text-xs"
                                >
                                    Remove
                                </button>
                            </div>
                            <input
                                type="url"
                                value={video.url || ''}
                                onChange={(e) => {
                                    const videos = [...(section.videos || [])];
                                    videos[vidIndex] = { ...videos[vidIndex], url: e.target.value };
                                    updateSection(index, 'videos', videos);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm mb-2"
                                placeholder="https://www.youtube.com/embed/VIDEO_ID"
                            />
                            <input
                                type="text"
                                value={video.title || ''}
                                onChange={(e) => {
                                    const videos = [...(section.videos || [])];
                                    videos[vidIndex] = { ...videos[vidIndex], title: e.target.value };
                                    updateSection(index, 'videos', videos);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Video Title (optional)"
                            />
                        </div>
                    ))}
                    <button
                        onClick={addVideo}
                        className="w-full border-2 border-dashed border-neutral-300 px-3 py-2 rounded text-sm text-neutral-500 hover:border-[#008060] hover:text-[#008060]"
                    >
                        + Add Video
                    </button>
                </div>
            </div>
            <div className="grid grid-cols-2 gap-3 border-t pt-3 mt-3">
                <div>
                    <label className="text-sm font-medium block mb-1">Autoplay</label>
                    <select
                        value={section.autoplay ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'autoplay', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Autoplay Speed (ms)</label>
                    <input
                        type="number"
                        value={section.autoplaySpeed || 5000}
                        onChange={(e) => updateSection(index, 'autoplaySpeed', parseInt(e.target.value))}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                        min="1000"
                        step="500"
                    />
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Show Dots</label>
                    <select
                        value={section.dots ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'dots', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Show Arrows</label>
                    <select
                        value={section.arrows ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'arrows', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                </div>
            </div>
        </div>
    );
};

export default PageBuilder;

