<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\DataProvider;

use Magento\Catalog\Model\Product;
use Magento\ConfigurableProduct\Block\Product\View\Type\Configurable;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable as ConfigurableType;
use Magento\Framework\View\LayoutInterface;
use Magento\Swatches\Block\Product\Renderer\Configurable as SwatchesConfigurable;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\DataProviderInterface;

class JsonConfigProvider implements DataProviderInterface
{
    /**
     * @var LayoutInterface
     */
    private $layout;

    /**
     * @var Configurable
     */
    private $configurableBlock = null;

    /**
     * @var SwatchesConfigurable
     */
    private $swatchesBlock = null;

    /**
     * JsonConfigProvider constructor.
     * @param LayoutInterface $layout
     */
    public function __construct(
        LayoutInterface $layout
    ) {
        $this->layout = $layout;
    }

    /**
     * @param array $products
     * @param FeedSpecificationInterface $feedSpecification
     * @return array
     */
    public function getData(array $products, FeedSpecificationInterface $feedSpecification): array
    {
        if (!$feedSpecification->getIncludeJSONConfig()) {
            return $products;
        }

        $ignoredFields = $feedSpecification->getIgnoreFields();
        foreach ($products as &$product) {
            /** @var Product $productModel */
            $productModel = $product['product_model'] ?? null;
            if (!$productModel) {
                continue;
            }

            if(ConfigurableType::TYPE_CODE === $productModel->getTypeId()) {
                if (!in_array('json_config', $ignoredFields)) {
                    $configurableBlock = $this->getConfigurableBlock();
                    $configurableBlock->setProduct($productModel);
                    $product['json_config'] = $configurableBlock->getJsonConfig();
                }

                if (!in_array('swatch_json_config', $ignoredFields)) {
                    $configurableBlock = $this->getSwatchesBlock();
                    $configurableBlock->setProduct($productModel);
                    $product['swatch_json_config'] = $configurableBlock->getJsonSwatchConfig();
                }
            }
        }

        return $products;
    }

    /**
     * @return Configurable
     */
    private function getConfigurableBlock() : Configurable
    {
        if (!$this->configurableBlock) {
            $this->configurableBlock = $this->layout->createBlock(Configurable::class);
        }

        return $this->configurableBlock;
    }

    /**
     * @return SwatchesConfigurable
     */
    private function getSwatchesBlock() : SwatchesConfigurable
    {
        if (!$this->swatchesBlock) {
            $this->swatchesBlock = $this->layout->createBlock(SwatchesConfigurable::class);
        }

        return $this->swatchesBlock;
    }

    /**
     *
     */
    public function reset(): void
    {
        $this->configurableBlock = null;
        $this->swatchesBlock = null;
    }
}
