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
        'name', 'file','description'
    ];

    static public function getLogo()
    {
        $logo = self::where('name', 'logo white')->first();

        if ($logo && Storage::disk('public')->exists($logo->file)) {
            return Storage::disk('public')->get($logo->file);
        }

        return null;
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
