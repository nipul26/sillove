<?php

namespace Sillove\JewelleryPrice\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class AddSilverWeightAttribute implements DataPatchInterface
{
    private $eavSetupFactory;
    private $moduleDataSetup;

    public function __construct(
        ModuleDataSetupInterface $moduleDataSetup,
        EavSetupFactory $eavSetupFactory
    ) {
        $this->moduleDataSetup = $moduleDataSetup;
        $this->eavSetupFactory = $eavSetupFactory;
    }

    private const SILVER_WEIGHT_NOTE = 'Weight in grams. Labor and Silver Rate are per gram; price = (Labor + Silver Rate) × Silver Weight.';

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        if (!$eavSetup->getAttributeId(Product::ENTITY, 'silver_weight')) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                'silver_weight',
                [
                    'type' => 'decimal',
                    'label' => 'Silver Weight',
                    'input' => 'price',
                    'required' => false,
                    'global' => 1,
                    'visible' => true,
                    'user_defined' => true,
                    'group' => 'General',
                    'note' => self::SILVER_WEIGHT_NOTE,
                ]
            );
        } else {
            $eavSetup->updateAttribute(
                Product::ENTITY,
                'silver_weight',
                'note',
                self::SILVER_WEIGHT_NOTE
            );
        }
    }

    public static function getDependencies()
    {
        return [AddJewelleryAttributes::class];
    }

    public function getAliases()
    {
        return [];
    }
}
