<?php

namespace App\Enums;

/**
 * SampleType enum represents the geometric type or specimen shape
 * used for physical testing. Using a dedicated enum avoids magic strings
 * across the codebase and enables strict type-checking.
 */
enum SampleType: string
{
    case Cube     = 'cube';
    case Cylinder = 'cylinder';
    case Dogbone  = 'dogbone';
    case Other    = 'other';

    /**
     * Returns a human-readable label for UI components.
     */
    public function label(): string
    {
        return match ($this) {
            self::Cube     => 'Cube',
            self::Cylinder => 'Cylinder',
            self::Dogbone  => 'Dogbone',
            self::Other    => 'Other',
        };
    }

    /**
     * Convenience helper that returns an associative array suitable for
     * Filament/Laravel form select options where the key is the enum value
     * and the value is the label.
     */
    public static function options(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->label();
        }

        return $options;
    }
}
