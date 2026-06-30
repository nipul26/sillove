<?php

namespace Sillove\JewelleryPrice\Test\Unit\Model;

use Sillove\JewelleryPrice\Model\JewelleryPriceCalculator;
use Magento\Framework\App\Config\ScopeConfigInterface;
use PHPUnit\Framework\TestCase;
use Magento\Store\Model\ScopeInterface;

class JewelleryPriceCalculatorTest extends TestCase
{
    /**
     * @var ScopeConfigInterface|\PHPUnit\Framework\MockObject\MockObject
     */
    private $scopeConfigMock;

    /**
     * @var JewelleryPriceCalculator
     */
    private $calculator;

    protected function setUp(): void
    {
        $this->scopeConfigMock = $this->getMockBuilder(ScopeConfigInterface::class)
            ->disableOriginalConstructor()
            ->getMock();

        $this->calculator = new JewelleryPriceCalculator($this->scopeConfigMock);
    }

    /**
     * @dataProvider calculationDataProvider
     */
    public function testCalculate($labor, $silverWeight, $silverRate, $discount, $expectedPrice, $expectedSpecialPrice)
    {
        $this->scopeConfigMock->expects($this->any())
            ->method('getValue')
            ->willReturnMap([
                ['jewellery/general/silver_rate', ScopeInterface::SCOPE_STORE, null, $silverRate],
                ['jewellery/general/discount_percentage', ScopeInterface::SCOPE_STORE, null, $discount]
            ]);

        $result = $this->calculator->calculate((float)$silverWeight, (float)$labor);
        $this->assertIsArray($result);
        $this->assertEquals($expectedPrice, $result['price']);
        $this->assertEquals($expectedSpecialPrice, $result['special_price']);
    }

    public function calculationDataProvider()
    {
        return [
            // (Labor + Rate) * Weight = Base
            // 5% discount: 5000 -> 4750 -> 4749
            [25, 40, 100, 5, 5000, 4749],
            
            // (50 + 150) * 10 = 2000
            // 10% discount: 2000 -> 1800 -> 1799
            [50, 10, 150, 10, 2000, 1799],
            
            // (10 + 90) * 100 = 10000
            // No discount: 10000 -> 10000 -> 9999
            [10, 100, 90, 0, 10000, 9999],
            
            // Decimal case: (25 + 100.5) * 40 = 125.5 * 40 = 5020
            // 5% discount: 5020 - 251 = 4769 -> 4769 (ends in 9)
            [25, 40, 100.5, 5, 5020, 4769],
            
            // Another decimal: 5021 -> 4769.95 -> 4769
            [25.025, 40, 100.5, 5, 5021, 4769]
        ];
    }
}
