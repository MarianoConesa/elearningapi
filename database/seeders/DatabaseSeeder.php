<?php

namespace Database\Seeders;

use App\Models\Image;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Schema;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        Model::unguard();
        Schema::disableForeignKeyConstraints();

        User::factory(10)->create();

        User::factory()->create([
            'name' => 'admin',
            'email' => env('ADMIN_EMAIL'),
            'password' => Hash::make(env('ADMIN_PASSWORD'))
        ]);

       foreach($this->images as $image){
        Image::create($image);
    }

        $this->call(CategorySeeder::class);

        Model::reguard();
        Schema::enableForeignKeyConstraints();
    }

    private $images = [
        ['name' => 'logo white',
         'file' => 'public/assets/logos/logoIconWhite.svg']
        ,['name' => 'logo Black',
          'file' => 'public/assets/logos/logoIcon.svg']
        ,['name' => 'logo Contrast',
        'file' => 'public/assets/logos/logoIconContrast.svg']
    ];

}
