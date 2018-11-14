<?php
namespace Omikron\Factfinder\Model\Source;

use Magento\Customer\Api\GroupRepositoryInterface;
use Magento\Framework\Api\SearchCriteriaBuilder;
use Magento\Framework\Data\OptionSourceInterface;

/**
 * Class CustomerGroup
 */
class CustomerGroup implements OptionSourceInterface
{
    /**
     * @var GroupRepositoryInterface
     */
    protected $customerGroupRepository;

    /**
     * @var SearchCriteriaBuilder
     */
    protected $searchCriteriaBuilder;

    /**
     * CustomerGroup constructor.
     *
     * @param GroupRepositoryInterface $groupRepository
     * @param SearchCriteriaBuilder    $criteriaBuilder
     */
    public function __construct(GroupRepositoryInterface $groupRepository, SearchCriteriaBuilder $criteriaBuilder)
    {
        $this->customerGroupRepository = $groupRepository;
        $this->searchCriteriaBuilder = $criteriaBuilder;
    }

    /**
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function toOptionArray()
    {
        $customerGroups = [];
        $groups         = $this->customerGroupRepository->getList($this->searchCriteriaBuilder->create());
        foreach ($groups->getItems() as $group) {
            $customerGroups[] = [
                'label' => $group->getCode(),
                'value' => $group->getId(),
            ];
        }

        return $customerGroups;
    }
}
