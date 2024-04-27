<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\Photographer;

class UserAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user(); 

        if ($user) {
            $token = $user->remember_token;
            
        } else {
            
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $user = User::where('remember_token', $token)->first();
        $isPhotographer = Photographer::where('user_id', $user->id)->first();

        if (!$isPhotographer) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied. You're not a Regular User."];
            return response($message, 401);
        }
    }
}
