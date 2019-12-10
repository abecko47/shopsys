<?php

namespace Tests\FrameworkBundle\Unit\Model\Product\Pricing;

use PHPUnit\Framework\TestCase;
use Shopsys\FrameworkBundle\Component\Domain\Domain;
use Shopsys\FrameworkBundle\Component\Money\Money;
use Shopsys\FrameworkBundle\Model\Customer\CurrentCustomerUser;
use Shopsys\FrameworkBundle\Model\Customer\User;
use Shopsys\FrameworkBundle\Model\Customer\UserData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroup;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupData;
use Shopsys\FrameworkBundle\Model\Pricing\Group\PricingGroupSettingFacade;
use Shopsys\FrameworkBundle\Model\Pricing\Price;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPrice;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculation;
use Shopsys\FrameworkBundle\Model\Product\Pricing\ProductPriceCalculationForUser;
use Shopsys\FrameworkBundle\Model\Product\Product;

class ProductPriceCalculationForUserTest extends TestCase
{
    public function testCalculatePriceByUserAndDomainIdWithUser()
    {
        $product = $this->createMock(Product::class);
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroup = new PricingGroup($pricingGroupData, 1);
        $userData = new UserData();
        $userData->pricingGroup = $pricingGroup;
        $userData->email = 'no-reply@shopsys.com';
        $userData->domainId = 1;
        $user = new User($userData, null);
        $expectedProductPrice = new ProductPrice(new Price(Money::create(1), Money::create(1)), false);

        $currentCustomerMock = $this->createMock(CurrentCustomerUser::class);
        $pricingGroupSettingFacadeMock = $this->createMock(PricingGroupSettingFacade::class);

        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->createMock(Domain::class);

        $productPriceCalculationForUser = new ProductPriceCalculationForUser(
            $productPriceCalculationMock,
            $currentCustomerMock,
            $pricingGroupSettingFacadeMock,
            $domainMock
        );

        $productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, 1, $user);
        $this->assertSame($expectedProductPrice, $productPrice);
    }

    public function testCalculatePriceByUserAndDomainIdWithoutUser()
    {
        $domainId = 1;
        $product = $this->createMock(Product::class);
        $pricingGroupData = new PricingGroupData();
        $pricingGroupData->name = 'name';
        $pricingGroup = new PricingGroup($pricingGroupData, $domainId);
        $expectedProductPrice = new ProductPrice(new Price(Money::create(1), Money::create(1)), false);

        $currentCustomerMock = $this->createMock(CurrentCustomerUser::class);

        $pricingGroupFacadeMock = $this->getMockBuilder(PricingGroupSettingFacade::class)
            ->setMethods(['getDefaultPricingGroupByDomainId'])
            ->disableOriginalConstructor()
            ->getMock();
        $pricingGroupFacadeMock
            ->expects($this->once())
            ->method('getDefaultPricingGroupByDomainId')
            ->with($this->equalTo($domainId))
            ->willReturn($pricingGroup);

        $productPriceCalculationMock = $this->getMockBuilder(ProductPriceCalculation::class)
            ->setMethods(['calculatePrice'])
            ->disableOriginalConstructor()
            ->getMock();
        $productPriceCalculationMock->expects($this->once())->method('calculatePrice')->willReturn($expectedProductPrice);

        $domainMock = $this->createMock(Domain::class);

        $productPriceCalculationForUser = new ProductPriceCalculationForUser(
            $productPriceCalculationMock,
            $currentCustomerMock,
            $pricingGroupFacadeMock,
            $domainMock
        );

        $productPrice = $productPriceCalculationForUser->calculatePriceForUserAndDomainId($product, $domainId, null);
        $this->assertSame($expectedProductPrice, $productPrice);
    }
}
