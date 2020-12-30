<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Home;
use App\Messenger\BlurImage\BlurImageMessage;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Messenger\MessageBusInterface;

class FileService
{
    const ALLOWED_IMAGE_EXTENSIONS = ['png', 'jpeg', 'jpg'];
    const ALLOWED_DOC_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];
    const PLACEHOLDER_PART = 'placeholder';

    /** @var EntityManagerInterface */
    private $entityManager;

    /** @var LoggerInterface */
    private $logger;

    /** @var StorageService */
    private $storageService;

    /** @var ContextService */
    private $contextService;

    /** @var FileRepository */
    private $repository;

    /** @var MessageBusInterface */
    private $messageBus;

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        StorageService $storageService,
        ContextService $contextService,
        MessageBusInterface $messageBus
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->storageService = $storageService;
        $this->contextService = $contextService;
        $this->messageBus = $messageBus;
        $this->repository = $entityManager->getRepository(File::class);
    }

    /**
     * @param int $id
     * @return File|null
     */
    public function get(int $id): ?File
    {
        return $this->repository->find($id);
    }

    /**
     * @param Home $home
     * @return File|null
     */
    public function getAvatarByHome(Home $home): ?File
    {
        return $this->repository->findOneBy(['home' => $home, 'origin' => File::HOME_AVATAR_ORIGIN]);
    }

    /**
     * @param string $filename
     * @param string $extension
     * @param string $origin
     * @param string|null $entityName
     *
     * @return File
     * @throws \Exception
     */
    public function create(string $filename, string $extension, string $origin, ?string $entityName): File
    {
        try {
            $file = new File();
            $file
                ->setFilename($filename)
                ->setExtension($extension)
                ->setType($this->guessTypeFromExtension($extension))
                ->setHome($this->contextService->getHome())
                ->setPath('temp')
                ->setOrigin($origin);

            $finalEntityName = $entityName !== null ? $entityName : $filename;
            $file->setName($finalEntityName);

            $this->entityManager->persist($file);
            $this->entityManager->flush();

            $this->logger->info(sprintf('Created File ID::%s', $file->getId()));

            return $file;
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error creating File. Error: %s', $e->getMessage()));

            throw $e;
        }
    }

    /**
     * @param File $file
     *
     * @return File
     * @throws \Exception
     */
    public function update(File $file): File
    {
        try {
            $this->entityManager->flush();

            $this->logger->info(sprintf('Updated File ID::%s', $file->getId()));

            return $file;
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error updating File with ID::%s. Error: %s',
                    $file->getId(),
                    $e->getMessage()
                )
            );

            throw $e;
        }
    }

    /**
     * @param File $file
     */
    public function remove(File $file): void
    {
        $this->storageService->remove($file->getPath());

        if ($file->getType() === File::IMAGE_TYPE) {
            $placeholderPath = $this->getPlaceholderPathFromFile($file);
            $this->storageService->remove($placeholderPath);
        }

        $this->entityManager->remove($file);
        $this->entityManager->flush();
    }

    /**
     * @param UploadedFile $uploadedFile
     * @param string $origin
     * @param string|null $entityName
     * @return File
     * @throws \Exception
     */
    public function handleUpload(UploadedFile $uploadedFile, string $origin, string $entityName = null): File
    {
        try {
            $filename = explode('.', $uploadedFile->getClientOriginalName());
            $name = (string) preg_replace('/[^\w\-\.]/', '', $filename[0]);
            $name = sprintf('%s_%s', $name, md5(time()));
            $filename = sprintf('%s.%s', $name, $uploadedFile->getClientOriginalExtension());

            $newFile = $this->create($name, $uploadedFile->getClientOriginalExtension(), $origin, $entityName);
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error handling upload when creating entity. Error: %s',
                    $e->getMessage()
                )
            );

            throw $e;
        }

        try {
            $relativePath = $this->getAssetsRelativePath(
                $this->contextService->getHome()->getId(),
                $origin,
                $newFile->getId()
            );
            $folder = $this->storageService->getAssetsDir($relativePath);
            $this->storageService->save($uploadedFile, $folder, $filename);

            $path = $this->storageService->getFilePath($relativePath, $filename);
            $newFile->setPath($path);

            $this->entityManager->flush();
        } catch (\Exception $e) {
            $this->logger->error(
                sprintf(
                    'Error creating file ID::%s when it was being uploaded. Error: %s',
                    $newFile->getId(),
                    $e->getMessage()
                )
            );

            throw $e;
        }

        if ($newFile->getType() === File::IMAGE_TYPE) {
            $this->messageBus->dispatch(new BlurImageMessage($newFile->getId()));
        }

        return $newFile;
    }

    /**
     * @param File $file
     */
    public function createBlurredImagePlaceholder(File $file): void
    {
        $newPath = $this->getPlaceholderPathFromFile($file);
        $newPath = sprintf('%s/%s', $this->storageService->getPublicFolder(), $newPath);
        $toClonePath = sprintf('%s/%s', $this->storageService->getPublicFolder(), $file->getPath());

        $this->storageService->clone($toClonePath, $newPath);

        try {
            $image = new \Imagick($newPath);
            $image->gaussianBlurImage(10, 8);

            $image->writeImage($newPath);
            $image->clear();
        } catch (\ImagickException $e) {
            $this->logger->error(
                sprintf('Image was not handled with Imagick due to error: %s', $e->getMessage())
            );
        }
    }

    /**
     * @param File $file
     * @return string|null
     */
    public function getPlaceholderPath(File $file): ?string
    {
        $placeholderPath = $this->getPlaceholderPathFromFile($file);
        return $this->storageService->assetExists($placeholderPath) ? $placeholderPath : null;
    }

    /**
     * @param string $extension
     * @return string
     * @throws \Exception
     */
    private function guessTypeFromExtension(string $extension): string
    {
        if (in_array($extension, self::ALLOWED_IMAGE_EXTENSIONS)) {
            return File::IMAGE_TYPE;
        }

        if (in_array($extension, self::ALLOWED_DOC_EXTENSIONS)) {
            return File::DOC_TYPE;
        }

        throw new \Exception('Not allowed file extension');
    }

    /**
     * @param int $homeId
     * @param string $origin
     * @param int $fileId
     * @return string
     */
    private function getAssetsRelativePath(int $homeId, string $origin, int $fileId)
    {
        return sprintf('%s/%s/%d', $homeId, $origin, floor($fileId/5000));
    }

    /**
     * @param File $file
     * @return string
     */
    private function getPlaceholderPathFromFile(File $file): string
    {
        return str_replace(
            sprintf('%s.%s', $file->getName(), $file->getExtension()),
            sprintf('%s_%s.%s', $file->getName(), self::PLACEHOLDER_PART, $file->getExtension()),
            $filePath = $file->getPath()
        );
    }
}
