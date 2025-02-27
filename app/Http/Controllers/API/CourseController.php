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

    static public function getAllCourses()
    {
        try {
            $courses = Course::with(['user.image', 'image', 'video'])->get();

            return response()->json([
                'message' => $courses->map(function ($course) {
                    return [
                        'id' => $course->id,
                        'title' => $course->title,
                        'user' => [
                            'id' => $course->user->id,
                            'name' => $course->user->name,
                            'username' => $course->user->username ?? null,
                            'profilePic' => $course->user->image
                                ? asset('storage/' . $course->user->image->file)
                                : null,
                        ],
                        'miniature' => $course->image
                            ? asset('storage/' . $course->image->file)
                            : null,
                        // 'video' => $course->video
                        //     ? asset('storage/' . $course->video->file)
                        //     : null,
                        'categories' => json_decode($course->catArr, true),
                        'isPrivate' => (bool) $course->isPrivate,
                        'created_at' => $course->created_at->format('Y-m-d H:i:s'),
                        'likes' => $course->likes,
                    ];
                }),
            ]);
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
