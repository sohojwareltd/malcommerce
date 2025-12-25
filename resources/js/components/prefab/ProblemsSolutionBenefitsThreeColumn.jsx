import React from 'react';

/**
 * SECTION NAME: ProblemsSolutionBenefitsThreeColumn
 * 
 * SECTION PURPOSE: Three-column comparison section showing problems on left, 
 * solution introduction in the middle, and benefits on right. Designed for 
 * product/service launches with social proof.
 */

const ProblemsSolutionBenefitsThreeColumn = ({ content, style }) => {
    const {
        leftTitle = '',
        leftList = [],
        centerImage = '',
        centerTitle = '',
        centerButtonText = '',
        centerButtonLink = '#',
        rightTitle = '',
        rightList = []
    } = content;

    const {
        // Backgrounds
        leftPanelBgColor = '#f3e8ff', // light purple
        middlePanelBgColor = '#7c3aed', // dark purple
        rightPanelBgColor = '#f3e8ff', // light purple
        
        // Typography
        titleSize = 'xl', // 'lg' | 'xl' | '2xl' | '3xl'
        titleColor = '#ffffff',
        titleTag = 'h2', // 'h1' | 'h2' | 'h3' | 'h4'
        listTextSize = 'base', // 'sm' | 'base' | 'lg'
        listTextColor = '#374151',
        centerTitleSize = '2xl', // 'xl' | '2xl' | '3xl' | '4xl'
        centerTitleColor = '#ffffff',
        centerTitleTag = 'h2',
        
        // List icons
        listIconColor = '#7c3aed',
        showListIcons = true,
        
        // Button
        buttonBgColor = '#7c3aed',
        buttonTextColor = '#ffffff',
        buttonSize = 'large', // 'medium' | 'large' | 'xl'
        
        // Layout
        spacing = 'normal', // 'normal' | 'large'
        sectionPadding = 'normal' // 'normal' | 'large'
    } = style;

    // Text size mapping
    const textSizeClasses = {
        sm: 'text-sm',
        base: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl',
        '2xl': 'text-2xl',
        '3xl': 'text-3xl',
        '4xl': 'text-4xl'
    };

    // Button size mapping
    const buttonSizeClasses = {
        medium: 'px-6 py-3 text-base',
        large: 'px-8 py-4 text-lg',
        xl: 'px-10 py-5 text-xl'
    };

    // Padding mapping
    const paddingClasses = {
        normal: 'py-12 md:py-16',
        large: 'py-16 md:py-24'
    };

    // Spacing mapping
    const spacingClasses = {
        normal: 'gap-4 md:gap-6',
        large: 'gap-6 md:gap-8'
    };

    // Render heading with dynamic tag
    const renderHeading = (text, size, color, tag) => {
        const HeadingTag = tag;
        const className = `${textSizeClasses[size]} font-bold mb-4`;
        return (
            <HeadingTag style={{ color }} className={className}>
                {text}
            </HeadingTag>
        );
    };

    return (
        <section className={`w-full ${paddingClasses[sectionPadding]}`}>
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className={`grid grid-cols-1 md:grid-cols-3 ${spacingClasses[spacing]}`}>
                    {/* Left Panel - Problems */}
                    <div 
                        className="rounded-lg p-6 md:p-8"
                        style={{ backgroundColor: leftPanelBgColor }}
                    >
                        {leftTitle && renderHeading(leftTitle, titleSize, titleColor, titleTag)}
                        <ul className="space-y-3">
                            {leftList.map((item, index) => (
                                <li key={index} className="flex items-start gap-3">
                                    {showListIcons && (
                                        <svg 
                                            className="w-5 h-5 flex-shrink-0 mt-0.5" 
                                            fill="currentColor" 
                                            viewBox="0 0 20 20"
                                            style={{ color: listIconColor }}
                                        >
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                        </svg>
                                    )}
                                    <span 
                                        className={`${textSizeClasses[listTextSize]} flex-1`}
                                        style={{ color: listTextColor }}
                                    >
                                        {item}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </div>

                    {/* Middle Panel - Solution */}
                    <div 
                        className="rounded-lg p-6 md:p-8 relative overflow-hidden"
                        style={{ backgroundColor: middlePanelBgColor }}
                    >
                        {centerImage && (
                            <div className="mb-6">
                                <img 
                                    src={centerImage} 
                                    alt={centerTitle || 'Solution'} 
                                    className="w-full h-auto rounded-lg"
                                />
                            </div>
                        )}
                        {centerTitle && renderHeading(centerTitle, centerTitleSize, centerTitleColor, centerTitleTag)}
                        {centerButtonText && (
                            <a
                                href={centerButtonLink}
                                className={`${buttonSizeClasses[buttonSize]} inline-block rounded-lg font-semibold transition-transform hover:scale-105 text-center mt-6`}
                                style={{
                                    backgroundColor: buttonBgColor,
                                    color: buttonTextColor
                                }}
                            >
                                {centerButtonText}
                            </a>
                        )}
                    </div>

                    {/* Right Panel - Benefits */}
                    <div 
                        className="rounded-lg p-6 md:p-8"
                        style={{ backgroundColor: rightPanelBgColor }}
                    >
                        {rightTitle && renderHeading(rightTitle, titleSize, titleColor, titleTag)}
                        <ul className="space-y-3">
                            {rightList.map((item, index) => (
                                <li key={index} className="flex items-start gap-3">
                                    {showListIcons && (
                                        <svg 
                                            className="w-5 h-5 flex-shrink-0 mt-0.5" 
                                            fill="currentColor" 
                                            viewBox="0 0 20 20"
                                            style={{ color: listIconColor }}
                                        >
                                            <path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"></path>
                                        </svg>
                                    )}
                                    <span 
                                        className={`${textSizeClasses[listTextSize]} flex-1`}
                                        style={{ color: listTextColor }}
                                    >
                                        {item}
                                    </span>
                                </li>
                            ))}
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    );
};

export default ProblemsSolutionBenefitsThreeColumn;

