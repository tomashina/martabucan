@php
    // A new URL for every changed asset prevents old CMS styles from being reused.
    $cmsAsset = static fn (string $path): string => $path.'?v='.substr(hash_file('sha256', public_path($path)), 0, 16);
    $cmsStyles = [
        '/assets/vendor/fontawesome/css/fontawesome.min.css',
        '/assets/vendor/fontawesome/css/light.min.css',
        '/assets/css/site.css',
        '/assets/css/editorial.css',
    ];
    if ($editor ?? false) $cmsStyles[] = '/assets/vendor/trix/trix.css';
    $cmsStyles[] = '/assets/css/admin.css';
@endphp
@foreach($cmsStyles as $stylesheet)
<link rel="stylesheet" href="{{ $cmsAsset($stylesheet) }}">
@endforeach
@if($editor ?? false)
<script src="{{ $cmsAsset('/assets/js/admin.js') }}" defer></script>
<script src="{{ $cmsAsset('/assets/vendor/trix/trix.min.js') }}" defer></script>
@endif
