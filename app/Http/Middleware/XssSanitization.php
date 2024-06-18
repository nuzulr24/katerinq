<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class XssSanitization
{
    /**
     * Handles the request by modifying the input data and passing it to the next middleware or route handler.
     *
     * @param Request $request The request object containing the input data.
     * @param Closure $next The next middleware or route handler.
     * @throws Some_Exception_Class [Optional] Description of any exception that may be thrown.
     * @return mixed The result of the next middleware or route handler.
     */
    public function handle(Request $request, Closure $next)
    {
        $input = $request->all();
        array_walk_recursive($input, function(&$input) {
            $input = strip_tags($input);
        });

        $request->merge($input);
        return $next($request);
    }
}
