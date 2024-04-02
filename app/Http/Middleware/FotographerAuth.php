<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use App\Models\Photographer;

class FotographerAuth
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $isPhotographer = Photographer::find($request->photographer_id);
        if ($isPhotographer) {
            return $next($request);
        } else {
            $message = ["message" => "Permission Denied. You're not a Photographer."];
            return response($message, 401);
        }
    }
}
