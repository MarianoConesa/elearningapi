<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    // static public function getInitialImg(){ (Funcion que trae los logos)
    //     try {
    //         // Obtenemos ambos logos
    //         $logos = Image::getLogo();

    //         if (empty($logos)) {
    //             return response()->json(['error' => 'Logos not found'], 404);
    //         }

    //         // Convertir cada imagen a Base64
    //         $base64Logos = [];
    //         foreach ($logos as $key => $logoContent) {
    //             $mimeType = 'image/svg+xml';
    //             $base64Logos[$key] = 'data:' . $mimeType . ';base64,' . base64_encode($logoContent);
    //         }

    //         return response()->json([
    //             'message' => $base64Logos
    //         ]);

    //     } catch (Exception $e) {
    //         return response()->json([
    //             'error' => $e->getMessage(),
    //         ], 500);
    //     }
    // }
}
