<?php

namespace App\Messenger\BlurImage;

class BlurImageMessage
{
    /** @var int */
    private $fileId;

    public function __construct(int $fileId)
    {
        $this->fileId = $fileId;
    }

    /**
     * @return int
     */
    public function getFileId(): int
    {
        return $this->fileId;
    }

    /**
     * @param int $fileId
     */
    public function setFileId(int $fileId): void
    {
        $this->fileId = $fileId;
    }
}
