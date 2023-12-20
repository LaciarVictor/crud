<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;



class TokenVerificationController extends Controller
{
    public function verify(Request $request)
    {

        $token = $request->bearerToken();
       // $token = $request->header('Authorization');

        if ($token) {
            $user = Auth::guard('sanctum')->user();

            if ($user) {
                return response()->json(['status' => true]);
            }
        }

        return response()->json(['status' => false]);
    }
}
