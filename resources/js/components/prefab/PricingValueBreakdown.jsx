import React from 'react';

/**
 * SECTION NAME: PricingValueBreakdown
 * 
 * SECTION PURPOSE: Pricing/value breakdown section showing individual items with prices, 
 * total value, discount offer, and CTA button. Perfect for course bundles, product packages, 
 * or service offerings with detailed value proposition.
 */

const PricingValueBreakdown = ({ content, style }) => {
    const {
        headerTopLine = '',
        headerBottomLine = '',
        items = [],
        totalValue = '',
        discountText = '',
        ctaButtonText = '',
        ctaButtonLink = '#'
    } = content;

    const {
        // Header styling
        headerBgType = 'gradient', // 'gradient' | 'solid'
        headerGradientStart = '#7c3aed', // dark purple
        headerGradientEnd = '#4c1d95',
        headerBgColor = '#7c3aed',
        headerTopLineColor = '#ffffff',
        headerTopLineSize = 'xl', // 'lg' | 'xl' | '2xl' | '3xl'
        headerBottomLineColor = '#c4b5fd', // light purple
        headerBottomLineSize = 'lg', // 'base' | 'lg' | 'xl'
        
        // List items styling
        itemTitleColor = '#1f2937',
        itemTitleSize = 'base', // 'sm' | 'base' | 'lg'
        itemValueColor = '#1f2937',
        itemValueSize = 'base', // 'sm' | 'base' | 'lg'
        itemBorderColor = '#e5e7eb',
        itemSpacing = 'normal', // 'normal' | 'large'
        
        // Summary styling
        totalValueColor = '#dc2626', // red
        totalValueSize = 'xl', // 'lg' | 'xl' | '2xl' | '3xl'
        totalValueWeight = 'bold', // 'semibold' | 'bold'
        discountTextColor = '#374151',
        discountTextSize = 'sm', // 'xs' | 'sm' | 'base'
        
        // Button styling
        buttonBgColor = '#7c3aed',
        buttonTextColor = '#ffffff',
        buttonSize = 'large', // 'medium' | 'large' | 'xl'
        buttonFullWidth = true,
        
        // Layout
        sectionPadding = 'normal', // 'normal' | 'large'
        backgroundColor = '#ffffff'
    } = style;

    // Generate header background style
    const getHeaderBackgroundStyle = () => {
        if (headerBgType === 'gradient') {
            return {
                background: `linear-gradient(to right, ${headerGradientStart}, ${headerGradientEnd})`
            };
        } else {
            return {
                backgroundColor: headerBgColor
            };
        }
    };

    // Text size mapping
    const textSizeClasses = {
        xs: 'text-xs',
        sm: 'text-sm',
        base: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl',
        '2xl': 'text-2xl',
        '3xl': 'text-3xl'
    };

    // Font weight mapping
    const fontWeightClasses = {
        semibold: 'font-semibold',
        bold: 'font-bold'
    };

    // Padding mapping
    const paddingClasses = {
        normal: 'py-12 md:py-16',
        large: 'py-16 md:py-24'
    };

    // Item spacing mapping
    const itemSpacingClasses = {
        normal: 'py-3 md:py-4',
        large: 'py-4 md:py-5'
    };

    // Button size mapping
    const buttonSizeClasses = {
        medium: 'px-6 py-3 text-base',
        large: 'px-8 py-4 text-lg',
        xl: 'px-10 py-5 text-xl'
    };

    return (
        <section 
            className={`w-full ${paddingClasses[sectionPadding]}`}
            style={{ backgroundColor }}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Header Section */}
                {(headerTopLine || headerBottomLine) && (
                    <div 
                        className="rounded-t-lg px-6 md:px-8 py-6 md:py-8 mb-6"
                        style={getHeaderBackgroundStyle()}
                    >
                        <div className="space-y-2">
                            {headerTopLine && (
                                <div 
                                    className={`${textSizeClasses[headerTopLineSize]} font-bold`}
                                    style={{ color: headerTopLineColor }}
                                >
                                    {headerTopLine}
                                </div>
                            )}
                            {headerBottomLine && (
                                <div 
                                    className={`${textSizeClasses[headerBottomLineSize]}`}
                                    style={{ color: headerBottomLineColor }}
                                >
                                    {headerBottomLine}
                                </div>
                            )}
                        </div>
                    </div>
                )}

                {/* Value List Section */}
                {items.length > 0 && (
                    <div className="bg-white rounded-lg border" style={{ borderColor: itemBorderColor }}>
                        {items.map((item, index) => (
                            <div
                                key={index}
                                className={`flex justify-between items-center ${itemSpacingClasses[itemSpacing]} px-6 md:px-8 ${
                                    index !== items.length - 1 ? 'border-b' : ''
                                }`}
                                style={{ 
                                    borderBottomColor: index !== items.length - 1 ? itemBorderColor : 'transparent'
                                }}
                            >
                                <div 
                                    className={`${textSizeClasses[itemTitleSize]} flex-1`}
                                    style={{ color: itemTitleColor }}
                                >
                                    {item.title}
                                </div>
                                <div 
                                    className={`${textSizeClasses[itemValueSize]} font-semibold ml-4`}
                                    style={{ color: itemValueColor }}
                                >
                                    {item.value}
                                </div>
                            </div>
                        ))}
                    </div>
                )}

                {/* Summary Section */}
                <div className="mt-6 space-y-3">
                    {/* Total Value */}
                    {totalValue && (
                        <div 
                            className={`${textSizeClasses[totalValueSize]} ${fontWeightClasses[totalValueWeight]}`}
                            style={{ color: totalValueColor }}
                        >
                            {totalValue}
                        </div>
                    )}

                    {/* Discount Text */}
                    {discountText && (
                        <div 
                            className={`${textSizeClasses[discountTextSize]}`}
                            style={{ color: discountTextColor }}
                        >
                            {discountText}
                        </div>
                    )}

                    {/* CTA Button */}
                    {ctaButtonText && (
                        <div className={buttonFullWidth ? 'w-full' : 'inline-block'}>
                            <a
                                href={ctaButtonLink}
                                className={`${buttonSizeClasses[buttonSize]} ${buttonFullWidth ? 'w-full' : ''} rounded-lg font-semibold transition-transform hover:scale-105 text-center inline-block`}
                                style={{
                                    backgroundColor: buttonBgColor,
                                    color: buttonTextColor
                                }}
                            >
                                {ctaButtonText}
                            </a>
                        </div>
                    )}
                </div>
            </div>
        </section>
    );
};

export default PricingValueBreakdown;

