<?php

namespace Orchestra\Workbench\Http\Middleware;

use Closure;
use Orchestra\Workbench\Workbench;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class CatchDefaultRoute
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (mixed)  $next
     */
    public function handle($request, Closure $next)
    {
        $workbench = Workbench::config();

        if ($request->decodedPath() === '/' && ! \is_null($workbench['user']) && \is_null($request->user())) {
            return redirect('/_workbench');
        }

        $response = $next($request);

        if (! \is_null($response->exception) && $response->exception instanceof NotFoundHttpException) {
            if ($request->decodedPath() === '/' && $workbench['start'] !== '/') {
                return redirect($workbench['start']);
            }
        }

        return $response;
    }
}
