<?php

namespace SearchSpring\Feed\Model;

use Magento\ConfigurableProduct\Pricing\Price\ConfigurableOptionsProviderInterface;
use SearchSpring\Feed\Api\ProductRepositoryInterface;
use SearchSpring\Feed\Api\RequestItemInterfaceFactory;
use SearchSpring\Feed\Api\ResponseItemInterfaceFactory;
use Magento\Catalog\Api\Data\ProductInterface;
use Magento\Catalog\Model\ResourceModel\Product\Action;
use Magento\Catalog\Model\ResourceModel\Product\CollectionFactory;
use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\StoreManagerInterface;
use Magento\CatalogInventory\Api\StockRegistryInterface;
use Magento\Catalog\Api\CategoryRepositoryInterface;
use Magento\ConfigurableProduct\Model\Product\Type\Configurable;
use Magento\Eav\Model\Config;
use Magento\ConfigurableProduct\Block\Product\View\Type\ConfigurableFactory;
use Magento\Catalog\Api\ProductRepositoryInterface as MagentoProductRepository;
use Magento\Review\Model\ReviewFactory;
use Magento\Catalog\Helper\ImageFactory;
use Magento\Swatches\Block\Product\Renderer\ConfigurableFactory as ConfigurableBlock;
use SearchSpring\Feed\Model\Feed\DataProvider\Configurable\DataProvider;
use Magento\Catalog\Helper\Data;

/**
 * Class ProductRepository
 */
class ProductRepository implements ProductRepositoryInterface
{
    /**
     * @var StockRegistryInterface
     */
    protected $stockRegistry;
    /**
     * @var CategoryRepositoryInterface
     */
    protected $categoryRepository;
    /**
     * @var Configurable
     */
    protected $configurableProduct;
    /**
     * @var ConfigurableFactory
     */
    protected $configurableBlockFactory;
    /**
     * @var Config
     */
    protected $eavConfig;
    /**
     * @var MagentoProductRepository
     */
    protected $productRepository;
    /**
     * @var ReviewFactory
     */
    protected $reviewFactory;
    /**
     * @var ImageFactory
     */
    protected $imageHelperFactory;
    /**
     * @var ConfigurableBlock
     */
    protected $swatchBlockFactory;
    /**
     * @var Action
     */
    private $productAction;
    /**
     * @var CollectionFactory
     */
    private $productCollectionFactory;
    /**
     * @var RequestItemInterfaceFactory
     */
    private $requestItemFactory;
    /**
     * @var ResponseItemInterfaceFactory
     */
    private $responseItemFactory;
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var DataProvider
     */
    private $provider;

    /**
     * @var ConfigurableOptionsProviderInterface
     */
    private $configurableOptionsProvider;
    /**
     * @var data
     */
    private $taxHelper;

    /**
     * @var GenerateFeed
     */
    private $generateFeed;

    /**
     * @param Action $productAction
     * @param CollectionFactory $productCollectionFactory
     * @param RequestItemInterfaceFactory $requestItemFactory
     * @param ResponseItemInterfaceFactory $responseItemFactory
     * @param StoreManagerInterface $storeManager
     * @param StockRegistryInterface $stockRegistry
     * @param CategoryRepositoryInterface $categoryRepository
     * @param Configurable $configurableProduct
     * @param Config $eavConfig
     * @param ConfigurableFactory $configurableBlockFactory
     * @param MagentoProductRepository $productRepository
     * @param ReviewFactory $reviewFactory
     * @param ImageFactory $imageHelperFactory
     * @param ConfigurableBlock $swatchBlockFactory
     * @param DataProvider $provider
     * @param ConfigurableOptionsProviderInterface $configurableOptionsProvider
     * @param data $taxHelper
     * @param GenerateFeed $generateFeed
     */
    public function __construct(
        Action                               $productAction,
        CollectionFactory                    $productCollectionFactory,
        RequestItemInterfaceFactory          $requestItemFactory,
        ResponseItemInterfaceFactory         $responseItemFactory,
        StoreManagerInterface                $storeManager,
        StockRegistryInterface               $stockRegistry,
        CategoryRepositoryInterface          $categoryRepository,
        Configurable                         $configurableProduct,
        Config                               $eavConfig,
        ConfigurableFactory                  $configurableBlockFactory,
        MagentoProductRepository             $productRepository,
        ReviewFactory                        $reviewFactory,
        ImageFactory                         $imageHelperFactory,
        ConfigurableBlock                    $swatchBlockFactory,
        DataProvider                         $provider,
        ConfigurableOptionsProviderInterface $configurableOptionsProvider,
        data                                 $taxHelper,
        GenerateFeed                         $generateFeed
    )
    {
        $this->productAction = $productAction;
        $this->productCollectionFactory = $productCollectionFactory;
        $this->requestItemFactory = $requestItemFactory;
        $this->responseItemFactory = $responseItemFactory;
        $this->storeManager = $storeManager;
        $this->stockRegistry = $stockRegistry;
        $this->categoryRepository = $categoryRepository;
        $this->configurableProduct = $configurableProduct;
        $this->eavConfig = $eavConfig;
        $this->configurableBlockFactory = $configurableBlockFactory;
        $this->productRepository = $productRepository;
        $this->reviewFactory = $reviewFactory;
        $this->imageHelperFactory = $imageHelperFactory;
        $this->swatchBlockFactory = $swatchBlockFactory;
        $this->provider = $provider;
        $this->configurableOptionsProvider = $configurableOptionsProvider;
        $this->taxHelper = $taxHelper;
        $this->generateFeed = $generateFeed;
    }

