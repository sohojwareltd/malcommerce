<div class="card card-hover group">
    <a href="{{ route('products.show', $product->slug) }}" class="block">
        <div class="relative overflow-hidden rounded-lg mb-4 bg-gray-100" style="aspect-ratio: 1/1;">
            <img 
                src="{{ $product->main_image ?? '/placeholder-product.jpg' }}" 
                alt="{{ $product->name }}" 
                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-300"
            >
            @if($product->compare_at_price && $product->compare_at_price > $product->price)
            <span class="absolute top-3 left-3 badge-sale font-bangla">
                {{ round((($product->compare_at_price - $product->price) / $product->compare_at_price) * 100) }}% ছাড়
            </span>
            @endif
        </div>
        <h3 class="font-semibold text-base md:text-lg mb-2 text-gray-900 line-clamp-2 min-h-[3rem]">{{ $product->name }}</h3>
        <div class="flex items-center gap-3 flex-wrap">
            <span class="text-lg md:text-xl font-bold" style="color: var(--color-primary);">৳{{ number_format($product->price, 0) }}</span>
            @if($product->compare_at_price && $product->compare_at_price > $product->price)
            <span class="text-sm text-gray-500 line-through">৳{{ number_format($product->compare_at_price, 0) }}</span>
            @endif
        </div>
    </a>
</div>
