<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Support\Facades\Storage;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'file', 'description'
    ];

    static public function getLogo()
    {
        $logos = [];

        // Logo White
        $logoWhite = self::where('name', 'logo white')->first();
        if ($logoWhite && Storage::disk('public')->exists($logoWhite->file)) {
            $logos['white'] = Storage::disk('public')->get($logoWhite->file);
        }

        // Logo Black
        $logoBlack = self::where('name', 'logo black')->first();
        if ($logoBlack && Storage::disk('public')->exists($logoBlack->file)) {
            $logos['black'] = Storage::disk('public')->get($logoBlack->file);
        }

        return $logos;
    }

    public function course(): HasOne
    {
        return $this->hasOne(Course::class);
    }

    public function userProfile(): HasOne
    {
        return $this->hasOne(User::class);
    }
}
