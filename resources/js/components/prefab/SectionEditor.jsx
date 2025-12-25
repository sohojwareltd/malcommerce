import React from 'react';

const SectionEditor = ({ section, onUpdate }) => {
    const { type, content, style } = section;

    const updateContent = (field, value) => {
        onUpdate({
            ...section,
            content: { ...content, [field]: value }
        });
    };

    const updateStyle = (field, value) => {
        onUpdate({
            ...section,
            style: { ...style, [field]: value }
        });
    };

    const updateStyleObject = (objectName, field, value) => {
        onUpdate({
            ...section,
            style: {
                ...style,
                [objectName]: {
                    ...(style[objectName] || {}),
                    [field]: value
                }
            }
        });
    };

    // Array management helpers
    const updateArrayItem = (field, index, item) => {
        const array = content[field] || [];
        const newArray = [...array];
        newArray[index] = item;
        updateContent(field, newArray);
    };

    const addArrayItem = (field, defaultItem = {}) => {
        const array = content[field] || [];
        updateContent(field, [...array, defaultItem]);
    };

    const removeArrayItem = (field, index) => {
        const array = content[field] || [];
        const newArray = array.filter((_, i) => i !== index);
        updateContent(field, newArray);
    };

    const updateStringArrayItem = (field, index, value) => {
        const array = content[field] || [];
        const newArray = [...array];
        newArray[index] = value;
        updateContent(field, newArray);
    };

    const addStringArrayItem = (field) => {
        const array = content[field] || [];
        updateContent(field, [...array, '']);
    };

    const removeStringArrayItem = (field, index) => {
        const array = content[field] || [];
        const newArray = array.filter((_, i) => i !== index);
        updateContent(field, newArray);
    };

    // Render editor based on section type
    const renderEditor = () => {
        switch (type) {
            case 'HeroPromoVideoSplit':
                return (
                    <div className="space-y-3">
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Headline</label>
                            <input
                                type="text"
                                value={content.headline || ''}
                                onChange={(e) => updateContent('headline', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Subtext</label>
                            <textarea
                                value={content.subtext || ''}
                                onChange={(e) => updateContent('subtext', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                rows={2}
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Video URL</label>
                            <input
                                type="url"
                                value={content.videoUrl || ''}
                                onChange={(e) => updateContent('videoUrl', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                placeholder="https://www.youtube.com/watch?v=..."
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Primary Button Text</label>
                            <input
                                type="text"
                                value={content.primaryButtonText || ''}
                                onChange={(e) => updateContent('primaryButtonText', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Background Type</label>
                            <select
                                value={style.backgroundType || 'gradient'}
                                onChange={(e) => updateStyle('backgroundType', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm mb-2"
                            >
                                <option value="color">Color</option>
                                <option value="gradient">Gradient</option>
                                <option value="image">Image</option>
                            </select>
                            {style.backgroundType === 'gradient' && (
                                <>
                                    <div className="mb-2">
                                        <label className="block text-xs text-gray-600 mb-1">Gradient Start</label>
                                        <input
                                            type="color"
                                            value={style.gradientStart || '#7c3aed'}
                                            onChange={(e) => updateStyle('gradientStart', e.target.value)}
                                            className="w-full h-10"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs text-gray-600 mb-1">Gradient End</label>
                                        <input
                                            type="color"
                                            value={style.gradientEnd || '#000000'}
                                            onChange={(e) => updateStyle('gradientEnd', e.target.value)}
                                            className="w-full h-10"
                                        />
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                );

            case 'BannerTwoLineText':
                return (
                    <div className="space-y-3">
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Top Line</label>
                            <input
                                type="text"
                                value={content.topLine || ''}
                                onChange={(e) => updateContent('topLine', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Bottom Line</label>
                            <input
                                type="text"
                                value={content.bottomLine || ''}
                                onChange={(e) => updateContent('bottomLine', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Background Type</label>
                            <select
                                value={style.backgroundType || 'gradient'}
                                onChange={(e) => updateStyle('backgroundType', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm mb-2"
                            >
                                <option value="color">Color</option>
                                <option value="gradient">Gradient</option>
                                <option value="image">Image</option>
                            </select>
                            {style.backgroundType === 'gradient' && (
                                <>
                                    <div className="mb-2">
                                        <label className="block text-xs text-gray-600 mb-1">Gradient Start</label>
                                        <input
                                            type="color"
                                            value={style.gradientStart || '#7c3aed'}
                                            onChange={(e) => updateStyle('gradientStart', e.target.value)}
                                            className="w-full h-10"
                                        />
                                    </div>
                                    <div>
                                        <label className="block text-xs text-gray-600 mb-1">Gradient End</label>
                                        <input
                                            type="color"
                                            value={style.gradientEnd || '#000000'}
                                            onChange={(e) => updateStyle('gradientEnd', e.target.value)}
                                            className="w-full h-10"
                                        />
                                    </div>
                                </>
                            )}
                        </div>
                    </div>
                );

            case 'PricingValueBreakdown':
                return (
                    <div className="space-y-3">
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Header Top Line</label>
                            <input
                                type="text"
                                value={content.headerTopLine || ''}
                                onChange={(e) => updateContent('headerTopLine', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Header Bottom Line</label>
                            <input
                                type="text"
                                value={content.headerBottomLine || ''}
                                onChange={(e) => updateContent('headerBottomLine', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Items</label>
                                <button
                                    type="button"
                                    onClick={() => addArrayItem('items', { title: '', value: '' })}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Item
                                </button>
                            </div>
                            <div className="space-y-2">
                                {(content.items || []).map((item, index) => (
                                    <div key={index} className="border border-gray-200 rounded p-2 bg-white">
                                        <div className="flex items-start justify-between mb-2">
                                            <span className="text-xs font-medium text-gray-600">Item {index + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('items', index)}
                                                className="text-xs text-red-500 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            <input
                                                type="text"
                                                value={item.title || ''}
                                                onChange={(e) => updateArrayItem('items', index, { ...item, title: e.target.value })}
                                                placeholder="Item Title"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={item.value || ''}
                                                onChange={(e) => updateArrayItem('items', index, { ...item, value: e.target.value })}
                                                placeholder="Item Value/Price"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                        </div>
                                    </div>
                                ))}
                                {(content.items || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No items added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Total Value</label>
                            <input
                                type="text"
                                value={content.totalValue || ''}
                                onChange={(e) => updateContent('totalValue', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Discount Text</label>
                            <input
                                type="text"
                                value={content.discountText || ''}
                                onChange={(e) => updateContent('discountText', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">CTA Button Text</label>
                            <input
                                type="text"
                                value={content.ctaButtonText || ''}
                                onChange={(e) => updateContent('ctaButtonText', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">CTA Button Link</label>
                            <input
                                type="text"
                                value={content.ctaButtonLink || '#'}
                                onChange={(e) => updateContent('ctaButtonLink', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Header Background Type</label>
                            <select
                                value={style.headerBgType || 'gradient'}
                                onChange={(e) => updateStyle('headerBgType', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="gradient">Gradient</option>
                                <option value="solid">Solid Color</option>
                            </select>
                        </div>
                        {style.headerBgType === 'gradient' && (
                            <>
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-1">Gradient Start</label>
                                    <input
                                        type="color"
                                        value={style.headerGradientStart || '#7c3aed'}
                                        onChange={(e) => updateStyle('headerGradientStart', e.target.value)}
                                        className="w-full h-10"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-1">Gradient End</label>
                                    <input
                                        type="color"
                                        value={style.headerGradientEnd || '#4c1d95'}
                                        onChange={(e) => updateStyle('headerGradientEnd', e.target.value)}
                                        className="w-full h-10"
                                    />
                                </div>
                            </>
                        )}
                        {style.headerBgType === 'solid' && (
                            <div>
                                <label className="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                                <input
                                    type="color"
                                    value={style.headerBgColor || '#7c3aed'}
                                    onChange={(e) => updateStyle('headerBgColor', e.target.value)}
                                    className="w-full h-10"
                                />
                            </div>
                        )}
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Button Background Color</label>
                            <input
                                type="color"
                                value={style.buttonBgColor || '#7c3aed'}
                                onChange={(e) => updateStyle('buttonBgColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Button Text Color</label>
                            <input
                                type="color"
                                value={style.buttonTextColor || '#ffffff'}
                                onChange={(e) => updateStyle('buttonTextColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Button Size</label>
                            <select
                                value={style.buttonSize || 'large'}
                                onChange={(e) => updateStyle('buttonSize', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="medium">Medium</option>
                                <option value="large">Large</option>
                                <option value="xl">Extra Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Background</label>
                            <input
                                type="color"
                                value={style.backgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Padding</label>
                            <select
                                value={style.sectionPadding || 'normal'}
                                onChange={(e) => updateStyle('sectionPadding', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                );

            case 'ProblemsSolutionBenefitsThreeColumn':
                return (
                    <div className="space-y-3">
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Left Panel Title</label>
                            <input
                                type="text"
                                value={content.leftTitle || ''}
                                onChange={(e) => updateContent('leftTitle', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Left Panel Items</label>
                                <button
                                    type="button"
                                    onClick={() => addStringArrayItem('leftList')}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Item
                                </button>
                            </div>
                            <div className="space-y-2">
                                {(content.leftList || []).map((item, index) => (
                                    <div key={index} className="flex gap-2">
                                        <input
                                            type="text"
                                            value={item}
                                            onChange={(e) => updateStringArrayItem('leftList', index, e.target.value)}
                                            placeholder={`Item ${index + 1}`}
                                            className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeStringArrayItem('leftList', index)}
                                            className="text-xs text-red-500 hover:text-red-700 px-2"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                ))}
                                {(content.leftList || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No items added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Center Image URL</label>
                            <input
                                type="url"
                                value={content.centerImage || ''}
                                onChange={(e) => updateContent('centerImage', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Center Title</label>
                            <input
                                type="text"
                                value={content.centerTitle || ''}
                                onChange={(e) => updateContent('centerTitle', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Center Button Text</label>
                            <input
                                type="text"
                                value={content.centerButtonText || ''}
                                onChange={(e) => updateContent('centerButtonText', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Right Panel Title</label>
                            <input
                                type="text"
                                value={content.rightTitle || ''}
                                onChange={(e) => updateContent('rightTitle', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Right Panel Items</label>
                                <button
                                    type="button"
                                    onClick={() => addStringArrayItem('rightList')}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Item
                                </button>
                            </div>
                            <div className="space-y-2">
                                {(content.rightList || []).map((item, index) => (
                                    <div key={index} className="flex gap-2">
                                        <input
                                            type="text"
                                            value={item}
                                            onChange={(e) => updateStringArrayItem('rightList', index, e.target.value)}
                                            placeholder={`Item ${index + 1}`}
                                            className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                                        />
                                        <button
                                            type="button"
                                            onClick={() => removeStringArrayItem('rightList', index)}
                                            className="text-xs text-red-500 hover:text-red-700 px-2"
                                        >
                                            Remove
                                        </button>
                                    </div>
                                ))}
                                {(content.rightList || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No items added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Left Panel Background</label>
                            <input
                                type="color"
                                value={style.leftPanelBgColor || '#f3e8ff'}
                                onChange={(e) => updateStyle('leftPanelBgColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Middle Panel Background</label>
                            <input
                                type="color"
                                value={style.middlePanelBgColor || '#7c3aed'}
                                onChange={(e) => updateStyle('middlePanelBgColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Right Panel Background</label>
                            <input
                                type="color"
                                value={style.rightPanelBgColor || '#f3e8ff'}
                                onChange={(e) => updateStyle('rightPanelBgColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                    </div>
                );

            case 'CourseModulesSection':
                return (
                    <div className="space-y-3">
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Title</label>
                            <input
                                type="text"
                                value={content.title || ''}
                                onChange={(e) => updateContent('title', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Subtitle</label>
                            <input
                                type="text"
                                value={content.subtitle || ''}
                                onChange={(e) => updateContent('subtitle', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            />
                        </div>
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Modules</label>
                                <button
                                    type="button"
                                    onClick={() => addArrayItem('modules', { title: '', content: [] })}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Module
                                </button>
                            </div>
                            <div className="space-y-3">
                                {(content.modules || []).map((module, moduleIndex) => (
                                    <div key={moduleIndex} className="border border-gray-200 rounded p-3 bg-white">
                                        <div className="flex items-start justify-between mb-2">
                                            <span className="text-xs font-medium text-gray-600">Module {moduleIndex + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('modules', moduleIndex)}
                                                className="text-xs text-red-500 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            <input
                                                type="text"
                                                value={module.title || ''}
                                                onChange={(e) => updateArrayItem('modules', moduleIndex, { ...module, title: e.target.value })}
                                                placeholder="Module Title"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <div>
                                                <div className="flex items-center justify-between mb-1">
                                                    <label className="text-xs text-gray-600">Content Items</label>
                                                    <button
                                                        type="button"
                                                        onClick={() => {
                                                            const moduleContent = module.content || [];
                                                            updateArrayItem('modules', moduleIndex, {
                                                                ...module,
                                                                content: [...moduleContent, '']
                                                            });
                                                        }}
                                                        className="text-xs bg-gray-500 text-white px-2 py-0.5 rounded hover:bg-gray-600"
                                                    >
                                                        + Add
                                                    </button>
                                                </div>
                                                <div className="space-y-1">
                                                    {(module.content || []).map((contentItem, contentIndex) => (
                                                        <div key={contentIndex} className="flex gap-2">
                                                            <input
                                                                type="text"
                                                                value={contentItem}
                                                                onChange={(e) => {
                                                                    const moduleContent = module.content || [];
                                                                    const newContent = [...moduleContent];
                                                                    newContent[contentIndex] = e.target.value;
                                                                    updateArrayItem('modules', moduleIndex, {
                                                                        ...module,
                                                                        content: newContent
                                                                    });
                                                                }}
                                                                placeholder={`Content item ${contentIndex + 1}`}
                                                                className="flex-1 px-2 py-1 border border-gray-300 rounded text-sm"
                                                            />
                                                            <button
                                                                type="button"
                                                                onClick={() => {
                                                                    const moduleContent = module.content || [];
                                                                    const newContent = moduleContent.filter((_, i) => i !== contentIndex);
                                                                    updateArrayItem('modules', moduleIndex, {
                                                                        ...module,
                                                                        content: newContent
                                                                    });
                                                                }}
                                                                className="text-xs text-red-500 hover:text-red-700 px-2"
                                                            >
                                                                ×
                                                            </button>
                                                        </div>
                                                    ))}
                                                    {(module.content || []).length === 0 && (
                                                        <p className="text-xs text-gray-400 text-center py-1">No content items</p>
                                                    )}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                {(content.modules || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No modules added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                            <input
                                type="color"
                                value={style.backgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                    </div>
                );

            case 'LogoCarousel':
                return (
                    <div className="space-y-3">
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Logos</label>
                                <button
                                    type="button"
                                    onClick={() => addArrayItem('logos', { imageUrl: '', altText: '' })}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Logo
                                </button>
                            </div>
                            <div className="space-y-2">
                                {(content.logos || []).map((logo, index) => (
                                    <div key={index} className="border border-gray-200 rounded p-2 bg-white">
                                        <div className="flex items-start justify-between mb-2">
                                            <span className="text-xs font-medium text-gray-600">Logo {index + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('logos', index)}
                                                className="text-xs text-red-500 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            <input
                                                type="url"
                                                value={logo.imageUrl || ''}
                                                onChange={(e) => updateArrayItem('logos', index, { ...logo, imageUrl: e.target.value })}
                                                placeholder="Logo Image URL"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={logo.altText || ''}
                                                onChange={(e) => updateArrayItem('logos', index, { ...logo, altText: e.target.value })}
                                                placeholder="Alt Text"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                        </div>
                                    </div>
                                ))}
                                {(content.logos || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No logos added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                            <input
                                type="color"
                                value={style.backgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Desktop Columns</label>
                            <select
                                value={style.columns || '6'}
                                onChange={(e) => updateStyle('columns', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="3">3</option>
                                <option value="4">4</option>
                                <option value="5">5</option>
                                <option value="6">6</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Logo Spacing</label>
                            <select
                                value={style.spacing || 'normal'}
                                onChange={(e) => updateStyle('spacing', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Logo Max Width</label>
                            <select
                                value={style.logoMaxWidth || 'medium'}
                                onChange={(e) => updateStyle('logoMaxWidth', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="small">Small (120px)</option>
                                <option value="medium">Medium (150px)</option>
                                <option value="large">Large (180px)</option>
                            </select>
                        </div>
                        <div className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={style.autoScroll !== false}
                                onChange={(e) => updateStyle('autoScroll', e.target.checked)}
                                className="w-4 h-4"
                            />
                            <label className="text-xs font-medium text-gray-700">Auto Scroll</label>
                        </div>
                        {style.autoScroll !== false && (
                            <div>
                                <label className="block text-xs font-medium text-gray-700 mb-1">Scroll Speed</label>
                                <select
                                    value={style.scrollSpeed || 'normal'}
                                    onChange={(e) => updateStyle('scrollSpeed', e.target.value)}
                                    className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                >
                                    <option value="slow">Slow</option>
                                    <option value="normal">Normal</option>
                                    <option value="fast">Fast</option>
                                </select>
                            </div>
                        )}
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Padding</label>
                            <select
                                value={style.sectionPadding || 'normal'}
                                onChange={(e) => updateStyle('sectionPadding', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                );

            case 'TestimonialsThreeColumn':
                return (
                    <div className="space-y-3">
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Testimonials</label>
                                <button
                                    type="button"
                                    onClick={() => addArrayItem('testimonials', { 
                                        profileImage: '', 
                                        name: '', 
                                        recommendsName: '', 
                                        date: '', 
                                        reviewText: '', 
                                        likes: 0, 
                                        comments: 0 
                                    })}
                                    className="text-xs bg-blue-500 text-white px-2 py-1 rounded hover:bg-blue-600"
                                >
                                    + Add Testimonial
                                </button>
                            </div>
                            <div className="space-y-3 max-h-96 overflow-y-auto">
                                {(content.testimonials || []).map((testimonial, index) => (
                                    <div key={index} className="border border-gray-200 rounded p-3 bg-white">
                                        <div className="flex items-start justify-between mb-2">
                                            <span className="text-xs font-medium text-gray-600">Testimonial {index + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('testimonials', index)}
                                                className="text-xs text-red-500 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            <input
                                                type="url"
                                                value={testimonial.profileImage || ''}
                                                onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, profileImage: e.target.value })}
                                                placeholder="Profile Image URL"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={testimonial.name || ''}
                                                onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, name: e.target.value })}
                                                placeholder="Name"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={testimonial.recommendsName || ''}
                                                onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, recommendsName: e.target.value })}
                                                placeholder="Recommends Name (optional)"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={testimonial.date || ''}
                                                onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, date: e.target.value })}
                                                placeholder="Date (e.g., Dec 13, 2024)"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <textarea
                                                value={testimonial.reviewText || ''}
                                                onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, reviewText: e.target.value })}
                                                placeholder="Review Text"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                rows={3}
                                            />
                                            <div className="grid grid-cols-2 gap-2">
                                                <div>
                                                    <label className="text-xs text-gray-600 mb-1 block">Likes</label>
                                                    <input
                                                        type="number"
                                                        value={testimonial.likes || 0}
                                                        onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, likes: parseInt(e.target.value) || 0 })}
                                                        className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    />
                                                </div>
                                                <div>
                                                    <label className="text-xs text-gray-600 mb-1 block">Comments</label>
                                                    <input
                                                        type="number"
                                                        value={testimonial.comments || 0}
                                                        onChange={(e) => updateArrayItem('testimonials', index, { ...testimonial, comments: parseInt(e.target.value) || 0 })}
                                                        className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                                    />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                ))}
                                {(content.testimonials || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No testimonials added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Background</label>
                            <input
                                type="color"
                                value={style.backgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Card Background</label>
                            <input
                                type="color"
                                value={style.cardBackgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('cardBackgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Card Border Color</label>
                            <input
                                type="color"
                                value={style.cardBorderColor || '#fbbf24'}
                                onChange={(e) => updateStyle('cardBorderColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Card Border Width</label>
                            <input
                                type="text"
                                value={style.cardBorderWidth || '1px'}
                                onChange={(e) => updateStyle('cardBorderWidth', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                placeholder="1px"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Card Border Radius</label>
                            <select
                                value={style.cardBorderRadius || 'md'}
                                onChange={(e) => updateStyle('cardBorderRadius', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="none">None</option>
                                <option value="sm">Small</option>
                                <option value="md">Medium</option>
                                <option value="lg">Large</option>
                                <option value="xl">Extra Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Name Size</label>
                            <select
                                value={style.nameSize || 'base'}
                                onChange={(e) => updateStyle('nameSize', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="sm">Small</option>
                                <option value="base">Base</option>
                                <option value="lg">Large</option>
                                <option value="xl">Extra Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Name Color</label>
                            <input
                                type="color"
                                value={style.nameColor || '#1f2937'}
                                onChange={(e) => updateStyle('nameColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Review Text Size</label>
                            <select
                                value={style.reviewTextSize || 'sm'}
                                onChange={(e) => updateStyle('reviewTextSize', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="xs">Extra Small</option>
                                <option value="sm">Small</option>
                                <option value="base">Base</option>
                                <option value="lg">Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Review Text Color</label>
                            <input
                                type="color"
                                value={style.reviewTextColor || '#374151'}
                                onChange={(e) => updateStyle('reviewTextColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Grid Spacing</label>
                            <select
                                value={style.spacing || 'normal'}
                                onChange={(e) => updateStyle('spacing', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Card Padding</label>
                            <select
                                value={style.cardPadding || 'normal'}
                                onChange={(e) => updateStyle('cardPadding', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Padding</label>
                            <select
                                value={style.sectionPadding || 'normal'}
                                onChange={(e) => updateStyle('sectionPadding', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                    </div>
                );

            case 'YouTubeVideosThreeColumn':
                return (
                    <div className="space-y-3">
                        <div>
                            <div className="flex items-center justify-between mb-2">
                                <label className="block text-xs font-medium text-gray-700">Videos (max 3)</label>
                                <button
                                    type="button"
                                    onClick={() => {
                                        if ((content.videos || []).length < 3) {
                                            addArrayItem('videos', { url: '', title: '' });
                                        }
                                    }}
                                    disabled={(content.videos || []).length >= 3}
                                    className={`text-xs px-2 py-1 rounded ${
                                        (content.videos || []).length >= 3
                                            ? 'bg-gray-300 text-gray-500 cursor-not-allowed'
                                            : 'bg-blue-500 text-white hover:bg-blue-600'
                                    }`}
                                >
                                    + Add Video
                                </button>
                            </div>
                            {(content.videos || []).length >= 3 && (
                                <p className="text-xs text-yellow-600 mb-2">Maximum 3 videos allowed</p>
                            )}
                            <div className="space-y-2">
                                {(content.videos || []).map((video, index) => (
                                    <div key={index} className="border border-gray-200 rounded p-2 bg-white">
                                        <div className="flex items-start justify-between mb-2">
                                            <span className="text-xs font-medium text-gray-600">Video {index + 1}</span>
                                            <button
                                                type="button"
                                                onClick={() => removeArrayItem('videos', index)}
                                                className="text-xs text-red-500 hover:text-red-700"
                                            >
                                                Remove
                                            </button>
                                        </div>
                                        <div className="space-y-2">
                                            <input
                                                type="url"
                                                value={video.url || ''}
                                                onChange={(e) => updateArrayItem('videos', index, { ...video, url: e.target.value })}
                                                placeholder="YouTube Video URL"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                            <input
                                                type="text"
                                                value={video.title || ''}
                                                onChange={(e) => updateArrayItem('videos', index, { ...video, title: e.target.value })}
                                                placeholder="Video Title (optional)"
                                                className="w-full px-2 py-1 border border-gray-300 rounded text-sm"
                                            />
                                        </div>
                                    </div>
                                ))}
                                {(content.videos || []).length === 0 && (
                                    <p className="text-xs text-gray-400 text-center py-2">No videos added yet</p>
                                )}
                            </div>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Background Color</label>
                            <input
                                type="color"
                                value={style.backgroundColor || '#ffffff'}
                                onChange={(e) => updateStyle('backgroundColor', e.target.value)}
                                className="w-full h-10"
                            />
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Video Spacing</label>
                            <select
                                value={style.spacing || 'normal'}
                                onChange={(e) => updateStyle('spacing', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Video Aspect Ratio</label>
                            <select
                                value={style.videoAspectRatio || '16:9'}
                                onChange={(e) => updateStyle('videoAspectRatio', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="16:9">16:9 (Widescreen)</option>
                                <option value="4:3">4:3 (Standard)</option>
                                <option value="1:1">1:1 (Square)</option>
                            </select>
                        </div>
                        <div>
                            <label className="block text-xs font-medium text-gray-700 mb-1">Section Padding</label>
                            <select
                                value={style.sectionPadding || 'normal'}
                                onChange={(e) => updateStyle('sectionPadding', e.target.value)}
                                className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                            >
                                <option value="normal">Normal</option>
                                <option value="large">Large</option>
                            </select>
                        </div>
                        <div className="flex items-center gap-2">
                            <input
                                type="checkbox"
                                checked={style.showTitles === true}
                                onChange={(e) => updateStyle('showTitles', e.target.checked)}
                                className="w-4 h-4"
                            />
                            <label className="text-xs font-medium text-gray-700">Show Video Titles</label>
                        </div>
                        {style.showTitles === true && (
                            <>
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-1">Title Size</label>
                                    <select
                                        value={style.titleSize || 'base'}
                                        onChange={(e) => updateStyle('titleSize', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                    >
                                        <option value="sm">Small</option>
                                        <option value="base">Base</option>
                                        <option value="lg">Large</option>
                                        <option value="xl">Extra Large</option>
                                    </select>
                                </div>
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-1">Title Color</label>
                                    <input
                                        type="color"
                                        value={style.titleColor || '#1f2937'}
                                        onChange={(e) => updateStyle('titleColor', e.target.value)}
                                        className="w-full h-10"
                                    />
                                </div>
                                <div>
                                    <label className="block text-xs font-medium text-gray-700 mb-1">Title Font Weight</label>
                                    <select
                                        value={style.titleWeight || 'semibold'}
                                        onChange={(e) => updateStyle('titleWeight', e.target.value)}
                                        className="w-full px-3 py-2 border border-gray-300 rounded text-sm"
                                    >
                                        <option value="normal">Normal</option>
                                        <option value="semibold">Semibold</option>
                                        <option value="bold">Bold</option>
                                    </select>
                                </div>
                            </>
                        )}
                    </div>
                );

            default:
                return (
                    <div className="text-sm text-gray-500">
                        Editor for {type} coming soon. Use JSON editor for now.
                    </div>
                );
        }
    };

    return (
        <div className="p-4 bg-gray-50 border-t border-gray-200">
            <div className="space-y-4">
                <div className="flex justify-between items-center">
                    <h4 className="text-sm font-semibold text-gray-900">Edit Section</h4>
                </div>
                {renderEditor()}
            </div>
        </div>
    );
};

export default SectionEditor;

