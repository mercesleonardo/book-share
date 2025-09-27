<?php

namespace App\Enums;

enum ModerationStatus: string
{
    case Pending  = 'pending';
    case Approved = 'approved';
    case Rejected = 'rejected';
    case Flagged  = 'flagged';

    public function label(): string
    {
    // Uses translation files: resources/lang/{locale}/moderation.php
    // Fallback: returns the raw value if translation key is missing.
        $key = 'moderation.' . $this->value;
        $translated = __($key);

        return $translated === $key ? ucfirst($this->value) : $translated;
    }

    public function color(): string
    {
        return match ($this) {
            self::Pending  => 'bg-amber-100 text-amber-700 dark:bg-amber-600/20 dark:text-amber-300',
            self::Approved => 'bg-green-100 text-green-700 dark:bg-green-600/20 dark:text-green-300',
            self::Rejected => 'bg-red-100 text-red-700 dark:bg-red-600/20 dark:text-red-300',
            self::Flagged  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-600/20 dark:text-yellow-300',
        };
    }
}
