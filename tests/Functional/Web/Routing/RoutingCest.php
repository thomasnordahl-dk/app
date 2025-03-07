<?php

declare(strict_types=1);

namespace Ricotta\App\Tests\Functional\Web\Routing;

use Ricotta\App\Module\Web\Routing\RouterException;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\DeleteController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\GetController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\HeadController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\OptionsController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\PatchController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\PostController;
use Ricotta\App\Tests\Functional\Web\Routing\Mock\PutController;
use Ricotta\App\Tests\Support\FunctionalTester;

class RoutingCest
{
    public function appWithRouting(FunctionalTester $I): void
    {
        $I->getApp()->routes['/']->get(GetController::class);
        $I->getApp()->routes['/tests/{subpath}/{name}/*']->post(PostController::class);
        $I->getApp()->routes['/bad']->get('NonExistentController');

        $I->getApp()->routes['/put']->put(PutController::class);
        $I->getApp()->routes['/delete']->delete(DeleteController::class);
        $I->getApp()->routes['/patch']->patch(PatchController::class);
        $I->getApp()->routes['/options']->options(OptionsController::class);
        $I->getApp()->routes['/head']->head(HeadController::class);

        $I->assertTrue(isset($I->getApp()->routes['/']));

        $I->amOnPage('/');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, World!');

        $I->amOnPage('');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, World!');

        $I->sendAjaxPostRequest('/');
        $I->seeResponseCodeIs(404);

        $I->amOnPage('/tests/value/id/wildcard/1234');
        $I->seeResponseCodeIs(404);

        $I->sendAjaxPostRequest('/tests/value/id/wildcard/1234');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Post!, subpath: value, name: id, wildcard: wildcard/1234');

        $I->sendAjaxRequest('PUT', '/put');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Put!');

        $I->sendAjaxRequest('DELETE', '/delete');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Delete!');

        $I->sendAjaxRequest('PATCH', '/patch');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Patch!');

        $I->sendAjaxRequest('OPTIONS', '/options');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Options!');

        $I->sendAjaxRequest('HEAD', '/head');
        $I->seeResponseCodeIs(200);
        $I->see('Hello, Head!');

        $I->sendAjaxRequest('PUT', '/delete');
        $I->seeResponseCodeIs(404);

        $I->sendAjaxRequest('DELETE', '/patch');
        $I->seeResponseCodeIs(404);

        $I->sendAjaxRequest('PATCH', '/options');
        $I->seeResponseCodeIs(404);

        $I->sendAjaxRequest('OPTIONS', '/head');
        $I->seeResponseCodeIs(404);

        $I->sendAjaxRequest('HEAD', '/put');
        $I->seeResponseCodeIs(404);

        $I->amOnPage('/not-found');
        $I->seeResponseCodeIs(404);

        $I->amOnPage('/bad');
        $I->seeResponseCodeIs(500);

        $app = $I->getApp();

        unset($app->routes['/']);

        $I->amOnPage('/');
        $I->seeResponseCodeIs(404);

        $I->expectThrowable(RouterException::class, fn() => $app->routes['/'] = 'test');

        $I->expectThrowable(
            RouterException::class,
            fn() => $app->routes['invalid\url@pattern']->get(GetController::class)
        );
    }

    public function canHaveMultipleMethodsDefined(FunctionalTester $I): void
    {
        $I->getApp()->routes['/route']
            ->get(GetController::class)
            ->post(PostController::class)
            ->put(PutController::class)
            ->patch(PatchController::class)
            ->delete(DeleteController::class)
            ->head(HeadController::class)
            ->options(OptionsController::class);
        ;

        $I->sendAjaxRequest('Get', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Post', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Put', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Patch', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Delete', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Head', '/route');
        $I->seeResponseCodeIs(200);
        $I->sendAjaxRequest('Options', '/route');
        $I->seeResponseCodeIs(200);
    }
}
