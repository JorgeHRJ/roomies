<?php

namespace App\Twig\Extension;

use App\Entity\File;
use App\Service\FileService;
use Twig\Extension\AbstractExtension;
use Twig\TwigFunction;

class FileExtension extends AbstractExtension
{
    /** @var FileService */
    private $fileService;

    public function __construct(FileService $fileService)
    {
        $this->fileService = $fileService;
    }

    public function getFunctions()
    {
        return [
            new TwigFunction('get_placeholder', [$this, 'getPlaceholderPath'])
        ];
    }

    public function getPlaceholderPath(?File $file): ?string
    {
        if (!$file instanceof File) {
            return null;
        }

        return $this->fileService->getPlaceholderPath($file);
    }
}
