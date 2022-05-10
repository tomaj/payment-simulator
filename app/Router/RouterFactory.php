<?php

declare(strict_types=1);

namespace App\Router;

use Nette;
use Nette\Application\Routers\RouteList;


final class RouterFactory
{
    use Nette\StaticClass;

    public static function createRouter(): RouteList
    {
        $router = new RouteList;

        $router->addRoute('/cgi-bin/e-commerce/start/api/cardpay/transaction/<processingId>', 'Payment:CardPayDirectPayCheckTransaction');
        $router->addRoute('/cgi-bin/e-commerce/start/api/form/cardpay/transaction/tds', 'Payment:CardPayDirectPay3ds');
        $router->addRoute('/cgi-bin/e-commerce/start/api/cardpay/transaction', 'Payment:CardPayDirectPayPostTransaction');

        $router->addRoute('<presenter>/<action>[/<id>]', 'Homepage:default');
        return $router;
    }
}
