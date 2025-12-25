import React from 'react';

/**
 * SECTION NAME: BannerTwoLineText
 * 
 * SECTION PURPOSE: Horizontal banner with two-line text messaging, suitable for 
 * social proof, callout statements, or promotional messages. Features customizable 
 * gradient backgrounds and flexible typography options.
 */

const BannerTwoLineText = ({ content, style }) => {
    const {
        topLine = '',
        bottomLine = ''
    } = content;

    const {
        backgroundType = 'gradient', // 'color' | 'gradient' | 'image'
        backgroundColor = '#1e1b4b', // used when backgroundType is 'color'
        gradientDirection = 'to right', // 'to right' | 'to left' | 'to bottom' | 'to top' | 'to bottom right' | 'to bottom left' | 'to top right' | 'to top left'
        gradientStart = '#7c3aed', // dark purple
        gradientEnd = '#000000', // black
        backgroundImage = '', // used when backgroundType is 'image'
        
        topLineColor = '#ffffff',
        bottomLineColor = '#a78bfa', // light purple
        topLineSize = 'xl', // 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl'
        bottomLineSize = 'xl', // 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl'
        fontWeight = 'bold', // 'normal' | 'semibold' | 'bold'
        
        alignment = 'center', // 'left' | 'center' | 'right'
        padding = 'normal', // 'normal' | 'large'
        borderRadius = 'lg', // 'none' | 'sm' | 'md' | 'lg' | 'xl' | 'full'
        cornerStyle = 'all' // 'all' | 'sides' (sides = left/right only)
    } = style;

    // Generate background style
    const getBackgroundStyle = () => {
        if (backgroundType === 'gradient') {
            return {
                background: `linear-gradient(${gradientDirection}, ${gradientStart}, ${gradientEnd})`
            };
        } else if (backgroundType === 'image' && backgroundImage) {
            return {
                backgroundImage: `url(${backgroundImage})`,
                backgroundSize: 'cover',
                backgroundPosition: 'center',
                backgroundRepeat: 'no-repeat'
            };
        } else {
            return {
                backgroundColor: backgroundColor
            };
        }
    };

    // Text size mapping
    const textSizeClasses = {
        sm: 'text-sm md:text-base',
        md: 'text-base md:text-lg',
        lg: 'text-lg md:text-xl',
        xl: 'text-xl md:text-2xl',
        '2xl': 'text-2xl md:text-3xl',
        '3xl': 'text-3xl md:text-4xl',
        '4xl': 'text-4xl md:text-5xl'
    };

    // Font weight mapping
    const fontWeightClasses = {
        normal: 'font-normal',
        semibold: 'font-semibold',
        bold: 'font-bold'
    };

    // Padding mapping
    const paddingClasses = {
        normal: 'py-4 md:py-6',
        large: 'py-6 md:py-8'
    };

    // Border radius mapping
    const borderRadiusClasses = {
        none: 'rounded-none',
        sm: 'rounded-sm',
        md: 'rounded-md',
        lg: 'rounded-lg',
        xl: 'rounded-xl',
        full: 'rounded-full'
    };

    // Corner style - sides only (left/right)
    const cornerStyleClasses = cornerStyle === 'sides' 
        ? 'rounded-l-lg rounded-r-lg' 
        : borderRadiusClasses[borderRadius];

    // Alignment classes
    const alignmentClasses = {
        left: 'text-left',
        center: 'text-center',
        right: 'text-right'
    };

    return (
        <section className="w-full py-8 md:py-12">
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div 
                    className={`w-full ${paddingClasses[padding]} px-6 md:px-8 ${cornerStyleClasses}`}
                    style={getBackgroundStyle()}
                >
                    <div className={`space-y-2 ${alignmentClasses[alignment]}`}>
                        {/* Top Line */}
                        {topLine && (
                            <div 
                                className={`${textSizeClasses[topLineSize]} ${fontWeightClasses[fontWeight]} leading-tight`}
                                style={{ color: topLineColor }}
                            >
                                {topLine}
                            </div>
                        )}

                        {/* Bottom Line */}
                        {bottomLine && (
                            <div 
                                className={`${textSizeClasses[bottomLineSize]} ${fontWeightClasses[fontWeight]} leading-tight`}
                                style={{ color: bottomLineColor }}
                            >
                                {bottomLine}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default BannerTwoLineText;

