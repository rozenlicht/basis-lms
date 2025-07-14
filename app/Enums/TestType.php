<?php

namespace App\Enums;

/**
 * TestType enum captures the mechanical test method that is applied to a sample.
 * Keeping this as an enum prevents invalid string values and simplifies
 * filtering/querying logic within the application.
 */
enum TestType: string
{
    case Tensile1D      = 'tensile_1d';
    case Tensile2D      = 'tensile_2d';
    case Compression    = 'compression';
    case HoleExpansion  = 'hole_expansion';
    case Other          = 'other';

    /**
     * Human-readable label for UI/select elements.
     */
    public function label(): string
    {
        return match ($this) {
            self::Tensile1D     => 'Tensile (1-D)',
            self::Tensile2D     => 'Tensile (2-D)',
            self::Compression   => 'Compression',
            self::HoleExpansion => 'Hole Expansion',
            self::Other         => 'Other',
        };
    }

    /**
     * Returns an associative array suitable for form select inputs.
     * Example: [ 'tensile_1d' => 'Tensile (1-D)', ... ]
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
