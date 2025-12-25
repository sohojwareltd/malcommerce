import React, { useState } from 'react';
import { DragDropContext, Droppable, Draggable } from 'react-beautiful-dnd';

// Import all prefab components
import HeroPromoVideoSplit from './HeroPromoVideoSplit.jsx';
import BannerTwoLineText from './BannerTwoLineText.jsx';
import LogoCarousel from './LogoCarousel.jsx';
import TestimonialsThreeColumn from './TestimonialsThreeColumn.jsx';
import YouTubeVideosThreeColumn from './YouTubeVideosThreeColumn.jsx';
import PricingValueBreakdown from './PricingValueBreakdown.jsx';
import ProblemsSolutionBenefitsThreeColumn from './ProblemsSolutionBenefitsThreeColumn.jsx';
import CourseModulesSection from './CourseModulesSection.jsx';
import SectionEditor from './SectionEditor.jsx';

const ThemeBuilder = ({ initialSections = [], onSave, productSlug, productData = {} }) => {
    const [sections, setSections] = useState(initialSections);
    const [showPreview, setShowPreview] = useState(true);
    const [previewDevice, setPreviewDevice] = useState('desktop');
    const [editingSection, setEditingSection] = useState(null);

    // Available section types
    const sectionTypes = [
        { value: 'HeroPromoVideoSplit', label: 'Hero with Video', icon: '🎥', description: 'Promotional hero with video embed' },
        { value: 'BannerTwoLineText', label: 'Banner Text', icon: '📢', description: 'Two-line promotional banner' },
        { value: 'ProblemsSolutionBenefitsThreeColumn', label: 'Problems/Solution/Benefits', icon: '⚖️', description: 'Three-column comparison section' },
        { value: 'LogoCarousel', label: 'Logo Carousel', icon: '🏢', description: 'Auto-scrolling company logos' },
        { value: 'TestimonialsThreeColumn', label: 'Testimonials', icon: '💬', description: 'Client reviews grid' },
        { value: 'YouTubeVideosThreeColumn', label: 'Video Grid', icon: '📹', description: '3 YouTube videos grid' },
        { value: 'PricingValueBreakdown', label: 'Pricing Breakdown', icon: '💰', description: 'Value breakdown with pricing' },
        { value: 'CourseModulesSection', label: 'Course Modules', icon: '📚', description: 'Expandable course modules' },
    ];

    const addSection = (type) => {
        const newSection = {
            id: `section-${Date.now()}`,
            type,
            content: getDefaultContent(type),
            style: getDefaultStyle(type)
        };
        setSections([...sections, newSection]);
        setEditingSection(newSection.id);
    };

    const getDefaultContent = (type) => {
        const defaults = {
            HeroPromoVideoSplit: {
                headline: '',
                subtext: '',
                discountText: '',
                primaryButtonText: '',
                primaryButtonLink: '#',
                secondaryButtonText: '',
                secondaryButtonLink: '#',
                videoUrl: ''
            },
            BannerTwoLineText: {
                topLine: '',
                bottomLine: ''
            },
            ProblemsSolutionBenefitsThreeColumn: {
                leftTitle: '',
                leftList: [],
                centerImage: '',
                centerTitle: '',
                centerButtonText: '',
                centerButtonLink: '#',
                rightTitle: '',
                rightList: []
            },
            LogoCarousel: {
                logos: []
            },
            TestimonialsThreeColumn: {
                testimonials: []
            },
            YouTubeVideosThreeColumn: {
                videos: []
            },
            PricingValueBreakdown: {
                headerTopLine: '',
                headerBottomLine: '',
                items: [],
                totalValue: '',
                discountText: '',
                ctaButtonText: '',
                ctaButtonLink: '#'
            },
            CourseModulesSection: {
                title: '',
                subtitle: '',
                modules: []
            }
        };
        return defaults[type] || {};
    };

    const getDefaultStyle = (type) => {
        const defaults = {
            HeroPromoVideoSplit: {
                backgroundType: 'gradient',
                gradientStart: '#7c3aed',
                gradientEnd: '#000000',
                textColor: '#ffffff',
                headingTag: 'h1',
                headingSize: 'xl',
                alignment: 'left'
            },
            BannerTwoLineText: {
                backgroundType: 'gradient',
                gradientDirection: 'to right',
                gradientStart: '#7c3aed',
                gradientEnd: '#000000',
                topLineColor: '#ffffff',
                bottomLineColor: '#a78bfa'
            },
            ProblemsSolutionBenefitsThreeColumn: {
                leftPanelBgColor: '#f3e8ff',
                middlePanelBgColor: '#7c3aed',
                rightPanelBgColor: '#f3e8ff',
                titleColor: '#ffffff',
                listIconColor: '#7c3aed',
                buttonBgColor: '#7c3aed',
                buttonTextColor: '#ffffff'
            },
            LogoCarousel: {
                backgroundColor: '#ffffff',
                columns: '6',
                spacing: 'normal',
                autoScroll: true
            },
            TestimonialsThreeColumn: {
                backgroundColor: '#ffffff',
                cardBackgroundColor: '#ffffff',
                cardBorderColor: '#fbbf24'
            },
            YouTubeVideosThreeColumn: {
                backgroundColor: '#ffffff',
                spacing: 'normal',
                videoAspectRatio: '16:9'
            },
            PricingValueBreakdown: {
                backgroundColor: '#ffffff',
                headerBgType: 'gradient',
                buttonSize: 'large',
                buttonFullWidth: true
            },
            CourseModulesSection: {
                backgroundColor: '#ffffff',
                titleColor: '#1f2937',
                moduleBgColor: '#f9fafb'
            }
        };
        return defaults[type] || {};
    };

    const updateSection = (sectionId, updates) => {
        setSections(sections.map(section => 
            section.id === sectionId 
                ? { ...section, ...updates }
                : section
        ));
    };

    const removeSection = (sectionId) => {
        setSections(sections.filter(section => section.id !== sectionId));
        if (editingSection === sectionId) {
            setEditingSection(null);
        }
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

    const renderSection = (section) => {
        const { type, content, style } = section;
        
        switch (type) {
            case 'HeroPromoVideoSplit':
                return <HeroPromoVideoSplit content={content} style={style} />;
            case 'BannerTwoLineText':
                return <BannerTwoLineText content={content} style={style} />;
            case 'ProblemsSolutionBenefitsThreeColumn':
                return <ProblemsSolutionBenefitsThreeColumn content={content} style={style} />;
            case 'LogoCarousel':
                return <LogoCarousel content={content} style={style} />;
            case 'TestimonialsThreeColumn':
                return <TestimonialsThreeColumn content={content} style={style} />;
            case 'YouTubeVideosThreeColumn':
                return <YouTubeVideosThreeColumn content={content} style={style} />;
            case 'PricingValueBreakdown':
                return <PricingValueBreakdown content={content} style={style} />;
            case 'CourseModulesSection':
                return <CourseModulesSection content={content} style={style} />;
            default:
                return null;
        }
    };

    const renderPreview = () => {
        const deviceStyles = {
            mobile: { width: '375px', maxWidth: '100%' },
            tablet: { width: '768px', maxWidth: '100%' },
            desktop: { width: '100%' }
        };

        const previewStyle = deviceStyles[previewDevice] || deviceStyles.desktop;

        return (
            <div className="w-full">
                {/* Device Selector */}
                <div className="mb-4 flex items-center justify-center gap-1 bg-white border border-[#E1E3E5] rounded-lg p-1 inline-flex shadow-sm">
                    {['mobile', 'tablet', 'desktop'].map((device) => (
                        <button
                            key={device}
                            onClick={() => setPreviewDevice(device)}
                            className={`px-3 py-1.5 rounded text-xs font-medium transition capitalize ${
                                previewDevice === device
                                    ? 'bg-[#008060] text-white'
                                    : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                            }`}
                        >
                            {device}
                        </button>
                    ))}
                </div>

                {/* Preview Container */}
                <div className="flex justify-center" style={{
                    backgroundColor: previewDevice !== 'desktop' ? '#E1E3E5' : 'transparent',
                    padding: previewDevice !== 'desktop' ? '2rem' : '0',
                    minHeight: previewDevice === 'mobile' ? '667px' : previewDevice === 'tablet' ? '1024px' : 'auto'
                }}>
                    <div
                        className="bg-white shadow-lg rounded overflow-hidden transition-all duration-200 border border-[#E1E3E5]"
                        style={previewStyle}
                    >
                        <div style={{ minHeight: previewDevice === 'mobile' ? '600px' : 'auto' }}>
                            {sections.map((section) => (
                                <div key={section.id}>
                                    {renderSection(section)}
                                </div>
                            ))}
                            {sections.length === 0 && (
                                <div className="text-center py-12 text-[#8C9196]">
                                    <p className="text-sm">No sections yet. Add a section to get started.</p>
                                </div>
                            )}
                        </div>
                    </div>
                </div>
            </div>
        );
    };

    const renderSectionEditor = (section) => {
        if (editingSection !== section.id) return null;

        return (
            <SectionEditor
                section={section}
                onUpdate={(updatedSection) => updateSection(section.id, updatedSection)}
            />
        );
    };

    return (
        <div className="h-full flex relative bg-[#F6F6F7]">
            {/* Sidebar */}
            <div className={`fixed left-0 top-0 h-full bg-white border-r border-[#E1E3E5] z-50 transform transition-transform duration-200 ease-in-out ${
                showPreview ? 'translate-x-0' : '-translate-x-full'
            }`} style={{ width: '360px', maxWidth: '90vw', boxShadow: showPreview ? '2px 0 8px rgba(0,0,0,0.08)' : 'none' }}>
                <div className="h-full flex flex-col">
                    {/* Sidebar Header */}
                    <div className="bg-white border-b border-[#E1E3E5] px-4 py-3">
                        <div className="flex items-center justify-between mb-2">
                            <h2 className="text-sm font-semibold text-[#202223]">Theme Builder</h2>
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
                    </div>

                    {/* Add Section Buttons */}
                    <div className="p-3 border-b border-[#E1E3E5] bg-[#FAFAFA] overflow-y-auto">
                        <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2 px-1">Add Section</h3>
                        <div className="grid grid-cols-1 gap-2">
                            {sectionTypes.map((type) => (
                                <button
                                    key={type.value}
                                    onClick={() => addSection(type.value)}
                                    className="bg-white border border-[#E1E3E5] px-3 py-3 rounded text-left hover:border-[#008060] hover:bg-[#F0FDF4] transition group"
                                >
                                    <div className="flex items-center gap-2 mb-1">
                                        <span className="text-xl">{type.icon}</span>
                                        <span className="text-xs font-medium text-[#202223]">{type.label}</span>
                                    </div>
                                    <p className="text-xs text-[#637381]">{type.description}</p>
                                </button>
                            ))}
                        </div>
                    </div>

                    {/* Sections List */}
                    <div className="flex-1 overflow-y-auto p-3">
                        <h3 className="text-xs font-semibold text-[#637381] uppercase mb-2 px-1">Page Sections</h3>
                        <DragDropContext onDragEnd={handleDragEnd}>
                            <Droppable droppableId="sections">
                                {(provided) => (
                                    <div {...provided.droppableProps} ref={provided.innerRef} className="space-y-2">
                                        {sections.map((section, index) => (
                                            <Draggable key={section.id} draggableId={section.id} index={index}>
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
                                                        <div className="px-3 py-2.5 border-b border-[#E1E3E5] flex items-center justify-between bg-[#FAFAFA]">
                                                            <div {...provided.dragHandleProps} className="flex items-center gap-2 cursor-move flex-1">
                                                                <svg className="w-4 h-4 text-[#637381]" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M4 8h16M4 16h16"></path>
                                                                </svg>
                                                                <span className="text-sm font-medium text-[#202223]">
                                                                    {sectionTypes.find(t => t.value === section.type)?.label || section.type}
                                                                </span>
                                                            </div>
                                                            <div className="flex items-center gap-1">
                                                                <button
                                                                    onClick={() => setEditingSection(editingSection === section.id ? null : section.id)}
                                                                    className="text-[#637381] hover:text-[#008060] p-1 rounded hover:bg-[#F6F6F7] transition"
                                                                    title="Edit section"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                    </svg>
                                                                </button>
                                                                <button
                                                                    onClick={() => removeSection(section.id)}
                                                                    className="text-[#637381] hover:text-[#BF0711] p-1 rounded hover:bg-[#F6F6F7] transition"
                                                                    title="Remove section"
                                                                >
                                                                    <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                        <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                    </svg>
                                                                </button>
                                                            </div>
                                                        </div>
                                                        {renderSectionEditor(section)}
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

                    {/* Save Button */}
                    <div className="sticky bottom-0 bg-white border-t border-[#E1E3E5] p-3 mt-auto">
                        <button
                            onClick={handleSave}
                            className="w-full bg-[#008060] text-white px-4 py-2 rounded text-sm font-medium hover:bg-[#006E52] transition shadow-sm"
                        >
                            Save Layout
                        </button>
                    </div>
                </div>
            </div>

            {/* Main Preview Area */}
            <div className="flex-1 h-full overflow-y-auto transition-all duration-200 bg-white" style={{ marginLeft: showPreview ? '360px' : '0' }}>
                <div className="h-full">
                    {/* Preview Header */}
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

