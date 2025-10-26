<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /** @var array<int, string> */
    protected $fillable = ['logo_path', 'school_name', 'school_logo'];

    public static function getLogo(): ?string
    {
        $settings = self::first();

        return $settings?->logo_path;
    }

    public static function updateLogo(?string $logoPath): void
    {
        $settings = self::first() ?? self::create([]);
        $settings->update(['logo_path' => $logoPath]);
    }

    public static function getSchoolName(): ?string
    {
        $settings = self::first();

        return $settings?->school_name ?? 'Mi Escuela';
    }

    public static function getSchoolLogo(): ?string
    {
        $settings = self::first();

        if ($settings?->school_logo) {
            return asset('storage/'.$settings->school_logo);
        }

        return asset('img/logo.png');
    }
}
