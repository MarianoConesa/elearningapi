<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        foreach($this->categories as $category){
            Category::create($category);
        }

    }

    private $categories = [
        // Categorías principales
        ['name' => 'Programación', 'parent_category_id' => null, 'active' => true, 'sequence' => 1],
        ['name' => 'Diseño', 'parent_category_id' => null, 'active' => true, 'sequence' => 2],
        ['name' => 'Marketing', 'parent_category_id' => null, 'active' => true, 'sequence' => 3],
        ['name' => 'Negocios', 'parent_category_id' => null, 'active' => true, 'sequence' => 4],
        ['name' => 'Desarrollo Personal', 'parent_category_id' => null, 'active' => true, 'sequence' => 5],
        ['name' => 'Idiomas', 'parent_category_id' => null, 'active' => true, 'sequence' => 6],
        ['name' => 'Música', 'parent_category_id' => null, 'active' => true, 'sequence' => 7],
        ['name' => 'Fotografía', 'parent_category_id' => null, 'active' => true, 'sequence' => 8],

        // Subcategorías de Programación
        ['name' => 'Desarrollo Web', 'parent_category_id' => 1, 'active' => true, 'sequence' => 1],
        ['name' => 'Desarrollo Móvil', 'parent_category_id' => 1, 'active' => true, 'sequence' => 2],
        ['name' => 'Ciencia de Datos', 'parent_category_id' => 1, 'active' => true, 'sequence' => 3],
        ['name' => 'Desarrollo de Juegos', 'parent_category_id' => 1, 'active' => true, 'sequence' => 4],

        // Subcategorías de Desarrollo Web
        ['name' => 'Front-End', 'parent_category_id' => 9, 'active' => true, 'sequence' => 1],
        ['name' => 'Back-End', 'parent_category_id' => 9, 'active' => true, 'sequence' => 2],
        ['name' => 'Full-Stack', 'parent_category_id' => 9, 'active' => true, 'sequence' => 3],

        // Subcategorías de Diseño
        ['name' => 'Diseño Gráfico', 'parent_category_id' => 2, 'active' => true, 'sequence' => 1],
        ['name' => 'Diseño UX/UI', 'parent_category_id' => 2, 'active' => true, 'sequence' => 2],
        ['name' => 'Diseño 3D', 'parent_category_id' => 2, 'active' => true, 'sequence' => 3],
        ['name' => 'Animación', 'parent_category_id' => 2, 'active' => true, 'sequence' => 4],

        // Subcategorías de Marketing
        ['name' => 'Marketing Digital', 'parent_category_id' => 3, 'active' => true, 'sequence' => 1],
        ['name' => 'SEO', 'parent_category_id' => 3, 'active' => true, 'sequence' => 2],
        ['name' => 'Marketing en Redes Sociales', 'parent_category_id' => 3, 'active' => true, 'sequence' => 3],
        ['name' => 'Branding', 'parent_category_id' => 3, 'active' => true, 'sequence' => 4],

        // Subcategorías de Negocios
        ['name' => 'Emprendimiento', 'parent_category_id' => 4, 'active' => true, 'sequence' => 1],
        ['name' => 'Gestión de Proyectos', 'parent_category_id' => 4, 'active' => true, 'sequence' => 2],
        ['name' => 'Liderazgo', 'parent_category_id' => 4, 'active' => true, 'sequence' => 3],
        ['name' => 'Finanzas', 'parent_category_id' => 4, 'active' => true, 'sequence' => 4],

        // Subcategorías de Desarrollo Personal
        ['name' => 'Productividad', 'parent_category_id' => 5, 'active' => true, 'sequence' => 1],
        ['name' => 'Mindfulness', 'parent_category_id' => 5, 'active' => true, 'sequence' => 2],
        ['name' => 'Habilidades de Comunicación', 'parent_category_id' => 5, 'active' => true, 'sequence' => 3],
        ['name' => 'Crecimiento Personal', 'parent_category_id' => 5, 'active' => true, 'sequence' => 4],

        // Subcategorías de Idiomas
        ['name' => 'Inglés', 'parent_category_id' => 6, 'active' => true, 'sequence' => 1],
        ['name' => 'Español', 'parent_category_id' => 6, 'active' => true, 'sequence' => 2],
        ['name' => 'Francés', 'parent_category_id' => 6, 'active' => true, 'sequence' => 3],
        ['name' => 'Alemán', 'parent_category_id' => 6, 'active' => true, 'sequence' => 4],

        // Subcategorías de Música
        ['name' => 'Guitarra', 'parent_category_id' => 7, 'active' => true, 'sequence' => 1],
        ['name' => 'Piano', 'parent_category_id' => 7, 'active' => true, 'sequence' => 2],
        ['name' => 'Teoría Musical', 'parent_category_id' => 7, 'active' => true, 'sequence' => 3],
        ['name' => 'Producción Musical', 'parent_category_id' => 7, 'active' => true, 'sequence' => 4],

        // Subcategorías de Fotografía
        ['name' => 'Fotografía Digital', 'parent_category_id' => 8, 'active' => true, 'sequence' => 1],
        ['name' => 'Edición de Fotos', 'parent_category_id' => 8, 'active' => true, 'sequence' => 2],
        ['name' => 'Fotografía de Retratos', 'parent_category_id' => 8, 'active' => true, 'sequence' => 3],
        ['name' => 'Fotografía de Paisajes', 'parent_category_id' => 8, 'active' => true, 'sequence' => 4],

        // Subcategorías de Front-End
        ['name' => 'HTML/CSS', 'parent_category_id' => 13, 'active' => true, 'sequence' => 1],
        ['name' => 'JavaScript', 'parent_category_id' => 13, 'active' => true, 'sequence' => 2],
        ['name' => 'React', 'parent_category_id' => 13, 'active' => true, 'sequence' => 3],
        ['name' => 'Angular', 'parent_category_id' => 13, 'active' => true, 'sequence' => 4],

        // Subcategorías de Back-End
        ['name' => 'Node.js', 'parent_category_id' => 14, 'active' => true, 'sequence' => 1],
        ['name' => 'Python', 'parent_category_id' => 14, 'active' => true, 'sequence' => 2],
        ['name' => 'Ruby on Rails', 'parent_category_id' => 14, 'active' => true, 'sequence' => 3],
        ['name' => 'PHP', 'parent_category_id' => 14, 'active' => true, 'sequence' => 4],

        // Subcategorías de Ciencia de Datos
        ['name' => 'Análisis de Datos', 'parent_category_id' => 11, 'active' => true, 'sequence' => 1],
        ['name' => 'Machine Learning', 'parent_category_id' => 11, 'active' => true, 'sequence' => 2],
        ['name' => 'Deep Learning', 'parent_category_id' => 11, 'active' => true, 'sequence' => 3],
        ['name' => 'Visualización de Datos', 'parent_category_id' => 11, 'active' => true, 'sequence' => 4],

        // Subcategorías de Desarrollo de Juegos
        ['name' => 'Unity', 'parent_category_id' => 12, 'active' => true, 'sequence' => 1],
        ['name' => 'Unreal Engine', 'parent_category_id' => 12, 'active' => true, 'sequence' => 2],
        ['name' => 'Desarrollo de Juegos Móviles', 'parent_category_id' => 12, 'active' => true, 'sequence' => 3],
        ['name' => 'Diseño de Juegos', 'parent_category_id' => 12, 'active' => true, 'sequence' => 4],
    ];

}
