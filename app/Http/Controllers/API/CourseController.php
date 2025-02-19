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
    static public function createCourse(Request $request){
        try {
            $catNames = $request->input('catArr', []);
            $catIds = Category::whereIn('name', $catNames)->pluck('id')->toArray();

            $catArr = self::addParentCategories($catIds);

            $reqObj = $request->all();
            $reqObj['user_id'] = Auth::id();
            $reqObj['catArr'] = json_encode($catArr); // Guardar solo los IDs
            $videoId = Video::create(['file' => $reqObj['video']]);
            $imageId = Image::create(['file' => $reqObj['miniature'], 'name' => 'courseImg']);
            $reqObj['video_id'] = $videoId->id;
            $reqObj['miniature'] = $imageId->id;

            $newCourse = Course::create($reqObj);

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
