<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Image extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'file','description'
    ];

    static public function getLogo(){
        $logo = self::where('name', 'logo white')->first();
        return $logo;
    }

    static public function insertImg($name = 'image', $file, $description= ''){
        $newImg = self::create([
                    'name' => $name,
                    'file' => $file,
                    'description' => $description
        ]);
        return $newImg->id;
    }
}
