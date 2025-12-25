import React from 'react';

/**
 * SECTION NAME: YouTubeVideosThreeColumn
 * 
 * SECTION PURPOSE: Responsive YouTube video embed section displaying 3 videos in a row 
 * on desktop and stacked vertically on smaller devices. Perfect for client reviews, 
 * testimonials, or promotional video content.
 */

const YouTubeVideosThreeColumn = ({ content, style }) => {
    const {
        videos = []
    } = content;

    const {
        backgroundColor = '#ffffff',
        spacing = 'normal', // 'normal' | 'large'
        videoAspectRatio = '16:9', // '16:9' | '4:3' | '1:1'
        sectionPadding = 'normal', // 'normal' | 'large'
        showTitles = false,
        titleSize = 'base', // 'sm' | 'base' | 'lg' | 'xl'
        titleColor = '#1f2937',
        titleWeight = 'semibold' // 'normal' | 'semibold' | 'bold'
    } = style;

    // Extract YouTube video ID from URL
    const getYouTubeVideoId = (url) => {
        if (!url) return '';
        const regExp = /^.*(youtu.be\/|v\/|u\/\w\/|embed\/|watch\?v=|&v=)([^#&?]*).*/;
        const match = url.match(regExp);
        return (match && match[2].length === 11) ? match[2] : null;
    };

    // Get embed URL
    const getEmbedUrl = (url) => {
        const videoId = getYouTubeVideoId(url);
        if (!videoId) return '';
        return `https://www.youtube.com/embed/${videoId}`;
    };

    // Aspect ratio mapping
    const aspectRatioClasses = {
        '16:9': 'aspect-video',
        '4:3': 'aspect-[4/3]',
        '1:1': 'aspect-square'
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

    // Text size mapping
    const textSizeClasses = {
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

    // Limit to 3 videos
    const displayVideos = videos.slice(0, 3);

    return (
        <section 
            className={`w-full ${paddingClasses[sectionPadding]}`}
            style={{ backgroundColor }}
        >
            <div className="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
                <div 
                    className={`grid grid-cols-1 lg:grid-cols-3 ${spacingClasses[spacing]}`}
                >
                    {displayVideos.map((video, index) => {
                        const embedUrl = getEmbedUrl(video.url);
                        if (!embedUrl) return null;

                        return (
                            <div key={index} className="w-full">
                                {/* Video Embed */}
                                <div className={`relative w-full ${aspectRatioClasses[videoAspectRatio]} rounded-lg overflow-hidden shadow-lg`}>
                                    <iframe
                                        src={embedUrl}
                                        className="absolute inset-0 w-full h-full"
                                        frameBorder="0"
                                        allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture"
                                        allowFullScreen
                                        title={video.title || `Video ${index + 1}`}
                                    />
                                </div>

                                {/* Optional Title */}
                                {showTitles && video.title && (
                                    <div 
                                        className={`mt-3 ${textSizeClasses[titleSize]} ${fontWeightClasses[titleWeight]}`}
                                        style={{ color: titleColor }}
                                    >
                                        {video.title}
                                    </div>
                                )}
                            </div>
                        );
                    })}
                </div>

                {/* Empty State */}
                {displayVideos.length === 0 && (
                    <div className="text-center py-12 text-gray-400">
                        <p className="text-sm">No videos added yet</p>
                    </div>
                )}
            </div>
        </section>
    );
};

export default YouTubeVideosThreeColumn;

