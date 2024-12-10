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
}