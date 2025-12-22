<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $babyCare = Category::where('slug', 'baby-care')->first();
        $health = Category::where('slug', 'health-wellness')->first();
        $personalCare = Category::where('slug', 'personal-care')->first();

        if (! $babyCare) {
            $babyCare = Category::create([
                'name' => 'Baby Care',
                'slug' => 'baby-care',
                'description' => 'Gentle care products crafted for babies.',
                'is_active' => true,
                'sort_order' => 2,
            ]);
        }

        if (! $health) {
            $health = Category::create([
                'name' => 'Health & Wellness',
                'slug' => 'health-wellness',
                'description' => 'Natural remedies, oils, and wellness products.',
                'is_active' => true,
                'sort_order' => 1,
            ]);
        }

        if (! $personalCare) {
            $personalCare = Category::create([
                'name' => 'Personal Care',
                'slug' => 'personal-care',
                'description' => 'Everyday personal care essentials.',
                'is_active' => true,
                'sort_order' => 3,
            ]);
        }

        $products = [
            [
                'category_id' => $babyCare->id,
                'name' => 'Tinibee Baby Oil',
                'slug' => Str::slug('Tinibee Baby Oil'),
                'description' => 'Lab-tested baby oil that helps relieve cough, cold, and congestion while strengthening bones.',
                'short_description' => 'Natural baby oil for cold, cough, and comfort.',
                'price' => 890,
                'compare_at_price' => 1290,
                'sku' => 'TB-BO-001',
                'stock_quantity' => 200,
                'in_stock' => true,
                'images' => [
                    'https://picsum.photos/seed/tinibee-hero/900/600',
                    'https://picsum.photos/seed/tinibee-bottle/900/600',
                    'https://picsum.photos/seed/tinibee-usage/900/600',
                ],
                'page_layout' => [
                    [
                        'type' => 'hero',
                        'title' => 'মাত্র ৭-১০ দিন ব্যবহারে শিশুর কফ, ঠান্ডা, সর্দি, কাশি দূর করবে ইনশাল্লাহ',
                        'subtitle' => 'ওষুধ সেবন ছাড়াই Tinibee Oil দিয়ে শিশুর কফ, ঠান্ডা, কাশি ও শ্বাসকষ্টে আরাম দিন',
                        'images' => ['https://picsum.photos/seed/tinibee-hero/900/600'],
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#FFFFFF',
                        'text_color' => '#1F2937',
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => '',
                        'content' => '',
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                    [
                        'type' => 'benefits',
                        'title' => 'Tinibee Oil এর উপকারিতা',
                        'items' => [
                            ['title' => '১. ঠান্ডা, সর্দি-কাশি ও শ্বাসকষ্ট দূর করে', 'description' => 'বুকের ভেতর স্বস্তি আনে এবং শ্বাসকষ্ট থেকে রক্ষা করে।'],
                            ['title' => '২. কফ সহজে বের করে', 'description' => 'শিশুর বুকের কফ নরম করে সহজে বের হতে সাহায্য করে।'],
                            ['title' => '৩. রোগ প্রতিরোধ ক্ষমতা বাড়ায়', 'description' => 'নিউমোনিয়া ও ঠান্ডার সমস্যা দূর করতে সহায়ক।'],
                            ['title' => '৪. হাড় মজবুত করে', 'description' => 'শিশুর হাড় ও পেশির গঠন শক্তিশালী করে।'],
                            ['title' => '৫. ঘুমের মান উন্নত করে', 'description' => 'কাশি বন্ধ করে শিশুকে আরামে ঘুমাতে সাহায্য করে।'],
                            ['title' => '৬. মসৃণ ম্যাসাজ', 'description' => 'ত্বক কোমল ও স্নিগ্ধ রাখে, ওষুধ খাওয়ানোর ঝামেলা নেই।'],
                            ['title' => '৭. ল্যাব টেস্টেড', 'description' => 'BCSIR ল্যাব টেস্টেড, বাচ্চার জন্য শতভাগ নিরাপদ।'],
                        ],
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => '',
                        'content' => '',
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                    [
                        'type' => 'steps',
                        'title' => 'এই তেল ব্যবহারের নিয়ম',
                        'items' => [
                            ['title' => 'ধাপ ১', 'description' => '৫-৬ ফোঁটা তেল হাতে নিন।'],
                            ['title' => 'ধাপ ২', 'description' => 'শিশুর বুকে, পিঠে এবং পায়ে ম্যাসাজ করুন।'],
                            ['title' => 'ধাপ ৩', 'description' => 'প্রতিদিন ২-৩ বার ৫-১০ মিনিট করে ম্যাসাজ করুন।'],
                        ],
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => '',
                        'content' => '',
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                    [
                        'type' => 'pricing',
                        'title' => '',
                        'original_price' => '৳1290.00',
                        'offer_price' => '৳890.00',
                        'discount_text' => 'আজকের অর্ডারে ফ্রি হোম ডেলিভারি',
                        'countdown_date' => now()->addDays(7)->format('Y-m-d\TH:i'),
                    ],
                    [
                        'type' => 'rich_text',
                        'title' => '',
                        'content' => '<div class="text-center py-4"><p class="text-lg font-semibold">২ টি তেল অর্ডার করলে পাচ্ছেন মাত্র ১৬৫০ টাকায়</p><p class="text-sm text-gray-600">আজকের অর্ডারে হোম ডেলিভারি ফ্রি</p></div>',
                    ],
                    [
                        'type' => 'rich_text',
                        'title' => '',
                        'content' => '<div class="text-center py-6 bg-green-50 rounded-lg"><p class="text-lg font-bold text-green-800">বাংলাদেশ বিজ্ঞান ও শিল্প গবেষণা পরিষদ (BCSIR) থেকে ল্যাব টেস্টেড</p><p class="text-sm text-green-700">বাচ্চার শরীরের জন্য শতভাগ নিরাপদ</p></div>',
                    ],
                    [
                        'type' => 'image_gallery',
                        'title' => 'Product Images',
                        'images' => [
                            'https://picsum.photos/seed/tinibee-bottle/900/600',
                            'https://picsum.photos/seed/tinibee-usage/900/600',
                            'https://picsum.photos/seed/tinibee-packaging/900/600',
                        ],
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => 'Place Your Order',
                        'content' => 'নিচে আপনার নাম, পূর্ণ ঠিকানা এবং মোবাইল নাম্বার লিখুন, তারপর Confirm Order ক্লিক করুন।',
                        'button_text' => 'Confirm Order',
                        'button_link' => '#order',
                        'background_color' => '#FFFFFF',
                        'text_color' => '#1F2937',
                    ],
                    [
                        'type' => 'social_links',
                        'title' => 'আমাদের সাথে যুক্ত থাকুন',
                        'items' => [
                            ['platform' => 'Facebook', 'url' => 'https://facebook.com/yourpage'],
                            ['platform' => 'WhatsApp', 'url' => 'https://wa.me/8801805417192'],
                            ['platform' => 'YouTube', 'url' => 'https://youtube.com/yourchannel'],
                            ['platform' => 'Instagram', 'url' => 'https://instagram.com/yourpage'],
                        ],
                    ],
                    [
                        'type' => 'contact_info',
                        'title' => 'কাস্টমার সাপোর্ট',
                        'phone' => '+880 1805-417192',
                        'email' => null,
                        'address' => null,
                    ],
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 1,
            ],
            [
                'category_id' => $health->id,
                'name' => 'Immunity Booster',
                'slug' => Str::slug('Immunity Booster'),
                'description' => 'Daily immunity booster with natural ingredients.',
                'short_description' => 'Natural immunity booster for the whole family.',
                'price' => 1250,
                'compare_at_price' => 1500,
                'sku' => 'IM-BO-001',
                'stock_quantity' => 150,
                'in_stock' => true,
                'images' => [
                    'https://picsum.photos/seed/immunity-hero/900/600',
                    'https://picsum.photos/seed/immunity-bottle/900/600',
                ],
                'page_layout' => [
                    [
                        'type' => 'hero',
                        'title' => 'Boost your family\'s immunity naturally',
                        'subtitle' => 'Daily immunity support with powerful natural ingredients.',
                        'images' => ['https://picsum.photos/seed/immunity-hero/900/600'],
                        'button_text' => 'Order Now',
                        'button_link' => '#order',
                        'background_color' => '#FFFFFF',
                        'text_color' => '#1F2937',
                    ],
                    [
                        'type' => 'benefits',
                        'title' => 'Key Benefits',
                        'items' => [
                            ['title' => '1. Stronger Immunity', 'description' => 'Helps your body fight against common illness.'],
                            ['title' => '2. Daily Energy', 'description' => 'Keeps you active and energetic throughout the day.'],
                            ['title' => '3. Natural Ingredients', 'description' => 'Made with carefully selected herbs and vitamins.'],
                        ],
                    ],
                    [
                        'type' => 'pricing',
                        'title' => 'Limited Time Offer',
                        'original_price' => '৳1500.00',
                        'offer_price' => '৳1250.00',
                        'discount_text' => 'Free home delivery on today\'s order',
                        'countdown_date' => now()->addDays(5)->format('Y-m-d\TH:i'),
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => 'Ready to order?',
                        'content' => 'Fill in your information below and confirm your order.',
                        'button_text' => 'Confirm Order',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 2,
            ],
            [
                'category_id' => $personalCare->id,
                'name' => 'Herbal Hair Oil',
                'slug' => Str::slug('Herbal Hair Oil'),
                'description' => 'Herbal hair oil that helps reduce hair fall, dandruff and promotes healthy hair growth.',
                'short_description' => 'Natural herbal oil for strong and shiny hair.',
                'price' => 990,
                'compare_at_price' => 1290,
                'sku' => 'HH-OL-001',
                'stock_quantity' => 120,
                'in_stock' => true,
                'images' => [
                    'https://picsum.photos/seed/hair-hero/900/600',
                    'https://picsum.photos/seed/hair-bottle/900/600',
                    'https://picsum.photos/seed/hair-ingredients/900/600',
                ],
                'page_layout' => [
                    [
                        'type' => 'hero',
                        'title' => 'Say goodbye to hair fall with Herbal Hair Oil',
                        'subtitle' => 'Blend of natural herbs to nourish your scalp and strengthen your hair from root to tip.',
                        'images' => ['https://picsum.photos/seed/hair-hero/900/600'],
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#FFFFFF',
                        'text_color' => '#1F2937',
                    ],
                    [
                        'type' => 'benefits',
                        'title' => 'Herbal Hair Oil Benefits',
                        'items' => [
                            ['title' => '1. Reduces Hair Fall', 'description' => 'Strengthens hair roots and reduces breakage.'],
                            ['title' => '2. Controls Dandruff', 'description' => 'Fights dandruff and keeps scalp clean.'],
                            ['title' => '3. Promotes Growth', 'description' => 'Stimulates hair follicles for better growth.'],
                        ],
                    ],
                    [
                        'type' => 'steps',
                        'title' => 'How to Use',
                        'items' => [
                            ['title' => 'Step 1', 'description' => 'Take enough oil on your palm.'],
                            ['title' => 'Step 2', 'description' => 'Gently massage on scalp for 5-10 minutes.'],
                            ['title' => 'Step 3', 'description' => 'Leave overnight and wash with mild shampoo.'],
                        ],
                    ],
                    [
                        'type' => 'pricing',
                        'title' => '',
                        'original_price' => '৳1290.00',
                        'offer_price' => '৳990.00',
                        'discount_text' => 'আজকের অর্ডারে ফ্রি হোম ডেলিভারি',
                        'countdown_date' => now()->addDays(3)->format('Y-m-d\TH:i'),
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => 'Order now for stronger hair',
                        'content' => '',
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                ],
                'is_active' => true,
                'is_featured' => true,
                'sort_order' => 3,
            ],
            [
                'category_id' => $health->id,
                'name' => 'Pain Relief Balm',
                'slug' => Str::slug('Pain Relief Balm'),
                'description' => 'Fast-acting balm for muscle pain, joint pain and headache relief.',
                'short_description' => 'Quick relief from everyday pain.',
                'price' => 450,
                'compare_at_price' => 600,
                'sku' => 'PR-BM-001',
                'stock_quantity' => 300,
                'in_stock' => true,
                'images' => [
                    'https://picsum.photos/seed/pain-hero/900/600',
                    'https://picsum.photos/seed/pain-pack/900/600',
                ],
                'page_layout' => [
                    [
                        'type' => 'hero',
                        'title' => 'Get instant relief from pain',
                        'subtitle' => 'Pain Relief Balm specially formulated for muscle and joint pain.',
                        'images' => ['https://picsum.photos/seed/pain-hero/900/600'],
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#FFFFFF',
                        'text_color' => '#1F2937',
                    ],
                    [
                        'type' => 'benefits',
                        'title' => 'Why choose Pain Relief Balm',
                        'items' => [
                            ['title' => '1. Fast Acting', 'description' => 'Gives quick relief after gentle massage.'],
                            ['title' => '2. Multi-purpose', 'description' => 'Suitable for muscle, joint and headache.'],
                            ['title' => '3. Non-greasy', 'description' => 'Absorbs quickly without sticky feeling.'],
                        ],
                    ],
                    [
                        'type' => 'pricing',
                        'title' => 'Special Offer',
                        'original_price' => '৳600.00',
                        'offer_price' => '৳450.00',
                        'discount_text' => 'Order 2 and get extra discount',
                        'countdown_date' => now()->addDays(4)->format('Y-m-d\TH:i'),
                    ],
                    [
                        'type' => 'call_to_action',
                        'title' => '',
                        'content' => '',
                        'button_text' => 'অর্ডার করুন',
                        'button_link' => '#order',
                        'background_color' => '#10B981',
                        'text_color' => '#FFFFFF',
                    ],
                ],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 4,
            ],
            [
                'category_id' => $babyCare->id,
                'name' => 'Baby Massage Oil',
                'slug' => Str::slug('Baby Massage Oil'),
                'description' => 'Gentle massage oil for baby\'s daily body massage and skin care.',
                'short_description' => 'Soft and nourishing massage oil for babies.',
                'price' => 790,
                'compare_at_price' => 950,
                'sku' => 'BM-OL-001',
                'stock_quantity' => 180,
                'in_stock' => true,
                'images' => [
                    'https://picsum.photos/seed/baby-oil-hero/900/600',
                    'https://picsum.photos/seed/baby-oil-bottle/900/600',
                ],
                // Empty layout so you can design it fully using ThemeBuilder
                'page_layout' => [],
                'is_active' => true,
                'is_featured' => false,
                'sort_order' => 5,
            ],
        ];

        foreach ($products as $product) {
            Product::updateOrCreate(
                ['slug' => $product['slug']],
                $product
            );
        }
    }
}

