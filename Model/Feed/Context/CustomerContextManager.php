<?php

declare(strict_types=1);

namespace SearchSpring\Feed\Model\Feed\Context;

use Magento\Customer\Api\CustomerRepositoryInterface;
use Magento\Customer\Model\Session;
use Magento\Framework\Exception\LocalizedException;
use Magento\Framework\Exception\NoSuchEntityException;
use SearchSpring\Feed\Api\Data\FeedSpecificationInterface;
use SearchSpring\Feed\Model\Feed\ContextManagerInterface;

class CustomerContextManager implements ContextManagerInterface
{
    /**
     * @var Session
     */
    private $session;
    /**
     * @var CustomerRepositoryInterface
     */
    private $customerRepository;

    /**
     * CustomerContextManager constructor.
     * @param Session $session
     * @param CustomerRepositoryInterface $customerRepository
     */
    public function __construct(
        Session $session,
        CustomerRepositoryInterface $customerRepository
    ) {
        $this->session = $session;
        $this->customerRepository = $customerRepository;
    }

    /**
     * @param FeedSpecificationInterface $feedSpecification
     * @throws LocalizedException
     * @throws NoSuchEntityException
     */
    public function setContextFromSpecification(FeedSpecificationInterface $feedSpecification): void
    {
        $customerId = $feedSpecification->getCustomerId();
        if (!$customerId) {
            return;
        }

        $customer = $this->customerRepository->getById((int) $customerId);
        $this->session->setCustomerDataAsLoggedIn($customer);
    }

    /**
     *
     */
    public function resetContext(): void
    {
        $this->session->logout();
    }
}
