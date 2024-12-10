<?php declare(strict_types=1);

namespace MinimalisticOffCanvas\Core\Content\SalesChannel\Checkout;

use Shopware\Core\Content\Product\Aggregate\ProductCrossSelling\ProductCrossSellingCollection;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Sorting\FieldSorting;
use Shopware\Core\System\SalesChannel\Entity\SalesChannelRepository;
use Shopware\Core\System\SalesChannel\SalesChannelContext;
use Shopware\Core\System\SystemConfig\SystemConfigService;
use Shopware\Storefront\Controller\CheckoutController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route(defaults: ['_routeScope' => ['storefront']])]
class CheckoutRouteDecorator extends AbstractCheckoutRouteDecorator
{
    protected SalesChannelRepository $productRepository;

    private CheckoutController $decorated;

    private SystemConfigService $systemConfigService;
    public function __construct( CheckoutController $exampleRoute, SalesChannelRepository $productRepository, SystemConfigService $systemConfigService)
    {
        $this->decorated = $exampleRoute;
        $this->productRepository = $productRepository;
        $this->systemConfigService = $systemConfigService;
    }

    public function getDecorated(): CheckoutController
    {
        return $this->decorated;
    }

    #[Route(path: '/checkout/offcanvas', name: 'frontend.cart.offcanvas', options: ['seo' => false], defaults: ['XmlHttpRequest' => true], methods: ['GET'])]
    public function offcanvas(Request $request, SalesChannelContext $context): Response
    {
        $newItemAdded = $request->get("newItemAdded");
        $itemIdsAdded = $request->get("itemIdsAdded");
        if($newItemAdded && $itemIdsAdded){

            $context->addArrayExtension('showMinimal', [ 'show-minimal' => 1,  'item-ids-added' => $itemIdsAdded]);

            $criteria = new Criteria();
            $criteria->addFilter(new EqualsFilter('id', $itemIdsAdded[0]));
            $criteria->addAssociation('crossSellings');
            $criteria->getAssociation('crossSellings')
                ->addSorting(new FieldSorting('position'));
            $criteria->addAssociation('crossSellings.position');
            $criteria->addAssociation('crossSellings.active');
            $criteria->addAssociation('crossSellings.assignedProducts');
            $criteria->getAssociation('crossSellings.assignedProducts')
                ->addSorting(new FieldSorting('position'));
            $criteria->addAssociation('crossSellings.assignedProducts.product');
            $mainProduct = $this->productRepository->search($criteria, $context)->first();

            $positionToUse = $mainProduct->getCustomFields()['cross_selling_group_position'] ?? $this->getCrossSellingPositionToUse();

            $productCrossSellings = $mainProduct->getCrossSellings() ?? new ProductCrossSellingCollection();
            foreach ($productCrossSellings as $productCrossSelling){
                // Return first active cross-selling group if no position to use was defined
                if($productCrossSelling->isActive() && (!$positionToUse || $productCrossSelling->getPosition() == $positionToUse)){

                    $crossSellingProducts = $productCrossSelling->getAssignedProducts();
                    $context->addExtension('crossSellingProducts', $crossSellingProducts);
                }
            }


        }

        return $this->decorated->offcanvas($request, $context);
    }

    private function getCrossSellingPositionToUse(): ?int
    {
        return $this->systemConfigService->get("MinimalisticOffCanvas.config.defaultCrossSellingGroupPosition");
    }
}