<?php

declare(strict_types=1);

use Ricotta\App\Module\Template\TemplateEngine;

return function (TemplateEngine $engine) {
    echo $engine->render('html-template', 'ricotta/app');
    
    echo $engine->render('throws', 'ricotta/app');
};
