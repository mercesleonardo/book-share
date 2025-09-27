<?php

namespace App\Support;

class Markdown
{
    public static function toHtml(?string $text): string
    {
        if (!$text) {
            return '';
        }
        $escaped    = e($text);
        $escaped    = preg_replace('/`([^`]+)`/', '<code class="px-1 py-0.5 rounded bg-gray-100 dark:bg-gray-800 text-pink-600 dark:text-pink-400 text-sm">$1</code>', $escaped);
        $escaped    = preg_replace('/\*\*(.+?)\*\*/s', '<strong>$1</strong>', $escaped);
        $escaped    = preg_replace('/(?<!\*)\*(?!\*)(.+?)(?<!\*)\*(?!\*)/s', '<em>$1</em>', $escaped);
        $paragraphs = preg_split('/\n{2,}/', trim($escaped));
        $htmlParts  = [];

        foreach ($paragraphs as $p) {
            $p           = preg_replace("/\n/", '<br>', $p);
            $htmlParts[] = '<p>' . $p . '</p>';
        }

        return implode("\n", $htmlParts);
    }
}
