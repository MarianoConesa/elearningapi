<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\CommentCollection;
use App\Http\Resources\CommentResource;
use App\Models\Comment;
use App\Models\Image;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use Illuminate\Support\Facades\Storage;

class CommentController extends Controller
{
    public function createComment(Request $request)
    {
        try {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'content' => 'required|string|max:1000',
            ]);

            $comment = Comment::create([
                'user_id' => Auth::id(),
                'course_id' => $request->course_id,
                'content' => $request->content,
            ]);

            $comment->load(['user:id,name,username,profilePic']);

            return (new CommentResource($comment))->response()->setStatusCode(201);

        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al crear comentario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getComments($courseId)
    {
        try {
            $comments = Comment::where('course_id', $courseId)
                ->with(['user:id,name,username,profilePic'])
                ->latest()
                ->get();

            return new CommentCollection($comments);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al obtener comentarios',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function updateComment(Request $request, $id)
    {
        try {
            $request->validate([
                'content' => 'required|string|max:1000',
            ]);

            $comment = Comment::findOrFail($id);

            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'No autorizado para editar este comentario'
                ], 403);
            }

            $comment->content = $request->content;
            $comment->save();

            return response()->json([
                'message' => $comment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar comentario',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function removeComment($id)
    {
        try {
            $comment = Comment::findOrFail($id);

            // Verificar si el usuario autenticado es el dueño del comentario
            if ($comment->user_id !== Auth::id()) {
                return response()->json([
                    'message' => 'No autorizado para eliminar este comentario'
                ], 403);
            }

            $comment->delete();

            return response()->json([
                'message' => 'Comentario eliminado con éxito'
            ]);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error al actualizar comentario',
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
