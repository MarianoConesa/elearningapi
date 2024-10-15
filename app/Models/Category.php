<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'parent_category_id','active','sequence'
    ];

    static public function getCategories($category = null, $allLevel = false)
    {
        $retObj = [];
        if ($allLevel){
            $retObj = Category::all();
            return $retObj;
        }
        if (!$category){
            $level = Category::where('parent_category_id', null)->get();
            foreach($level as $category){
                array_push($retObj, (object)$category);
            }
        }else{
            $level = Category::where('parent_category_id', $category)->get();
            foreach($level as $category){
                array_push($retObj, (object)$category);
            }
        }

        return $retObj;

    }

    static public function constructCategories()
    {
        $retObj = (object)[];
        $retObj->level_1 = self::getCategories();
        foreach($retObj->level_1 as $category){
            $category->child = $level_2 = self::getCategories($category->id);
            foreach($level_2 as $subCategory){
                $level_3 = self::getCategories($subCategory->id);
                $subCategory->child = $level_3;
            }
        }
        $retObj->allLevel = self::getCategories(null, true);
        return $retObj;
    }
}
