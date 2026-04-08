<?php

/**
 * Copyright © Lillik. All rights reserved.
 * See LICENSE.txt for license details.
 */

declare(strict_types=1);

namespace Lillik\PriceDecimal\Test\Unit\Plugin\Model;

use Lillik\PriceDecimal\Model\ConfigInterface;
use Lillik\PriceDecimal\Model\Plugin\PriceCurrency;
use Magento\Directory\Model\PriceCurrency as MagentoPriceCurrency;
use Magento\Framework\TestFramework\Unit\Helper\ObjectManager;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

/**
 * Unit test for PriceCurrency plugin.
 */
class PriceCurrencyTest extends TestCase
{
    /**
     * @var callable
     */
    private $closureMock;

    /**
     * @var MagentoPriceCurrency|MockObject
     */
    private $priceCurrencyMock;

    /**
     * @inheritDoc
     */
    protected function setUp(): void
    {
        $this->closureMock = function (...$args) {
            return number_format($args[0], $args[2]);
        };

        $this->priceCurrencyMock = $this->getMockBuilder(MagentoPriceCurrency::class)
            ->disableOriginalConstructor()
            ->getMock();
    }

    /**
     * Test aroundFormat plugin method applies correct precision.
     *
     * @return void
     */
    public function testAroundFormat(): void
    {
        $price = 22.54;
        $args = [
            $price,
            true,
            2
        ];

        $pricePrecision = 2;

        $configMock = $this->getMockBuilder(ConfigInterface::class)
            ->disableOriginalConstructor()
            ->onlyMethods(['isEnable', 'getScopeConfig'])
            ->addMethods(['canShowPriceDecimal', 'getPricePrecision'])
            ->getMock();

        $configMock->expects($this->any())->method('isEnable')->willReturn(1);
        $configMock->expects($this->any())->method('canShowPriceDecimal')->willReturn(1);
        $configMock->expects($this->any())->method('getPricePrecision')->willReturn($pricePrecision);

        $objectManager = new ObjectManager($this);
        $model = $objectManager->getObject(
            PriceCurrency::class,
            [
                'moduleConfig' => $configMock
            ]
        );

        $result = $model->aroundRound(
            $this->priceCurrencyMock,
            function ($price) {
                return round($price, 2);
            },
            $price
        );

        $this->assertEquals(round($price, $pricePrecision), $result);
    }
}
