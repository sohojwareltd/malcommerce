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
     * Product feed as XML tree (for feeds, Facebook catalog, etc.)
     */
    public function productsXml(Request $request)
    {
        $products = Product::where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('created_at', 'desc')
            ->get();

        $siteName = \App\Models\Setting::get('site_name', config('app.name'));
        $baseUrl = url('/');

        $xml = '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
        $xml .= '<products xmlns="https://schema.org/Product" baseUrl="' . e($baseUrl) . '" siteName="' . e($siteName) . '" generated="' . now()->toIso8601String() . '">' . "\n";

        foreach ($products as $product) {
            $productUrl = route('products.show', $product->slug);
            $imageUrl = $this->productImageUrl($product->main_image);
            $description = $product->short_description ?? $product->description ?? '';
            $description = strip_tags($description);
            $description = preg_replace('/\s+/', ' ', $description);

            $xml .= "  <product>\n";
            $xml .= '    <id>' . (int) $product->id . "</id>\n";
            $xml .= '    <name><![CDATA[' . $product->name . "]]></name>\n";
            $xml .= '    <slug>' . e($product->slug) . "</slug>\n";
            $xml .= '    <url>' . e($productUrl) . "</url>\n";
            $xml .= '    <price>' . e((string) $product->price) . "</price>\n";
            $xml .= '    <currency>BDT</currency>' . "\n";
            if ($product->compare_at_price && $product->compare_at_price > $product->price) {
                $xml .= '    <compare_at_price>' . e((string) $product->compare_at_price) . "</compare_at_price>\n";
            }
            if ($imageUrl) {
                $xml .= '    <image>' . e($imageUrl) . "</image>\n";
            }
            $xml .= '    <description><![CDATA[' . $description . "]]></description>\n";
            $xml .= '    <sku>' . e($product->sku ?? '') . "</sku>\n";
            $xml .= '    <in_stock>' . ($product->in_stock ? '1' : '0') . "</in_stock>\n";
            $xml .= '    <updated_at>' . $product->updated_at->toIso8601String() . "</updated_at>\n";
            $xml .= "  </product>\n";
        }

        $xml .= '</products>';

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
