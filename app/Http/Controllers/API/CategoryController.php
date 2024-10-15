<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Exception;
use Illuminate\Http\Request;

class CategoryController extends Controller
{
    public static function getCategories()
    {
        try{
         $catObj = Category::constructCategories();
         return response()->json(['message' => $catObj]);
        }catch(Exception $e){
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }
}
