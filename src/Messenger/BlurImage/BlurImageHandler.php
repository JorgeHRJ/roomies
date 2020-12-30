<?php

namespace App\Messenger\BlurImage;

use App\Service\FileService;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

class BlurImageHandler implements MessageHandlerInterface
{
    /** @var FileService */
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function __invoke(BlurImageMessage $message): void
    {
        $file = $this->fileService->get($message->getFileId());
        $this->fileService->createBlurredImagePlaceholder($file);
    }
}
