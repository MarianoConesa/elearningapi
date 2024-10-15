<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use Exception;
use Illuminate\Http\Request;

class ImageController extends Controller
{
    static public function getInitialImg(){
        try{
            $logoObj = Image::getLogo();
            return response()->json(['message' => [$logoObj]]);
        }catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
