import React, { useEffect, useRef } from 'react';

/**
 * SECTION NAME: LogoCarousel
 * 
 * SECTION PURPOSE: Horizontal auto-scrolling logo carousel displaying company/client 
 * logos for trust badges, partnerships, or social proof. Features infinite scroll 
 * animation and responsive horizontal scrolling on mobile.
 */

const LogoCarousel = ({ content, style }) => {
    const {
        logos = []
    } = content;

    const {
        backgroundColor = '#ffffff',
        columns = 6, // fixed number of columns on desktop
        spacing = 'normal', // 'normal' | 'large'
        logoMaxWidth = 'medium', // 'small' | 'medium' | 'large'
        autoScroll = true,
        scrollSpeed = 'normal', // 'slow' | 'normal' | 'fast'
        sectionPadding = 'normal' // 'normal' | 'large'
    } = style;

    const carouselRef = useRef(null);
    const scrollContainerRef = useRef(null);

    // Logo max width mapping
    const maxWidthClasses = {
        small: 'max-w-[120px]',
        medium: 'max-w-[150px]',
        large: 'max-w-[180px]'
    };

    // Spacing mapping
    const spacingClasses = {
        normal: 'gap-8 md:gap-12',
        large: 'gap-12 md:gap-16'
    };

    // Padding mapping
    const paddingClasses = {
        normal: 'py-12 md:py-16',
        large: 'py-16 md:py-24'
    };

    // Scroll speed mapping (pixels per frame)
    const scrollSpeeds = {
        slow: 0.5,
        normal: 1,
        fast: 1.5
    };

    // Auto-scroll animation (only on mobile/tablet, desktop uses grid)
    useEffect(() => {
        if (!autoScroll || logos.length === 0) return;

        const container = scrollContainerRef.current;
        if (!container) return;

        // Only auto-scroll on mobile/tablet (below lg breakpoint)
        const mediaQuery = window.matchMedia('(max-width: 1023px)');
        
        if (!mediaQuery.matches) {
            return; // Desktop uses grid, no auto-scroll needed
        }

        let animationFrameId;
        let scrollPosition = 0;
        const speed = scrollSpeeds[scrollSpeed];

        const animate = () => {
            if (!mediaQuery.matches) {
                // Stop animation if screen becomes desktop size
                if (animationFrameId) {
                    cancelAnimationFrame(animationFrameId);
                }
                return;
            }

            scrollPosition += speed;
            
            // Reset scroll position when it reaches the width of one set of logos
            const containerWidth = container.scrollWidth / 2; // Divide by 2 because we duplicate logos
            if (scrollPosition >= containerWidth) {
                scrollPosition = 0;
            }

            container.style.transform = `translateX(-${scrollPosition}px)`;
            animationFrameId = requestAnimationFrame(animate);
        };

        animate();

        const handleMediaChange = (e) => {
            if (!e.matches && animationFrameId) {
                cancelAnimationFrame(animationFrameId);
                container.style.transform = 'translateX(0)';
            }
        };

        mediaQuery.addEventListener('change', handleMediaChange);

        return () => {
            if (animationFrameId) {
                cancelAnimationFrame(animationFrameId);
            }
            mediaQuery.removeEventListener('change', handleMediaChange);
        };
    }, [autoScroll, logos.length, scrollSpeed]);

    // Duplicate logos for seamless infinite scroll (only needed for mobile auto-scroll)
    const duplicatedLogos = autoScroll ? [...logos, ...logos] : logos;

    return (
        <section 
            className={`w-full ${paddingClasses[sectionPadding]}`}
            style={{ backgroundColor }}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                {/* Desktop: Grid Layout */}
                <div className="hidden lg:grid lg:grid-cols-6 items-center justify-items-center"
                     style={{ 
                         gridTemplateColumns: `repeat(${columns}, 1fr)`,
                         gap: spacing === 'normal' ? '3rem' : '4rem'
                     }}>
                    {logos.map((logo, index) => (
                        <div
                            key={`${logo.imageUrl}-${index}`}
                            className={`flex items-center justify-center ${maxWidthClasses[logoMaxWidth]}`}
                        >
                            <img
                                src={logo.imageUrl}
                                alt={logo.altText || `Logo ${index + 1}`}
                                className="w-full h-auto object-contain opacity-80"
                                style={{ maxHeight: '80px' }}
                            />
                        </div>
                    ))}
                </div>

                {/* Mobile/Tablet: Horizontal Scroll */}
                <div 
                    ref={carouselRef}
                    className="lg:hidden overflow-x-auto overflow-y-hidden"
                    style={{
                        scrollbarWidth: 'none',
                        msOverflowStyle: 'none',
                        WebkitOverflowScrolling: 'touch'
                    }}
                >
                    <style>{`
                        .logo-carousel-mobile::-webkit-scrollbar {
                            display: none;
                        }
                    `}</style>
                    <div
                        ref={scrollContainerRef}
                        className={`logo-carousel-mobile flex ${spacingClasses[spacing]} items-center`}
                        style={{
                            width: autoScroll ? 'fit-content' : 'auto',
                            willChange: autoScroll ? 'transform' : 'auto'
                        }}
                    >
                        {duplicatedLogos.map((logo, index) => (
                            <div
                                key={`${logo.imageUrl}-${index}`}
                                className={`flex-shrink-0 flex items-center justify-center ${maxWidthClasses[logoMaxWidth]}`}
                            >
                                <img
                                    src={logo.imageUrl}
                                    alt={logo.altText || `Logo ${index + 1}`}
                                    className="w-full h-auto object-contain opacity-80"
                                    style={{ maxHeight: '80px' }}
                                />
                            </div>
                        ))}
                    </div>
                </div>
            </div>
        </section>
    );
};

export default LogoCarousel;

