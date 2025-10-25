<?php

namespace App\Concerns;

trait SanitizesHtml
{
    public function sanitizeHtml(mixed $html, string $allowedTags = '<p><br><a><strong><em><ul><ol><li><b><i><u>'): mixed
    {
        if (! is_string($html)) {
            return $html;
        }

        return strip_tags($html, $allowedTags);
    }
}
