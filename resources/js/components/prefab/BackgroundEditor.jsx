import React, { useState } from 'react';

const BackgroundEditor = ({ value = {}, onChange }) => {
    const [type, setType] = useState(value.type || 'color'); // 'color', 'gradient', 'image'
    const [color, setColor] = useState(value.color || '#FFFFFF');
    const [gradient, setGradient] = useState(value.gradient || {
        type: 'linear',
        direction: 'to right',
        colors: ['#4F46E5', '#7C3AED'],
        stops: [0, 100]
    });
    const [image, setImage] = useState(value.image || '');
    const [imagePosition, setImagePosition] = useState(value.imagePosition || 'center');
    const [imageSize, setImageSize] = useState(value.imageSize || 'cover');
    const [imageRepeat, setImageRepeat] = useState(value.imageRepeat || 'no-repeat');

    const handleTypeChange = (newType) => {
        setType(newType);
        const newValue = { ...value, type: newType };
        if (newType === 'color') {
            newValue.color = color;
        } else if (newType === 'gradient') {
            newValue.gradient = gradient;
        } else if (newType === 'image') {
            newValue.image = image;
        }
        onChange(newValue);
    };

    const handleColorChange = (newColor) => {
        setColor(newColor);
        onChange({ ...value, type: 'color', color: newColor });
    };

    const handleGradientChange = (field, val) => {
        const newGradient = { ...gradient, [field]: val };
        setGradient(newGradient);
        onChange({ ...value, type: 'gradient', gradient: newGradient });
    };

    const handleGradientColorChange = (index, newColor) => {
        const newColors = [...gradient.colors];
        newColors[index] = newColor;
        handleGradientChange('colors', newColors);
    };

    const handleImageChange = (newImage) => {
        setImage(newImage);
        onChange({ 
            ...value, 
            type: 'image', 
            image: newImage,
            imagePosition,
            imageSize,
            imageRepeat
        });
    };

    const generateCSS = () => {
        if (type === 'color') {
            return { backgroundColor: color };
        } else if (type === 'gradient') {
            const gradientStr = gradient.colors.map((c, i) => 
                `${c} ${gradient.stops[i]}%`
            ).join(', ');
            if (gradient.type === 'linear') {
                return { background: `linear-gradient(${gradient.direction}, ${gradientStr})` };
            } else {
                return { background: `radial-gradient(circle, ${gradientStr})` };
            }
        } else if (type === 'image') {
            return {
                backgroundImage: image ? `url(${image})` : 'none',
                backgroundPosition: imagePosition,
                backgroundSize: imageSize,
                backgroundRepeat: imageRepeat
            };
        }
        return {};
    };

    const previewStyle = generateCSS();

    return (
        <div className="space-y-4">
            {/* Type Selector */}
            <div className="flex gap-2 border-b border-[#E1E3E5] pb-3">
                <button
                    onClick={() => handleTypeChange('color')}
                    className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                        type === 'color'
                            ? 'bg-[#008060] text-white'
                            : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                    }`}
                >
                    Color
                </button>
                <button
                    onClick={() => handleTypeChange('gradient')}
                    className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                        type === 'gradient'
                            ? 'bg-[#008060] text-white'
                            : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                    }`}
                >
                    Gradient
                </button>
                <button
                    onClick={() => handleTypeChange('image')}
                    className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                        type === 'image'
                            ? 'bg-[#008060] text-white'
                            : 'text-[#637381] hover:text-[#202223] hover:bg-[#F6F6F7]'
                    }`}
                >
                    Image
                </button>
            </div>

            {/* Color Editor */}
            {type === 'color' && (
                <div className="space-y-2">
                    <label className="text-xs font-semibold text-[#637381] uppercase">Background Color</label>
                    <div className="flex gap-2 items-center">
                        <input
                            type="color"
                            value={color}
                            onChange={(e) => handleColorChange(e.target.value)}
                            className="w-16 h-10 rounded border border-[#E1E3E5] cursor-pointer"
                        />
                        <input
                            type="text"
                            value={color}
                            onChange={(e) => handleColorChange(e.target.value)}
                            className="flex-1 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                            placeholder="#FFFFFF"
                        />
                    </div>
                </div>
            )}

            {/* Gradient Editor */}
            {type === 'gradient' && (
                <div className="space-y-3">
                    <div>
                        <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Gradient Type</label>
                        <div className="flex gap-2">
                            <button
                                onClick={() => handleGradientChange('type', 'linear')}
                                className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                                    gradient.type === 'linear'
                                        ? 'bg-[#008060] text-white'
                                        : 'bg-white border border-[#E1E3E5] text-[#637381] hover:bg-[#F6F6F7]'
                                }`}
                            >
                                Linear
                            </button>
                            <button
                                onClick={() => handleGradientChange('type', 'radial')}
                                className={`px-3 py-1.5 rounded text-xs font-medium transition ${
                                    gradient.type === 'radial'
                                        ? 'bg-[#008060] text-white'
                                        : 'bg-white border border-[#E1E3E5] text-[#637381] hover:bg-[#F6F6F7]'
                                }`}
                            >
                                Radial
                            </button>
                        </div>
                    </div>

                    {gradient.type === 'linear' && (
                        <div>
                            <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Direction</label>
                            <select
                                value={gradient.direction}
                                onChange={(e) => handleGradientChange('direction', e.target.value)}
                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                            >
                                <option value="to right">To Right</option>
                                <option value="to left">To Left</option>
                                <option value="to bottom">To Bottom</option>
                                <option value="to top">To Top</option>
                                <option value="to bottom right">To Bottom Right</option>
                                <option value="to bottom left">To Bottom Left</option>
                                <option value="to top right">To Top Right</option>
                                <option value="to top left">To Top Left</option>
                            </select>
                        </div>
                    )}

                    <div>
                        <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Colors</label>
                        <div className="space-y-2">
                            {gradient.colors.map((color, index) => (
                                <div key={index} className="flex gap-2 items-center">
                                    <input
                                        type="color"
                                        value={color}
                                        onChange={(e) => handleGradientColorChange(index, e.target.value)}
                                        className="w-12 h-10 rounded border border-[#E1E3E5] cursor-pointer"
                                    />
                                    <input
                                        type="text"
                                        value={color}
                                        onChange={(e) => handleGradientColorChange(index, e.target.value)}
                                        className="flex-1 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                        placeholder="#000000"
                                    />
                                    <input
                                        type="number"
                                        min="0"
                                        max="100"
                                        value={gradient.stops[index]}
                                        onChange={(e) => {
                                            const newStops = [...gradient.stops];
                                            newStops[index] = parseInt(e.target.value);
                                            handleGradientChange('stops', newStops);
                                        }}
                                        className="w-16 px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                                        placeholder="0"
                                    />
                                    <span className="text-xs text-[#637381]">%</span>
                                </div>
                            ))}
                            <button
                                onClick={() => {
                                    const newColors = [...gradient.colors, '#000000'];
                                    const newStops = [...gradient.stops, 100];
                                    handleGradientChange('colors', newColors);
                                    handleGradientChange('stops', newStops);
                                }}
                                className="text-[#008060] hover:text-[#006E52] text-xs font-medium transition"
                            >
                                + Add Color
                            </button>
                        </div>
                    </div>
                </div>
            )}

            {/* Image Editor */}
            {type === 'image' && (
                <div className="space-y-3">
                    <div>
                        <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Image URL</label>
                        <input
                            type="text"
                            value={image}
                            onChange={(e) => handleImageChange(e.target.value)}
                            className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] placeholder:text-[#8C9196] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                            placeholder="https://example.com/image.jpg"
                        />
                    </div>

                    <div className="grid grid-cols-2 gap-3">
                        <div>
                            <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Position</label>
                            <select
                                value={imagePosition}
                                onChange={(e) => {
                                    setImagePosition(e.target.value);
                                    onChange({ ...value, type: 'image', image, imagePosition: e.target.value, imageSize, imageRepeat });
                                }}
                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                            >
                                <option value="center">Center</option>
                                <option value="top">Top</option>
                                <option value="bottom">Bottom</option>
                                <option value="left">Left</option>
                                <option value="right">Right</option>
                                <option value="top left">Top Left</option>
                                <option value="top right">Top Right</option>
                                <option value="bottom left">Bottom Left</option>
                                <option value="bottom right">Bottom Right</option>
                            </select>
                        </div>

                        <div>
                            <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Size</label>
                            <select
                                value={imageSize}
                                onChange={(e) => {
                                    setImageSize(e.target.value);
                                    onChange({ ...value, type: 'image', image, imagePosition, imageSize: e.target.value, imageRepeat });
                                }}
                                className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                            >
                                <option value="cover">Cover</option>
                                <option value="contain">Contain</option>
                                <option value="auto">Auto</option>
                                <option value="100%">100%</option>
                            </select>
                        </div>
                    </div>

                    <div>
                        <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Repeat</label>
                        <select
                            value={imageRepeat}
                            onChange={(e) => {
                                setImageRepeat(e.target.value);
                                onChange({ ...value, type: 'image', image, imagePosition, imageSize, imageRepeat: e.target.value });
                            }}
                            className="w-full px-2.5 py-1.5 border border-[#E1E3E5] rounded text-sm text-[#202223] focus:outline-none focus:ring-1 focus:ring-[#008060] focus:border-[#008060]"
                        >
                            <option value="no-repeat">No Repeat</option>
                            <option value="repeat">Repeat</option>
                            <option value="repeat-x">Repeat X</option>
                            <option value="repeat-y">Repeat Y</option>
                        </select>
                    </div>
                </div>
            )}

            {/* Preview */}
            <div className="mt-4 pt-4 border-t border-[#E1E3E5]">
                <label className="text-xs font-semibold text-[#637381] uppercase mb-2 block">Preview</label>
                <div 
                    className="w-full h-24 rounded border-2 border-dashed border-[#E1E3E5] flex items-center justify-center"
                    style={previewStyle}
                >
                    <span className="text-xs text-[#637381]">Background Preview</span>
                </div>
            </div>
        </div>
    );
};

export default BackgroundEditor;

