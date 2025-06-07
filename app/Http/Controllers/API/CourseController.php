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
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\CourseResource;
use App\Http\Resources\CourseCollection;
use App\Models\User;

class CourseController extends Controller
{
    static public function createCourse(Request $request)
    {
        try {
            $courseObj = $request->all();
            $catIds = array_map('intval', $request->catArr);
            $catArr = self::addParentCategories($catIds);

            $courseObj['user_id'] = Auth::id();
            $courseObj['catArr'] = json_encode($catArr);
            $courseObj['isPrivate'] = $request->isPrivate === true ? 1 : 0;

            // Guardar imagen en storage y guardar la ruta
            if ($request->hasFile('miniature')) {
                $imagePath = $request->file('miniature')->store('miniatures', 'public');
                $image = Image::create(['file' => $imagePath, 'name' => 'courseImg']);
                $courseObj['miniature'] = $image->id;
            }

            $newCourse = Course::create($courseObj);

            if ($request->hasFile('video')) {
                $videos = $request->file('video');
                $indices = $request->input('indice', []);

                foreach ($videos as $key => $file) {
                    $videoPath = $file->store('videos', 'public');

                    $video = new Video([
                        'file' => $videoPath,
                        'title' => isset($indices[$key]) ? $indices[$key] : null
                    ]);
                    $video->course()->associate($newCourse);
                    $video->save();
                }
            }

            //Crea las interacciones en bbdd
            $newCourse->interaction()->create();



            return response()->json(['message' => ['Course created', $newCourse->id]]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    static public function getAllCourses()
    {
        try {
            return response()->json(['message' => new CourseCollection(Course::all())]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getCourseById(Request $request)
    {
        try {
            return response()->json(['message' => new CourseResource(Course::findOrFail($request->id))]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getOwnedCourses(Request $request) {
        try {
            $userId = Auth::id();
            $ownedCourses = Course::where('user_id', $userId)->get();

            return response()->json(['message' => new CourseCollection($ownedCourses)]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getCoursesByUserId($id)
    {
        try{
            $courses = Course::where('user_id', $id)->get();
            return response()->json(['message' => new CourseCollection($courses)]);
        }catch (Exception $e){
            return response()->json(['error' => $e->getMessage()], 500);
        }

    }

    public static function searchCourses(Request $request)
    {
        try {
            $coursesByTitle = Course::where('title', 'like', '%' . $request[0] . '%');

            $userIds = User::where('username', 'like', '%' . $request[0] . '%')->pluck('id');

            $coursesByUser = Course::whereIn('user_id', $userIds);

            $courses = $coursesByTitle->union($coursesByUser)->get()->unique('id');

            return response()->json(['message' => new CourseCollection($courses)]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function getFollowedCourses(Request $request)
    {
        return self::getCoursesByUserField('followed');
    }

    public static function getLikedCourses(Request $request)
    {
        return self::getCoursesByUserField('liked');
    }

    public static function getEndedCourses(Request $request)
    {
        return self::getCoursesByUserField('ended');
    }

    static private function getCoursesByUserField(string $field)
    {
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);

            $courseIds = json_decode($user->{$field}, true) ?? [];

            $courses = count($courseIds) > 0
                ? Course::whereIn('id', $courseIds)->get()
                : collect();

            return response()->json(['message' => new CourseCollection($courses)]);
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
                $category = Category::find($category->parent_category_id);
            }
        }

        return array_unique($addedCategories);
    }

}
