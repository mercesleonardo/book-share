<?php

namespace Tests\Unit;

use App\Enums\ModerationStatus;
use Tests\TestCase;

class ModerationStatusTest extends TestCase
{
    public function test_color_mapping(): void
    {
        $expected = [
            ModerationStatus::Pending->value  => 'bg-amber-100 text-amber-700 dark:bg-amber-600/20 dark:text-amber-300',
            ModerationStatus::Approved->value => 'bg-green-100 text-green-700 dark:bg-green-600/20 dark:text-green-300',
            ModerationStatus::Rejected->value => 'bg-red-100 text-red-700 dark:bg-red-600/20 dark:text-red-300',
            ModerationStatus::Flagged->value  => 'bg-yellow-100 text-yellow-700 dark:bg-yellow-600/20 dark:text-yellow-300',
        ];

        foreach ($expected as $value => $classes) {
            $status = ModerationStatus::from($value);
            $this->assertSame($classes, $status->color(), "Color classes mismatch for status {$value}");
        }
    }

    public function test_label_translates_in_locales(): void
    {
        $locales = [
            'en' => [
                'pending'  => 'Pending',
                'approved' => 'Approved',
                'rejected' => 'Rejected',
                'flagged'  => 'Flagged',
            ],
            'pt_BR' => [
                'pending'  => 'Pendente',
                'approved' => 'Aprovado',
                'rejected' => 'Rejeitado',
                'flagged'  => 'Sinalizado',
            ],
        ];

        foreach ($locales as $locale => $expected) {
            \Illuminate\Support\Facades\App::setLocale($locale);

            foreach ($expected as $value => $label) {
                $this->assertSame($label, ModerationStatus::from($value)->label(), "Label mismatch for {$value} in locale {$locale}");
            }
        }
    }
}
