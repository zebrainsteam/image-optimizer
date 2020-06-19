<?php

declare(strict_types=1);

namespace ImageOptimizer;

use Imagine\Gd\Imagine;
use Imagine\Image\{Box, ImageInterface};

class ImagineDriver implements DriverInterface
{
    /**
     * @var Imagine $imagine
     */
    protected $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();
    }

    /**
     * @inheritDoc
     */
    public function process(string $path, int $width, int $height, string $resizedFile): void
    {
        $this->imagine->open($path)
            ->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save($resizedFile)
            ->save($resizedFile . '.webp');
    }
}
