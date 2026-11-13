<?php
namespace App\Support;

final class RichText
{
    public static function clean(?string $html): string
    {
        static $purifier;
        if (!$purifier) {
            $config = \HTMLPurifier_Config::createDefault();
            $config->set('Core.Encoding', 'UTF-8');
            $config->set('HTML.Allowed', 'p,div,br,strong,b,em,i,u,s,h2,h3,ul,ol,li,blockquote,a[href|title]');
            $config->set('URI.AllowedSchemes', ['http' => true, 'https' => true, 'mailto' => true, 'tel' => true]);
            $config->set('HTML.Nofollow', true);
            $config->set('Cache.SerializerPath', storage_path('framework/cache'));
            $purifier = new \HTMLPurifier($config);
        }
        return $purifier->purify(preg_replace('/<(\/?)h1\b/i', '<$1h2', $html ?? ''));
    }

    public static function html(?string $text, bool $headings = false): string
    {
        $text = trim($text ?? '');
        if ($text === '') return '';
        if (preg_match('/<\/?[a-z][^>]*>/i', $text)) return self::clean($text);
        return implode('', array_map(function ($paragraph) use ($headings) {
            $parts = explode("\n", $paragraph, 2);
            return $headings && count($parts) === 2
                ? '<h2>'.e($parts[0]).'</h2><p>'.nl2br(e($parts[1])).'</p>'
                : '<p>'.nl2br(e($paragraph)).'</p>';
        }, preg_split('/\n\s*\n/', $text)));
    }

    public static function render(?string $text, string $firstClass = '', bool $headings = false): string
    {
        $html = self::html($text, $headings);
        if ($html === '') return '';
        $document = new \DOMDocument('1.0', 'UTF-8');
        $previous = libxml_use_internal_errors(true);
        $document->loadHTML('<?xml encoding="UTF-8"><html><body>'.$html.'</body></html>', LIBXML_NONET);
        libxml_clear_errors(); libxml_use_internal_errors($previous);
        // Trix uses divs for paragraphs. Render them with the site's existing paragraph styles.
        $divs = iterator_to_array($document->getElementsByTagName('div'));
        foreach (array_reverse($divs) as $div) {
            $hasBlocks = (new \DOMXPath($document))->query('./p|./div|./h2|./h3|./ul|./ol|./blockquote', $div)->length > 0;
            if ($hasBlocks) {
                while ($div->firstChild) $div->parentNode->insertBefore($div->firstChild, $div);
                $div->parentNode->removeChild($div);
            } else {
                $paragraph = $document->createElement('p');
                while ($div->firstChild) $paragraph->appendChild($div->firstChild);
                $div->parentNode->replaceChild($paragraph, $div);
            }
        }
        $body = $document->getElementsByTagName('body')->item(0); $output = ''; $first = true;
        foreach ($body->childNodes as $node) {
            if ($node instanceof \DOMElement) {
                $node->setAttribute('class', trim('cms-copy '.($first ? $firstClass : '')));
                $first = false;
            }
            $output .= $document->saveHTML($node);
        }
        return $output;
    }
}
