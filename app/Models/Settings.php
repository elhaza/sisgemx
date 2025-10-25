<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    /** @var array<int, string> */
    protected $fillable = ['logo_path'];

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
}
