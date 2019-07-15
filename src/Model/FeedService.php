<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Model;

use Omikron\Factfinder\Api\Config\ChannelProviderInterface;
use Omikron\Factfinder\Api\FeedServiceInterface;
use Omikron\Factfinder\Model\Api\PushImport;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\Stream\Csv;
use Omikron\Factfinder\Model\Stream\CsvFactory;

class FeedService implements FeedServiceInterface
{
    /** @var ChannelProviderInterface */
    private $channelProvider;

    /** @var StoreEmulation */
    private $storeEmulation;

    /** @var FeedGeneratorFactory */
    private $feedGeneratorFactory;

    /** @var CsvFactory */
    private $csvFactory;

    /** @var FtpUploader */
    private $ftpUploader;

    /** @var PushImport */
    private $pushImport;

    /** @var string */
    private $type;

    public function __construct(
        StoreEmulation $storeEmulation,
        FeedGeneratorFactory $feedGeneratorFactory,
        CsvFactory $csvFactory,
        FtpUploader $ftpUploader,
        PushImport $pushImport,
        ChannelProviderInterface $channelProvider,
        string $type
    ) {
        $this->channelProvider      = $channelProvider;
        $this->storeEmulation       = $storeEmulation;
        $this->feedGeneratorFactory = $feedGeneratorFactory;
        $this->csvFactory           = $csvFactory;
        $this->ftpUploader          = $ftpUploader;
        $this->pushImport           = $pushImport;
        $this->type                 = $type;
    }

    public function integrate(int $storeId): void
    {
        $this->storeEmulation->runInStore($storeId, function () use ($storeId) {
            $feed = $this->create();
            $this->ftpUploader->upload($feed->getFileName(), $feed);
            $this->pushImport->execute($storeId);
        });
    }

    public function get(int $storeId): Csv
    {
        return $this->storeEmulation->runInStore($storeId, function () use ($storeId) {
            return $this->create();
        });
    }

    private function create(): Csv
    {
        $stream = $this->csvFactory->create(['filename' => "factfinder/export.{$this->channelProvider->getChannel()}.csv"]);
        $this->feedGeneratorFactory->create($this->type)->generate($stream);

        return $stream;
    }
}
