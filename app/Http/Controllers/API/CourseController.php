<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Category;
use App\Models\Video;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CourseController extends Controller
{
    static public function createCourse(Request $request)
{
    try {
        $courseObj = $request->all();
        $catIds = array_map('intval', $request->catArr); //Se están pasando todos los datos como string por FormData y necesitamos un array de int
        $catArr = self::addParentCategories($catIds);

        $courseObj['user_id'] = Auth::id();
        $courseObj['catArr'] = json_encode($catArr);
        $courseObj['isPrivate'] = $request->isPrivate === true ? 1 : 0;

        // Guardar video en storage y guardar la ruta
        if ($request->hasFile('video')) {
            $videoPath = $request->file('video')->store('videos', 'public');
            $video = Video::create(['file' => $videoPath]);
            $courseObj['video_id'] = $video->id;
        }

        // Guardar imagen en storage y guardar la ruta
        if ($request->hasFile('miniature')) {
            $imagePath = $request->file('miniature')->store('miniatures', 'public');
            $image = Image::create(['file' => $imagePath, 'name' => 'courseImg']);
            $courseObj['miniature'] = $image->id;
        }


        $newCourse = Course::create($courseObj);

        return response()->json(['message' => ['Course created', $newCourse->id]]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    static private function addParentCategories(array $catArr): array {
        $addedCategories = $catArr;

        foreach ($catArr as $categoryId) {
            $category = Category::find($categoryId);
            while ($category && $category->parent_category_id && !in_array($category->parent_category_id, $addedCategories)) {
                $addedCategories[] = $category->parent_category_id;
                $category = Category::find($category->parent_category_id); // Seguir subiendo en la jerarquía
            }
        }

        return array_unique($addedCategories);
    }

}
