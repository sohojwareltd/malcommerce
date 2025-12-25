import React from 'react';

/**
 * SECTION NAME: HeroPromoVideoSplit
 * 
 * SECTION PURPOSE: Hero section with promotional text content and embedded video,
 * designed for course/product launches with split layout (video on left, content on right).
 * Mobile-first: video stacks on top.
 */

const HeroPromoVideoSplit = ({ content, style }) => {
    const {
        headline = '',
        subtext = '',
        discountText = '',
        primaryButtonText = '',
        primaryButtonLink = '#',
        secondaryButtonText = '',
        secondaryButtonLink = '#',
        videoUrl = ''
    } = content;

    const {
        backgroundType = 'gradient', // 'color' | 'gradient' | 'image'
        backgroundColor = '#1e1b4b', // used when backgroundType is 'color'
        gradientStart = '#7c3aed', // purple
        gradientEnd = '#000000', // black
        backgroundImage = '', // used when backgroundType is 'image'
        
        textColor = '#ffffff',
        headingTag = 'h1', // 'h1' | 'h2' | 'h3' | 'h4'
        headingSize = 'xl', // 'sm' | 'md' | 'lg' | 'xl' | '2xl' | '3xl' | '4xl'
        subtextSize = 'base', // 'xs' | 'sm' | 'base' | 'lg' | 'xl'
        discountTextSize = 'lg', // 'sm' | 'base' | 'lg' | 'xl'
        
        primaryButtonBgColor = '#7c3aed',
        primaryButtonTextColor = '#ffffff',
        secondaryButtonBgColor = '#7c3aed',
        secondaryButtonTextColor = '#ffffff',
        
        alignment = 'left' // 'left' | 'center'
    } = style;

    // Generate background style
    const getBackgroundStyle = () => {
        if (backgroundType === 'gradient') {
            return {
                background: `linear-gradient(to right, ${gradientStart}, ${gradientEnd})`
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

    // Heading size mapping
    const headingSizeClasses = {
        sm: 'text-2xl md:text-3xl',
        md: 'text-3xl md:text-4xl',
        lg: 'text-4xl md:text-5xl',
        xl: 'text-5xl md:text-6xl',
        '2xl': 'text-6xl md:text-7xl',
        '3xl': 'text-7xl md:text-8xl',
        '4xl': 'text-8xl md:text-9xl'
    };

    // Subtext size mapping
    const subtextSizeClasses = {
        xs: 'text-xs md:text-sm',
        sm: 'text-sm md:text-base',
        base: 'text-base md:text-lg',
        lg: 'text-lg md:text-xl',
        xl: 'text-xl md:text-2xl'
    };

    // Discount text size mapping
    const discountSizeClasses = {
        sm: 'text-sm md:text-base',
        base: 'text-base md:text-lg',
        lg: 'text-lg md:text-xl',
        xl: 'text-xl md:text-2xl'
    };

    // Alignment classes
    const alignmentClasses = alignment === 'center' ? 'text-center' : 'text-left';

    // Render heading with dynamic tag
    const renderHeading = () => {
        const HeadingTag = headingTag;
        const className = `${headingSizeClasses[headingSize]} font-bold leading-tight mb-4 ${alignmentClasses}`;
        return (
            <HeadingTag style={{ color: textColor }} className={className}>
                {headline}
            </HeadingTag>
        );
    };

    // Extract YouTube video ID from URL
    const getYouTubeEmbedUrl = (url) => {
        if (!url) return '';
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        const videoId = (match && match[2].length === 11) ? match[2] : null;
        return videoId ? `https://www.youtube.com/embed/${videoId}` : url;
    };

    return (
        <section 
            className="w-full py-16 md:py-24"
            style={getBackgroundStyle()}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div className="grid grid-cols-1 lg:grid-cols-2 gap-8 lg:gap-12 items-center">
                    {/* Video Section - First on mobile, right on desktop */}
                    <div className="order-1 lg:order-2">
                        {videoUrl && (
                            <div className="relative w-full rounded-lg overflow-hidden border-2" style={{ borderColor: gradientStart }}>
                                <div className="aspect-video">
                                    <iframe
                                        src={getYouTubeEmbedUrl(videoUrl)}
                                        className="w-full h-full"
                                        frameBorder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowFullScreen
                                        title="Promotional Video"
                                    />
                                </div>
                            </div>
                        )}
                    </div>

                    {/* Content Section - Second on mobile, left on desktop */}
                    <div className="order-2 lg:order-1 space-y-6">
                        {/* Headline */}
                        {headline && renderHeading()}

                        {/* Subtext */}
                        {subtext && (
                            <p 
                                className={`${subtextSizeClasses[subtextSize]} ${alignmentClasses}`}
                                style={{ color: textColor }}
                            >
                                {subtext}
                            </p>
                        )}

                        {/* Discount Text */}
                        {discountText && (
                            <p 
                                className={`${discountSizeClasses[discountTextSize]} font-semibold ${alignmentClasses}`}
                                style={{ color: '#fbbf24' }}
                            >
                                {discountText}
                            </p>
                        )}

                        {/* Buttons */}
                        {(primaryButtonText || secondaryButtonText) && (
                            <div className={`flex flex-col sm:flex-row gap-4 ${alignment === 'center' ? 'justify-center' : 'justify-start'}`}>
                                {primaryButtonText && (
                                    <a
                                        href={primaryButtonLink}
                                        className="inline-block px-8 py-3 rounded-lg font-semibold text-base md:text-lg transition-transform hover:scale-105 text-center"
                                        style={{
                                            backgroundColor: primaryButtonBgColor,
                                            color: primaryButtonTextColor
                                        }}
                                    >
                                        {primaryButtonText}
                                    </a>
                                )}
                                {secondaryButtonText && (
                                    <a
                                        href={secondaryButtonLink}
                                        className="inline-block px-8 py-3 rounded-lg font-semibold text-base md:text-lg transition-transform hover:scale-105 text-center"
                                        style={{
                                            backgroundColor: secondaryButtonBgColor,
                                            color: secondaryButtonTextColor
                                        }}
                                    >
                                        {secondaryButtonText}
                                    </a>
                                )}
                            </div>
                        )}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default HeroPromoVideoSplit;

