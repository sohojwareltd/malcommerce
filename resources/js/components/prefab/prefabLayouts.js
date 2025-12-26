// Prefab Layouts - Complete page templates for quick page creation

export const prefabLayouts = {
    product_landing: {
        name: 'Product Landing Page',
        description: 'Complete product landing page with hero, benefits, pricing, testimonials, and order form',
        sections: [
            {
                type: 'hero',
                title: 'Welcome to Our Amazing Product',
                subtitle: 'Discover the best solution for your needs',
                button_text: 'Order Now',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'benefits',
                title: 'Why Choose Us',
                items: [
                    {
                        title: 'Premium Quality',
                        description: 'Made with the finest materials and attention to detail'
                    },
                    {
                        title: 'Fast Delivery',
                        description: 'Quick and reliable shipping to your doorstep'
                    },
                    {
                        title: 'Great Support',
                        description: '24/7 customer service to help you anytime'
                    }
                ]
            },
            {
                type: 'pricing',
                title: 'Special Offer',
                original_price: '৳1,500',
                offer_price: '৳999',
                discount_text: 'Limited Time Offer - Save 33%',
                countdown_date: ''
            },
            {
                type: 'testimonials',
                title: 'What Our Customers Say',
                items: [
                    {
                        text: 'This product changed my life! Highly recommended.',
                        author: 'John Doe'
                    },
                    {
                        text: 'Excellent quality and great value for money.',
                        author: 'Jane Smith'
                    },
                    {
                        text: 'Best purchase I\'ve made this year!',
                        author: 'Mike Johnson'
                    }
                ]
            },
            {
                type: 'order_form',
                title: 'Order Now',
                content: 'Fill out the form below to place your order'
            }
        ]
    },
    
    sales_page: {
        name: 'Sales Page',
        description: 'High-converting sales page with hero, features, pricing, FAQs, and call-to-action',
        sections: [
            {
                type: 'hero',
                title: 'Transform Your Business Today',
                subtitle: 'Join thousands of satisfied customers who have already made the switch',
                button_text: 'Get Started',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'benefits',
                title: 'Key Features',
                items: [
                    {
                        title: 'Feature 1',
                        description: 'Detailed description of the first key feature'
                    },
                    {
                        title: 'Feature 2',
                        description: 'Detailed description of the second key feature'
                    },
                    {
                        title: 'Feature 3',
                        description: 'Detailed description of the third key feature'
                    }
                ]
            },
            {
                type: 'pricing',
                title: 'Choose Your Plan',
                original_price: '',
                offer_price: '৳2,499',
                discount_text: '',
                countdown_date: ''
            },
            {
                type: 'faq',
                title: 'Frequently Asked Questions',
                items: [
                    {
                        question: 'What is the return policy?',
                        answer: 'We offer a 30-day money-back guarantee on all products.'
                    },
                    {
                        question: 'How long does shipping take?',
                        answer: 'Standard shipping takes 3-5 business days.'
                    },
                    {
                        question: 'Do you offer customer support?',
                        answer: 'Yes, we provide 24/7 customer support via email and phone.'
                    }
                ]
            },
            {
                type: 'call_to_action',
                title: 'Ready to Get Started?',
                content: 'Join thousands of happy customers today!',
                button_text: 'Order Now',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            }
        ]
    },
    
    product_showcase: {
        name: 'Product Showcase',
        description: 'Beautiful product showcase with hero, image gallery, specifications, testimonials, and order form',
        sections: [
            {
                type: 'hero',
                title: 'Our Premium Product',
                subtitle: 'Experience the difference quality makes',
                button_text: 'View Gallery',
                button_link: '#gallery',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'image_gallery',
                title: 'Product Gallery',
                images: []
            },
            {
                type: 'specifications',
                title: 'Product Specifications',
                items: [
                    {
                        label: 'Material',
                        value: 'Premium Quality'
                    },
                    {
                        label: 'Dimensions',
                        value: '10" x 8" x 6"'
                    },
                    {
                        label: 'Weight',
                        value: '1.5 kg'
                    },
                    {
                        label: 'Warranty',
                        value: '1 Year'
                    }
                ]
            },
            {
                type: 'testimonials',
                title: 'Customer Reviews',
                items: [
                    {
                        text: 'Amazing product! Exceeded my expectations.',
                        author: 'Sarah Williams'
                    },
                    {
                        text: 'Great quality and fast delivery. Highly recommended!',
                        author: 'David Brown'
                    }
                ]
            },
            {
                type: 'order_form',
                title: 'Place Your Order',
                content: ''
            }
        ]
    },
    
    informational_page: {
        name: 'Informational Page',
        description: 'Informative page with hero, rich text content, benefits, and contact information',
        sections: [
            {
                type: 'hero',
                title: 'Welcome',
                subtitle: 'Learn more about what we offer',
                button_text: 'Learn More',
                button_link: '#content',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'rich_text',
                title: 'About Us',
                content: '<p>Welcome to our company! We are dedicated to providing the best products and services to our customers.</p><h3>Our Mission</h3><p>Our mission is to deliver exceptional quality and value in everything we do.</p>'
            },
            {
                type: 'benefits',
                title: 'Our Services',
                items: [
                    {
                        title: 'Service 1',
                        description: 'Description of our first service'
                    },
                    {
                        title: 'Service 2',
                        description: 'Description of our second service'
                    },
                    {
                        title: 'Service 3',
                        description: 'Description of our third service'
                    }
                ]
            },
            {
                type: 'contact_info',
                title: 'Contact Us',
                phone: '+880 1234 567890',
                email: 'info@example.com',
                address: '123 Business Street, City, Country',
                content: ''
            }
        ]
    },
    
    step_by_step_guide: {
        name: 'Step-by-Step Guide',
        description: 'Instructional page with hero, step-by-step guide, benefits, and CTA',
        sections: [
            {
                type: 'hero',
                title: 'How It Works',
                subtitle: 'Follow these simple steps to get started',
                button_text: 'Get Started',
                button_link: '#steps',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'steps',
                title: 'Simple Steps to Success',
                items: [
                    {
                        title: 'Step 1: Sign Up',
                        description: 'Create your account in just a few minutes'
                    },
                    {
                        title: 'Step 2: Choose Your Plan',
                        description: 'Select the plan that best fits your needs'
                    },
                    {
                        title: 'Step 3: Get Started',
                        description: 'Start using our service immediately'
                    }
                ]
            },
            {
                type: 'benefits',
                title: 'Why Follow This Process',
                items: [
                    {
                        title: 'Proven Method',
                        description: 'Tested and verified approach'
                    },
                    {
                        title: 'Easy to Follow',
                        description: 'Clear and simple instructions'
                    },
                    {
                        title: 'Quick Results',
                        description: 'See results in no time'
                    }
                ]
            },
            {
                type: 'call_to_action',
                title: 'Ready to Begin?',
                content: 'Start your journey today!',
                button_text: 'Get Started Now',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            }
        ]
    },
    
    comparison_page: {
        name: 'Comparison Page',
        description: 'Product comparison page with hero, comparison table, benefits, and CTA',
        sections: [
            {
                type: 'hero',
                title: 'Compare Our Products',
                subtitle: 'Find the perfect solution for your needs',
                button_text: 'View Comparison',
                button_link: '#comparison',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'comparison',
                title: 'Product Comparison',
                items: [
                    {
                        name: 'Basic Plan',
                        features: [
                            { label: 'Feature 1', value: 'Yes' },
                            { label: 'Feature 2', value: 'No' },
                            { label: 'Feature 3', value: 'Yes' }
                        ]
                    },
                    {
                        name: 'Premium Plan',
                        features: [
                            { label: 'Feature 1', value: 'Yes' },
                            { label: 'Feature 2', value: 'Yes' },
                            { label: 'Feature 3', value: 'Yes' }
                        ]
                    }
                ]
            },
            {
                type: 'benefits',
                title: 'Why Choose Premium',
                items: [
                    {
                        title: 'More Features',
                        description: 'Access to all premium features'
                    },
                    {
                        title: 'Better Support',
                        description: 'Priority customer support'
                    }
                ]
            },
            {
                type: 'call_to_action',
                title: 'Choose Your Plan',
                content: 'Select the plan that works best for you',
                button_text: 'Order Now',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            }
        ]
    },
    
    video_promo_page: {
        name: 'Video Promo Page',
        description: 'Video-focused page with hero, video embed, benefits, testimonials, and order form',
        sections: [
            {
                type: 'hero',
                title: 'Watch Our Product in Action',
                subtitle: 'See how our product can transform your business',
                button_text: 'Watch Video',
                button_link: '#video',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'video',
                title: 'Product Demonstration',
                url: ''
            },
            {
                type: 'benefits',
                title: 'What You\'ll Learn',
                items: [
                    {
                        title: 'Key Feature 1',
                        description: 'Learn about our first key feature'
                    },
                    {
                        title: 'Key Feature 2',
                        description: 'Discover our second key feature'
                    },
                    {
                        title: 'Key Feature 3',
                        description: 'Explore our third key feature'
                    }
                ]
            },
            {
                type: 'testimonials',
                title: 'What Customers Say',
                items: [
                    {
                        text: 'The video really helped me understand the product better!',
                        author: 'Customer Name'
                    }
                ]
            },
            {
                type: 'order_form',
                title: 'Ready to Get Started?',
                content: 'Place your order now'
            }
        ]
    },
    
    high_converting_sales: {
        name: 'High-Converting Sales Page',
        description: 'Professional sales page with hero, problem/solution, social proof, pricing with countdown, and multiple CTAs (like webkaku.com style)',
        sections: [
            {
                type: 'hero',
                title: 'গ্যারান্টি সহকারে খরচ কমান, বিক্রি বাড়ান!',
                subtitle: 'বিশ্ব ব্যাপি ১০০০০+ সফল ব্যবসায়ী দ্বারা প্রমানিত',
                button_text: 'এখনই জয়েন করুন',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'banner',
                title: 'প্রথম ১০০ জন ৯০% ছাড়ে',
                content: 'এখনই জয়েন করুন',
                background_color: '#FF0000',
                text_color: '#FFFFFF',
                background_image: ''
            },
            {
                type: 'benefits',
                title: '৯৫% উদ্যোক্তার কমন সমস্যা',
                items: [
                    {
                        title: 'বুস্ট করেও বিক্রি হচ্ছে না',
                        description: ''
                    },
                    {
                        title: 'দিন দিন ডলার খরচ বেড়ে যাচ্ছে',
                        description: ''
                    },
                    {
                        title: 'প্রচুর মেসেজ আসে কিন্তু, সেলস নাই',
                        description: ''
                    },
                    {
                        title: 'ল্যান্ডিং পেইজ আছে সেলস নাই',
                        description: ''
                    },
                    {
                        title: 'এজেন্সির কাছে গিয়ে প্রতারিত হচ্ছেন',
                        description: ''
                    },
                    {
                        title: 'কেন সেল হচ্ছে না, তা বুঝতে পারছেন না',
                        description: ''
                    },
                    {
                        title: 'বাজেট বাড়ালেই এড পার্ফর্মেন্স খারাপ হয় ও সেলস কমে যায়',
                        description: ''
                    }
                ]
            },
            {
                type: 'call_to_action',
                title: 'প্রথম ১০০ জন ৯০% ছাড়ে',
                content: '',
                button_text: 'এখনই জয়েন করুন',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            },
            {
                type: 'benefits',
                title: 'সেলস ইঞ্জিন = সমস্যার সমাধান',
                items: [
                    {
                        title: '২ থেকে ১০ গুন পর্যন্ত সেলস বাড়বে',
                        description: ''
                    },
                    {
                        title: 'ডলার খরচ কমবে কয়েকগুন',
                        description: ''
                    },
                    {
                        title: 'ম্যাসেজের প্যারা নাই, সেলস হবে অটো',
                        description: ''
                    },
                    {
                        title: 'কাস্টমার এটেনশন বাড়াতে পারবেন',
                        description: ''
                    },
                    {
                        title: 'এড পারফর্মেন্স আগের চেয়ে দিগুন বাড়বে',
                        description: ''
                    },
                    {
                        title: 'কিভাবে সেলস হয় সেই সাইকোলোজি বুঝতে পারবেন',
                        description: ''
                    },
                    {
                        title: 'বাজেট বাড়ালেও পারফর্মেন্স কমবে না',
                        description: ''
                    }
                ]
            },
            {
                type: 'testimonials',
                title: 'এই সিক্রেট ফলো করে যারা ইতিমধ্যে সফল হয়েছে',
                items: [
                    {
                        text: 'এই কোর্স আমার ব্যবসা সম্পূর্ণ বদলে দিয়েছে!',
                        author: 'সফল ব্যবসায়ী'
                    },
                    {
                        text: 'ডলার খরচ কমিয়ে সেলস কয়েকগুন বেড়েছে',
                        author: 'সফল ব্যবসায়ী'
                    },
                    {
                        text: 'এখন আমি আমার এড পারফর্মেন্স নিজেই বুঝতে পারি',
                        author: 'সফল ব্যবসায়ী'
                    }
                ]
            },
            {
                type: 'call_to_action',
                title: 'প্রথম ১০০ জন ৯০% ছাড়ে',
                content: '',
                button_text: 'এখনই জয়েন করুন',
                button_link: '#order',
                background_color: '#FF0000',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            },
            {
                type: 'rich_text',
                title: 'Sales Engine এ কি কি থাকছে?',
                content: '<h3>Myth Buster Bundle</h3><p>যে ১০টি ভুলের কারণে সেলস কমে যায় তা জানতে পারবেন, এবং কিভাবে নিজেই সেগুলো সমাধান করতে পারবেন তা হাতে-কলমে শিখতে পারবেন।</p><h3>Anatomy of Sales Page</h3><p>যে ৭ টি সিক্রেট ফলো করে সফল উদ্দোক্তাদের ২ থেকে ৭ গুন সেলস বৃদ্ধি পেয়েছে সেগুলো কিভাবে নিজের ব্যবসায় কাজে লাগাবেন তা শিখতে পারবেন।</p><h3>Type & Psychology of Sales Page</h3><p>আপনি জানবেন সফল ব্যবসায়ীরা কিভাবে কাস্টমার স্যাইকোলোজি হ্যাক করে ল্যান্ডিং পেইজকে সেলস পেইজে কনভার্ট করে সেলস বাড়ায়।</p>'
            },
            {
                type: 'rich_text',
                title: 'কোর্সের মডিউল',
                content: '<h3>মডিউল ১ঃ শুরু</h3><p>কোর্স ওভারভিউ ও ওয়েলকাম</p><h3>মডিউল ২ঃ Myth Buster</h3><p>যে ১০টি ভুলের কারণে সেলস কমে যায়</p><h3>মডিউল ৩ঃ Sales Page Anatomy</h3><p>Sales Page এর ৭টি সিক্রেট</p><h3>মডিউল ৪ঃ Psychology & Types</h3><p>কাস্টমার সাইকোলোজি ও Sales Page এর ধরন</p><h3>মডিউল ৫ঃ Funnel Mapping</h3><p>Squeeze Page, Lead Page, Sales Page এর ব্যবহার</p><h3>মডিউল ৬ঃ CRO হ্যাকস</h3><p>Conversion Rate Optimize করার গোপন কৌশল</p><h3>মডিউল ৭ঃ Sales Page Design</h3><p>কালার থিওরি, টাইপোগ্রাফি, Layout Design</p><h3>মডিউল ৮ঃ Practical Design</h3><p>Hero Section, Problem Section, Solution, Pricing, CTA Design</p>'
            },
            {
                type: 'pricing',
                title: 'বিশেষ অফার',
                original_price: '৳১০,০০০',
                offer_price: '৳১,০০০',
                discount_text: 'প্রথম ১০০ জন ৯০% ছাড়ে',
                countdown_date: ''
            },
            {
                type: 'call_to_action',
                title: '৯০% ছাড়ে এখনই জয়েন করুন',
                content: 'সীমিত সময়ের অফার, দেরি করবেন না',
                button_text: 'এখনই জয়েন করুন',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            },
            {
                type: 'faq',
                title: 'সচরাচর জিজ্ঞাসিত প্রশ্ন',
                items: [
                    {
                        question: 'কোর্সটি কতদিনের?',
                        answer: 'কোর্সটি লাইফটাইম এক্সেস সহ। আপনি যেকোন সময় দেখতে পারবেন।'
                    },
                    {
                        question: 'কোর্স সম্পন্ন করতে কতদিন লাগবে?',
                        answer: 'কোর্সটি সম্পন্ন করতে ৪-৬ সপ্তাহ সময় লাগতে পারে, তবে আপনি নিজের গতিতে শিখতে পারবেন।'
                    },
                    {
                        question: 'কোর্স শেষে সার্টিফিকেট পাবো?',
                        answer: 'হ্যাঁ, কোর্স সম্পন্ন করার পর আপনি একটি সার্টিফিকেট পাবেন।'
                    },
                    {
                        question: 'মাসিক পেমেন্ট করা যাবে?',
                        answer: 'বর্তমানে এককালীন পেমেন্ট গ্রহণ করা হচ্ছে।'
                    },
                    {
                        question: 'কোর্স আপডেট পাবো?',
                        answer: 'হ্যাঁ, কোর্সটি নিয়মিত আপডেট করা হয় এবং আপনি সব আপডেট বিনামূল্যে পাবেন।'
                    }
                ]
            },
            {
                type: 'order_form',
                title: 'এখনই অর্ডার করুন',
                content: 'নিচের ফর্মটি পূরণ করে অর্ডার সম্পন্ন করুন'
            },
            {
                type: 'call_to_action',
                title: 'এখনই শুরু করুন',
                content: 'আজই শুরু করুন আপনার সফলতার যাত্রা',
                button_text: 'এখনই জয়েন করুন',
                button_link: '#order',
                background_color: '#FF0000',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            }
        ]
    },
    
    course_sales_page: {
        name: 'Course/Educational Sales Page',
        description: 'Course sales page with hero, modules list, instructor info, testimonials, and enrollment form',
        sections: [
            {
                type: 'hero',
                title: 'আপনার ভবিষ্যৎ আজই শুরু করুন',
                subtitle: 'জানুন বিশ্বমানের কোর্সে যা আপনাকে সফল করবে',
                button_text: 'এখনই এনরোল করুন',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'rich_text',
                title: 'কোর্স সম্পর্কে',
                content: '<p>এই কোর্সে আপনি শিখবেন কিভাবে আপনার দক্ষতা উন্নত করতে হবে এবং ক্যারিয়ারে সফল হতে হবে।</p><h3>কোর্সের বিশেষত্ব</h3><ul><li>হাতে-কলমে শিখুন</li><li>লাইভ ক্লাস</li><li>প্রজেক্ট ভিত্তিক শিক্ষা</li><li>লাইফটাইম এক্সেস</li></ul>'
            },
            {
                type: 'tabs',
                title: 'কোর্স মডিউল',
                items: [
                    {
                        title: 'মডিউল ১',
                        content: '<h3>শুরুর দিকনির্দেশনা</h3><p>কোর্সের ভূমিকা ও প্রাথমিক ধারণা</p><ul><li>ভিডিও লেকচার: ৫টি</li><li>সময়: ২ ঘন্টা</li><li>অ্যাসাইনমেন্ট: ১টি</li></ul>'
                    },
                    {
                        title: 'মডিউল ২',
                        content: '<h3>মৌলিক ধারণা</h3><p>প্রাথমিক ধারণাগুলো বিস্তারিত শিখুন</p><ul><li>ভিডিও লেকচার: ৮টি</li><li>সময়: ৪ ঘন্টা</li><li>অ্যাসাইনমেন্ট: ২টি</li></ul>'
                    },
                    {
                        title: 'মডিউল ৩',
                        content: '<h3>এডভান্সড টপিক</h3><p>উন্নত বিষয়গুলো শিখুন</p><ul><li>ভিডিও লেকচার: ১০টি</li><li>সময়: ৬ ঘন্টা</li><li>প্রজেক্ট: ১টি</li></ul>'
                    }
                ]
            },
            {
                type: 'testimonials',
                title: 'শিক্ষার্থীদের মতামত',
                items: [
                    {
                        text: 'এই কোর্স আমার জীবন বদলে দিয়েছে। সব কিছু খুব পরিষ্কারভাবে শেখানো হয়েছে।',
                        author: 'রাহিম আহমেদ'
                    },
                    {
                        text: 'সেরা কোর্স! প্রশিক্ষক খুব ভালোভাবে বুঝিয়েছেন।',
                        author: 'ফাতেমা খাতুন'
                    },
                    {
                        text: 'অবশ্যই সুপারিশ করব এই কোর্সটি।',
                        author: 'করিম হাসান'
                    }
                ]
            },
            {
                type: 'pricing',
                title: 'কোর্স ফি',
                original_price: '৳৫,০০০',
                offer_price: '৳২,৯৯৯',
                discount_text: 'লিমিটেড টাইম অফার - ৪০% ছাড়',
                countdown_date: ''
            },
            {
                type: 'faq',
                title: 'সচরাচর জিজ্ঞাসিত প্রশ্ন',
                items: [
                    {
                        question: 'কোর্সটি অনলাইনে নাকি অফলাইনে?',
                        answer: 'কোর্সটি সম্পূর্ণ অনলাইন ভিত্তিক। আপনি বাড়ি থেকে শিখতে পারবেন।'
                    },
                    {
                        question: 'কোর্সের ডিউরেশন কত?',
                        answer: 'কোর্স সম্পন্ন করতে ৬-৮ সপ্তাহ সময় লাগতে পারে।'
                    },
                    {
                        question: 'সার্টিফিকেট পাবো?',
                        answer: 'হ্যাঁ, কোর্স সম্পন্ন করার পর আপনি একটি সার্টিফিকেট পাবেন।'
                    }
                ]
            },
            {
                type: 'order_form',
                title: 'এখনই এনরোল করুন',
                content: 'নিচের ফর্মটি পূরণ করে কোর্সে এনরোল করুন'
            }
        ]
    },
    
    faq_page: {
        name: 'FAQ Page',
        description: 'FAQ-focused page with hero, comprehensive FAQs, contact info, and CTA',
        sections: [
            {
                type: 'hero',
                title: 'Frequently Asked Questions',
                subtitle: 'Find answers to common questions',
                button_text: 'Contact Us',
                button_link: '#contact',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_bg_color: '#FFFFFF',
                button_text_color: '#008060',
                background_image: '',
                images: []
            },
            {
                type: 'faq',
                title: 'Common Questions',
                items: [
                    {
                        question: 'What is your return policy?',
                        answer: 'We offer a 30-day money-back guarantee on all products.'
                    },
                    {
                        question: 'How long does shipping take?',
                        answer: 'Standard shipping takes 3-5 business days. Express shipping is available for faster delivery.'
                    },
                    {
                        question: 'Do you ship internationally?',
                        answer: 'Yes, we ship to most countries worldwide. Shipping costs and delivery times vary by location.'
                    },
                    {
                        question: 'What payment methods do you accept?',
                        answer: 'We accept all major credit cards, PayPal, and cash on delivery (where available).'
                    },
                    {
                        question: 'How can I track my order?',
                        answer: 'Once your order ships, you will receive a tracking number via email.'
                    }
                ]
            },
            {
                type: 'contact_info',
                title: 'Still Have Questions?',
                phone: '+880 1234 567890',
                email: 'support@example.com',
                address: '123 Support Street, City, Country',
                content: '<p>Our support team is available 24/7 to help you with any questions.</p>'
            },
            {
                type: 'call_to_action',
                title: 'Ready to Get Started?',
                content: 'Browse our products and place your order today!',
                button_text: 'Shop Now',
                button_link: '#order',
                background_color: '#008060',
                text_color: '#FFFFFF',
                button_color: '#FFFFFF',
                background_image: ''
            }
        ]
    }
};

export default prefabLayouts;

