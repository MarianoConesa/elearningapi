<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CourseInteraction;
use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rules\Exists;

class UserController extends Controller
{
    public static function getInitialInfo(Request $request)
{
    try {
        $user = $request->user();
        $retObj = (object)[];
        $retObj->id = $user->id;
        $retObj->username = $user->username;
        $retObj->name = $user->name;
        $retObj->email = $user->email;

        // Obtener la imagen desde la base de datos
        $image = Image::find($user->profilePic);

        if ($image && Storage::disk('public')->exists($image->file)) {
            $fileContent = Storage::disk('public')->get($image->file);
            $mimeType = Storage::disk('public')->mimeType($image->file);
            $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($fileContent);
            $retObj->profilePic = $base64Image;
        } else {
            $retObj->profilePic = null;
        }

        // Añadir los arrays followed y ended
        $retObj->followed = json_decode($user->followed ?? '[]');
        $retObj->ended = json_decode($user->ended ?? '[]');

        return response()->json(['message' => $retObj]);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'No hay una sesión iniciada',
            'error' => $e->getMessage(),
        ], 500);
    }
}



public static function updateUser($id, $updated)
{
    try {
        $user = User::findOrFail($id);

        if (isset($updated['profilePic']) && $updated['profilePic'] instanceof \Illuminate\Http\UploadedFile) {
            // Obtener la imagen anterior y eliminarla si existe
            if (!empty($user->profilePic)) {
                $oldImage = Image::find($user->profilePic);
                if ($oldImage) {
                    Storage::disk('public')->delete($oldImage->file);
                    $oldImage->delete();
                }
            }

            // Guardar la nueva imagen en storage
            $imagePath = $updated['profilePic']->store('profile_pics', 'public');

            // Crear un nuevo registro en la tabla `images`
            $image = Image::create([
                'file' => $imagePath,
                'name' => $user->username . '-profilePic',
            ]);

            // Asignar el nuevo ID de la imagen al usuario
            $updated['profilePic'] = $image->id;
        }

        $user->update($updated);
        return $user->id;
    } catch (Exception $e) {
        return response()->json([
            'message' => 'Error al actualizar el usuario',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public static function followCourse(Request $request)
    {
        try {
            $user = $request->user();
            $courseId = intval($request->input('id'));

            // Obtener arrays actuales o inicializarlos como vacíos
            $followed = empty($user->followed) ? [] : json_decode($user->followed);
            $ended = empty($user->ended) ? [] : json_decode($user->ended);

            // Agregar a 'followed' si no está
            if (!in_array($courseId, $followed)) {
                $followed[] = $courseId;
            }

            // Eliminar de 'ended' si está
            $ended = array_filter($ended, fn($id) => intval($id) !== $courseId);

            // Guardar los cambios
            $user->followed = array_values($followed);
            $user->ended = array_values($ended);
            $user->save();
            CourseInteraction::where('course_id', $courseId)->increment('follows_count');

            return response()->json([
                'message' => 'Curso seguido correctamente.',
                'followed' => $user->followed,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function endCourse(Request $request)
    {
        try {
            $user = $request->user();
            $courseId = intval($request->input('id'));

            // Obtener y decodificar los arrays actuales
            $followed = empty($user->followed) ? [] : json_decode($user->followed);
            $ended = empty($user->ended) ? [] : json_decode($user->ended);

            // Quitar el curso de 'followed' si existe
            $followed = array_filter($followed, fn($id) => intval($id) !== $courseId);

            // Agregar a 'ended' si no está ya
            if (!in_array($courseId, $ended)) {
                $ended[] = $courseId;
            }

            // Guardar los cambios
            $user->followed = array_values($followed); // Reindexar
            $user->ended = $ended;
            $user->save();

            return response()->json([
                'message' => 'Curso marcado como terminado.',
                'ended' => $user->ended,
            ], 200);
        } catch (Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }

    public static function unfollowCourse(Request $request)
{
    try {
        $user = $request->user();
        $courseId = intval($request->input('id'));

        // Obtener y decodificar los arrays actuales
        $followed = empty($user->followed) ? [] : json_decode($user->followed);
        $ended = empty($user->ended) ? [] : json_decode($user->ended);

        // Filtrar el ID del curso de ambos arrays
        $followed = array_filter($followed, fn($id) => intval($id) !== $courseId);
        $ended = array_filter($ended, fn($id) => intval($id) !== $courseId);

        // Guardar cambios
        $user->followed = array_values($followed); // Reindexar para evitar índices rotos
        $user->ended = array_values($ended);
        $user->save();
        CourseInteraction::where('course_id', $courseId)->decrement('follows_count');

        return response()->json([
            'message' => 'Curso eliminado de tu progreso.',
            'followed' => $user->followed,
            'ended' => $user->ended,
        ], 200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}



}
