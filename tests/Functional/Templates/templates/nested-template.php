<?php

use Ricotta\App\Module\Template\TemplateEngine;

return function (TemplateEngine $engine) {
    echo "Message: {$engine->render('html-template', 'ricotta/app')}";
};
