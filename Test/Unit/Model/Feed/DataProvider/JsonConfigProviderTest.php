<?php

namespace SearchSpring\Feed\Test\Unit\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\Framework\View\LayoutInterface;
use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchesConfigurable;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\DataProvider;
use SearchSpring\Feed\Model\Feed\DataProvider\JsonConfigProvider;

class JsonConfigProviderTest extends \PHPUnit\Framework\TestCase
{
    public function setUp(): void
    {
        $this->layoutMock = $this->createMock(LayoutInterface::class);
        $this->dataProviderMock = $this->createMock(DataProvider::class);
        $this->jsonConfigProvider = new JsonConfigProvider($this->layoutMock, $this->dataProviderMock);
    }

    public function testGetData()
    {
        $configurableBlockMock = $this->createMock(Configurable::class);
        $configurableSwatchesBlockMock = $this->createMock(SwatchesConfigurable::class);
        $productMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $configurableProductMock = $this->getMockBuilder(Product::class)
            ->disableOriginalConstructor()
            ->getMock();
        $products = [
            [
                'product_model' => $productMock,
            ],
            [
                'product_model' => $configurableProductMock,
            ],
        ];
        $configurableProductMock->expects($this->any())
            ->method('getId')
            ->willReturn(1);
        $feedSpecificationMock = $this->getMockForAbstractClass(FeedSpecificationInterface::class);
        $feedSpecificationMock->expects($this->once())
            ->method('getIncludeJSONConfig')
            ->willReturn(true);
        $this->dataProviderMock->expects($this->once())
            ->method('getAllChildProducts')
            ->with($products, $feedSpecificationMock)
            ->willReturn([]);
        $feedSpecificationMock->expects($this->once())
            ->method('getIgnoreFields')
            ->willReturn([]);

        $productMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn('simple');
        $configurableProductMock->expects($this->once())
            ->method('getTypeId')
            ->willReturn('configurable');
        $this->layoutMock->expects($this->at(0))
            ->method('createBlock')
            ->with(Configurable::class)
            ->willReturn($configurableBlockMock);
        $this->layoutMock->expects($this->at(1))
            ->method('createBlock')
            ->with(SwatchesConfigurable::class)
            ->willReturn($configurableSwatchesBlockMock);
        $configurableBlockMock->expects($this->once())
            ->method('unsetData')
            ->willReturnSelf();
        $configurableSwatchesBlockMock->expects($this->once())
            ->method('unsetData')
            ->willReturnSelf();
        $configurableSwatchesBlockMock->expects($this->once())
            ->method('setProduct')
            ->with($configurableProductMock)
            ->willReturnSelf();
        $configurableBlockMock->expects($this->once())
            ->method('getJsonConfig')
            ->willReturn('{testc: testc}');
        $configurableSwatchesBlockMock->expects($this->once())
            ->method('getJsonSwatchConfig')
            ->willReturn('{testsw: testsw}');

        $this->assertSame(
            [
                [
                    'product_model' => $productMock
                ],
                [
                    'product_model' => $configurableProductMock,
                    'json_config' => '{testc: testc}',
                    'swatch_json_config' => '{testsw: testsw}'
                ]
            ],
            $this->jsonConfigProvider->getData($products, $feedSpecificationMock)
        );
    }
}
