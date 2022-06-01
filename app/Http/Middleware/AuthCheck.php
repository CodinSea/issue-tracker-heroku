<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use App\Models\User;

class AuthCheck
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(!session()->has('LoggedUser') && ($request->path() != 'login' && $request->path() != 'register' && $request->path() != 'demoUser')){
            return redirect('login')->with('fail', 'You must be logged in.');
        }

        View::share('currentUser', User::find(session('LoggedUserId')));

        if(session()->has('LoggedUser') && ($request->path() == 'login' || $request->path() == 'register')){
            return back();
        }

        $headers = [
            'Cache-Control' => 'nocache, no-store, max-age=0, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => 'Sun, 02 Jan 1990 00:00:00 GMT'
        ];
        $response = $next($request);
        
        foreach($headers as $key => $value) {
            $response->headers->set($key, $value);
        }
 
        return $response;

//        return $next($request)->header('Cache-Control','no-cache, no-store, max-age=0, must-revalidate')
//                              ->header('Pragma', 'no-cache')
//                              ->header('Expires', 'Sat 01 Jan 1990 00:00:00 GMT');
    }
}