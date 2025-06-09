<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\CourseInteraction;
use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
            $retObj->profilePic = asset('storage/' . $image->file);
        } else {
            $retObj->profilePic = null;
        }

        // Añadir los arrays followed, ended y liked
        $retObj->followed = json_decode($user->followed ?? '[]');
        $retObj->ended = json_decode($user->ended ?? '[]');
        $retObj->liked = json_decode($user->liked ?? '[]');

        return response()->json(['message' => $retObj]);
    } catch (Exception $e) {
        return response()->json([
            'message' => 'No hay una sesión iniciada',
            'error' => $e->getMessage(),
        ], 500);
    }
}

    public static function getForeignUserInfo($id)
    {
        try {
            $user = User::select('id', 'name', 'username', 'email', 'profilePic')->findOrFail($id);
            $image = Image::find($user->profilePic);
            if ($image && $image->file) {
                $user->profilePic = asset('storage/' . $image->file);
            }else{
                $user->profilePic = null;
            }

            return response()->json(['message' => $user]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'No se pudo recoger la informacion de este usuario',
                'error' => $e->getMessage(),
            ], 500);
        }

    }



    public static function updateUser(Request $request)
    {
        try {
            $user = User::findOrFail($request->input('id'));

            $updated = [];

            if ($request->hasFile('profilePic')) {
                // Subir nueva imagen
                $imagePath = $request->file('profilePic')->store('profile_pics', 'public');

                $image = Image::create([
                    'file' => $imagePath,
                    'name' => $user->username . '-profilePic',
                ]);

                // Guardar nueva imagen en el usuario primero (esto evita la violación de la FK)
                $oldImageId = $user->profilePic;
                $user->profilePic = $image->id;
                $user->save();

                // Ahora eliminar la imagen anterior (ya no está referenciada)
                if (!empty($oldImageId)) {
                    $oldImage = Image::find($oldImageId);
                    if ($oldImage) {
                        Storage::disk('public')->delete($oldImage->file);
                        $oldImage->delete();
                    }
                }
            }

            // Actualizar otros campos si vienen en el request
            if ($request->filled('username')) {
                $user->username = $request->input('username');
            }

            if ($request->filled('name')) {
                $user->name = $request->input('name');
            }

            $user->save();

            return response()->json(['message' => 'Usuario actualizado correctamente']);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar el usuario',
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    public static function updatePassword(Request $request)
{
    try {
        // Validar datos
        $request->validate([
            'id' => 'required|integer|exists:users,id',
            'currentPassword' => 'required|string',
            'newPassword' => 'required|string|min:6|confirmed', // password_confirmation requerido
        ]);

        // Buscar usuario
        $user = User::findOrFail($request->id);

        // Comprobar que currentPassword es correcto
        if (!Hash::check($request->currentPassword, $user->password)) {
            return response()->json([
                'message' => 'La contraseña antigua es incorrecta',
            ], 400);
        }

        // Actualizar contraseña con hash
        $user->password = Hash::make($request->newPassword);
        $user->save();

        return response()->json([
            'message' => 'Contraseña actualizada correctamente',
        ], 200);

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

    public static function likeCourse(Request $request)
{
    try {
        $user = $request->user();
        $courseId = intval($request->input('id'));

        // Obtener y decodificar el array actual
        $liked = empty($user->liked) ? [] : json_decode($user->liked);

        // Agregar a 'liked' si no está
        if (!in_array($courseId, $liked)) {
            $liked[] = $courseId;
            $user->liked = array_values($liked); // Reindexar
            $user->save();

            CourseInteraction::where('course_id', $courseId)->increment('likes_count');
        }

        return response()->json([
            'message' => 'Curso marcado como favorito.',
            'liked' => $user->liked,
        ], 200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

public static function dislikeCourse(Request $request)
{
    try {
        $user = $request->user();
        $courseId = intval($request->input('id'));

        // Obtener y decodificar el array actual
        $liked = empty($user->liked) ? [] : json_decode($user->liked);

        // Quitar el curso de 'liked' si existe
        $liked = array_filter($liked, fn($id) => intval($id) !== $courseId);
        $user->liked = array_values($liked); // Reindexar
        $user->save();

        CourseInteraction::where('course_id', $courseId)->decrement('likes_count');

        return response()->json([
            'message' => 'Curso quitado de favoritos.',
            'liked' => $user->liked,
        ], 200);
    } catch (Exception $e) {
        return response()->json(['error' => $e->getMessage()], 500);
    }
}

}
