<?php

use Ricotta\App\Tests\Functional\Templates\Mock\MockView;

return function (MockView $view) {
    echo $view->message;    
};
