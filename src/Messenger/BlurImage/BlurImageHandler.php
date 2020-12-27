<?php

namespace App\Messenger\BlurImage;

use Psr\Log\LoggerInterface;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BlurImageHandler implements MessageHandlerInterface
{
    private $logger;

    public function __construct(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function __invoke(BlurImageMessage $message)
    {
        $imageId = $message->getImageId();
        $message = sprintf('Blurring image with ID %d', $imageId);

        $this->logger->info($message);
        echo $message . PHP_EOL;
    }
}
