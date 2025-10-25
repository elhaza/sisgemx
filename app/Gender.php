<?php

namespace App;

enum Gender: string
{
    case Male = 'male';
    case Female = 'female';
    case Unspecified = 'unspecified';

    public function label(): string
    {
        return match ($this) {
            self::Male => 'Hombre',
            self::Female => 'Mujer',
            self::Unspecified => 'No especificar',
        };
    }
}
