import React, { useState, useEffect, useRef, useCallback } from 'react';
import ProductSections from './ProductSections';

const HISTORY_LIMIT = 50;

const PageBuilder = ({ initialSections = [], productId = null, productName = '', productImage = '', productPrice = null, productComparePrice = null, productInStock = false, productStockQuantity = null, orderSettings = {} }) => {
    const [sections, setSections] = useState(initialSections);
    const [selectedSectionIndex, setSelectedSectionIndex] = useState(null);
    const [uploading, setUploading] = useState({});
    const [draggedIndex, setDraggedIndex] = useState(null);
    const [viewMode, setViewMode] = useState('edit'); // 'edit' or 'preview'
    const [devicePreview, setDevicePreview] = useState('desktop'); // 'desktop' | 'tablet' | 'mobile'
    const [sectionPickerOpen, setSectionPickerOpen] = useState(false);
    const [insertAfterIndex, setInsertAfterIndex] = useState(null); // null = append at end
    const [history, setHistory] = useState([]);
    const [historyIndex, setHistoryIndex] = useState(-1);
    const sectionListRef = useRef(null);
    const selectedItemRef = useRef(null);

    // Push current state to history (skip initial and when undoing/redoing)
    const pushHistory = useCallback((nextSections) => {
        setHistory((prev) => {
            const trimmed = prev.slice(0, historyIndex + 1);
            const snapshot = JSON.stringify(nextSections);
            if (trimmed.length > 0 && trimmed[trimmed.length - 1] === snapshot) return prev;
            const next = [...trimmed, snapshot].slice(-HISTORY_LIMIT);
            setHistoryIndex(next.length - 1);
            return next;
        });
    }, [historyIndex]);

    const setSectionsWithHistory = useCallback((updater) => {
        setSections((prev) => {
            const next = typeof updater === 'function' ? updater(prev) : updater;
            pushHistory(next);
            return next;
        });
    }, [pushHistory]);

    const undo = useCallback(() => {
        if (historyIndex <= 0) return;
        const prev = historyIndex - 1;
        setHistoryIndex(prev);
        const restored = JSON.parse(history[prev]);
        setSections(restored);
        if (selectedSectionIndex !== null && selectedSectionIndex >= restored.length) {
            setSelectedSectionIndex(restored.length > 0 ? restored.length - 1 : null);
        }
    }, [history, historyIndex, selectedSectionIndex]);

    const redo = useCallback(() => {
        if (historyIndex >= history.length - 1 || historyIndex < 0) return;
        const next = historyIndex + 1;
        setHistoryIndex(next);
        const restored = JSON.parse(history[next]);
        setSections(restored);
        setSelectedSectionIndex(null);
    }, [history, historyIndex]);

    // Update global sections for save function
    useEffect(() => {
        window.currentSections = sections;
    }, [sections]);

    // Initial history snapshot
    useEffect(() => {
        if (history.length === 0 && sections.length >= 0) {
            setHistory([JSON.stringify(sections)]);
            setHistoryIndex(0);
        }
    }, []);

    // Keyboard: undo / redo
    useEffect(() => {
        const handleKeyDown = (e) => {
            if (!e.target || e.target.closest('input, textarea, select')) return;
            if ((e.metaKey || e.ctrlKey) && e.key === 'z') {
                e.preventDefault();
                if (e.shiftKey) redo();
                else undo();
            }
        };
        window.addEventListener('keydown', handleKeyDown);
        return () => window.removeEventListener('keydown', handleKeyDown);
    }, [undo, redo]);

    // Scroll selected section into view in left sidebar
    useEffect(() => {
        if (selectedSectionIndex !== null && selectedItemRef.current && sectionListRef.current) {
            selectedItemRef.current.scrollIntoView({ block: 'nearest', behavior: 'smooth' });
        }
    }, [selectedSectionIndex]);

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
        setSectionsWithHistory(newSections);
        setSelectedSectionIndex(newSections.length - 1);
    };

    const addSectionAfter = (afterIndex, type) => {
        const newSection = createDefaultSection(type);
        const insertAt = afterIndex === null || afterIndex === undefined ? sections.length : afterIndex + 1;
        const newSections = [...sections.slice(0, insertAt), newSection, ...sections.slice(insertAt)];
        setSectionsWithHistory(newSections);
        setSelectedSectionIndex(insertAt);
        setSectionPickerOpen(false);
        setInsertAfterIndex(null);
    };

    const removeSection = (index) => {
        const newSections = sections.filter((_, i) => i !== index);
        setSectionsWithHistory(newSections);
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
        setSectionsWithHistory(newSections);
        setSelectedSectionIndex(index + 1);
    };

    const moveSection = (index, direction) => {
        const target = direction === 'up' ? index - 1 : index + 1;
        if (target < 0 || target >= sections.length) return;
        const newSections = [...sections];
        [newSections[index], newSections[target]] = [newSections[target], newSections[index]];
        setSectionsWithHistory(newSections);
        setSelectedSectionIndex(target);
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
        setSectionsWithHistory(newSections);
        setDraggedIndex(null);

        if (selectedSectionIndex === draggedIndex) {
            setSelectedSectionIndex(dropIndex);
        } else if (selectedSectionIndex === dropIndex) {
            setSelectedSectionIndex(draggedIndex);
        }
    };

    const openSectionPicker = (afterIndex = null) => {
        setInsertAfterIndex(afterIndex);
        setSectionPickerOpen(true);
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
        return <SectionEditor section={section} index={index} updateSection={updateSection} uploadImage={uploadImage} uploading={uploading} />;
    };

    return (
        <div className="h-full flex overflow-hidden">
            {/* Left Sidebar - Section List */}
            <div className="w-80 bg-white border-r border-[#E1E3E5] flex flex-col overflow-hidden">
                <div ref={sectionListRef} className="flex-1 overflow-y-auto p-4 min-h-0">
                    <h2 className="text-sm font-semibold text-[#202223] mb-3">Sections</h2>
                    <div className="space-y-2">
                        {sections.length === 0 ? (
                            <p className="text-xs text-[#637381] py-4 text-center">No sections yet</p>
                        ) : (
                            sections.map((section, index) => (
                                <div
                                    key={index}
                                    ref={selectedSectionIndex === index ? selectedItemRef : null}
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
                        <button
                            type="button"
                            onClick={() => openSectionPicker(null)}
                            className="w-full bg-[#008060] text-white px-3 py-2 rounded text-sm font-medium hover:bg-[#006E52] transition"
                        >
                            + Add section
                        </button>
                    </div>
                </div>
            </div>

            {/* Center - Canvas */}
            <div className="flex-1 flex flex-col overflow-hidden bg-[#F6F6F7] min-w-0">
                {/* Toolbar */}
                <div className="bg-white border-b border-[#E1E3E5] px-4 py-2 flex items-center justify-between flex-shrink-0 gap-4">
                    <div className="flex items-center gap-2">
                        <button
                            onClick={() => setViewMode('edit')}
                            className={`px-3 py-1.5 rounded text-sm font-medium transition ${
                                viewMode === 'edit' ? 'bg-[#008060] text-white' : 'bg-transparent text-[#637381] hover:bg-[#F6F6F7]'
                            }`}
                        >
                            Edit
                        </button>
                        <button
                            onClick={() => setViewMode('preview')}
                            className={`px-3 py-1.5 rounded text-sm font-medium transition ${
                                viewMode === 'preview' ? 'bg-[#008060] text-white' : 'bg-transparent text-[#637381] hover:bg-[#F6F6F7]'
                            }`}
                        >
                            Preview
                        </button>
                        <span className="text-[#C9CCCF] mx-1">|</span>
                        <span className="text-xs text-[#637381]">Device:</span>
                        {['desktop', 'tablet', 'mobile'].map((d) => (
                            <button
                                key={d}
                                onClick={() => setDevicePreview(d)}
                                className={`px-2 py-1 rounded text-xs font-medium capitalize transition ${
                                    devicePreview === d ? 'bg-[#008060] text-white' : 'text-[#637381] hover:bg-[#F6F6F7]'
                                }`}
                            >
                                {d}
                            </button>
                        ))}
                        <span className="text-[#C9CCCF] mx-1">|</span>
                        <button
                            onClick={undo}
                            disabled={historyIndex <= 0}
                            className="p-1.5 rounded text-[#637381] hover:bg-[#F6F6F7] disabled:opacity-40 disabled:cursor-not-allowed"
                            title="Undo (Ctrl+Z)"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6" /></svg>
                        </button>
                        <button
                            onClick={redo}
                            disabled={historyIndex >= history.length - 1 || historyIndex < 0}
                            className="p-1.5 rounded text-[#637381] hover:bg-[#F6F6F7] disabled:opacity-40 disabled:cursor-not-allowed"
                            title="Redo (Ctrl+Shift+Z)"
                        >
                            <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M21 10h-10a8 8 0 00-8 8v2M21 10l-6 6m6-6l-6-6" /></svg>
                        </button>
                    </div>
                </div>

                {/* Canvas area */}
                <div className={`flex-1 overflow-y-auto min-h-0 flex justify-center ${viewMode === 'preview' ? '' : 'p-4'}`}>
                    <div
                        className={`bg-white min-h-full transition-all duration-200 ${
                            devicePreview === 'tablet' ? 'max-w-[768px] w-full shadow-lg' : devicePreview === 'mobile' ? 'max-w-[375px] w-full shadow-lg' : 'w-full max-w-full'
                        }`}
                    >
                        {viewMode === 'preview' ? (
                            sections.length === 0 ? (
                                <div className="text-center py-12 text-[#637381]">No sections to preview. Add sections to see the preview.</div>
                            ) : (
                                <ProductSections
                                    layout={sections}
                                    productId={productId}
                                    productName={productName}
                                    productImage={productImage}
                                    productPrice={productPrice}
                                    productComparePrice={productComparePrice}
                                    productInStock={productInStock}
                                    productStockQuantity={productStockQuantity}
                                    orderSettings={orderSettings}
                                />
                            )
                        ) : (
                            sections.length === 0 ? (
                                <div className="flex flex-col items-center justify-center py-16 px-6 text-center">
                                    <p className="text-[#637381] mb-4">No sections yet. Add a section to get started.</p>
                                    <button
                                        onClick={() => openSectionPicker(null)}
                                        className="bg-[#008060] text-white px-4 py-2 rounded text-sm font-medium hover:bg-[#006E52] transition"
                                    >
                                        Add section
                                    </button>
                                </div>
                            ) : (
                                <ProductSections
                                    layout={sections}
                                    productId={productId}
                                    productName={productName}
                                    productImage={productImage}
                                    productPrice={productPrice}
                                    productComparePrice={productComparePrice}
                                    productInStock={productInStock}
                                    productStockQuantity={productStockQuantity}
                                    orderSettings={orderSettings}
                                    builderMode={true}
                                    selectedSectionIndex={selectedSectionIndex}
                                    onSectionClick={setSelectedSectionIndex}
                                    renderAddBetween={(afterIndex) => (
                                        <div key={`add-${afterIndex}`} className="flex justify-center py-3">
                                            <button
                                                type="button"
                                                onClick={(e) => { e.stopPropagation(); openSectionPicker(afterIndex - 1); }}
                                                className="text-sm text-[#008060] hover:text-[#006E52] border border-dashed border-[#008060] rounded px-4 py-2 hover:bg-[#F0FDF4] transition"
                                            >
                                                + Add section
                                            </button>
                                        </div>
                                    )}
                                />
                            )
                        )}
                    </div>
                </div>
            </div>

            {/* Right Sidebar - Section Settings (edit mode only) */}
            {viewMode === 'edit' && (
                <div className="w-96 bg-white border-l border-[#E1E3E5] flex flex-col overflow-hidden flex-shrink-0">
                    {selectedSectionIndex !== null && sections[selectedSectionIndex] ? (
                        <>
                            <div className="p-4 border-b border-[#E1E3E5] flex items-center justify-between flex-shrink-0">
                                <h3 className="text-sm font-semibold text-[#202223]">Settings</h3>
                                <div className="flex items-center gap-1">
                                    <button
                                        onClick={() => moveSection(selectedSectionIndex, 'up')}
                                        disabled={selectedSectionIndex === 0}
                                        className="p-1.5 rounded text-[#637381] hover:bg-[#F6F6F7] disabled:opacity-40"
                                        title="Move up"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M5 15l7-7 7 7" /></svg>
                                    </button>
                                    <button
                                        onClick={() => moveSection(selectedSectionIndex, 'down')}
                                        disabled={selectedSectionIndex === sections.length - 1}
                                        className="p-1.5 rounded text-[#637381] hover:bg-[#F6F6F7] disabled:opacity-40"
                                        title="Move down"
                                    >
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" /></svg>
                                    </button>
                                    <button onClick={() => duplicateSection(selectedSectionIndex)} className="text-xs text-[#637381] hover:text-[#202223] px-2 py-1 rounded hover:bg-[#F6F6F7]" title="Duplicate">Duplicate</button>
                                    <button onClick={() => removeSection(selectedSectionIndex)} className="p-1.5 rounded text-red-600 hover:bg-red-50" title="Remove">
                                        <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </div>
                            </div>
                            <div className="flex-1 overflow-y-auto p-4 min-h-0">
                                <p className="text-xs text-[#637381] mb-3">{getSectionLabel(sections[selectedSectionIndex])}</p>
                                {renderSectionEditor(sections[selectedSectionIndex], selectedSectionIndex)}
                            </div>
                        </>
                    ) : (
                        <div className="flex-1 flex items-center justify-center p-6 text-center text-[#637381] text-sm">
                            <p>Click a section on the page or in the left list to edit its settings.</p>
                        </div>
                    )}
                </div>
            )}

            {/* Section Picker Modal */}
            {sectionPickerOpen && (
                <SectionPickerModal
                    onSelect={(type) => addSectionAfter(insertAfterIndex, type)}
                    onClose={() => { setSectionPickerOpen(false); setInsertAfterIndex(null); }}
                />
            )}
        </div>
    );
};

// Section categories for picker (grouped like Shopify)
const SECTION_CATEGORIES = [
    { id: 'content', label: 'Content', types: ['rich_text', 'hero', 'banner', 'tabs', 'faq', 'testimonials', 'benefits', 'steps'] },
    { id: 'media', label: 'Media', types: ['image_gallery', 'image_slider', 'video_slider', 'video'] },
    { id: 'commerce', label: 'Commerce', types: ['order_form', 'call_to_action', 'pricing', 'specifications', 'comparison'] },
    { id: 'social', label: 'Social & Contact', types: ['social_links', 'contact_info'] },
];

const SECTION_TYPE_LABELS = {
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
    hero: 'Hero',
    benefits: 'Benefits',
    pricing: 'Pricing',
    steps: 'Steps',
    social_links: 'Social Links',
    contact_info: 'Contact Info',
};

// Section Picker Modal with categories and search
const SectionPickerModal = ({ onSelect, onClose }) => {
    const [search, setSearch] = useState('');
    const searchLower = search.trim().toLowerCase();
    const filtered = SECTION_CATEGORIES.map((cat) => ({
        ...cat,
        types: cat.types.filter((t) => SECTION_TYPE_LABELS[t]?.toLowerCase().includes(searchLower)),
    })).filter((cat) => cat.types.length > 0);

    return (
        <div className="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onClick={onClose}>
            <div className="bg-white rounded-lg shadow-xl w-full max-w-lg max-h-[80vh] flex flex-col" onClick={(e) => e.stopPropagation()}>
                <div className="p-4 border-b border-[#E1E3E5] flex items-center justify-between">
                    <h2 className="text-lg font-semibold text-[#202223]">Add section</h2>
                    <button type="button" onClick={onClose} className="p-1 rounded text-[#637381] hover:bg-[#F6F6F7]">
                        <svg className="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M6 18L18 6M6 6l12 12" /></svg>
                    </button>
                </div>
                <div className="p-4 border-b border-[#E1E3E5]">
                    <input
                        type="text"
                        placeholder="Search sections..."
                        value={search}
                        onChange={(e) => setSearch(e.target.value)}
                        className="w-full px-3 py-2 border border-[#E1E3E5] rounded text-sm placeholder-[#637381] focus:ring-2 focus:ring-[#008060] focus:border-[#008060]"
                    />
                </div>
                <div className="flex-1 overflow-y-auto p-4 space-y-4">
                    {filtered.length === 0 ? (
                        <p className="text-sm text-[#637381] text-center py-6">No sections match your search.</p>
                    ) : (
                        filtered.map((cat) => (
                            <div key={cat.id}>
                                <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2">{cat.label}</h3>
                                <div className="space-y-1">
                                    {cat.types.map((type) => (
                                        <button
                                            key={type}
                                            type="button"
                                            onClick={() => onSelect(type)}
                                            className="w-full text-left px-3 py-2 text-sm text-[#202223] hover:bg-[#F0FDF4] rounded border border-transparent hover:border-[#008060] transition"
                                        >
                                            {SECTION_TYPE_LABELS[type] || type}
                                        </button>
                                    ))}
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </div>
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

    return (
        <div className="space-y-4">
            {renderEditor()}
            <SpacingEditor section={section} index={index} updateSection={updateSection} />
        </div>
    );
};

// Reusable padding & margin editor for sections (values in px)
const SpacingEditor = ({ section, index, updateSection }) => {
    const sides = [
        { key: 'top', label: 'Top' },
        { key: 'right', label: 'Right' },
        { key: 'bottom', label: 'Bottom' },
        { key: 'left', label: 'Left' },
    ];

    const updateSpacing = (kind, side, value) => {
        const field = `${kind}_${side}`;
        const v = value === '' ? '' : (parseInt(value, 10) || 0);
        updateSection(index, field, v === 0 && value !== '0' ? '' : v);
    };

    return (
        <div className="border-t border-[#E1E3E5] pt-4 mt-4 space-y-4">
            <h4 className="text-sm font-semibold text-[#202223]">Spacing (px)</h4>
            <div className="space-y-3">
                <div>
                    <p className="text-xs font-medium text-[#637381] mb-1.5">Padding</p>
                    <div className="grid grid-cols-4 gap-2">
                        {sides.map(({ key, label }) => (
                            <div key={`p-${key}`} className="min-w-0">
                                <label className="text-xs text-[#637381] block mb-0.5">{label}</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={section[`padding_${key}`] ?? ''}
                                    onChange={(e) => updateSpacing('padding', key, e.target.value)}
                                    className="w-full min-w-[3.5rem] px-2 py-1.5 border border-[#E1E3E5] rounded text-sm tabular-nums"
                                    style={{ fontSize: '14px' }}
                                    placeholder="0"
                                />
                            </div>
                        ))}
                    </div>
                </div>
                <div>
                    <p className="text-xs font-medium text-[#637381] mb-1.5">Margin</p>
                    <div className="grid grid-cols-4 gap-2">
                        {sides.map(({ key, label }) => (
                            <div key={`m-${key}`} className="min-w-0">
                                <label className="text-xs text-[#637381] block mb-0.5">{label}</label>
                                <input
                                    type="number"
                                    min="0"
                                    value={section[`margin_${key}`] ?? ''}
                                    onChange={(e) => updateSpacing('margin', key, e.target.value)}
                                    className="w-full min-w-[3.5rem] px-2 py-1.5 border border-[#E1E3E5] rounded text-sm tabular-nums"
                                    style={{ fontSize: '14px' }}
                                    placeholder="0"
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </div>
    );
};

// Shared spacing defaults for all sections (padding & margin in px, empty = not set)
const SECTION_SPACING_DEFAULTS = {
    padding_top: '',
    padding_right: '',
    padding_bottom: '',
    padding_left: '',
    margin_top: '',
    margin_right: '',
    margin_bottom: '',
    margin_left: '',
};

// Helper function to create default sections
const createDefaultSection = (type) => {
    const defaults = {
        rich_text: { type: 'rich_text', title: '', content: '', ...SECTION_SPACING_DEFAULTS },
        image_gallery: { type: 'image_gallery', title: '', images: [], ...SECTION_SPACING_DEFAULTS },
        image_slider: { type: 'image_slider', title: '', images: [], autoplay: true, autoplaySpeed: 5000, dots: true, arrows: true, ...SECTION_SPACING_DEFAULTS },
        video_slider: { type: 'video_slider', title: '', videos: [], autoplay: true, autoplaySpeed: 5000, dots: true, arrows: true, videoAutoplay: false, videoMute: false, ...SECTION_SPACING_DEFAULTS },
        banner: { 
            type: 'banner', 
            title: '', 
            content: '', 
            background_type: 'color',
            background_color: '#FFD700', 
            gradient_start: '#FFD700',
            gradient_end: '#FFA500',
            background_image: '', 
            text_color: '#000000',
            ...SECTION_SPACING_DEFAULTS 
        },
        faq: { type: 'faq', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        testimonials: { type: 'testimonials', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        video: { type: 'video', title: '', url: '', ...SECTION_SPACING_DEFAULTS },
        specifications: { type: 'specifications', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        comparison: { type: 'comparison', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        order_form: { type: 'order_form', title: '', content: '', background_color: '', ...SECTION_SPACING_DEFAULTS },
        call_to_action: { type: 'call_to_action', title: '', content: '', button_text: '', button_link: '#', background_color: '#008060', text_color: '#FFFFFF', button_color: '#FFFFFF', background_image: '', ...SECTION_SPACING_DEFAULTS },
        tabs: { type: 'tabs', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        hero: { 
            type: 'hero', 
            title: '', 
            subtitle: '', 
            discount_text: '',
            html_content: '',
            video_url: '',
            video_title: '',
            video_autoplay: false,
            button_text: '', 
            button_link: '#',
            button2_text: '',
            button2_link: '#',
            background_type: 'color',
            background_color: '#008060',
            gradient_start: '#008060',
            gradient_end: '#006E52',
            background_image: '', 
            text_color: '#FFFFFF', 
            button_bg_color: '#FFFFFF', 
            button_text_color: '#008060',
            button2_bg_color: '#008060',
            button2_text_color: '#FFFFFF',
            images: [],
            ...SECTION_SPACING_DEFAULTS 
        },
        benefits: { type: 'benefits', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        pricing: { type: 'pricing', title: '', original_price: '', offer_price: '', discount_text: '', countdown_date: '', ...SECTION_SPACING_DEFAULTS },
        steps: { type: 'steps', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        social_links: { type: 'social_links', title: '', items: [], ...SECTION_SPACING_DEFAULTS },
        contact_info: { type: 'contact_info', title: '', phone: '', email: '', address: '', content: '', ...SECTION_SPACING_DEFAULTS },
    };
    return defaults[type] || { type, ...SECTION_SPACING_DEFAULTS };
};

// Rich text toolbar button helper
const RichTextToolbar = ({ editorRef }) => {
    const exec = (cmd, value = null) => {
        if (editorRef.current) {
            editorRef.current.focus();
            document.execCommand(cmd, false, value);
        }
    };

    const addLink = () => {
        const url = window.prompt('Enter URL:', 'https://');
        if (url) exec('createLink', url);
    };

    return (
        <div className="flex flex-wrap items-center gap-0.5 p-1.5 border border-[#E1E3E5] border-b-0 rounded-t bg-[#F6F6F7]">
            <button type="button" onClick={() => exec('bold')} className="p-2 rounded hover:bg-[#E1E3E5] font-bold text-sm" title="Bold">B</button>
            <button type="button" onClick={() => exec('italic')} className="p-2 rounded hover:bg-[#E1E3E5] italic text-sm" title="Italic">I</button>
            <button type="button" onClick={() => exec('underline')} className="p-2 rounded hover:bg-[#E1E3E5] underline text-sm" title="Underline">U</button>
            <span className="w-px h-5 bg-[#E1E3E5] mx-0.5" />
            <button type="button" onClick={addLink} className="p-2 rounded hover:bg-[#E1E3E5] text-sm" title="Insert link">
                <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
            </button>
            <span className="w-px h-5 bg-[#E1E3E5] mx-0.5" />
            <button type="button" onClick={() => exec('insertUnorderedList')} className="p-2 rounded hover:bg-[#E1E3E5] text-sm" title="Bullet list">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M4 6h2v2H4V6zm0 5h2v2H4v-2zm0 5h2v2H4v-2zm4-10h12v2H8V6zm0 5h12v2H8v-2zm0 5h12v2H8v-2z" /></svg>
            </button>
            <button type="button" onClick={() => exec('insertOrderedList')} className="p-2 rounded hover:bg-[#E1E3E5] text-sm" title="Numbered list">
                <svg className="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M2 5v2h2V5H2zm0 6v2h2v-2H2zm0 6v2h2v-2H2zm4-12h14v2H6V5zm0 5h14v2H6v-2zm0 5h14v2H6v-2z" /></svg>
            </button>
            <span className="w-px h-5 bg-[#E1E3E5] mx-0.5" />
            <select
                className="text-xs border border-[#E1E3E5] rounded px-2 py-1.5 bg-white"
                defaultValue="p"
                onChange={(e) => { const v = e.target.value; exec('formatBlock', v); }}
                title="Block format"
            >
                <option value="p">Paragraph</option>
                <option value="h2">Heading 2</option>
                <option value="h3">Heading 3</option>
            </select>
        </div>
    );
};

// Individual Section Editors
const RichTextEditor = ({ section, index, updateSection }) => {
    const editorRef = React.useRef(null);
    const isInternalChange = React.useRef(false);

    React.useEffect(() => {
        if (!editorRef.current) return;
        const current = (editorRef.current.innerHTML || '').trim();
        const fromSection = (section.content || '').trim();
        if (isInternalChange.current) {
            isInternalChange.current = false;
            return;
        }
        if (current !== fromSection) {
            editorRef.current.innerHTML = fromSection || '';
        }
    }, [section.content, index]);

    const handleInput = () => {
        if (!editorRef.current) return;
        isInternalChange.current = true;
        const html = editorRef.current.innerHTML;
        updateSection(index, 'content', html);
    };

    const handlePaste = (e) => {
        e.preventDefault();
        const text = e.clipboardData.getData('text/plain') || '';
        const safe = text.replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;');
        document.execCommand('insertHTML', false, safe);
    };

    return (
        <div className="space-y-3">
            <div>
                <label className="text-sm font-medium block mb-1">Title (optional)</label>
                <input
                    type="text"
                    value={section.title || ''}
                    onChange={(e) => updateSection(index, 'title', e.target.value)}
                    className="w-full px-3 py-2 border border-[#E1E3E5] rounded text-sm"
                    placeholder="Section Title"
                />
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Content</label>
                <RichTextToolbar editorRef={editorRef} />
                <div
                    ref={editorRef}
                    contentEditable
                    suppressContentEditableWarning
                    onInput={handleInput}
                    onBlur={handleInput}
                    onPaste={handlePaste}
                    className="min-h-[200px] w-full px-3 py-2 border border-[#E1E3E5] rounded-b text-sm text-[#202223] focus:outline-none focus:ring-2 focus:ring-[#008060] focus:border-[#008060] prose prose-sm max-w-none"
                    style={{ outline: 'none' }}
                    data-placeholder="Start typing..."
                />
                <style>{`
                    [data-placeholder]:empty:before { content: attr(data-placeholder); color: #637381; }
                    [contenteditable] h2 { font-size: 1.25rem; font-weight: 700; margin: 0.75em 0 0.25em; }
                    [contenteditable] h3 { font-size: 1.125rem; font-weight: 600; margin: 0.5em 0 0.25em; }
                    [contenteditable] ul, [contenteditable] ol { margin: 0.5em 0; padding-left: 1.5rem; }
                `}</style>
            </div>
        </div>
    );
};

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
            <div>
                <label className="text-sm font-medium block mb-1">Video Autoplay</label>
                <select
                    value={section.video_autoplay ? 'true' : 'false'}
                    onChange={(e) => updateSection(index, 'video_autoplay', e.target.value === 'true')}
                    className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                >
                    <option value="false">No</option>
                    <option value="true">Yes</option>
                </select>
                <p className="text-xs text-neutral-500 mt-1">Auto-play the video when the page loads (sound will be enabled)</p>
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
                    <label className="text-sm font-medium block mb-1">Slider Autoplay</label>
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
                    <label className="text-sm font-medium block mb-1">Video Autoplay</label>
                    <select
                        value={section.videoAutoplay ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'videoAutoplay', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="true">Yes</option>
                        <option value="false">No</option>
                    </select>
                    <p className="text-xs text-neutral-500 mt-1">Auto-play videos when slider autoplay is active</p>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Video Mute</label>
                    <select
                        value={section.videoMute ? 'true' : 'false'}
                        onChange={(e) => updateSection(index, 'videoMute', e.target.value === 'true')}
                        className="w-full px-3 py-2 border border-neutral-300 rounded text-sm"
                    >
                        <option value="false">No (Sound On)</option>
                        <option value="true">Yes (Sound Off)</option>
                    </select>
                    <p className="text-xs text-neutral-500 mt-1">Mute videos (default: unmuted)</p>
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

