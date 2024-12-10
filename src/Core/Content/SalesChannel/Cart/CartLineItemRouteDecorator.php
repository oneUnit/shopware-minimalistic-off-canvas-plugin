<?php declare(strict_types=1);

namespace MinimalisticOffCanvas\Core\Content\SalesChannel\Cart;

use Shopware\Core\Checkout\Cart\Cart;
use Shopware\Core\Framework\DataAbstractionLayer\EntityRepository;
use Shopware\Core\Framework\Validation\DataBag\RequestDataBag;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Storefront\Controller\CartLineItemController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CartLineItemRouteDecorator extends AbstractCartLineItemRoute
{
    protected EntityRepository $exampleRepository;

    private CartLineItemController $decorated;

    public function __construct( CartLineItemController $cartLineItemController)
    {
        $this->decorated = $cartLineItemController;
    }

    public function getDecorated(): CartLineItemController
    {
        return $this->decorated;
    }

    #[Route(path: '/checkout/line-item/add', name: 'frontend.checkout.line-item.add', defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function addLineItems(Cart $cart, RequestDataBag $requestDataBag, Request $request, SalesChannelContext $context): Response
    {
        $itemIdsAdded = [];
        if($requestDataBag->get("lineItems") !== null){
            foreach ($requestDataBag->get("lineItems") as $lineItem){
                if($lineItem->get("id")){
                    $itemIdsAdded[] = $lineItem->get("id");
                }
            }
        }
        if(count($itemIdsAdded) > 0){
            $request->attributes->add(["redirectParameters" => '{"newItemAdded" : true, "itemIdsAdded" : '. json_encode($itemIdsAdded) . '}']);
        }

        return $this->decorated->addLineItems($cart, $requestDataBag, $request, $context);
    }

    #[Route(path: '/checkout/line-item/delete/{id}', name: 'frontend.checkout.line-item.delete', defaults: ['XmlHttpRequest' => true], methods: ['POST', 'DELETE'])]
    public function deleteLineItem(Cart $cart, string $id, Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->deleteLineItems($cart, $id, $request, $context);
    }

    #[Route(path: '/checkout/line-item/delete', name: 'frontend.checkout.line-items.delete', defaults: ['XmlHttpRequest' => true], methods: ['POST', 'DELETE'])]
    public function deleteLineItems(Cart $cart, Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->deleteLineItems($cart, $request, $context);
    }

    #[Route(path: '/checkout/promotion/add', name: 'frontend.checkout.promotion.add', defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function addPromotion(Cart $cart, Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->addPromotion($cart, $request, $context);
    }

    #[Route(path: '/checkout/line-item/change-quantity/{id}', name: 'frontend.checkout.line-item.change-quantity', defaults: ['XmlHttpRequest' => true], methods: ['POST'])]
    public function changeQuantity(Cart $cart, string $id, Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->changeQuantity($cart, $id, $request, $context);
    }

    #[Route(path: '/checkout/line-item/update', name: 'frontend.checkout.line-items.update', defaults: ['XmlHttpRequest' => true], methods: ['POST', 'PATCH'])]
    public function updateLineItems(Cart $cart, RequestDataBag $requestDataBag, Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->updateLineItems($cart, $requestDataBag, $request, $context);
    }

    #[Route(path: '/checkout/product/add-by-number', name: 'frontend.checkout.product.add-by-number', methods: ['POST'])]
    public function addProductByNumber(Request $request, SalesChannelContext $context): Response
    {
        return $this->decorated->addProductByNumber($request, $context);
    }
}