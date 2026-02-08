<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Cache;

class SeoController extends Controller
{
    /**
     * Product feed as RSS 2.0 XML (Facebook Commerce / Google Merchant format).
     * Facebook requires RSS or ATOM with item nodes; uses g: namespace for product fields.
     */
    public function productsXml(Request $request)
    {
        $products = Product::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $siteName = \App\Models\Setting::get('site_name', config('app.name'));
        $baseUrl = rtrim(url('/'), '/');
        $feedUrl = url('/feed/products.xml');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<rss version="2.0" xmlns:g="http://base.google.com/ns/1.0" xmlns:atom="http://www.w3.org/2005/Atom">' . "\n";
        $xml .= "  <channel>\n";
        $xml .= '    <title>' . e($siteName) . " Product Catalog</title>\n";
        $xml .= '    <description>Product feed for ' . e($siteName) . "</description>\n";
        $xml .= '    <link>' . e($baseUrl) . "</link>\n";
        $xml .= '    <atom:link href="' . e($feedUrl) . '" rel="self" type="application/rss+xml" />' . "\n";

        foreach ($products as $product) {
            $productUrl = route('products.show', $product->slug);
            $imageUrl = $this->productImageUrl($product->main_image);
            $description = $product->short_description ?? $product->description ?? '';
            $description = strip_tags($description);
            $description = preg_replace('/\s+/', ' ', trim($description));
            $description = mb_substr($description, 0, 9999);

            $id = (string) $product->id;
            $price = number_format((float) $product->price, 2, '.', '') . ' BDT';
            $availability = $product->in_stock ? 'in stock' : 'out of stock';

            $xml .= "    <item>\n";
            $xml .= '      <g:id>' . e($id) . "</g:id>\n";
            $xml .= '      <g:title><![CDATA[' . $product->name . "]]></g:title>\n";
            $xml .= '      <g:description><![CDATA[' . $description . "]]></g:description>\n";
            $xml .= '      <g:link>' . e($productUrl) . "</g:link>\n";
            if ($imageUrl) {
                $xml .= '      <g:image_link>' . e($imageUrl) . "</g:image_link>\n";
            }
            $xml .= '      <g:brand>' . e($siteName) . "</g:brand>\n";
            $xml .= '      <g:condition>new</g:condition>' . "\n";
            $xml .= '      <g:availability>' . $availability . "</g:availability>\n";
            $xml .= '      <g:price>' . e($price) . "</g:price>\n";
            if ($product->compare_at_price && $product->compare_at_price > $product->price) {
                $salePrice = number_format((float) $product->price, 2, '.', '') . ' BDT';
                $xml .= '      <g:sale_price>' . e($salePrice) . "</g:sale_price>\n";
            }
            if (!empty($product->sku)) {
                $xml .= '      <g:gtin>' . e($product->sku) . "</g:gtin>\n";
            }
            $xml .= "    </item>\n";
        }

        $xml .= "  </channel>\n";
        $xml .= '</rss>';

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * Sitemap XML (site index) for SEO.
     */
    public function sitemap(Request $request)
    {
        $cacheKey = 'sitemap_xml';
        $xml = Cache::remember($cacheKey, 3600, function () {
            $baseUrl = rtrim(url('/'), '/');
            $products = Product::where('is_active', true)->orderBy('updated_at', 'desc')->get();

            $urls = [
                ['loc' => $baseUrl . '/', 'priority' => '1.0', 'changefreq' => 'daily', 'lastmod' => now()],
                ['loc' => $baseUrl . '/products', 'priority' => '0.9', 'changefreq' => 'daily', 'lastmod' => now()],
                ['loc' => $baseUrl . '/videos', 'priority' => '0.8', 'changefreq' => 'weekly', 'lastmod' => now()],
            ];

            foreach ($products as $product) {
                $urls[] = [
                    'loc' => route('products.show', $product->slug),
                    'priority' => '0.8',
                    'changefreq' => 'weekly',
                    'lastmod' => $product->updated_at,
                ];
            }

            $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">' . "\n";
            foreach ($urls as $u) {
                $lastmod = $u['lastmod'] instanceof \DateTimeInterface
                    ? $u['lastmod']->format('Y-m-d')
                    : $u['lastmod'];
                $xml .= "  <url>\n";
                $xml .= '    <loc>' . e($u['loc']) . "</loc>\n";
                $xml .= '    <lastmod>' . e($lastmod) . "</lastmod>\n";
                $xml .= '    <changefreq>' . e($u['changefreq']) . "</changefreq>\n";
                $xml .= '    <priority>' . e($u['priority']) . "</priority>\n";
                $xml .= "  </url>\n";
            }
            $xml .= '</urlset>';
            return $xml;
        });

        return response($xml, 200, [
            'Content-Type' => 'application/xml; charset=UTF-8',
            'Cache-Control' => 'public, max-age=3600',
        ]);
    }

    /**
     * robots.txt (dynamic so we can point to absolute sitemap URL).
     */
    public function robots()
    {
        $sitemapUrl = url('/sitemap.xml');
        $body = "User-agent: *\n";
        $body .= "Allow: /\n";
        $body .= "Disallow: /admin\n";
        $body .= "Disallow: /login\n";
        $body .= "Disallow: /register\n";
        $body .= "Disallow: /sponsor\n";
        $body .= "Sitemap: {$sitemapUrl}\n";

        return response($body, 200, [
            'Content-Type' => 'text/plain; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }

    private function productImageUrl($path)
    {
        if (empty($path)) {
            return null;
        }
        if (filter_var($path, FILTER_VALIDATE_URL)) {
            return $path;
        }
        if (str_starts_with($path, '/')) {
            return url($path);
        }
        return url('/storage/' . ltrim($path, '/'));
    }
}
