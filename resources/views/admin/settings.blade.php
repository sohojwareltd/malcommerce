@extends('layouts.admin')

@section('title', 'Settings')

@section('content')
<div class="mb-6">
    <h1 class="text-3xl font-bold">Settings</h1>
</div>

<form action="{{ route('admin.settings.update') }}" method="POST" class="space-y-8" id="settings-form">
    @csrf
    @method('POST')
    
    <!-- Tabs Navigation -->
    <div class="border-b border-neutral-200 mb-6">
        <nav class="flex space-x-8" role="tablist">
            <button type="button" onclick="switchTab('general')" id="tab-general" class="tab-button py-4 px-1 border-b-2 font-medium text-sm active" style="border-bottom-color: var(--color-primary); color: var(--color-primary);">
                General
            </button>
            <button type="button" onclick="switchTab('design')" id="tab-design" class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                Design
            </button>
            <button type="button" onclick="switchTab('home')" id="tab-home" class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                Home Page
            </button>
            <button type="button" onclick="switchTab('footer')" id="tab-footer" class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                Footer
            </button>
            <button type="button" onclick="switchTab('analytics')" id="tab-analytics" class="tab-button py-4 px-1 border-b-2 font-medium text-sm border-transparent text-neutral-500 hover:text-neutral-700 hover:border-neutral-300">
                Analytics
            </button>
        </nav>
    </div>
    
    <!-- General Settings Tab -->
    <div id="tab-content-general" class="tab-content">
    <!-- General Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">General Settings</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Site Name</label>
                <input type="text" name="site_name" value="{{ \App\Models\Setting::get('site_name', config('app.name')) }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Footer About</label>
                <textarea name="footer_about" rows="3" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ \App\Models\Setting::get('footer_about') }}</textarea>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Contact Email</label>
                <input type="email" name="contact_email" value="{{ \App\Models\Setting::get('contact_email') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Contact Phone</label>
                <input type="text" name="contact_phone" value="{{ \App\Models\Setting::get('contact_phone') }}" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Meta Description</label>
                <textarea name="meta_description" rows="3" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">{{ \App\Models\Setting::get('meta_description') }}</textarea>
                <p class="text-xs text-neutral-500 mt-1">Used for SEO and social media sharing</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Open Graph Image (OG Image)</label>
                <div class="flex gap-4 items-start">
                    <input type="text" name="og_image" value="{{ \App\Models\Setting::get('og_image') }}" placeholder="Image URL (recommended: 1200x630px)" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    <button type="button" onclick="uploadOgImage(this)" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 rounded-lg text-sm font-medium transition">Upload Image</button>
                </div>
                <p class="text-xs text-neutral-500 mt-1">Image shown when sharing on Facebook, Twitter, etc. Recommended size: 1200x630px. Must be JPG or PNG format.</p>
                @if(\App\Models\Setting::get('og_image'))
                <div class="mt-2">
                    <img src="{{ \App\Models\Setting::get('og_image') }}" alt="OG Image Preview" class="max-w-xs max-h-48 object-contain rounded-lg border border-neutral-300">
                </div>
                @endif
            </div>
        </div>
    </div>
    
    </div>
    
    <!-- Design Tab -->
    <div id="tab-content-design" class="tab-content" style="display: none;">
    <!-- Color Palette Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Color Palette</h2>
        <p class="text-sm text-neutral-600 mb-4">Set your brand colors. These will be used throughout the website.</p>
        @php
            $colorPalette = json_decode(\App\Models\Setting::get('color_palette', '{}'), true);
            $primaryColor = $colorPalette['primary'] ?? '#2563EB';
            $secondaryColor = $colorPalette['secondary'] ?? '#64748B';
            $accentColor = $colorPalette['accent'] ?? '#10B981';
        @endphp
        <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                    Primary Color
                    <span class="text-xs text-neutral-500 block">Main brand color for buttons, links, and CTAs</span>
                </label>
                <div class="flex gap-2 items-center">
                    <input type="color" 
                           id="primary-color" 
                           value="{{ $primaryColor }}" 
                           class="w-16 h-10 rounded border border-neutral-300 cursor-pointer">
                    <input type="text" 
                           name="color_palette[primary]" 
                           value="{{ $primaryColor }}" 
                           id="primary-color-text"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                    Secondary Color
                    <span class="text-xs text-neutral-500 block">Supporting color for secondary elements</span>
                </label>
                <div class="flex gap-2 items-center">
                    <input type="color" 
                           id="secondary-color" 
                           value="{{ $secondaryColor }}" 
                           class="w-16 h-10 rounded border border-neutral-300 cursor-pointer">
                    <input type="text" 
                           name="color_palette[secondary]" 
                           value="{{ $secondaryColor }}" 
                           id="secondary-color-text"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                </div>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">
                    Accent Color
                    <span class="text-xs text-neutral-500 block">Highlight color for special elements</span>
                </label>
                <div class="flex gap-2 items-center">
                    <input type="color" 
                           id="accent-color" 
                           value="{{ $accentColor }}" 
                           class="w-16 h-10 rounded border border-neutral-300 cursor-pointer">
                    <input type="text" 
                           name="color_palette[accent]" 
                           value="{{ $accentColor }}" 
                           id="accent-color-text"
                           pattern="^#[0-9A-Fa-f]{6}$"
                           class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                </div>
            </div>
        </div>
        <div class="mt-4 p-4 bg-neutral-50 rounded-lg">
            <p class="text-xs text-neutral-600">
                <strong>Preview:</strong>
                <span class="inline-block w-6 h-6 rounded ml-2" style="background-color: {{ $primaryColor }}"></span>
                <span class="inline-block w-6 h-6 rounded ml-2" style="background-color: {{ $secondaryColor }}"></span>
                <span class="inline-block w-6 h-6 rounded ml-2" style="background-color: {{ $accentColor }}"></span>
            </p>
        </div>
    </div>
    
    <!-- Marquee Banner Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Marquee Banner</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Marquee Text</label>
                <input type="text" name="marquee_text" value="{{ \App\Models\Setting::get('marquee_text', '') }}" placeholder="বিশেষ অফার! এখনই কিনুন" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">This text will scroll at the top of the page. Leave empty to hide the banner.</p>
            </div>
        </div>
    </div>
    
    <!-- Hero Slider Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Hero Slider</h2>
        <p class="text-sm text-neutral-600 mb-4">Manage your homepage hero slider slides. Add images, titles, subtitles, and links.</p>
        
        @php
            $sliderSlides = json_decode(\App\Models\Setting::get('hero_slider', '[]'), true);
            if (empty($sliderSlides)) {
                $sliderSlides = [[
                    'title' => '',
                    'subtitle' => '',
                    'cta' => '',
                    'link' => '',
                    'image' => ''
                ]];
            }
        @endphp
        
        <div id="slider-slides-container" class="space-y-4">
            @foreach($sliderSlides as $index => $slide)
                <div class="slider-slide-item border border-neutral-300 rounded-lg p-4 bg-neutral-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-neutral-700">Slide {{ $index + 1 }}</h3>
                        <button type="button" onclick="removeSliderSlide(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Title</label>
                            <input type="text" name="hero_slider[{{ $index }}][title]" value="{{ $slide['title'] ?? '' }}" placeholder="Slide Title" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Subtitle</label>
                            <input type="text" name="hero_slider[{{ $index }}][subtitle]" value="{{ $slide['subtitle'] ?? '' }}" placeholder="Slide Subtitle" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Button Text</label>
                            <input type="text" name="hero_slider[{{ $index }}][cta]" value="{{ $slide['cta'] ?? '' }}" placeholder="Button Text" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Link URL</label>
                            <input type="text" name="hero_slider[{{ $index }}][link]" value="{{ $slide['link'] ?? '' }}" placeholder="/products" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Background Image</label>
                            <div class="flex gap-4 items-start">
                                <input type="text" name="hero_slider[{{ $index }}][image]" value="{{ $slide['image'] ?? '' }}" placeholder="Image URL" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary image-url-input">
                                <button type="button" onclick="uploadSliderImage(this)" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 rounded-lg text-sm font-medium transition">Upload Image</button>
                            </div>
                            <p class="text-xs text-neutral-500 mt-1">Images will display at their original size</p>
                            <div class="image-preview mt-2" style="{{ !empty($slide['image']) ? '' : 'display: none;' }}">
                                @if(!empty($slide['image']))
                                    <img src="{{ $slide['image'] }}" alt="Slide Preview" class="w-full max-h-64 object-contain rounded-lg border border-neutral-300">
                                @else
                                    <img src="" alt="Slide Preview" class="w-full max-h-64 object-contain rounded-lg border border-neutral-300">
                                @endif
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Title Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="hero_slider[{{ $index }}][title_color]" value="{{ $slide['title_color'] ?? '#000000' }}" class="w-16 h-10 border border-neutral-300 rounded">
                                <input type="text" name="hero_slider[{{ $index }}][title_color_text]" value="{{ $slide['title_color'] ?? '#000000' }}" placeholder="#000000" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Subtitle Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="hero_slider[{{ $index }}][subtitle_color]" value="{{ $slide['subtitle_color'] ?? '#666666' }}" class="w-16 h-10 border border-neutral-300 rounded">
                                <input type="text" name="hero_slider[{{ $index }}][subtitle_color_text]" value="{{ $slide['subtitle_color'] ?? '#666666' }}" placeholder="#666666" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Button Background Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="hero_slider[{{ $index }}][button_bg_color]" value="{{ $slide['button_bg_color'] ?? '#2563EB' }}" class="w-16 h-10 border border-neutral-300 rounded">
                                <input type="text" name="hero_slider[{{ $index }}][button_bg_color_text]" value="{{ $slide['button_bg_color'] ?? '#2563EB' }}" placeholder="#2563EB" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Button Text Color</label>
                            <div class="flex gap-2">
                                <input type="color" name="hero_slider[{{ $index }}][button_text_color]" value="{{ $slide['button_text_color'] ?? '#FFFFFF' }}" class="w-16 h-10 border border-neutral-300 rounded">
                                <input type="text" name="hero_slider[{{ $index }}][button_text_color_text]" value="{{ $slide['button_text_color'] ?? '#FFFFFF' }}" placeholder="#FFFFFF" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                            </div>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Content Placement</label>
                            <select name="hero_slider[{{ $index }}][placement]" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                                <option value="center-center" {{ ($slide['placement'] ?? 'center-center') === 'center-center' ? 'selected' : '' }}>Center Center</option>
                                <option value="center-left" {{ ($slide['placement'] ?? '') === 'center-left' ? 'selected' : '' }}>Center Left</option>
                                <option value="center-right" {{ ($slide['placement'] ?? '') === 'center-right' ? 'selected' : '' }}>Center Right</option>
                                <option value="top-center" {{ ($slide['placement'] ?? '') === 'top-center' ? 'selected' : '' }}>Top Center</option>
                                <option value="top-left" {{ ($slide['placement'] ?? '') === 'top-left' ? 'selected' : '' }}>Top Left</option>
                                <option value="top-right" {{ ($slide['placement'] ?? '') === 'top-right' ? 'selected' : '' }}>Top Right</option>
                                <option value="bottom-center" {{ ($slide['placement'] ?? '') === 'bottom-center' ? 'selected' : '' }}>Bottom Center</option>
                                <option value="bottom-left" {{ ($slide['placement'] ?? '') === 'bottom-left' ? 'selected' : '' }}>Bottom Left</option>
                                <option value="bottom-right" {{ ($slide['placement'] ?? '') === 'bottom-right' ? 'selected' : '' }}>Bottom Right</option>
                            </select>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
        
        <button type="button" onclick="addSliderSlide()" class="mt-4 px-4 py-2 bg-primary text-white rounded-lg hover:bg-primary-light transition font-medium">
            + Add Slide
        </button>
    </div>
    </div>
    
    <!-- Home Page Tab -->
    <div id="tab-content-home" class="tab-content" style="display: none;">
    <!-- Home Features Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Home Features</h2>
        <p class="text-sm text-neutral-600 mb-4">Manage the features section displayed on the homepage. You can customize icons, titles, and descriptions for up to 4 features.</p>
        
        @php
            $homeFeatures = json_decode(\App\Models\Setting::get('home_features', '[]'), true);
            if (empty($homeFeatures)) {
                $homeFeatures = [
                    ['icon' => 'fas fa-truck', 'title' => 'ফ্রি ডেলিভারি', 'description' => 'সারা দেশে'],
                    ['icon' => 'fas fa-money-bill-wave', 'title' => 'ক্যাশ অন ডেলিভারি', 'description' => 'নিরাপদ পেমেন্ট'],
                    ['icon' => 'fas fa-shield-alt', 'title' => '৩০ দিন গ্যারান্টি', 'description' => 'মানি-ব্যাক'],
                    ['icon' => 'fas fa-headset', 'title' => '২৪/৭ সাপোর্ট', 'description' => 'সাহায্য পাওয়া যাবে'],
                ];
            }
            // Ensure we have exactly 4 features
            while (count($homeFeatures) < 4) {
                $homeFeatures[] = ['icon' => '', 'title' => '', 'description' => ''];
            }
            $homeFeatures = array_slice($homeFeatures, 0, 4);
        @endphp
        
        <div id="home-features-container" class="space-y-4">
            @foreach($homeFeatures as $index => $feature)
                <div class="home-feature-item border border-neutral-300 rounded-lg p-4 bg-neutral-50">
                    <div class="flex justify-between items-center mb-4">
                        <h3 class="font-semibold text-neutral-700">Feature {{ $index + 1 }}</h3>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Icon Class</label>
                            <input type="text" name="home_features[{{ $index }}][icon]" value="{{ $feature['icon'] ?? '' }}" placeholder="fas fa-truck" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <p class="text-xs text-neutral-500 mt-1">Font Awesome icon class (e.g., fas fa-truck)</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Title</label>
                            <input type="text" name="home_features[{{ $index }}][title]" value="{{ $feature['title'] ?? '' }}" placeholder="Feature Title" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Description</label>
                            <input type="text" name="home_features[{{ $index }}][description]" value="{{ $feature['description'] ?? '' }}" placeholder="Feature Description" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
    </div>
    
    <!-- Footer Tab -->
    <div id="tab-content-footer" class="tab-content" style="display: none;">
    <!-- Footer Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Footer Customization</h2>
        <p class="text-sm text-neutral-600 mb-6">Customize all footer sections including columns, links, and copyright text.</p>
        
        @php
            $footerSettings = json_decode(\App\Models\Setting::get('footer_settings', '{}'), true);
            
            // Default footer structure
            $defaultFooter = [
                'columns' => [
                    [
                        'title' => 'আমাদের সম্পর্কে',
                        'content' => 'আমাদের অনলাইন শপে আপনাকে স্বাগতম। আমরা গুণগত মানসম্পন্ন পণ্য সরবরাহ করি।',
                        'type' => 'text'
                    ],
                    [
                        'title' => 'দ্রুত লিংক',
                        'type' => 'links',
                        'links' => [
                            ['text' => 'হোম', 'url' => '/'],
                            ['text' => 'সব পণ্য', 'url' => '/products']
                        ]
                    ],
                    [
                        'title' => 'গ্রাহক সেবা',
                        'type' => 'service',
                        'items' => [
                            ['icon' => '📞', 'text' => '{{contact_phone}}'],
                            ['icon' => '✉️', 'text' => '{{contact_email}}'],
                            ['icon' => '🚚', 'text' => 'ফ্রি ডেলিভারি'],
                            ['icon' => '💳', 'text' => 'ক্যাশ অন ডেলিভারি']
                        ]
                    ],
                    [
                        'title' => 'আমাদের প্রতিশ্রুতি',
                        'type' => 'badges',
                        'items' => [
                            ['icon' => '✅', 'text' => '৩০ দিনের মানি-ব্যাক গ্যারান্টি'],
                            ['icon' => '✅', 'text' => '১০০% অরিজিনাল পণ্য'],
                            ['icon' => '✅', 'text' => 'নিরাপদ পেমেন্ট'],
                            ['icon' => '✅', 'text' => '২৪/৭ গ্রাহক সেবা']
                        ]
                    ]
                ],
                'copyright' => '&copy; {{year}} {{site_name}}। সর্বস্বত্ব সংরক্ষিত।'
            ];
            
            $footerData = array_merge($defaultFooter, $footerSettings);
            if (!isset($footerData['columns'])) {
                $footerData['columns'] = $defaultFooter['columns'];
            }
            if (!isset($footerData['copyright'])) {
                $footerData['copyright'] = $defaultFooter['copyright'];
            }
        @endphp
        
        <!-- Footer Columns -->
        <div class="mb-8">
            <h3 class="text-lg font-semibold mb-4">Footer Columns</h3>
            <p class="text-sm text-neutral-600 mb-4">You can customize up to 4 columns in the footer.</p>
            
            <div id="footer-columns-container" class="space-y-6">
                @for($i = 0; $i < 4; $i++)
                    @php
                        $column = $footerData['columns'][$i] ?? [
                            'title' => '',
                            'type' => 'text',
                            'content' => '',
                            'links' => [],
                            'items' => []
                        ];
                    @endphp
                    <div class="footer-column-item border border-neutral-300 rounded-lg p-4 bg-neutral-50">
                        <div class="flex justify-between items-center mb-4">
                            <h4 class="font-semibold text-neutral-700">Column {{ $i + 1 }}</h4>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Column Title</label>
                                <input type="text" name="footer_settings[columns][{{ $i }}][title]" value="{{ $column['title'] ?? '' }}" placeholder="Column Title" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-neutral-700 mb-2">Column Type</label>
                                <select name="footer_settings[columns][{{ $i }}][type]" class="column-type-select w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary" onchange="updateColumnType(this, {{ $i }})">
                                    <option value="text" {{ ($column['type'] ?? 'text') === 'text' ? 'selected' : '' }}>Text Content</option>
                                    <option value="links" {{ ($column['type'] ?? '') === 'links' ? 'selected' : '' }}>Links Menu</option>
                                    <option value="service" {{ ($column['type'] ?? '') === 'service' ? 'selected' : '' }}>Service Items</option>
                                    <option value="badges" {{ ($column['type'] ?? '') === 'badges' ? 'selected' : '' }}>Badges/Features</option>
                                </select>
                            </div>
                        </div>
                        
                        <!-- Text Content -->
                        <div class="column-content-text" style="{{ ($column['type'] ?? 'text') === 'text' ? '' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Content</label>
                            <textarea name="footer_settings[columns][{{ $i }}][content]" rows="4" placeholder="Enter column content" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">{{ $column['content'] ?? '' }}</textarea>
                            <p class="text-xs text-neutral-500 mt-1">You can use @{{ contact_phone }}, @{{ contact_email }}, @{{ site_name }} placeholders</p>
                        </div>
                        
                        <!-- Links -->
                        <div class="column-content-links" style="{{ ($column['type'] ?? '') === 'links' ? '' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Links</label>
                            <div class="footer-links-container space-y-2" data-column="{{ $i }}">
                                @if(isset($column['links']) && is_array($column['links']))
                                    @foreach($column['links'] as $linkIndex => $link)
                                        <div class="flex gap-2 footer-link-item">
                                            <input type="text" name="footer_settings[columns][{{ $i }}][links][{{ $linkIndex }}][text]" value="{{ $link['text'] ?? '' }}" placeholder="Link Text" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                                            <input type="text" name="footer_settings[columns][{{ $i }}][links][{{ $linkIndex }}][url]" value="{{ $link['url'] ?? '' }}" placeholder="/url" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                                            <button type="button" onclick="removeFooterLink(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" onclick="addFooterLink({{ $i }})" class="mt-2 px-4 py-2 bg-neutral-200 hover:bg-neutral-300 rounded-lg text-sm font-medium transition">+ Add Link</button>
                        </div>
                        
                        <!-- Service Items / Badges -->
                        <div class="column-content-service column-content-badges" style="{{ ($column['type'] ?? '') === 'service' || ($column['type'] ?? '') === 'badges' ? '' : 'display: none;' }}">
                            <label class="block text-sm font-medium text-neutral-700 mb-2">Items</label>
                            <div class="footer-items-container space-y-2" data-column="{{ $i }}">
                                @if(isset($column['items']) && is_array($column['items']))
                                    @foreach($column['items'] as $itemIndex => $item)
                                        <div class="flex gap-2 footer-item-item">
                                            <input type="text" name="footer_settings[columns][{{ $i }}][items][{{ $itemIndex }}][icon]" value="{{ $item['icon'] ?? '' }}" placeholder="Icon (📞, ✅, etc.)" class="w-24 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                                            <input type="text" name="footer_settings[columns][{{ $i }}][items][{{ $itemIndex }}][text]" value="{{ $item['text'] ?? '' }}" placeholder="Item Text" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                                            <button type="button" onclick="removeFooterItem(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                            <button type="button" onclick="addFooterItem({{ $i }})" class="mt-2 px-4 py-2 bg-neutral-200 hover:bg-neutral-300 rounded-lg text-sm font-medium transition">+ Add Item</button>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
        
        <!-- Copyright -->
        <div class="mb-6">
            <h3 class="text-lg font-semibold mb-4">Copyright Text</h3>
            <input type="text" name="footer_settings[copyright]" value="{{ $footerData['copyright'] ?? '' }}" placeholder="&copy; @{{ year }} @{{ site_name }}। সর্বস্বত্ব সংরক্ষিত।" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
            <p class="text-xs text-neutral-500 mt-1">Use @{{ year }} for current year and @{{ site_name }} for site name</p>
        </div>
    </div>
    </div>
    
    <!-- Analytics Tab -->
    <div id="tab-content-analytics" class="tab-content" style="display: none;">
    <!-- Analytics Settings -->
    <div class="bg-white rounded-lg shadow-md p-6">
        <h2 class="text-xl font-bold mb-4">Analytics</h2>
        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Facebook Pixel ID</label>
                <input type="text" name="fb_pixel_id" value="{{ \App\Models\Setting::get('fb_pixel_id') }}" placeholder="123456789012345" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Enter your Facebook Pixel ID</p>
            </div>
            <div>
                <label class="block text-sm font-medium text-neutral-700 mb-2">Google Analytics Tracking ID</label>
                <input type="text" name="ga_tracking_id" value="{{ \App\Models\Setting::get('ga_tracking_id') }}" placeholder="G-XXXXXXXXXX" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <p class="text-xs text-neutral-500 mt-1">Enter your Google Analytics tracking ID</p>
            </div>
        </div>
    </div>
    </div>
    
    <div>
        <button type="submit" class="bg-primary text-white px-6 py-2 rounded-lg hover:bg-primary-light transition">
            Save Settings
        </button>
    </div>
</form>

@push('scripts')
<script>
    // Tab switching functionality
    function switchTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.style.display = 'none';
        });
        
        // Remove active class from all tabs
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('active');
            button.style.borderBottomColor = 'transparent';
            button.style.color = '';
        });
        
        // Show selected tab content
        const selectedContent = document.getElementById('tab-content-' + tabName);
        if (selectedContent) {
            selectedContent.style.display = 'block';
        }
        
        // Add active class to selected tab
        const selectedTab = document.getElementById('tab-' + tabName);
        if (selectedTab) {
            selectedTab.classList.add('active');
            selectedTab.style.borderBottomColor = 'var(--color-primary)';
            selectedTab.style.color = 'var(--color-primary)';
        }
    }
    
    // Sync color picker with text input
    document.getElementById('primary-color').addEventListener('input', function(e) {
        document.getElementById('primary-color-text').value = e.target.value.toUpperCase();
    });
    document.getElementById('primary-color-text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('primary-color').value = e.target.value;
        }
    });
    
    document.getElementById('secondary-color').addEventListener('input', function(e) {
        document.getElementById('secondary-color-text').value = e.target.value.toUpperCase();
    });
    document.getElementById('secondary-color-text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('secondary-color').value = e.target.value;
        }
    });
    
    document.getElementById('accent-color').addEventListener('input', function(e) {
        document.getElementById('accent-color-text').value = e.target.value.toUpperCase();
    });
    document.getElementById('accent-color-text').addEventListener('input', function(e) {
        if (/^#[0-9A-Fa-f]{6}$/.test(e.target.value)) {
            document.getElementById('accent-color').value = e.target.value;
        }
    });
    
    // Slider Management
    let slideCounter = {{ count($sliderSlides) }};
    
    function addSliderSlide() {
        const container = document.getElementById('slider-slides-container');
        const slideHtml = `
            <div class="slider-slide-item border border-neutral-300 rounded-lg p-4 bg-neutral-50">
                <div class="flex justify-between items-center mb-4">
                    <h3 class="font-semibold text-neutral-700">Slide ${slideCounter + 1}</h3>
                    <button type="button" onclick="removeSliderSlide(this)" class="text-red-600 hover:text-red-800 text-sm font-medium">Remove</button>
                </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Title</label>
                        <input type="text" name="hero_slider[${slideCounter}][title]" placeholder="Slide Title" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Subtitle</label>
                        <input type="text" name="hero_slider[${slideCounter}][subtitle]" placeholder="Slide Subtitle" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Button Text</label>
                        <input type="text" name="hero_slider[${slideCounter}][cta]" placeholder="Button Text" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Link URL</label>
                        <input type="text" name="hero_slider[${slideCounter}][link]" placeholder="/products" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Background Image</label>
                        <div class="flex gap-4 items-start">
                            <input type="text" name="hero_slider[${slideCounter}][image]" placeholder="Image URL" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <button type="button" onclick="uploadSliderImage(this)" class="px-4 py-2 bg-neutral-200 hover:bg-neutral-300 rounded-lg text-sm font-medium transition">Upload Image</button>
                        </div>
                        <p class="text-xs text-neutral-500 mt-1">Images will display at their original size</p>
                        <div class="image-preview mt-2" style="display: none;">
                            <img src="" alt="Slide Preview" class="w-full max-h-64 object-contain rounded-lg border border-neutral-300">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Title Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="hero_slider[${slideCounter}][title_color]" value="#000000" class="w-16 h-10 border border-neutral-300 rounded">
                            <input type="text" name="hero_slider[${slideCounter}][title_color_text]" value="#000000" placeholder="#000000" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Subtitle Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="hero_slider[${slideCounter}][subtitle_color]" value="#666666" class="w-16 h-10 border border-neutral-300 rounded">
                            <input type="text" name="hero_slider[${slideCounter}][subtitle_color_text]" value="#666666" placeholder="#666666" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Button Background Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="hero_slider[${slideCounter}][button_bg_color]" value="#2563EB" class="w-16 h-10 border border-neutral-300 rounded">
                            <input type="text" name="hero_slider[${slideCounter}][button_bg_color_text]" value="#2563EB" placeholder="#2563EB" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Button Text Color</label>
                        <div class="flex gap-2">
                            <input type="color" name="hero_slider[${slideCounter}][button_text_color]" value="#FFFFFF" class="w-16 h-10 border border-neutral-300 rounded">
                            <input type="text" name="hero_slider[${slideCounter}][button_text_color_text]" value="#FFFFFF" placeholder="#FFFFFF" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-mono text-sm">
                        </div>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-neutral-700 mb-2">Content Placement</label>
                        <select name="hero_slider[${slideCounter}][placement]" class="w-full px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                            <option value="center-center">Center Center</option>
                            <option value="center-left">Center Left</option>
                            <option value="center-right">Center Right</option>
                            <option value="top-center">Top Center</option>
                            <option value="top-left">Top Left</option>
                            <option value="top-right">Top Right</option>
                            <option value="bottom-center">Bottom Center</option>
                            <option value="bottom-left">Bottom Left</option>
                            <option value="bottom-right">Bottom Right</option>
                        </select>
                    </div>
                </div>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', slideHtml);
        slideCounter++;
    }
    
    function removeSliderSlide(button) {
        button.closest('.slider-slide-item').remove();
    }
    
    function uploadSliderImage(button) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/*';
        input.onchange = async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            const formData = new FormData();
            formData.append('image', file);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const response = await fetch('/admin/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                
                const data = await response.json();
                if (data.success) {
                    const slideItem = button.closest('.slider-slide-item');
                    const imageInput = slideItem.querySelector('input[name*="[image]"]');
                    const previewDiv = slideItem.querySelector('.image-preview');
                    const previewImg = previewDiv.querySelector('img');
                    
                    imageInput.value = data.url;
                    previewImg.src = data.url;
                    previewDiv.style.display = 'block';
                } else {
                    alert('Failed to upload image');
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Error uploading image');
            }
        };
        input.click();
    }
    
    function uploadOgImage(button) {
        const input = document.createElement('input');
        input.type = 'file';
        input.accept = 'image/jpeg,image/png,image/jpg';
        input.onchange = async function(e) {
            const file = e.target.files[0];
            if (!file) return;
            
            // Validate file type
            if (!file.type.match(/^image\/(jpeg|jpg|png)$/)) {
                alert('Please upload a JPG or PNG image. Facebook cannot process other formats.');
                return;
            }
            
            const formData = new FormData();
            formData.append('image', file);
            
            const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
            try {
                const response = await fetch('/admin/upload-image', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                    },
                    body: formData,
                });
                
                const data = await response.json();
                if (data.success) {
                    const ogImageInput = document.querySelector('input[name="og_image"]');
                    ogImageInput.value = data.url;
                    
                    // Update preview if exists
                    const previewContainer = ogImageInput.closest('div').nextElementSibling;
                    if (previewContainer && previewContainer.tagName === 'DIV') {
                        if (previewContainer.querySelector('img')) {
                            previewContainer.querySelector('img').src = data.url;
                        } else {
                            const img = document.createElement('img');
                            img.src = data.url;
                            img.alt = 'OG Image Preview';
                            img.className = 'max-w-xs max-h-48 object-contain rounded-lg border border-neutral-300';
                            previewContainer.appendChild(img);
                        }
                        previewContainer.style.display = 'block';
                    }
                } else {
                    alert('Failed to upload image: ' + (data.message || 'Unknown error'));
                }
            } catch (error) {
                console.error('Upload error:', error);
                alert('Error uploading image');
            }
        };
        input.click();
    }
    
    // Sync color pickers with text inputs
    document.addEventListener('DOMContentLoaded', function() {
        // Sync all color pickers
        document.querySelectorAll('input[type="color"]').forEach(colorPicker => {
            const textInput = colorPicker.parentElement.querySelector('input[type="text"]');
            if (textInput) {
                colorPicker.addEventListener('input', function() {
                    textInput.value = this.value.toUpperCase();
                });
                textInput.addEventListener('input', function() {
                    if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
                        colorPicker.value = this.value;
                    }
                });
            }
        });
        
        // Update image preview when image URL changes
        document.querySelectorAll('input[name*="[image]"]').forEach(input => {
            input.addEventListener('input', function() {
                const slideItem = this.closest('.slider-slide-item');
                const previewDiv = slideItem.querySelector('.image-preview');
                const previewImg = previewDiv ? previewDiv.querySelector('img') : null;
                if (previewImg && this.value) {
                    previewImg.src = this.value;
                    if (previewDiv) previewDiv.style.display = 'block';
                } else if (previewDiv) {
                    previewDiv.style.display = 'none';
                }
            });
        });
    });
    
    // Footer Column Management
    function updateColumnType(select, columnIndex) {
        const columnItem = select.closest('.footer-column-item');
        const type = select.value;
        
        // Hide all content types
        columnItem.querySelectorAll('.column-content-text, .column-content-links, .column-content-service, .column-content-badges').forEach(el => {
            el.style.display = 'none';
        });
        
        // Show appropriate content type
        if (type === 'text') {
            const textContent = columnItem.querySelector('.column-content-text');
            if (textContent) textContent.style.display = 'block';
        } else if (type === 'links') {
            const linksContent = columnItem.querySelector('.column-content-links');
            if (linksContent) linksContent.style.display = 'block';
        } else if (type === 'service' || type === 'badges') {
            const serviceBadges = columnItem.querySelector('.column-content-service.column-content-badges');
            if (serviceBadges) serviceBadges.style.display = 'block';
        }
    }
    
    function addFooterLink(columnIndex) {
        const container = document.querySelector(`.footer-links-container[data-column="${columnIndex}"]`);
        const linkCount = container.querySelectorAll('.footer-link-item').length;
        const linkHtml = `
            <div class="flex gap-2 footer-link-item">
                <input type="text" name="footer_settings[columns][${columnIndex}][links][${linkCount}][text]" placeholder="Link Text" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                <input type="text" name="footer_settings[columns][${columnIndex}][links][${linkCount}][url]" placeholder="/url" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <button type="button" onclick="removeFooterLink(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', linkHtml);
    }
    
    function removeFooterLink(button) {
        button.closest('.footer-link-item').remove();
    }
    
    function addFooterItem(columnIndex) {
        const container = document.querySelector(`.footer-items-container[data-column="${columnIndex}"]`);
        const itemCount = container.querySelectorAll('.footer-item-item').length;
        const itemHtml = `
            <div class="flex gap-2 footer-item-item">
                <input type="text" name="footer_settings[columns][${columnIndex}][items][${itemCount}][icon]" placeholder="Icon (📞, ✅)" class="w-24 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary">
                <input type="text" name="footer_settings[columns][${columnIndex}][items][${itemCount}][text]" placeholder="Item Text" class="flex-1 px-4 py-2 border border-neutral-300 rounded-lg focus:ring-2 focus:ring-primary font-bangla">
                <button type="button" onclick="removeFooterItem(this)" class="px-3 py-2 bg-red-100 text-red-600 rounded-lg hover:bg-red-200">Remove</button>
            </div>
        `;
        container.insertAdjacentHTML('beforeend', itemHtml);
    }
    
    function removeFooterItem(button) {
        button.closest('.footer-item-item').remove();
    }
</script>
@endpush
@endsection

