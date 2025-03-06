<?php

/**
 * This template is for testing the availability of injections in non callback based templates.
 */

declare(strict_types=1);

use Ricotta\App\Tests\Functional\Templates\Mock\MockView;

/**
 * @var MockView $view
 */
echo $view->message;
