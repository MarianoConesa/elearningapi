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
            $catArr = self::addParentCategories($catIds); // si entra una categoria con padre, el padre tambien se añade

            $courseObj['user_id'] = Auth::id();
            $courseObj['catArr'] = json_encode($catArr);
            $courseObj['isPrivate'] = $request->isPrivate === true ? 1 : 0;


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


            $newCourse->interaction()->create();



            return response()->json(['message' => ['Course created', $newCourse->id]]);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function updateCourse(Request $request, $id)
{
    try {
        $course = Course::with('videos')->findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $course->user_id && !$user->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        $catIds = array_map('intval', $request->catArr);
        $catArr = self::addParentCategories($catIds);

        $course->title = $request->title;
        $course->description = $request->description;
        $course->catArr = json_encode($catArr);
        $course->isPrivate = $request->isPrivate === true ? 1 : 0;
        $course->password = $request->password ?? null;

        if ($request->hasFile('miniature')) {
            $imagePath = $request->file('miniature')->store('miniatures', 'public');
            $image = Image::create(['file' => $imagePath, 'name' => 'courseImg']);
            $course->miniature = $image->id;
        }

        $course->save();

        $oldVideos = collect($request->input('old_videos', []))
            ->map(fn($v) => json_decode($v, true))
            ->filter(fn($v) => isset($v['id']));

        $oldVideoIds = $oldVideos->pluck('id')->toArray();

        foreach ($course->videos as $video) {
            if (in_array($video->id, $oldVideoIds)) {
                $newData = $oldVideos->firstWhere('id', $video->id);
                $video->title = $newData['title'];
                $video->save();
            } else {
                Storage::disk('public')->delete($video->file);
                $video->delete();
            }
        }

        if ($request->hasFile('video')) {
            $videos = $request->file('video');
            $indices = $request->input('indice', []);

            foreach ($videos as $key => $file) {
                $videoPath = $file->store('videos', 'public');
                $video = new Video([
                    'file' => $videoPath,
                    'title' => $indices[$key] ?? null
                ]);
                $video->course()->associate($course);
                $video->save();
            }
        }

        return response()->json(['message' => ['Course updated', $course->id]]);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}


    public static function getAllCourses(Request $request)
    {
        try {
            $usePagination = $request->has('page') || $request->has('per_page');

            if ($usePagination) {
                $perPage = (int) $request->input('per_page', 30);
                $courses = Course::paginate($perPage);

                return response()->json([
                    'data' => new CourseCollection($courses->items()),
                    'pagination' => [
                        'current_page' => $courses->currentPage(),
                        'last_page' => $courses->lastPage(),
                        'per_page' => $courses->perPage(),
                        'total' => $courses->total(),
                        'has_more_pages' => $courses->hasMorePages(),
                    ]
                ]);
            } else {
                $courses = Course::all();
                return response()->json([
                    'data' => new CourseCollection($courses)
                ]);
            }
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

    public static function getOwnedCourses(Request $request)
    {
        try {
            $userId = Auth::id();
            $usePagination = $request->has('page') || $request->has('per_page');
            $courseQuery = Course::where('user_id', $userId);

            if ($usePagination) {
                $perPage = (int) $request->input('per_page', 10);
                $courses = $courseQuery->paginate($perPage);

                return response()->json([
                    'data' => new CourseCollection($courses->items()),
                    'pagination' => [
                        'current_page' => $courses->currentPage(),
                        'last_page' => $courses->lastPage(),
                        'per_page' => $courses->perPage(),
                        'total' => $courses->total(),
                        'has_more_pages' => $courses->hasMorePages(),
                    ]
                ]);
            } else {
                $courses = $courseQuery->get();
                return response()->json(['data' => new CourseCollection($courses)]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }


    public static function getCoursesByUserId($id, Request $request)
    {
        try {
            $usePagination = $request->has('page') || $request->has('per_page');
            $courseQuery = Course::where('user_id', $id);

            if ($usePagination) {
                $perPage = (int) $request->input('per_page', 10);
                $courses = $courseQuery->paginate($perPage);

                return response()->json([
                    'data' => new CourseCollection($courses->items()),
                    'pagination' => [
                        'current_page' => $courses->currentPage(),
                        'last_page' => $courses->lastPage(),
                        'per_page' => $courses->perPage(),
                        'total' => $courses->total(),
                        'has_more_pages' => $courses->hasMorePages(),
                    ]
                ]);
            } else {
                $courses = $courseQuery->get();
                return response()->json(['data' => new CourseCollection($courses)]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function searchCourses(Request $request)
    {
        try {
            $body = $request->all();
            $query = $body[0] ?? '';
            $pagination = $body[1] ?? [];

            $coursesByTitle = Course::where('title', 'like', '%' . $query . '%')->get();
            $userIds = User::where('username', 'like', '%' . $query . '%')->pluck('id');
            $coursesByUser = Course::whereIn('user_id', $userIds)->get();

            $merged = $coursesByTitle->merge($coursesByUser)->unique('id')->values();

            $usePagination = isset($pagination['page']) || isset($pagination['per_page']);

            if ($usePagination) {
                $page = (int) ($pagination['page'] ?? 1);
                $perPage = (int) ($pagination['per_page'] ?? 10);

                $paginated = $merged->slice(($page - 1) * $perPage, $perPage)->values();

                return response()->json([
                    'data' => new CourseCollection($paginated),
                    'pagination' => [
                        'current_page' => $page,
                        'last_page' => ceil($merged->count() / $perPage),
                        'per_page' => $perPage,
                        'total' => $merged->count(),
                        'has_more_pages' => $page * $perPage < $merged->count(),
                    ]
                ]);
            } else {
                return response()->json(['data' => new CourseCollection($merged)]);
            }
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function removeCourse($id)
{
    try {
        $course = Course::with('videos')->findOrFail($id);
        $user = Auth::user();

        if ($user->id !== $course->user_id && !$user->isAdmin()) {
            return response()->json(['error' => 'No autorizado'], 403);
        }

        // Eliminar archivos y registros de videos
        foreach ($course->videos as $video) {
            Storage::disk('public')->delete($video->file);
            $video->delete();
        }

        // Eliminar imagen si existe
        if ($course->miniature) {
            $image = Image::find($course->miniature);
            if ($image) {
                Storage::disk('public')->delete($image->file);
                $image->delete();
            }
        }

        // Eliminar interacciones relacionadas
        $course->interaction()->delete();

        // Eliminar el curso
        $course->delete();

        return response()->json(['message' => 'Curso eliminado con éxito']);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



    public static function getFollowedCourses(Request $request)
    {
        return self::getCoursesByUserField('followed', $request);
    }

    public static function getLikedCourses(Request $request)
    {
        return self::getCoursesByUserField('liked', $request);
    }

    public static function getEndedCourses(Request $request)
    {
        return self::getCoursesByUserField('ended', $request);
    }

    static private function getCoursesByUserField(string $field, Request $request)
    {
        try {
            $userId = Auth::id();
            $user = User::findOrFail($userId);

            $courseIds = json_decode($user->{$field}, true) ?? [];
            $coursesQuery = Course::whereIn('id', $courseIds);

            if ($request->has('page') || $request->has('per_page')) {
                $perPage = (int) $request->input('per_page', 10);
                $courses = $coursesQuery->paginate($perPage);

                return response()->json([
                    'data' => new CourseCollection($courses->items()),
                    'pagination' => [
                        'current_page' => $courses->currentPage(),
                        'last_page' => $courses->lastPage(),
                        'per_page' => $courses->perPage(),
                        'total' => $courses->total(),
                        'has_more_pages' => $courses->hasMorePages(),
                    ]
                ]);
            } else {
                $courses = $coursesQuery->get();
                return response()->json(['message' => new CourseCollection($courses)]);
            }
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
