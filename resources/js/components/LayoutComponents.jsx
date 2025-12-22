import React, { useState } from 'react';

// Helper function for image upload
const createUploadHandler = (uploadImageFn) => {
    return async (sectionIndex, identifier) => {
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
                const url = await uploadImageFn(sectionIndex, identifier);
                resolve(url);
            };
            input.click();
        });
    };
};

// Grid Component
export const GridSection = ({ section, index, updateSection, removeImage, uploadImage, uploading }) => {
    const [columns, setColumns] = useState(section.columns || 2);
    
    const updateColumns = (newColumns) => {
        setColumns(newColumns);
        updateSection(index, 'columns', newColumns);
        // Initialize items if needed
        if (!section.items || section.items.length < newColumns) {
            const items = [...(section.items || [])];
            while (items.length < newColumns) {
                items.push({ content: '', type: 'text' });
            }
            updateSection(index, 'items', items);
        }
    };
    
    return (
        <div className="space-y-3">
            <div className="flex items-center gap-2">
                <label className="text-sm font-medium">Columns:</label>
                <select 
                    value={columns} 
                    onChange={(e) => updateColumns(parseInt(e.target.value))}
                    className="px-3 py-1 border border-neutral-300 rounded text-sm"
                >
                    {[1, 2, 3, 4, 5, 6].map(num => (
                        <option key={num} value={num}>{num}</option>
                    ))}
                </select>
                <label className="text-sm font-medium ml-4">Gap:</label>
                <select 
                    value={section.gap || 'medium'} 
                    onChange={(e) => updateSection(index, 'gap', e.target.value)}
                    className="px-3 py-1 border border-neutral-300 rounded text-sm"
                >
                    <option value="none">None</option>
                    <option value="small">Small</option>
                    <option value="medium">Medium</option>
                    <option value="large">Large</option>
                </select>
            </div>
            
            <div className="grid gap-2" style={{ gridTemplateColumns: `repeat(${columns}, 1fr)` }}>
                {(section.items || []).slice(0, columns).map((item, itemIndex) => (
                    <div key={itemIndex} className="border-2 border-dashed border-neutral-300 p-3 rounded">
                        <label className="text-xs text-neutral-500 mb-1 block">Column {itemIndex + 1}</label>
                        <select
                            value={item.type || 'text'}
                            onChange={(e) => {
                                const items = [...(section.items || [])];
                                items[itemIndex] = { ...items[itemIndex], type: e.target.value, content: '' };
                                updateSection(index, 'items', items);
                            }}
                            className="w-full mb-2 px-2 py-1 border border-neutral-300 rounded text-xs"
                        >
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                            <option value="html">HTML</option>
                            <option value="video">Video</option>
                        </select>
                        
                        {item.type === 'text' && (
                            <textarea
                                value={item.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={3}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Enter text content"
                            />
                        )}
                        
                        {item.type === 'image' && (
                            <div className="space-y-2">
                                {item.image && (
                                    <div className="relative">
                                        <img src={item.image} alt="" className="w-full h-32 object-cover rounded" />
                                        <button
                                            onClick={() => {
                                                const items = [...(section.items || [])];
                                                items[itemIndex] = { ...items[itemIndex], image: '' };
                                                updateSection(index, 'items', items);
                                            }}
                                            className="absolute top-1 right-1 bg-red-500 text-white rounded-full w-5 h-5 text-xs"
                                        >
                                            ×
                                        </button>
                                    </div>
                                )}
                                <button
                                    onClick={async () => {
                                        const uploadKey = `col-${itemIndex}`;
                                        try {
                                            const url = await uploadImage(index, uploadKey);
                                            if (url) {
                                                const items = [...(section.items || [])];
                                                items[itemIndex] = { ...items[itemIndex], image: url };
                                                updateSection(index, 'items', items);
                                            }
                                        } catch (error) {
                                            console.error('Upload error:', error);
                                            alert('Failed to upload image');
                                        }
                                    }}
                                    disabled={uploading[`${index}-col-${itemIndex}`]}
                                    className="w-full bg-primary text-white px-3 py-1 rounded text-xs hover:bg-primary-light disabled:opacity-50"
                                >
                                    {uploading[`${index}-col-${itemIndex}`] ? 'Uploading...' : '+ Upload Image'}
                                </button>
                            </div>
                        )}
                        
                        {item.type === 'html' && (
                            <textarea
                                value={item.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={4}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm font-mono text-xs"
                                placeholder="Enter HTML"
                            />
                        )}
                        
                        {item.type === 'video' && (
                            <input
                                type="url"
                                value={item.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                placeholder="Video embed URL"
                            />
                        )}
                    </div>
                ))}
            </div>
        </div>
    );
};

// Flex/Row Component
export const FlexSection = ({ section, index, updateSection, removeImage, uploadImage, uploading }) => {
    return (
        <div className="space-y-3">
            <div className="flex items-center gap-2">
                <label className="text-sm font-medium">Direction:</label>
                <select 
                    value={section.direction || 'row'} 
                    onChange={(e) => updateSection(index, 'direction', e.target.value)}
                    className="px-3 py-1 border border-neutral-300 rounded text-sm"
                >
                    <option value="row">Horizontal</option>
                    <option value="column">Vertical</option>
                </select>
                <label className="text-sm font-medium ml-4">Align:</label>
                <select 
                    value={section.align || 'start'} 
                    onChange={(e) => updateSection(index, 'align', e.target.value)}
                    className="px-3 py-1 border border-neutral-300 rounded text-sm"
                >
                    <option value="start">Start</option>
                    <option value="center">Center</option>
                    <option value="end">End</option>
                    <option value="stretch">Stretch</option>
                </select>
                <label className="text-sm font-medium ml-4">Justify:</label>
                <select 
                    value={section.justify || 'start'} 
                    onChange={(e) => updateSection(index, 'justify', e.target.value)}
                    className="px-3 py-1 border border-neutral-300 rounded text-sm"
                >
                    <option value="start">Start</option>
                    <option value="center">Center</option>
                    <option value="end">End</option>
                    <option value="between">Space Between</option>
                    <option value="around">Space Around</option>
                </select>
            </div>
            
            <div className="flex gap-2 flex-wrap">
                {(section.items || []).map((item, itemIndex) => (
                    <div key={itemIndex} className="border-2 border-dashed border-neutral-300 p-3 rounded flex-1 min-w-[200px]">
                        <div className="flex justify-between items-center mb-2">
                            <label className="text-xs text-neutral-500">Item {itemIndex + 1}</label>
                            <button
                                onClick={() => {
                                    const items = section.items.filter((_, i) => i !== itemIndex);
                                    updateSection(index, 'items', items);
                                }}
                                className="text-red-500 text-xs"
                            >
                                Remove
                            </button>
                        </div>
                        <select
                            value={item.type || 'text'}
                            onChange={(e) => {
                                const items = [...(section.items || [])];
                                items[itemIndex] = { ...items[itemIndex], type: e.target.value, content: '' };
                                updateSection(index, 'items', items);
                            }}
                            className="w-full mb-2 px-2 py-1 border border-neutral-300 rounded text-xs"
                        >
                            <option value="text">Text</option>
                            <option value="image">Image</option>
                            <option value="html">HTML</option>
                            <option value="button">Button</option>
                        </select>
                        
                        {item.type === 'text' && (
                            <textarea
                                value={item.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={2}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                            />
                        )}
                        
                        {item.type === 'image' && (
                            <div className="space-y-2">
                                {item.image && (
                                    <img src={item.image} alt="" className="w-full h-24 object-cover rounded" />
                                )}
                                <button
                                    onClick={async () => {
                                        const uploadKey = `flex-${itemIndex}`;
                                        try {
                                            const url = await uploadImage(index, uploadKey);
                                            if (url) {
                                                const items = [...(section.items || [])];
                                                items[itemIndex] = { ...items[itemIndex], image: url };
                                                updateSection(index, 'items', items);
                                            }
                                        } catch (error) {
                                            console.error('Upload error:', error);
                                            alert('Failed to upload image');
                                        }
                                    }}
                                    disabled={uploading[`${index}-flex-${itemIndex}`]}
                                    className="w-full bg-primary text-white px-3 py-1 rounded text-xs hover:bg-primary-light disabled:opacity-50"
                                >
                                    {uploading[`${index}-flex-${itemIndex}`] ? 'Uploading...' : 'Upload'}
                                </button>
                            </div>
                        )}
                        
                        {item.type === 'html' && (
                            <textarea
                                value={item.content || ''}
                                onChange={(e) => {
                                    const items = [...(section.items || [])];
                                    items[itemIndex] = { ...items[itemIndex], content: e.target.value };
                                    updateSection(index, 'items', items);
                                }}
                                rows={3}
                                className="w-full px-2 py-1 border border-neutral-300 rounded text-sm font-mono text-xs"
                            />
                        )}
                        
                        {item.type === 'button' && (
                            <div className="space-y-2">
                                <input
                                    type="text"
                                    value={item.buttonText || ''}
                                    onChange={(e) => {
                                        const items = [...(section.items || [])];
                                        items[itemIndex] = { ...items[itemIndex], buttonText: e.target.value };
                                        updateSection(index, 'items', items);
                                    }}
                                    className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                    placeholder="Button Text"
                                />
                                <input
                                    type="url"
                                    value={item.buttonLink || ''}
                                    onChange={(e) => {
                                        const items = [...(section.items || [])];
                                        items[itemIndex] = { ...items[itemIndex], buttonLink: e.target.value };
                                        updateSection(index, 'items', items);
                                    }}
                                    className="w-full px-2 py-1 border border-neutral-300 rounded text-sm"
                                    placeholder="Button Link"
                                />
                            </div>
                        )}
                    </div>
                ))}
                <button
                    onClick={() => {
                        const items = [...(section.items || []), { type: 'text', content: '' }];
                        updateSection(index, 'items', items);
                    }}
                    className="border-2 border-dashed border-neutral-300 p-3 rounded text-neutral-500 hover:border-primary hover:text-primary text-sm"
                >
                    + Add Item
                </button>
            </div>
        </div>
    );
};

// Spacer Component
export const SpacerSection = ({ section, index, updateSection }) => {
    return (
        <div className="space-y-2">
            <label className="text-sm font-medium">Height (px):</label>
            <input
                type="number"
                value={section.height || 50}
                onChange={(e) => updateSection(index, 'height', parseInt(e.target.value))}
                className="w-full px-3 py-2 border border-neutral-300 rounded-lg"
                min="0"
                max="500"
            />
        </div>
    );
};

// Container Component
export const ContainerSection = ({ section, index, updateSection }) => {
    return (
        <div className="space-y-3">
            <div className="grid grid-cols-2 gap-2">
                <div>
                    <label className="text-sm font-medium block mb-1">Max Width:</label>
                    <select
                        value={section.maxWidth || 'full'}
                        onChange={(e) => updateSection(index, 'maxWidth', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm"
                    >
                        <option value="full">Full Width</option>
                        <option value="7xl">7xl (1280px)</option>
                        <option value="6xl">6xl (1152px)</option>
                        <option value="5xl">5xl (1024px)</option>
                        <option value="4xl">4xl (896px)</option>
                        <option value="3xl">3xl (768px)</option>
                    </select>
                </div>
                <div>
                    <label className="text-sm font-medium block mb-1">Padding:</label>
                    <select
                        value={section.padding || 'medium'}
                        onChange={(e) => updateSection(index, 'padding', e.target.value)}
                        className="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm"
                    >
                        <option value="none">None</option>
                        <option value="small">Small</option>
                        <option value="medium">Medium</option>
                        <option value="large">Large</option>
                    </select>
                </div>
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Background Color:</label>
                <div className="flex gap-2">
                    <input
                        type="color"
                        value={section.backgroundColor || '#FFFFFF'}
                        onChange={(e) => updateSection(index, 'backgroundColor', e.target.value)}
                        className="w-16 h-10"
                    />
                    <input
                        type="text"
                        value={section.backgroundColor || '#FFFFFF'}
                        onChange={(e) => updateSection(index, 'backgroundColor', e.target.value)}
                        className="flex-1 px-3 py-2 border border-neutral-300 rounded-lg text-sm font-mono"
                        placeholder="#FFFFFF"
                    />
                </div>
            </div>
            <div>
                <label className="text-sm font-medium block mb-1">Content (HTML):</label>
                <textarea
                    value={section.content || ''}
                    onChange={(e) => updateSection(index, 'content', e.target.value)}
                    rows={6}
                    className="w-full px-3 py-2 border border-neutral-300 rounded-lg text-sm font-mono"
                    placeholder="Enter HTML content for this container"
                />
            </div>
        </div>
    );
};

