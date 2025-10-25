<?php

namespace App;

enum Relationship: string
{
    case Grandparent = 'grandparent';
    case Uncle = 'uncle';
    case Aunt = 'aunt';
    case Family = 'family';
    case Friend = 'friend';
    case Other = 'other';

    public function label(): string
    {
        return match ($this) {
            self::Grandparent => 'Abuelo/a',
            self::Uncle => 'Tío',
            self::Aunt => 'Tía',
            self::Family => 'Familiar',
            self::Friend => 'Amigo/a',
            self::Other => 'Otro',
        };
    }
}
