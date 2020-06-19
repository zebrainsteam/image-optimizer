<?php

namespace ImageOptimizer;

use Imagine\Gd\Imagine;
use ImageOptimized;
use Imagine\Image\{Box, ImageInterface};
use ImageOptimizer\Exception\ImageNotFoundException;
use ImageOptimizer\Exception\DirectoryNotFoundException;

class ImageOptimizer
{
    protected $imagine;

    public function __construct()
    {
        $this->imagine = new Imagine();

        $this->resizedFolder = '/resizes_images';
    }

    public function getOptimizedImage(string $path, int $height, int $width): ImageOptimized
    {
        $appPath = $_ENV['APP_PATH'];

        $relativePath = parse_url($path, PHP_URL_PATH);

        $pathinfo = pathinfo($relativePath);

        $absolutePath = $appPath . $path;

        if (!file_exists($absolutePath)) {
            throw new ImageNotFoundException($absolutePath);
        }

        $resizedUrl = $pathinfo['dirname'] . $pathinfo['filename'] . '_' . $height . 'x' . $width . '.' . $pathinfo['extension'];

        $resizedFile = $this->getBasePath() . $resizedUrl;

        $optimizedImage = new ImageOptimized([
            'original_url' => $path,
            'resized_url' => $this->resizedFolder . $resizedUrl,
            'resized_webp_url' => $this->resizedFolder . $resizedUrl . '.webp',
        ]);

        if (!file_exists($resizedFile)) {

            $this->directoryExists($resizedFile);

            $this->imagine->open($absolutePath)
                ->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)
                ->save($resizedFile)
                ->save($resizedFile . '.webp');
        }

        return $optimizedImage;
    }

    /**
     * @param	string	$path	
     * @return	void
     */
    protected function directoryExists(string $path): void
    {
        $directory = dirname($path);

        if (!file_exists($directory)) {

            if (!mkdir($directory, 0755, true)) {
                throw new DirectoryNotFoundException();
            }
        }
    }

    /**
     * @return	string
     */
    protected function getBasePath(): string
    {
        return $_ENV['APP_PATH'] . '/upload' . $this->resizedFolder;
    }
}