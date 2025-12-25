import React, { useState } from 'react';

/**
 * SECTION NAME: CourseModulesSection
 * 
 * SECTION PURPOSE: Expandable course modules section displaying detailed course 
 * content with collapsible modules. Perfect for educational product pages.
 */

const CourseModulesSection = ({ content, style }) => {
    const {
        title = '',
        subtitle = '',
        modules = []
    } = content;

    const {
        backgroundColor = '#ffffff',
        titleSize = '2xl', // 'xl' | '2xl' | '3xl' | '4xl'
        titleColor = '#1f2937',
        titleTag = 'h2', // 'h1' | 'h2' | 'h3' | 'h4'
        subtitleSize = 'lg',
        subtitleColor = '#6b7280',
        moduleBgColor = '#f9fafb',
        moduleBorderColor = '#e5e7eb',
        moduleTitleColor = '#1f2937',
        moduleTitleSize = 'xl',
        moduleTextColor = '#374151',
        sectionPadding = 'normal'
    } = style;

    const [expandedModules, setExpandedModules] = useState({});

    const toggleModule = (index) => {
        setExpandedModules(prev => ({
            ...prev,
            [index]: !prev[index]
        }));
    };

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

    // Padding mapping
    const paddingClasses = {
        normal: 'py-12 md:py-16',
        large: 'py-16 md:py-24'
    };

    // Render heading with dynamic tag
    const renderHeading = (text, size, color, tag) => {
        const HeadingTag = tag;
        const className = `${textSizeClasses[size]} font-bold mb-4 text-center`;
        return (
            <HeadingTag style={{ color }} className={className}>
                {text}
            </HeadingTag>
        );
    };

    return (
        <section 
            className={`w-full ${paddingClasses[sectionPadding]}`}
            style={{ backgroundColor }}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {title && renderHeading(title, titleSize, titleColor, titleTag)}
                {subtitle && (
                    <p 
                        className={`${textSizeClasses[subtitleSize]} text-center mb-8`}
                        style={{ color: subtitleColor }}
                    >
                        {subtitle}
                    </p>
                )}

                <div className="space-y-4">
                    {modules.map((module, index) => (
                        <div
                            key={index}
                            className="rounded-lg border overflow-hidden"
                            style={{
                                backgroundColor: moduleBgColor,
                                borderColor: moduleBorderColor
                            }}
                        >
                            <button
                                onClick={() => toggleModule(index)}
                                className="w-full px-6 py-4 flex items-center justify-between hover:bg-gray-50 transition"
                            >
                                <h3 
                                    className={`${textSizeClasses[moduleTitleSize]} font-semibold text-left`}
                                    style={{ color: moduleTitleColor }}
                                >
                                    {module.title}
                                </h3>
                                <svg
                                    className={`w-5 h-5 transition-transform ${expandedModules[index] ? 'rotate-180' : ''}`}
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    style={{ color: moduleTitleColor }}
                                >
                                    <path strokeLinecap="round" strokeLinejoin="round" strokeWidth="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                            
                            {expandedModules[index] && module.content && (
                                <div 
                                    className="px-6 py-4 border-t"
                                    style={{ 
                                        borderTopColor: moduleBorderColor,
                                        color: moduleTextColor
                                    }}
                                >
                                    {typeof module.content === 'string' ? (
                                        <div 
                                            className="prose max-w-none"
                                            dangerouslySetInnerHTML={{ __html: module.content }}
                                        />
                                    ) : Array.isArray(module.content) ? (
                                        <ul className="space-y-2">
                                            {module.content.map((item, itemIndex) => (
                                                <li key={itemIndex} className="flex items-start gap-2">
                                                    <span className="text-[#7c3aed] mt-1">•</span>
                                                    <span>{item}</span>
                                                </li>
                                            ))}
                                        </ul>
                                    ) : null}
                                </div>
                            )}
                        </div>
                    ))}
                </div>
            </div>
        </section>
    );
};

export default CourseModulesSection;

