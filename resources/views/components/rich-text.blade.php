@props(['text' => '', 'firstClass' => '', 'headings' => false])
{!! \App\Support\RichText::render($text, $firstClass, $headings) !!}
