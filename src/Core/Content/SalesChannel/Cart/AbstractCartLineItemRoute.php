<?php declare(strict_types=1);

namespace MinimalisticOffCanvas\Core\Content\SalesChannel\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\CartLineItemController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

abstract class AbstractCartLineItemRoute
{
    abstract public function getDecorated(): CartLineItemController;

    abstract public function addLineItems(Cart $cart, RequestDataBag $requestDataBag, Request $request, SalesChannelContext $context): Response;

}