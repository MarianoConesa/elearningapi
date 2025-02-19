<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Image;
use App\Models\User;
use Exception;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public static function getInitialInfo(Request $request){
        try{
            $user = $request->user();
            $retObj = (object)[];
            $retObj->id = $user->id;
            $retObj->username = $user->username;
            $retObj->name = $user->name;
            $retObj->email = $user->email;
            $retObj->profilePic = !empty($user->profilePic) ? Image::where('id', $user->profilePic)->first()->file : null;
            return response()->json(['message' => $retObj]);
        }catch(Exception $e){
            return response()->json([
                'message' => 'No hay una sesion iniciada',
                'error' => $e->getMessage(),
            ], 500);
        }

    }

    public static function updateUser($id, $updated){
        $user = User::findOrFail($id)->first();
        $user->update($updated);
        return $user->id;
    }
}
