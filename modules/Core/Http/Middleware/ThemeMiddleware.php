<?php

namespace Modules\Core\Http\Middleware;

use Closure;
use Shipu\Themevel\Middleware\RouteMiddleware;

class ThemeMiddleware extends RouteMiddleware
{
    public function handle($request, Closure $next, $themeName = null)
    {
        $themeName = (null === $themeName) ? config('theme.active') : $themeName;

        if (null !== $themeName) {
            foreach (modules()->getOrdered() as $module) {
                /**
                 * Order priority for resources selection :
                 * 1. Inside theme in /themes/THEME_NAME/view/modules/MODULE_NAME
                 * 2. With default laravel resources in /resources/views/modules/MODULE_NAME
                 * 3. Inside the module himself in /modules/MODULE_NAME/Resources/view
                 */
                view()->replaceNamespace($module->getLowerName(), [
                    themevel()->get($themeName)['path'] . '/views/modules/' . $module->getLowerName(),
                    base_path('resources/views/modules/' . $module->getLowerName()),
                    $module->getPath() . '/Resources/views'
                ]);
            }
        }

        return parent::handle($request, $next, $themeName);
    }
}