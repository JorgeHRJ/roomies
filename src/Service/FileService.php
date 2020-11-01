<?php

namespace App\Service;

use App\Entity\File;
use App\Entity\Home;
use App\Repository\FileRepository;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class FileService
{
    const ALLOWED_IMAGE_EXTENSIONS = ['png', 'jpeg', 'jpg'];
    const ALLOWED_DOC_EXTENSIONS = ['pdf', 'doc', 'docx', 'xls', 'xlsx'];

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

    public function __construct(
        EntityManagerInterface $entityManager,
        LoggerInterface $logger,
        StorageService $storageService,
        ContextService $contextService
    ) {
        $this->entityManager = $entityManager;
        $this->logger = $logger;
        $this->storageService = $storageService;
        $this->contextService = $contextService;
        $this->repository = $entityManager->getRepository(File::class);
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
     * @param UploadedFile $uploadedFile
     * @param string $origin
     * @param string|null $entityName
     * @return File
     * @throws \Exception
     */
    public function handleUpload(
        UploadedFile $uploadedFile,
        string $origin,
        string $entityName = null
    ): File {
        try {
            $filename = explode('.', $uploadedFile->getClientOriginalName());
            $name = (string) preg_replace('/[^\w\-\.]/', '', $filename[0]);
            $filename = sprintf('%s.%s', $name, $uploadedFile->getClientOriginalExtension());

            $newFile = $this->create($name, $uploadedFile->getClientOriginalExtension(), $origin, $entityName);
        } catch (\Exception $e) {
            $this->logger->error(sprintf('Error handling upload. Error: %s', $e->getMessage()));

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

            return $newFile;
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

        return $file;
    }

    /**
     * @param File $file
     */
    public function remove(File $file): void
    {
        $this->storageService->remove($file->getPath());

        $this->entityManager->remove($file);
        $this->entityManager->flush();
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
        return sprintf('%s/%s/%d', $homeId, $origin, floor($fileId/10000));
    }
}