    /**
     * {@inheritDoc}
     *
     * @param int $id
     * @return array
     * @throws NoSuchEntityException
     */
    public function getItem(int $id): array
    {
        // First get the raw product data array
        $rawProductData = $this->getRawProductData($id);

        // Apply field mapping
        $productData = $this->applyFieldMapping($rawProductData);

        // Get the product object for additional data
        $product = $this->productRepository->getById($id);


        // Add all additional fields that weren't in the original mapping
        $productData = array_merge($productData, $this->getAdditionalProductData($product));

        // Handle configurable product specific data
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $productData = array_merge($productData, $this->getConfigurableProductData($product));
        }
        // Handle configurable product specific data
        if ($product->getTypeId() === \Magento\Bundle\Model\Product\Type::TYPE_CODE) {
            $productData = array_merge($productData, $this->getBundleProductPrices($product));
        }

        // Handle configurable product specific data
        if ($product->getTypeId() === \Magento\GroupedProduct\Model\Product\Type\Grouped::TYPE_CODE) {
            $productData = array_merge($productData, $this->getGroupedProductPrices($product, $id));
        }

        return $this->formatProductData([$productData]);
        // return $productData;

    }

    /**
     * Get raw product data array (as you originally had it)
     */
    protected function getRawProductData(int $id): array
    {
        $collection = $this->getProductCollection()
            ->addAttributeToFilter('entity_id', ['eq' => $id])
            ->addAttributeToSelect('*');

        /** @var ProductInterface $product */
        $product = $collection->getFirstItem();

        if (!$product->getId()) {
            throw new NoSuchEntityException(__('Product not found'));
        }

        // Return as numerically indexed array as per your original format
        return [
            $product->getId(),
            $product->getAttributeSetId(),
            $product->getTypeId(),
            $product->getSku(),
            $product->getHasOptions(),
            $product->getRequiredOptions(),
            $product->getCreatedAt(),
            $product->getUpdatedAt(),
            $product->getStatus(),
            $product->getPrice(),
            $product->getFinalPrice(),
            $product->getName(),
            $product->getImage(),
            $product->getSmallImage(),
            $product->getThumbnail(),
            $product->getDescription(),
            $product->getShortDescription(),
            $product->getUrlKey(),
            $product->getVisibility(),
            $this->getStockStatusText($product),
            $this->stockRegistry->getStockItem($product->getId())->getIsInStock(),
            $this->stockRegistry->getStockItem($product->getId())->getQty(),
            $this->getCategoryNames($product),
            $product->getCategoryIds(),
            $this->getCategoryHierarchy($product),
            $this->getMenuHierarchy($product),
            $this->getUrlHierarchy($product),
            $this->getMediaGallery($product),
            $this->getJsonConfig($product),
            $this->getSwatchConfig($product),
            $this->getCustomAttributesArray($product),
            $product->getSpecialPrice(),
            $product->getSpecialFromDate(),
            $product->getOptionsContainer(),
            $product->getMsrpDisplayActualPriceType(),
            $product->getGiftMessageAvailable(),
            $product->getImageLabel(),
            $product->getSmallImageLabel(),
            $product->getThumbnailLabel(),
            $product->getTaxClassId(),
            $product->getActivityIds(),
            $product->getStyleBagsIds(),
            $product->getMaterialIds(),
            $product->getStrapBagsIds(),
            $product->getFeaturesBagsIds(),
            $product->getEcoCollection(),
            $product->getPerformanceFabric(),
            $product->getErinRecommends(),
            $product->getNew(),
            $product->getSale()
        ];
    }

    /**
     * @return Collection
     */
    private function getProductCollection(): mixed
    {
        /** @var Collection $collection */
        $collection = $this->productCollectionFactory->create();
        $collection
            ->addAttributeToSelect('*');
        return $collection;
    }

    /**
     * Get stock status text
     */
    protected function getStockStatusText($product): string
    {
        $stockItem = $this->stockRegistry->getStockItem($product->getId());
        return $stockItem->getIsInStock() ? 'In Stock' : 'Out of Stock';
    }

    /**
     * Get category names
     */
    protected function getCategoryNames($product): array
    {
        $categoryNames = [];
        foreach ($product->getCategoryIds() as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $categoryNames[] = $category->getName();
            } catch (\Exception $e) {
                continue;
            }
        }
        return $categoryNames;
    }

    /**
     * Get category hierarchy
     */
    protected function getCategoryHierarchy($product): array
    {
        $hierarchy = [];
        foreach ($product->getCategoryIds() as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $pathIds = $category->getPathIds();
                $pathNames = [];
                foreach ($pathIds as $pathId) {
                    if ($pathId == 1) continue; // Skip root category
                    $pathCategory = $this->categoryRepository->get($pathId);
                    $pathNames[] = $pathCategory->getName();
                }
                $hierarchy[] = implode('>', $pathNames);
            } catch (\Exception $e) {
                continue;
            }
        }
        return $hierarchy;
    }

    /**
     * Get menu hierarchy
     */
    protected function getMenuHierarchy($product): array
    {
        // For most cases, same as category hierarchy
        return $this->getCategoryHierarchy($product);
    }

    /**
     * Get URL hierarchy
     */
    protected function getUrlHierarchy($product): array
    {
        $hierarchy = [];
        foreach ($product->getCategoryIds() as $categoryId) {
            try {
                $category = $this->categoryRepository->get($categoryId);
                $pathIds = $category->getPathIds();
                $pathUrls = [];
                foreach ($pathIds as $pathId) {
                    if ($pathId == 1) continue;
                    $pathCategory = $this->categoryRepository->get($pathId);
                    $pathUrls[] = $pathCategory->getName() . '[' . $pathCategory->getUrl() . ']';
                }
                $hierarchy[] = implode('>', $pathUrls);
            } catch (\Exception $e) {
                continue;
            }
        }
        return $hierarchy;
    }

    /**
     * Get media gallery
     */
    protected function getMediaGallery($product): array
    {
        $gallery = [];
        $mediaGallery = $product->getMediaGalleryImages();

        if ($mediaGallery instanceof \Magento\Framework\Data\Collection) {
            foreach ($mediaGallery as $image) {
                $gallery[] = [
                    'image' => $image->getFile(),
                    'label' => $image->getLabel(),
                    'position' => $image->getPosition()
                ];
            }
        }

        return $gallery;
    }

    /**
     * Get JSON config for configurable products
     */
    protected function getJsonConfig($product)
    {
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $configurableBlock = $this->configurableBlockFactory->create();
            $configurableBlock->setProduct($product);
            return $configurableBlock->getJsonConfig();
        }
        return '';
    }

    /**
     * Get swatch config for configurable products
     */
    protected function getSwatchConfig($product)
    {
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            $swatchBlock = $this->swatchBlockFactory->create();
            $swatchBlock->setProduct($product);
            return $swatchBlock->getJsonSwatchConfig();
        }
        return '';
    }

    /**
     * Get custom attributes as array
     */
    protected function getCustomAttributesArray($product): array
    {
        $attributes = [];
        foreach ($product->getCustomAttributes() as $attribute) {
            $attributes[] = [
                'attribute_code' => $attribute->getAttributeCode(),
                'value' => $attribute->getValue()
            ];
        }
        return $attributes;
    }

    /**
     * Get formatted custom attributes
     */
    protected function getCustomAttributes($product): array
    {
        $attributes = [];
        foreach ($product->getCustomAttributes() as $attribute) {
            $attr = $product->getResource()->getAttribute($attribute->getAttributeCode());

            if ($attr && $attr->usesSource()) {
                $value = $attr->getSource()->getOptionText($attribute->getValue());
                if (is_array($value)) {
                    $value = implode(', ', $value);
                }
            } else {
                $value = $attribute->getValue();
            }

            $attributes[] = [
                'attribute_code' => $attribute->getAttributeCode(),
                'value' => $value,
            ];
        }

        return $attributes;
    }

    /**
     * Apply field mapping to raw data
     */
    protected function applyFieldMapping(array $rawData): array
    {
        $fieldMap = [
            0 => 'entity_id',
            1 => 'attribute_set_id',
            2 => 'type_id',
            3 => 'sku',
            4 => 'has_options',
            5 => 'required_options',
            6 => 'created_at',
            7 => 'updated_at',
            8 => 'status',
            9 => 'price',
            10 => 'final_price',
            11 => 'name',
            12 => 'image',
            13 => 'small_image',
            14 => 'thumbnail',
            15 => 'description',
            16 => 'short_description',
            17 => 'url_key',
            18 => 'visibility',
            19 => 'quantity_and_stock_status',
            20 => 'in_stock',
            21 => 'stock_qty',
            22 => 'categories',
            23 => 'category_ids',
            24 => 'category_hierarchy',
            25 => 'menu_hierarchy',
            26 => 'url_hierarchy',
            27 => 'media_gallery',
            28 => 'json_config',
            29 => 'swatch_json_config',
            30 => 'custom_attributes',
            31 => 'special_price',
            32 => 'special_from_date',
            33 => 'options_container',
            34 => 'msrp_display_actual_price_type',
            35 => 'gift_message_available',
            36 => 'image_label',
            37 => 'small_image_label',
            38 => 'thumbnail_label',
            39 => 'tax_class_id',
            40 => 'activity_ids',
            41 => 'style_bags_ids',
            42 => 'material_ids',
            43 => 'strap_bags_ids',
            44 => 'features_bags_ids',
            45 => 'eco_collection',
            46 => 'performance_fabric',
            47 => 'erin_recommends',
            48 => 'new',
            49 => 'sale'
        ];

        $mappedData = [];
        foreach ($fieldMap as $index => $fieldName) {
            if (isset($rawData[$index])) {
                $mappedData[$fieldName] = $rawData[$index];
            }
        }

        return $mappedData;
    }

    /**
     * Get all additional product fields not in the original mapping
     */
    protected function getAdditionalProductData($product): array
    {
        return [
            // Pricing fields
            'regular_price' => $this->getMaxPrice($product),
            'max_price' => $this->getMaxPrice($product),
            'minimal_price' => $product->getMinimalPrice(),
            'special_to_date' => $product->getSpecialToDate(),
            'cost' => $product->getCost(),
            'msrp' => $product->getMsrp(),

            // Inventory fields
            'is_stock_managed' => $this->stockRegistry->getStockItem($product->getId())->getManageStock(),
            'saleable' => $product->isSalable(),

            // Media fields
            'cached_thumbnail' => $this->getCachedThumbnail($product),
            'swatch_image' => $product->getSwatchImage(),

            // URL fields
            'url' => $this->storeManager->getStore()->getBaseUrl() . $product->getUrlKey() . '.html',
            'url_path' => $product->getUrlPath(),

            // Design fields
            'custom_design' => $product->getCustomDesign(),
            'custom_design_from' => $product->getCustomDesignFrom(),
            'custom_design_to' => $product->getCustomDesignTo(),
            'custom_layout_update' => $product->getCustomLayoutUpdate(),
            'custom_layout_update_file' => $product->getCustomLayoutUpdateFile(),
            'page_layout' => $product->getPageLayout(),

            // Product characteristics
            'weight' => $product->getWeight(),
            'color' => $this->getAttributeText($product, 'color'),
            'size' => $this->getAttributeText($product, 'size'),
            'material' => $this->getAttributeText($product, 'material'),
            'pattern' => $this->getAttributeText($product, 'pattern'),
            'climate' => $this->getAttributeText($product, 'climate'),
            'gender' => $this->getAttributeText($product, 'gender'),
            'style_bags' => $this->getAttributeText($product, 'style_bags'),
            'strap_bags' => $this->getAttributeText($product, 'strap_bags'),
            'features_bags' => $this->getAttributeText($product, 'features_bags'),
            'activity' => $this->getAttributeText($product, 'activity'),
            'category_gear' => $this->getAttributeText($product, 'category_gear'),
            'format' => $this->getAttributeText($product, 'format'),
            'style_bottom' => $this->getAttributeText($product, 'style_bottom'),
            'style_general' => $this->getAttributeText($product, 'style_general'),
            'sleeve' => $this->getAttributeText($product, 'sleeve'),
            'collar' => $this->getAttributeText($product, 'collar'),

            // Dates
            'news_from_date' => $product->getNewsFromDate(),
            'news_to_date' => $product->getNewsToDate(),

            // Other attributes
            'country_of_manufacture' => $product->getCountryOfManufacture(),
            'gift_message_available' => $product->getGiftMessageAvailable(),
            'old_id' => $product->getOldId(),

            // Tier pricing
            'tier_price' => $product->getTierPrice(),
            'tier_pricing' => $this->getFormattedTierPrices($product),

            // Downloadable product fields
            'links_purchased_separately' => $product->getLinksPurchasedSeparately(),
            'samples_title' => $product->getSamplesTitle(),
            'links_title' => $product->getLinksTitle(),
            'links_exist' => $product->getLinksExist(),

            // Configurable product options
            'price_type' => $product->getPriceType(),
            'weight_type' => $product->getWeightType(),
            'sku_type' => $product->getSkuType(),
            'price_view' => $product->getPriceView(),
            'shipment_type' => $product->getShipmentType(),

            // Ratings
            'rating' => $this->getRatingSummary($product),
            'rating_count' => $this->getRatingCount($product),

            // Child product name for configurable variants
            'child_name' => $product->getTypeId() === 'simple' ? $product->getName() : null,
        ];
    }

    /**
     * Get max price (for configurable products)
     */
    protected function getMaxPrice($product)
    {
        if ($product->getTypeId() === \Magento\ConfigurableProduct\Model\Product\Type\Configurable::TYPE_CODE) {
            return $product->getPriceInfo()->getPrice('regular_price')->getMaxRegularAmount()->getValue();
        }
        return $product->getPrice();
    }

    /**
     * Get cached thumbnail URL
     */
    protected function getCachedThumbnail($product)
    {
        return $this->imageHelperFactory->create()
            ->init($product, 'product_thumbnail_image')
            ->setImageFile($product->getThumbnail())
            ->getUrl();
    }

    /**
     * Helper method to get attribute option text
     */
    protected function getAttributeText($product, $attributeCode)
    {
        try {
            $attribute = $product->getResource()->getAttribute($attributeCode);
            if ($attribute && $attribute->usesSource()) {
                $value = $attribute->getSource()->getOptionText($product->getData($attributeCode));
                return is_array($value) ? implode('>', $value) : $value;
            }
        } catch (\Exception $e) {
            $this->_logger->error("Error getting attribute text for {$attributeCode}: " . $e->getMessage());
        }
        return $product->getData($attributeCode);
    }

    /**
     * Get formatted tier prices
     */
    protected function getFormattedTierPrices($product)
    {
        $tierPrices = [];
        foreach ($product->getTierPrice() as $tier) {
            $tierPrices[] = [
                'qty' => $tier['price_qty'],
                'price' => $tier['price'],
                'website_price' => $tier['website_price']
            ];
        }
        return $tierPrices;
    }

    /**
     * Get rating summary
     */
    protected function getRatingSummary($product)
    {
        $this->reviewFactory->create()->getEntitySummary($product, $this->storeManager->getStore()->getId());
        return $product->getRatingSummary()->getRatingSummary();
    }

    /**
     * Get rating count
     */
    protected function getRatingCount($product)
    {
        return $this->reviewFactory->create()->getTotalReviews($product->getId(), false, $this->storeManager->getStore()->getId());
    }

    /**
     * Get configurable product specific data
     */
    protected function getConfigurableProductData($product): array
    {
        /** @var \Magento\ConfigurableProduct\Model\Product\Type\Configurable $configurableType */
        $configurableType = $product->getTypeInstance();

        $configurableData = [
            'configurable_attributes' => $configurableType->getConfigurableAttributesAsArray($product),
            'variants' => []
        ];

        // Get child products
        $childProducts = $configurableType->getUsedProducts($product);
        foreach ($childProducts as $child) {
            $configurableData['variants'][] = [
                'id' => $child->getId(),
                'sku' => $child->getSku(),
                'price' => $child->getPrice(),
                'final_price' => $child->getFinalPrice(),
                'is_in_stock' => $child->isSalable(),
                'images' => $this->getProductImages($child)
            ];
        }

        // Get JSON config for frontend
        $configurableBlock = $this->configurableBlockFactory->create();
        $configurableBlock->setProduct($product);
        $configurableData['json_config'] = $configurableBlock->getJsonConfig();
        $configurableData['swatch_json_config'] = $configurableProduct['swatch_config'] ?? '';

        return $configurableData;
    }

    /**
     * Get product images including thumbnails and different sizes
     */
    protected function getProductImages($product): array
    {
        $images = [];
        $mediaGallery = $product->getMediaGalleryImages();

        if ($mediaGallery instanceof \Magento\Framework\Data\Collection) {
            foreach ($mediaGallery as $image) {
                $images[] = [
                    'image' => $image->getFile(),
                    'label' => $image->getLabel(),
                    'position' => $image->getPosition(),
                    'thumb' => $image->getData('small_image_url'),
                    'small_image' => $image->getData('medium_image_url'),
                    'full' => $image->getData('large_image_url')
                ];
            }
        }


        if (empty($images) && $product->getImage()) {
            $images[] = [
                'image' => $product->getImage(),
                'label' => $product->getImageLabel(),
                'position' => 1,
                'thumb' => $product->getSmallImage(),
                'small_image' => $product->getSmallImage(),
                'full' => $product->getImage()
            ];
        }

        return $images;
    }

    protected function getBundleProductPrices($product)
    {
        /** @var \Magento\Bundle\Model\Product\Type $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $selectionCollection = $typeInstance->getSelectionsCollection(
            $typeInstance->getOptionsIds($product),
            $product
        );

        // Initialize price collectors
        $optionRegularPrices = [];
        $optionFinalPrices = [];
        $allSelectionPrices = [];

        foreach ($typeInstance->getOptions($product) as $option) {
            $optionRegularPrices[$option->getId()] = [];
            $optionFinalPrices[$option->getId()] = [];

            foreach ($selectionCollection as $selection) {
                if ($selection->getOptionId() == $option->getId()) {
                    try {
                        $selectionProduct = $this->productRepository->getById($selection->getProductId());
                        $qty = max(1, $selection->getSelectionQty());

                        $regularPrice = $selectionProduct->getPrice() * $qty;
                        $finalPrice = $selectionProduct->getFinalPrice() * $qty;

                        $optionRegularPrices[$option->getId()][] = $regularPrice;
                        $optionFinalPrices[$option->getId()][] = $finalPrice;
                        $allSelectionPrices[] = [
                            'option_id' => $option->getId(),
                            'selection_id' => $selection->getId(),
                            'regular_price' => $regularPrice,
                            'final_price' => $finalPrice,
                            'qty' => $qty
                        ];
                    } catch (\Exception $e) {
                        continue;
                    }
                }
            }
        }

        // Calculate maximum possible regular price combination
        $maxRegularCombination = 0;
        foreach ($optionRegularPrices as $optionId => $prices) {
            if (!empty($prices)) {
                $maxRegularCombination += max($prices);
            }
        }

        // Calculate minimum possible final price combination
        $minFinalCombination = 0;
        foreach ($optionFinalPrices as $optionId => $prices) {
            if (!empty($prices)) {
                $minFinalCombination += min($prices);
            }
        }

        // Calculate maximum possible final price combination
        $maxFinalCombination = 0;
        foreach ($optionFinalPrices as $optionId => $prices) {
            if (!empty($prices)) {
                $maxFinalCombination += max($prices);
            }
        }

        // For fixed price bundles
        if ($product->getPriceType() == \Magento\Bundle\Model\Product\Price::PRICE_TYPE_FIXED) {
            $minFinalCombination = $maxFinalCombination = $product->getFinalPrice();
            $maxRegularCombination = $product->getPrice();
        }

        return [
            'price_type' => $product->getPriceType() == 1 ? 'fixed' : 'dynamic',
            'absolute_prices' => [
                'min_price' => $minFinalCombination,
                'max_price' => $maxFinalCombination,
                'regular_price' => $maxRegularCombination,
                'final_price' => $minFinalCombination
            ],
            'price_ranges' => [
                'from_price' => $minFinalCombination,
                'to_price' => $maxFinalCombination,
                'regular_from_price' => $this->getMinRegularCombination($optionRegularPrices),
                'regular_to_price' => $maxRegularCombination
            ],
            // 'option_prices' => $this->getOptionPriceDetails($optionRegularPrices, $optionFinalPrices),
            'selection_prices' => $allSelectionPrices,
            'tax_adjusted' => $this->getBundleTaxAdjustedPrices($product, $minFinalCombination, $maxFinalCombination, $maxRegularCombination)
        ];
    }

    protected function getMinRegularCombination($optionRegularPrices)
    {
        $minCombination = 0;
        foreach ($optionRegularPrices as $optionId => $prices) {
            if (!empty($prices)) {
                $minCombination += min($prices);
            }
        }
        return $minCombination;
    }

    protected function getBundleTaxAdjustedPrices($product, $minPrice, $maxPrice, $regularPrice)
    {
        return [
            'min_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $minPrice, true),
                'excl_tax' => $minPrice
            ],
            'max_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $maxPrice, true),
                'excl_tax' => $maxPrice
            ],
            'regular_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $regularPrice, true),
                'excl_tax' => $regularPrice
            ]
        ];
    }

    protected function getGroupedProductPrices($product, $id)
    {
        /** @var \Magento\GroupedProduct\Model\Product\Type\Grouped $typeInstance */
        $typeInstance = $product->getTypeInstance();
        $associatedProducts = $typeInstance->getAssociatedProducts($product);

        $pricingData = [
            'product_id' => $id,
            'type' => 'grouped',
            'prices' => [
                'regular_price' => 0, // Grouped products don't have their own price
                'min_price' => null,
                'max_price' => null,
                'child_products' => []
            ]
        ];

        foreach ($associatedProducts as $associatedProduct) {
            if (!$associatedProduct->isSalable()) {
                continue;
            }

            $priceInfo = $associatedProduct->getPriceInfo();
            $finalPrice = $priceInfo->getPrice('final_price')->getAmount()->getValue();

            $childData = [
                'product_id' => $associatedProduct->getId(),
                'sku' => $associatedProduct->getSku(),
                'regular_price' => $associatedProduct->getPrice(),
                'final_price' => $finalPrice,
                'tax_adjusted' => [
                    'incl_tax' => $this->taxHelper->getTaxPrice($associatedProduct, $finalPrice, true),
                    'excl_tax' => $finalPrice
                ]
            ];

            $pricingData['prices']['child_products'][] = $childData;

            // Calculate min/max prices
            if ($pricingData['prices']['min_price'] === null || $finalPrice < $pricingData['prices']['min_price']) {
                $pricingData['prices']['min_price'] = $finalPrice;
            }
            if ($pricingData['prices']['max_price'] === null || $finalPrice > $pricingData['prices']['max_price']) {
                $pricingData['prices']['max_price'] = $finalPrice;
            }
        }

        // Handle case when no salable products exist
        if ($pricingData['prices']['min_price'] === null) {
            $pricingData['prices']['min_price'] = 0;
            $pricingData['prices']['max_price'] = 0;
        }

        // Add tax adjusted prices for the group
        $pricingData['prices']['tax_adjusted'] = [
            'min_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $pricingData['prices']['min_price'], true),
                'excl_tax' => $pricingData['prices']['min_price']
            ],
            'max_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $pricingData['prices']['max_price'], true),
                'excl_tax' => $pricingData['prices']['max_price']
            ]
        ];

        return $pricingData;
    }

    /**
     * Transform raw product data array into associative array with proper keys
     *
     * @param array $productArray
     * @return array
     */
    public function formatProductData(array $productArray): array
    {
        // Map array indexes to field names
        $fieldMap = [
            0 => 'entity_id',
            1 => 'attribute_set_id',
            2 => 'type_id',
            3 => 'sku',
            4 => 'has_options',
            5 => 'required_options',
            6 => 'created_at',
            7 => 'updated_at',
            8 => 'status',
            9 => 'price',
            10 => 'final_price',
            11 => 'name',
            12 => 'image',
            13 => 'small_image',
            14 => 'thumbnail',
            15 => 'description',
            16 => 'short_description',
            17 => 'url_key',
            18 => 'visibility',
            19 => 'quantity_and_stock_status',
            20 => 'in_stock',
            21 => 'stock_qty',
            22 => 'categories',
            23 => 'category_ids',
            24 => 'category_hierarchy',
            25 => 'menu_hierarchy',
            26 => 'url_hierarchy',
            27 => 'media_gallery',
            28 => 'json_config',
            29 => 'swatch_json_config',
            30 => 'custom_attributes',
            31 => 'special_price',
            32 => 'special_from_date',
            33 => 'options_container',
            34 => 'msrp_display_actual_price_type',
            35 => 'gift_message_available',
            36 => 'image_label',
            37 => 'small_image_label',
            38 => 'thumbnail_label',
            39 => 'tax_class_id',
            40 => 'activity_ids',
            41 => 'style_bags_ids',
            42 => 'material_ids',
            43 => 'strap_bags_ids',
            44 => 'features_bags_ids',
            45 => 'eco_collection',
            46 => 'performance_fabric',
            47 => 'erin_recommends',
            48 => 'new',
            49 => 'sale'
        ];

        $formattedData = [];

        // Map the numeric keys to field names
        foreach ($fieldMap as $index => $fieldName) {
            if (isset($productArray[$index])) {
                $formattedData[$fieldName] = $productArray[$index];
            }
        }

        // Process custom attributes to make them more accessible
        if (isset($formattedData['custom_attributes']) && is_array($formattedData['custom_attributes'])) {
            foreach ($formattedData['custom_attributes'] as $attribute) {
                if (isset($attribute['attribute_code']) && isset($attribute['value'])) {
                    $formattedData[$attribute['attribute_code']] = $attribute['value'];
                }
            }
        }

        // Handle ID-based attributes that need text values
        $this->processTextAttributes($formattedData);

        return $formattedData;
    }

    /**
     * Process ID-based attributes to get their text values
     */
    protected function processTextAttributes(&$productData)
    {
        // Map for attribute codes that contain IDs but should display text values
        $idAttributes = [
            'activity' => 'activity_ids',
            'style_bags' => 'style_bags_ids',
            'material' => 'material_ids',
            'strap_bags' => 'strap_bags_ids',
            'features_bags' => 'features_bags_ids'
        ];

        foreach ($idAttributes as $textField => $idField) {
            if (isset($productData[$idField])) {
                $ids = explode(',', $productData[$idField]);
                $productData[$textField] = $this->getAttributeOptionsText($textField, $ids);
                unset($productData[$idField]);
            }
        }
    }

    /**
     * Get text values for attribute options
     */
    protected function getAttributeOptionsText($attributeCode, $optionIds)
    {

        $attribute = $this->eavConfig->getAttribute('catalog_product', $attributeCode);
        $options = [];

        if ($attribute->usesSource()) {
            foreach ($optionIds as $optionId) {
                $optionText = $attribute->getSource()->getOptionText($optionId);
                if ($optionText) {
                    $options[] = $optionText;
                }
            }
        }

        return implode(', ', $options);
    }

    protected function getGroupedTaxAdjustedPrices($product, $finalPrices)
    {
        $minDisplay = !empty($displayPrices) ? min($displayPrices) : 0;
        $maxDisplay = !empty($displayPrices) ? max($displayPrices) : 0;

        return [
            'min_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $minDisplay, true),
                'excl_tax' => $minDisplay
            ],
            'max_price' => [
                'incl_tax' => $this->taxHelper->getTaxPrice($product, $maxDisplay, true),
                'excl_tax' => $maxDisplay
            ]
        ];
    }

    /**
     * Get tax class text
     */
    protected function getTaxClassText($product)
    {
        $taxClassId = $product->getTaxClassId();
        $taxClasses = [
            0 => 'None',
            2 => 'Taxable Goods',
            4 => 'Shipping',
        ];
        return $taxClasses[$taxClassId] ?? 'Unknown';
    }

    /**
     * Get tier prices
     */
    protected function getTierPrices($product)
    {
        return $product->getTierPrice();
    }

    /**
     * Get visibility text from code
     */
    protected function getVisibilityText($visibilityCode): string
    {
        $visibility = [
            1 => 'Not Visible Individually',
            2 => 'Catalog',
            3 => 'Search',
            4 => 'Catalog, Search',
        ];

        return $visibility[$visibilityCode] ?? 'Unknown';
    }
}

