<?php

namespace Sillove\JewelleryPrice\Setup\Patch\Data;

use Magento\Eav\Setup\EavSetupFactory;
use Magento\Framework\Setup\Patch\DataPatchInterface;
use Magento\Framework\Setup\ModuleDataSetupInterface;
use Magento\Catalog\Model\Product;

class AddJewelleryAttributes implements DataPatchInterface
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

    public function apply()
    {
        $eavSetup = $this->eavSetupFactory->create(['setup' => $this->moduleDataSetup]);

        $attributes = [
            'labor_charge' => [
                'label' => 'Labor Charge',
                'note' => 'Labor cost per gram. Price = (Labor + Silver Rate) × Silver Weight.',
            ],
            'silver_weight' => [
                'label' => 'Silver Weight',
                'note' => 'Weight in grams. Labor and Silver Rate are per gram; price = (Labor + Silver Rate) × Silver Weight.',
            ],
        ];

        foreach ($attributes as $code => $data) {
            $eavSetup->addAttribute(
                Product::ENTITY,
                $code,
                [
                    'type' => 'decimal',
                    'label' => $data['label'],
                    'input' => 'price',
                    'required' => false,
                    'global' => 1,
                    'visible' => true,
                    'user_defined' => true,
                    'group' => 'General',
                    'note' => $data['note'],
                ]
            );
        }
    }

    public static function getDependencies() { return []; }
    public function getAliases() { return []; }
}
