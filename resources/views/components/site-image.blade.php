@props(['path', 'alt' => '', 'sizes' => '100vw', 'loading' => 'lazy', 'priority' => 'auto'])
@php $image=\App\Support\ImageAsset::attributes($path); @endphp
<img src="{{ $image['src'] }}" @if($image['srcset']) srcset="{{ $image['srcset'] }}" @endif sizes="{{ $sizes }}" width="{{ $image['width'] }}" height="{{ $image['height'] }}" loading="{{ $loading }}" decoding="async" fetchpriority="{{ $priority }}" alt="{{ $alt }}" {{ $attributes }}>
