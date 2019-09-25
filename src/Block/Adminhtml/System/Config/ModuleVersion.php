<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Block\Adminhtml\System\Config;

use Magento\Backend\Block\Template\Context;
use Magento\Config\Block\System\Config\Form\Field;
use Magento\Framework\Data\Form\Element\AbstractElement;
use Magento\Framework\Filesystem\DriverPool;
use Magento\Framework\Filesystem\File\ReadFactory;
use Magento\Framework\Module\Dir\Reader;

class ModuleVersion extends Field
{
    private const CHANGELOG_FILE_NAME = '/CHANGELOG.md';

    /** @var Reader */
    private $moduleReader;

    /** @var ReadFactory */
    private $readFactory;

    public function __construct(
        Reader $moduleReader,
        ReadFactory $readFactory,
        Context $context,
        array $data = []
    ) {
        parent::__construct($context, $data);
        $this->moduleReader = $moduleReader;
        $this->readFactory  = $readFactory;
    }

    /**
     * @inheritDoc
     * @param AbstractElement $element
     * @return string
     *
     * @SuppressWarnings(PHPMD.UnusedFormalParameter)
     */
    public function render(AbstractElement $element)
    {
        try {
            $modulePath = $this->moduleReader->getModuleDir('', 'Omikron_Factfinder');
            $changelog  = $this->readFactory->create($modulePath . self::CHANGELOG_FILE_NAME, DriverPool::FILE)->readAll();
            preg_match('/(?:version|v)\s*((?:[0-9]+\.?)+)/i', $changelog, $versions);

            return !empty($versions) ? $this->versionInfo(reset($versions)) : $this->noVersionFound();
        } catch (\Exception $e) {
            return $this->noVersionFound();
        }
    }

    private function versionInfo(string $version): string
    {
        return
            '<div class="comment">
                <div class="message message-notice">
                    <div data-ui-id="messages-message-success"> Installed module version: ' . $version . '</div>
                    <div data-ui-id="messages-message-success"> Remember to periodically check for new <a target="_blank" href="https://github.com/FACT-Finder-Web-Components/magento2-module/releases">releases</a></div>
                </div>
            </div>';
    }

    private function noVersionFound(): string
    {
        return
            '<div class="comment">
                    <div class="message message-error">
                        <div data-ui-id="messages-message-error">Cannot detect module version</div>
                    </div>
            </div>';
    }
}
