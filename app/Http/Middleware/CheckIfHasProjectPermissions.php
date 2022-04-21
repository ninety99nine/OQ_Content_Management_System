<?php

namespace App\Http\Middleware;

use Closure;
use Exception;
use Illuminate\Http\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;

class CheckIfHasProjectPermissions
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next, $permission)
    {
        //  If we have the project via the request
        if( $request->project ) {

            $project = $request->project;

        //  If we have the product via the request
        }elseif( $request->product ) {

            //  Get the product project
            $project = $request->product->project;

        }else{

            $project = null;

        }


        if( $project ){

            if( request()->user()->hasProjectPermissionTo($project, $permission) ) {

                return $next($request);

            }

            throw new AccessDeniedHttpException;

        }

        throw new Exception('This route does not contain the project id required to check permissions.', 400);

    }
}
