<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    static public function getInitialImg(){
        try {
            $logoObj = Image::getLogo();

            if (!$logoObj) {
                return response()->json(['error' => 'Logo not found'], 404);
            }

            // Obtener el contenido del archivo
            $imageContent = $logoObj;

            // Detectar MIME type (asumiendo que es SVG, pero puedes usar `finfo` para más tipos)
            $mimeType = 'image/svg+xml';

            // Convertir a Base64
            $base64Image = 'data:' . $mimeType . ';base64,' . base64_encode($imageContent);

            return response()->json([
                'message' => [$base64Image]
            ]);

        } catch (Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
