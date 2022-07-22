<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Context;

use Magento\Framework\Exception\NoSuchEntityException;
use Magento\Store\Model\App\Emulation;
use Magento\Store\Model\StoreManagerInterface;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;

class StoreContextManager implements ContextManagerInterface
{
    /**
     * @var StoreManagerInterface
     */
    private $storeManager;
    /**
     * @var Emulation
     */
    private $emulation;

    /**
     * StoreContextManager constructor.
     * @param StoreManagerInterface $storeManager
     * @param Emulation $emulation
     */
    public function __construct(
        StoreManagerInterface $storeManager,
        Emulation $emulation
    ) {
        $this->storeManager = $storeManager;
        $this->emulation = $emulation;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @throws NoSuchEntityException
     */
    public function setContextFromSpecification(FeedSpecificationInterface $feedSpecification): void
    {
        $storeCode = $feedSpecification->getStoreCode();
        if (!$storeCode) {
            return;
        }

        $store = $this->storeManager->getStore($storeCode);
        $this->emulation->startEnvironmentEmulation((int) $store->getId());
    }

    /**
     *
     */
    public function resetContext(): void
    {
        $this->emulation->stopEnvironmentEmulation();
    }
}
