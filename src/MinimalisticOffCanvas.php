<?php declare(strict_types=1);

namespace MinimalisticOffCanvas;

use Shopware\Core\Defaults;
use Shopware\Core\Framework\Context;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Criteria;
use Shopware\Core\Framework\DataAbstractionLayer\Search\Filter\EqualsAnyFilter;
use Shopware\Core\Framework\DataAbstractionLayer\Search\IdSearchResult;
use Shopware\Core\Framework\Plugin;
use Shopware\Core\Framework\Plugin\Context\ActivateContext;
use Shopware\Core\Framework\Plugin\Context\DeactivateContext;
use Shopware\Core\Framework\Plugin\Context\InstallContext;
use Shopware\Core\Framework\Plugin\Context\UninstallContext;
use Shopware\Core\Framework\Plugin\Context\UpdateContext;
use Shopware\Core\Framework\Uuid\Uuid;
use Shopware\Core\System\CustomField\CustomFieldTypes;

class MinimalisticOffCanvas extends Plugin
{
    protected const CUSTOMER_FIELD_SET_NAME = "product_cross_selling_group_position";
    public function install(InstallContext $installContext): void
    {
        if($this->customFieldsExist($installContext->getContext())){
            return;
        }
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $customFieldSetRepository->create([
            [
                'name' => self::CUSTOMER_FIELD_SET_NAME,
                'config' => [
                    'label' => [
                        'en-GB' => 'Minimalistic Off Canvas',
                        'de-DE' => 'Minimalistic Off Canvas',
                        Defaults::LANGUAGE_SYSTEM => "Minimalistic Off Canvas"
                    ]
                ],
                'customFields' => [
                    [
                        'name' => 'cross_selling_group_position',
                        'type' => CustomFieldTypes::INT,
                        'config' => [
                            'label' => [
                                'en-GB' => 'Cross Selling Group position to use',
                                'de-DE' => 'Cross Selling Group position zu nutzen',
                                Defaults::LANGUAGE_SYSTEM => "Cross Selling Group position to use"
                            ],
                            'customFieldPosition' => 1,
                            'defaultValue' => null,
                            'customFieldRequired' => false,
                        ]
                    ]
                ],
                'relations' => [
                    [
                        'id' => Uuid::randomHex(),
                        'entityName' => 'product'
                    ]
                ]
            ]
        ], $installContext->getContext());
    }

    public function uninstall(UninstallContext $uninstallContext): void
    {
        parent::uninstall($uninstallContext);

        if ($uninstallContext->keepUserData()) {
            return;
        }

        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $fieldIds = $this->customFieldsExist($uninstallContext->getContext());

        if ($fieldIds) {
            $customFieldSetRepository->delete(array_values($fieldIds->getData()), $uninstallContext->getContext());
        }

    }

    public function activate(ActivateContext $activateContext): void
    {
        // Activate entities, such as a new payment method
        // Or create new entities here, because now your plugin is installed and active for sure
    }

    public function deactivate(DeactivateContext $deactivateContext): void
    {
        // Deactivate entities, such as a new payment method
        // Or remove previously created entities
    }

    public function update(UpdateContext $updateContext): void
    {
        // Update necessary stuff, mostly non-database related
    }

    public function postInstall(InstallContext $installContext): void
    {
    }

    public function postUpdate(UpdateContext $updateContext): void
    {
    }

    private function customFieldsExist(Context $context): ?IdSearchResult
    {
        $customFieldSetRepository = $this->container->get('custom_field_set.repository');

        $criteria = new Criteria();
        $criteria->addFilter(new EqualsAnyFilter('name', [self::CUSTOMER_FIELD_SET_NAME]));

        $ids = $customFieldSetRepository->searchIds($criteria, $context);

        return $ids->getTotal() > 0 ? $ids : null;
    }

}
