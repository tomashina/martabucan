<?php
namespace App\Support;

use Illuminate\Http\UploadedFile;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

final class ImageAsset
{
    public static function url(?string $path): string
    {
        if (!$path) return '';
        return str_starts_with($path, '/') ? $path : '/assets/images/'.$path;
    }

    public static function allowed(?string $path): bool
    {
        $url = self::url($path);
        return (bool) preg_match('~^/assets/(?:images|uploads)/[a-zA-Z0-9_-]+\.(?:webp|png|jpe?g)$~', $url) && is_file(public_path($url));
    }

    public static function attributes(string $path): array
    {
        $url = self::url($path);
        $custom = str_starts_with($url, '/assets/uploads/');
        $base = preg_replace('/-1920\.webp$/', '', $url);
        $variants = $custom ? [$base.'-480.webp' => 480, $base.'-960.webp' => 960, $base.'-1920.webp' => 1920] : (config('cms_images', [])[$path] ?? []);
        $variants = array_filter($variants, fn ($width, $file) => is_file(public_path($file)), ARRAY_FILTER_USE_BOTH);
        if ($custom) {
            $actual = [];
            foreach ($variants as $file => $width) {
                $size = @getimagesize(public_path($file));
                if ($size) $actual[$size[0]] = $file;
            }
            $variants = array_flip($actual);
        }
        $dimensions = self::allowed($path) ? @getimagesize(public_path($url)) : false;
        return ['src' => $url, 'srcset' => implode(', ', array_map(fn ($file, $width) => $file.' '.$width.'w', array_keys($variants), $variants)), 'width' => $dimensions[0] ?? 1280, 'height' => $dimensions[1] ?? 853];
    }

    public static function upload(UploadedFile $file, string $field, array &$created): string
    {
        $info = @getimagesize($file->getRealPath());
        if (!$info || $info[0] * $info[1] > 24000000) throw ValidationException::withMessages([$field => 'Fotografija smije imati najviše 24 megapiksela.']);
        $image = @imagecreatefromstring(file_get_contents($file->getRealPath()));
        if (!$image) throw ValidationException::withMessages([$field => 'Fotografiju nije moguće otvoriti. Odaberite JPG, PNG ili WebP.']);
        try {
            if (($info['mime'] ?? '') === 'image/jpeg' && function_exists('exif_read_data')) {
                $orientation = @exif_read_data($file->getRealPath())['Orientation'] ?? 1;
                if (in_array($orientation, [2, 4, 5, 7])) imageflip($image, IMG_FLIP_HORIZONTAL);
                $angle = match ($orientation) {3, 4 => 180, 5, 6 => -90, 7, 8 => 90, default => 0};
                if ($angle) { $rotated = imagerotate($image, $angle, 0); imagedestroy($image); $image = $rotated; }
            }
            $directory = public_path('assets/uploads');
            if (!is_dir($directory)) mkdir($directory, 0755, true);
            $base = '/assets/uploads/'.Str::uuid();
            foreach ([480, 960, 1920] as $limit) {
                $width = min($limit, imagesx($image));
                $height = max(1, (int) round(imagesy($image) * $width / imagesx($image)));
                $resized = imagecreatetruecolor($width, $height);
                imagealphablending($resized, false); imagesavealpha($resized, true);
                imagecopyresampled($resized, $image, 0, 0, 0, 0, $width, $height, imagesx($image), imagesy($image));
                $destination = $base.'-'.$limit.'.webp';
                $ok = imagewebp($resized, public_path($destination), 86);
                imagedestroy($resized);
                if (!$ok) throw new \RuntimeException('Fotografiju nije moguće spremiti.');
                $created[] = public_path($destination);
            }
            return $base.'-1920.webp';
        } finally { imagedestroy($image); }
    }
}
