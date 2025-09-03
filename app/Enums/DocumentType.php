<?php

namespace App\Enums;

/**
 * DocumentType enum represents the kind of document stored in the system.
 *
 * Having a dedicated enum avoids the use of magic strings throughout the codebase
 * and makes refactoring / type-checking easier.
 */
enum DocumentType: string
{
    case Drawing       = 'drawing';
    case Photo         = 'photo';
    case Specification = 'specification';
    case Micrograph    = 'micrograph';
    case Other         = 'other';

    /**
     * A human-readable label for UI purposes.
     */
    public function label(): string
    {
        return match ($this) {
            self::Drawing       => 'Drawing',
            self::Photo         => 'Photo',
            self::Specification => 'Specification',
            self::Micrograph    => 'Micrograph',
            self::Other         => 'Other',
        };
    }

    /**
     * Convenience method that returns an associative array suitable for form
     * select options where the key is the enum value and the value is the label.
     *
     * Example: [ 'drawing' => 'Drawing', 'photo' => 'Photo', ... ]
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
