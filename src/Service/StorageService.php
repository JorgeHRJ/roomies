<?php

namespace App\Service;

use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Exception;

class StorageService
{
    /** @var string */
    private $publicFolder;

    /** @var string */
    private $assetsFolder;

    /** @var LoggerInterface */
    private $logger;

    public function __construct(string $publicFolder, string $assetsFolder, LoggerInterface $logger)
    {
        $this->publicFolder = $publicFolder;
        $this->assetsFolder = $assetsFolder;
        $this->logger = $logger;
    }

    /**
     * Save a file in the filesystem
     *
     * @param UploadedFile $file
     * @param string $folder Folder where save the file
     * @param string $filename
     *
     * @throws Exception
     */
    public function save(UploadedFile $file, string $folder, string $filename): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0764, true);
        }

        try {
            $file->move($folder, $filename);
        } catch (FileException $e) {
            $this->logger->error(sprintf('Error when saving file "%s". Error: %s', $filename, $e->getMessage()));

            throw $e;
        }
    }

    /**
     * Save a file in the filesystem from a url
     *
     * @param string $url
     * @param string $folder
     * @param string $filename
     * @throws Exception
     */
    public function saveFromUrl(string $url, string $folder, string $filename): void
    {
        if (!file_exists($folder)) {
            mkdir($folder, 0764, true);
        }

        try {
            file_put_contents(sprintf('%s/%s', $folder, $filename), file_get_contents($url));
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error when saving file "%s". Error: %s', $filename, $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param string $path
     */
    public function remove(string $path): void
    {
        if (file_exists($path)) {
            unlink($path);
        }
    }

    /**
     * @return string
     */
    public function getPublicFolder(): string
    {
        return $this->publicFolder;
    }

    /**
     * @param string $relativePath
     * @return string
     */
    public function getAssetsDir(string $relativePath): string
    {
        return sprintf('%s/%s/%s/', $this->publicFolder, $this->assetsFolder, $relativePath);
    }

    /**
     * @param string $relativePath
     * @param string $filename
     * @return string
     */
    public function getFilePath(string $relativePath, string $filename): string
    {
        return sprintf('%s/%s/%s', $this->assetsFolder, $relativePath, $filename);
    }
}
