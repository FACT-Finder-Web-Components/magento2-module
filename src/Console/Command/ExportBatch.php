<?php

declare(strict_types=1);

namespace Omikron\Factfinder\Console\Command;

use Magento\Framework\App\State;
use Omikron\Factfinder\Api\StreamInterfaceFactory;
use Omikron\Factfinder\Model\Export\FeedFactory as FeedGeneratorFactory;
use Omikron\Factfinder\Model\StoreEmulation;
use Omikron\Factfinder\Service\FeedFileService;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class ExportBatch extends Command
{
    public function __construct(
        private readonly FeedGeneratorFactory   $feedGeneratorFactory,
        private readonly StoreEmulation         $storeEmulation,
        private readonly StreamInterfaceFactory $streamFactory,
        private readonly State                  $state,
        private readonly FeedFileService        $feedFileService
    ) {
        parent::__construct();
    }

    protected function configure(): void
    {
        $this->setName('factfinder:export:batch')
            ->setDescription('Internal worker command for exporting a batch of products')
            ->setHidden(true);

        $this->addArgument('type', InputArgument::REQUIRED, 'Type of data to export');
        $this->addArgument('store', InputArgument::REQUIRED, 'Store ID');
        $this->addArgument('offset', InputArgument::REQUIRED, 'Offset');
        $this->addArgument('limit', InputArgument::REQUIRED, 'Limit');
        $this->addArgument('file_path', InputArgument::REQUIRED, 'Path to output file');

        parent::configure();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        $this->state->setAreaCode('frontend');

        $type     = $input->getArgument('type');
        $storeId  = (int) $input->getArgument('store');
        $offset   = (int) $input->getArgument('offset');
        $limit    = (int) $input->getArgument('limit');
        $filePath = $input->getArgument('file_path');

        $processedCount = 0;

        $this->storeEmulation->runInStore($storeId, function () use ($type, $offset, $limit, $filePath, &$processedCount) {
            $mode = ($offset === 0) ? 'w+' : 'a+';
            $stream = $this->streamFactory->create([
                'filename' => $filePath,
                'mode'     => $mode
            ]);

            $generator = $this->feedGeneratorFactory->create($type);
            $processedCount = $generator->generateBatch($stream, $offset, $limit);
        });

        $memoryUsageMB = memory_get_usage(true) / 1024 / 1024;
        $peakMemoryMB  = memory_get_peak_usage(true) / 1024 / 1024;

        $result = [
            'count'  => $processedCount,
            'memory' => round($memoryUsageMB, 2),
            'peak'   => round($peakMemoryMB, 2),
        ];

        $output->write(json_encode($result));

        return Command::SUCCESS;
    }
}
