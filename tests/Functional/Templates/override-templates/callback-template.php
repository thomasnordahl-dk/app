<?php

use Ricotta\App\Tests\Functional\Templates\Mock\MockView;

/**
 * This template is for testing the availability of injections in non callback based templates.
 */

/**
 * @var MockView $view
 */
echo $view->message;
?>