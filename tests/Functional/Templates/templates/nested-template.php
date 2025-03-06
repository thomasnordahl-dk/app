<?php

declare(strict_types=1);

use Ricotta\App\Module\Template\TemplateEngine;

return function (TemplateEngine $engine) {
    echo "Message: {$engine->render('html-template', 'ricotta/app')}";
};
