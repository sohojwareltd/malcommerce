# Shopify-Style Theme Builder Guide

## How to Use the Theme Builder

### Step 1: Access the Builder
1. Login as admin: `https://shop.test/admin/dashboard`
2. Go to **Products** → Click **Edit** on any product
3. Scroll down to **"Page Layout Builder"** section

### Step 2: Add Sections

#### 1. Rich Text Section
- Click **"Rich Text"** button
- Enter HTML content (you can use HTML tags)
- Example:
```html
<h2>Product Features</h2>
<ul>
    <li>Premium Quality Material</li>
    <li>30-Day Money Back Guarantee</li>
    <li>Free Shipping</li>
</ul>
```

#### 2. Image Gallery
- Click **"Image Gallery"** button
- Add image URLs (one per line):
```
https://example.com/image1.jpg
https://example.com/image2.jpg
https://example.com/image3.jpg
```

#### 3. FAQs Section
- Click **"FAQs"** button
- Click **"+ Add FAQ"** for each question
- Fill in Question and Answer fields
- Example:
  - Q: "What is the return policy?"
  - A: "We offer 30-day returns on all products."

#### 4. Testimonials
- Click **"Testimonials"** button
- Click **"+ Add Testimonial"** for each review
- Enter testimonial text and author name
- Example:
  - Text: "Amazing product! Highly recommend."
  - Author: "John Doe"

#### 5. Video Embed
- Click **"Video Embed"** button
- Enter video embed URL (YouTube, Vimeo, etc.)
- Example: `https://www.youtube.com/embed/VIDEO_ID`

### Step 3: Reorder Sections
- Drag sections using the ☰ icon on the left
- Sections will reorder automatically

### Step 4: Save
1. Click **"Save Layout"** button (saves the layout)
2. Click **"Update Product"** button (saves the entire product)

### Step 5: View on Frontend
- Visit the product page: `https://shop.test/products/{product-slug}`
- Custom sections appear below the product details

## Example: Complete Product Page Layout

Here's a typical layout structure:

1. **Rich Text** - Product Features
2. **Image Gallery** - Product in Use
3. **Video** - Product Demo
4. **Testimonials** - Customer Reviews
5. **FAQs** - Common Questions
6. **Rich Text** - Warranty Information

## Tips

- **Rich Text**: Use HTML for formatting (bold, lists, links, etc.)
- **Image Gallery**: Use full URLs to images (hosted on your server or CDN)
- **Video**: Use embed URLs, not regular YouTube links
- **Order Matters**: Arrange sections in the order you want them displayed
- **Save Often**: Click "Save Layout" before "Update Product"

## Technical Details

- Layout is stored as JSON in `products.page_layout` column
- Sections render on frontend via React component
- Fully responsive and mobile-friendly
- Supports unlimited sections per product


