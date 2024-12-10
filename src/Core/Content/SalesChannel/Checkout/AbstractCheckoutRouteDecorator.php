<?php declare(strict_types=1);

namespace MinimalisticOffCanvas\Core\Content\SalesChannel\Checkout;

use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\CheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCheckoutRouteDecorator
{
    abstract public function getDecorated(): CheckoutController;

    abstract public function offcanvas(Request $request, SalesChannelContext $context): Response;
}