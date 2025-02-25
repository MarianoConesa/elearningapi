<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
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

    if ($logo && Storage::exists($logo->file)) {
        return Storage::get($logo->file);
    }

    return null;
}

}
