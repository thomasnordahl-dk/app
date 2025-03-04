<?php

declare(strict_types=1);

namespace Ricotta\App\Module\Template;

use Ricotta\App\App;
use Ricotta\App\Module\Module;

/**
 * @internal
 */
class TemplateModule implements Module
{
    public function register(App $app): void {
        $app->bootstrap[TemplateEngine::class]->register();
    }
}
