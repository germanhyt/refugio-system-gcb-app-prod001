<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

/**
 * Normaliza URLs con barra final: redirige 301 a la versión sin barra.
 * Preserva el valor SEO acumulado en las URLs del WordPress origen (todas con "/").
 */
class StripTrailingSlash
{
    public function handle(Request $request, Closure $next): Response
    {
        $method = $request->method();

        if (in_array($method, ['GET', 'HEAD'], true)) {
            // getPathInfo() preserva la barra final; path() la recorta y oculta el caso.
            $pathInfo = $request->getPathInfo();

            // No tocar la raíz ni rutas sin barra final.
            if ($pathInfo !== '/' && str_ends_with($pathInfo, '/')) {
                $target = rtrim($pathInfo, '/');
                $query = $request->server('QUERY_STRING');

                if (filled($query)) {
                    $target .= '?' . $query;
                }

                /** @var RedirectResponse $redirect */
                $redirect = redirect()->to($target, 301);

                return $redirect;
            }
        }

        return $next($request);
    }
}
