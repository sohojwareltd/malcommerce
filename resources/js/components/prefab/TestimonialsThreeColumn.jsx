import React from 'react';

/**
 * SECTION NAME: TestimonialsThreeColumn
 * 
 * SECTION PURPOSE: Responsive testimonials/reviews grid displaying social media-style 
 * recommendation cards. Desktop: 3 columns, Tablet: 2 columns, Mobile: 1 column. 
 * Supports unlimited number of testimonials.
 */

const TestimonialsThreeColumn = ({ content, style }) => {
    const {
        testimonials = []
    } = content;

    const {
        backgroundColor = '#ffffff',
        cardBackgroundColor = '#ffffff',
        cardBorderColor = '#fbbf24', // yellow
        cardBorderWidth = '1px',
        cardBorderRadius = 'md',
        
        nameSize = 'base', // 'sm' | 'base' | 'lg' | 'xl'
        nameColor = '#1f2937',
        nameWeight = 'semibold', // 'normal' | 'semibold' | 'bold'
        
        reviewTextSize = 'sm', // 'xs' | 'sm' | 'base' | 'lg'
        reviewTextColor = '#374151',
        reviewTextLineHeight = 'relaxed', // 'normal' | 'relaxed' | 'loose'
        
        dateSize = 'xs', // 'xs' | 'sm'
        dateColor = '#6b7280',
        
        recommendsTextColor = '#6b7280',
        recommendsTextSize = 'sm',
        
        spacing = 'normal', // 'normal' | 'large'
        cardPadding = 'normal', // 'normal' | 'large'
        
        profileImageSize = 'medium', // 'small' | 'medium' | 'large'
        profileImageShape = 'circle', // 'circle' | 'square' | 'rounded'
        
        showEngagement = true,
        engagementIconColor = '#6b7280',
        
        sectionPadding = 'normal' // 'normal' | 'large'
    } = style;

    // Text size mapping
    const textSizeClasses = {
        xs: 'text-xs',
        sm: 'text-sm',
        base: 'text-base',
        lg: 'text-lg',
        xl: 'text-xl'
    };

    // Font weight mapping
    const fontWeightClasses = {
        normal: 'font-normal',
        semibold: 'font-semibold',
        bold: 'font-bold'
    };

    // Line height mapping
    const lineHeightClasses = {
        normal: 'leading-normal',
        relaxed: 'leading-relaxed',
        loose: 'leading-loose'
    };

    // Profile image size mapping
    const profileImageSizes = {
        small: 'w-10 h-10',
        medium: 'w-12 h-12',
        large: 'w-16 h-16'
    };

    // Profile image shape mapping
    const profileImageShapes = {
        circle: 'rounded-full',
        square: 'rounded-none',
        rounded: 'rounded-lg'
    };

    // Spacing mapping
    const spacingClasses = {
        normal: 'gap-4 md:gap-6',
        large: 'gap-6 md:gap-8'
    };

    // Padding mapping
    const paddingClasses = {
        normal: 'py-12 md:py-16',
        large: 'py-16 md:py-24'
    };

    // Card padding mapping
    const cardPaddingClasses = {
        normal: 'p-4 md:p-6',
        large: 'p-6 md:p-8'
    };

    // Border radius mapping
    const borderRadiusClasses = {
        none: 'rounded-none',
        sm: 'rounded-sm',
        md: 'rounded-md',
        lg: 'rounded-lg',
        xl: 'rounded-xl'
    };

    return (
        <section 
            className={`w-full ${paddingClasses[sectionPadding]}`}
            style={{ backgroundColor }}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div 
                    className={`grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 ${spacingClasses[spacing]}`}
                >
                    {testimonials.map((testimonial, index) => (
                        <div
                            key={index}
                            className={`${cardPaddingClasses[cardPadding]} ${borderRadiusClasses[cardBorderRadius]}`}
                            style={{
                                backgroundColor: cardBackgroundColor,
                                borderWidth: cardBorderWidth,
                                borderColor: cardBorderColor,
                                borderStyle: 'solid'
                            }}
                        >
                            {/* Header: Profile + Name + Recommends */}
                            <div className="flex items-center gap-3 mb-3">
                                {testimonial.profileImage && (
                                    <img
                                        src={testimonial.profileImage}
                                        alt={testimonial.name || 'Profile'}
                                        className={`${profileImageSizes[profileImageSize]} ${profileImageShapes[profileImageShape]} object-cover flex-shrink-0`}
                                    />
                                )}
                                <div className="flex-1 min-w-0">
                                    <div 
                                        className={`${textSizeClasses[nameSize]} ${fontWeightClasses[nameWeight]} truncate`}
                                        style={{ color: nameColor }}
                                    >
                                        {testimonial.name}
                                    </div>
                                    {testimonial.recommendsName && (
                                        <div 
                                            className={`${textSizeClasses[recommendsTextSize]} truncate`}
                                            style={{ color: recommendsTextColor }}
                                        >
                                            recommends {testimonial.recommendsName}
                                        </div>
                                    )}
                                </div>
                            </div>

                            {/* Date */}
                            {testimonial.date && (
                                <div 
                                    className={`${textSizeClasses[dateSize]} mb-3`}
                                    style={{ color: dateColor }}
                                >
                                    {testimonial.date}
                                </div>
                            )}

                            {/* Review Text */}
                            {testimonial.reviewText && (
                                <div 
                                    className={`${textSizeClasses[reviewTextSize]} ${lineHeightClasses[reviewTextLineHeight]} mb-4`}
                                    style={{ color: reviewTextColor }}
                                >
                                    {testimonial.reviewText}
                                </div>
                            )}

                            {/* Engagement Icons */}
                            {showEngagement && (testimonial.likes !== undefined || testimonial.comments !== undefined) && (
                                <div className="flex items-center gap-4 pt-3 border-t" style={{ borderColor: '#e5e7eb' }}>
                                    {testimonial.likes !== undefined && (
                                        <div className="flex items-center gap-1">
                                            <svg 
                                                className="w-4 h-4" 
                                                fill="currentColor" 
                                                viewBox="0 0 20 20"
                                                style={{ color: engagementIconColor }}
                                            >
                                                <path fillRule="evenodd" d="M3.172 5.172a4 4 0 015.656 0L10 6.343l1.172-1.171a4 4 0 115.656 5.656L10 17.657l-6.828-6.829a4 4 0 010-5.656z" clipRule="evenodd" />
                                            </svg>
                                            <span 
                                                className={`${textSizeClasses.xs}`}
                                                style={{ color: engagementIconColor }}
                                            >
                                                {testimonial.likes}
                                            </span>
                                        </div>
                                    )}
                                    {testimonial.comments !== undefined && (
                                        <div className="flex items-center gap-1">
                                            <svg 
                                                className="w-4 h-4" 
                                                fill="currentColor" 
                                                viewBox="0 0 20 20"
                                                style={{ color: engagementIconColor }}
                                            >
                                                <path fillRule="evenodd" d="M18 10c0 3.866-3.582 7-8 7a8.841 8.841 0 01-4.083-.98L2 17l1.338-3.123C2.493 12.767 2 11.434 2 10c0-3.866 3.582-7 8-7s8 3.134 8 7zM7 9H5v2h2V9zm8 0h-2v2h2V9zM9 9h2v2H9V9z" clipRule="evenodd" />
                                            </svg>
                                            <span 
                                                className={`${textSizeClasses.xs}`}
                                                style={{ color: engagementIconColor }}
                                            >
                                                {testimonial.comments}
                                            </span>
                                        </div>
                                    )}
                                </div>
                            )}
                        </div>
                    ))}
                </div>

                {/* Empty State */}
                {testimonials.length === 0 && (
                    <div className="text-center py-12 text-gray-400">
                        <p className="text-sm">No testimonials yet</p>
                    </div>
                )}
            </div>
        </section>
    );
};

export default TestimonialsThreeColumn;

