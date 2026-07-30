<?php

declare(strict_types=1);

namespace Omikron\Factfinder\ViewModel;

use Magento\Framework\App\Config\ScopeConfigInterface;
use Magento\Framework\Serialize\SerializerInterface;
use Magento\Framework\View\Element\Block\ArgumentInterface;
use Magento\Store\Model\ScopeInterface;
use Omikron\Factfinder\Model\Config\CommunicationParametersProvider;
use Omikron\Factfinder\Model\FieldRoles;

class Communication implements ArgumentInterface
{
    private const PATH_USE_SRR = 'factfinder/general/use_ssr';
    private const PATH_SUPPORT_ATLAS_AI = 'factfinder/advanced/atlas_ai_user_id';

    public function __construct(
        private readonly FieldRoles                      $fieldRoles,
        private readonly SerializerInterface             $serializer,
        private readonly CommunicationParametersProvider $parametersProvider,
        private readonly ScopeConfigInterface            $scopeConfig,
    ) {
    }

    public function getParameters(array $blockParams = []): array
    {
        if (isset($blockParams['search-immediate'])) {
            unset($blockParams['search-immediate']);
        }

        $params = $this->parametersProvider->getParameters();

        return ['version' => 'ng']
            + array_filter($this->mergeParameters($blockParams, $params) + $blockParams + $params, 'boolval');
    }

    public function getFieldRoles(): string
    {
        return (string) $this->serializer->serialize($this->fieldRoles->getFieldRoles());
    }

    public function isSsrEnable(): bool
    {
        return (bool) $this->scopeConfig->isSetFlag(self::PATH_USE_SRR);
    }

    public function isAtlasAIEnabled(?int $scopeId = null): bool
    {
        return $this->scopeConfig->isSetFlag(self::PATH_SUPPORT_ATLAS_AI, ScopeInterface::SCOPE_STORES, $scopeId);
    }

    private function mergeParameters(array ...$params): array
    {
        $params = array_map(fn (array $param) => array_intersect_key($param, []), $params);

        return $params;
    }
}
