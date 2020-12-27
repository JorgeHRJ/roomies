<?php

namespace App\Messenger\BlurImage;

class BlurImageMessage
{
    /** @var int */
    private $imageId;

    public function __construct(int $imageId)
    {
        $this->imageId = $imageId;
    }

    /**
     * @return int
     */
    public function getImageId(): int
    {
        return $this->imageId;
    }

    /**
     * @param int $imageId
     */
    public function setImageId(int $imageId): void
    {
        $this->imageId = $imageId;
    }
}
