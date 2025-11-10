<?php

namespace App\Enums;

enum Measurement: string
{
    case LOT = 'lot';
    case DOZEN = 'lusin';
    case GROSS = 'gross';
    case PACK = 'pack';
    case BOX = 'box';
    case CARTON = 'carton';
    case PIECE = 'piece';
    case PAIR = 'pair';
    case SET = 'set';
    case BUNDLE = 'bundle';
    case ROLL = 'roll';
    case METER = 'meter';
    case KILOGRAM = 'kilogram';
    case GRAM = 'gram';
    case LITER = 'liter';
    case MILLILITER = 'milliliter';
    case UNIT = 'unit';
    case SHEET = 'sheet';
    case BOTTLE = 'bottle';
    case CAN = 'can';

    /**
     * Get the unit value for each measurement
     */
    public function getUnitValue(): int
    {
        return match ($this) {
            self::LOT => 100,
            self::DOZEN => 12,
            self::GROSS => 144, // 12 dozen
            self::PACK => 10,
            self::BOX => 24,
            self::CARTON => 48,
            self::PIECE => 1,
            self::PAIR => 2,
            self::SET => 1,
            self::BUNDLE => 25,
            self::ROLL => 1,
            self::METER => 1,
            self::KILOGRAM => 1000, // in grams
            self::GRAM => 1,
            self::LITER => 1000, // in milliliters
            self::MILLILITER => 1,
            self::UNIT => 1,
            self::SHEET => 1,
            self::BOTTLE => 1,
            self::CAN => 1,
        };
    }

    /**
     * Get the display name for each measurement
     */
    public function getDisplayName(): string
    {
        return match ($this) {
            self::LOT => 'Lot',
            self::DOZEN => 'Lusin',
            self::GROSS => 'Gross',
            self::PACK => 'Pack',
            self::BOX => 'Box',
            self::CARTON => 'Carton',
            self::PIECE => 'Piece',
            self::PAIR => 'Pair',
            self::SET => 'Set',
            self::BUNDLE => 'Bundle',
            self::ROLL => 'Roll',
            self::METER => 'Meter',
            self::KILOGRAM => 'Kilogram',
            self::GRAM => 'Gram',
            self::LITER => 'Liter',
            self::MILLILITER => 'Milliliter',
            self::UNIT => 'Unit',
            self::SHEET => 'Sheet',
            self::BOTTLE => 'Bottle',
            self::CAN => 'Can',
        };
    }

    /**
     * Get the short form/abbreviation for each measurement
     */
    public function getAbbreviation(): string
    {
        return match ($this) {
            self::LOT => 'lot',
            self::DOZEN => 'dzn',
            self::GROSS => 'grs',
            self::PACK => 'pck',
            self::BOX => 'box',
            self::CARTON => 'ctn',
            self::PIECE => 'pcs',
            self::PAIR => 'pr',
            self::SET => 'set',
            self::BUNDLE => 'bdl',
            self::ROLL => 'roll',
            self::METER => 'm',
            self::KILOGRAM => 'kg',
            self::GRAM => 'g',
            self::LITER => 'L',
            self::MILLILITER => 'mL',
            self::UNIT => 'unit',
            self::SHEET => 'sht',
            self::BOTTLE => 'btl',
            self::CAN => 'can',
        };
    }

    /**
     * Get all measurements as an array
     */
    public static function toArray(): array
    {
        return array_map(fn($case) => [
            'value' => $case->value,
            'display_name' => $case->getDisplayName(),
            'unit_value' => $case->getUnitValue(),
            'abbreviation' => $case->getAbbreviation(),
        ], self::cases());
    }

    /**
     * Get measurement by value
     */
    public static function fromValue(string $value): ?self
    {
        foreach (self::cases() as $case) {
            if ($case->value === $value) {
                return $case;
            }
        }
        return null;
    }

    /**
     * Get all measurement options for select dropdown
     */
    public static function getSelectOptions(): array
    {
        $options = [];
        foreach (self::cases() as $case) {
            $options[$case->value] = $case->getDisplayName() . ' (' . $case->getUnitValue() . ' unit)';
        }
        return $options;
    }
}