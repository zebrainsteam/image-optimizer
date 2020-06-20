<?php

declare(strict_types=1);

namespace ImageOptimizer;

use ImageOptimizer\Exception\ImageNotFoundException;
use ImageOptimizer\Exception\DirectoryNotFoundException;

class ImageOptimizer
{
    /**
     * @var DriverInterface $imagine
     */
    protected $driver;

    /**
     * @var string $appPath
     */
    protected $appPath;

    public function __construct(DriverInterface $driver, string $resizedFolder, string $appPath)
    {
        $this->driver = $driver;
        $this->resizedFolder = $resizedFolder;
        $this->appPath = $appPath;
    }

    /**
     * Optimizes image
     *
     * @access	public
     * @param	string	$path  	
     * @param	int   	$height	
     * @param	int   	$width 	
     * @return	ImageOptimized
     */
    public function getOptimizedImage(string $path, int $height, int $width): ImageOptimized
    {
        $relativePath = parse_url($path, PHP_URL_PATH);

        $pathinfo = pathinfo($relativePath);

        $absolutePath = $this->appPath . $path;

        if (! $this->fileExists($absolutePath)) {
            throw new ImageNotFoundException($absolutePath);
        }

        $resizedUrl = $pathinfo['dirname'] . $pathinfo['filename'] . '_' . $height . 'x' . $width . '.' . $pathinfo['extension'];

        $resizedFile = $this->getBasePath() . $resizedUrl;

        $optimizedImage = new ImageOptimized([
            'original_url' => $path,
            'resized_url' => $this->resizedFolder . $resizedUrl,
            'resized_webp_url' => $this->resizedFolder . $resizedUrl . '.webp',
        ]);

        if (! $this->fileExists($resizedFile)) {
            $this->ensureDirectoryExists($resizedFile);
            $this->driver->process($absolutePath, $width, $height, $resizedFile);
        }

        return $optimizedImage;
    }

    /**
     * @param	string	$path	
     * @return	void
     */
    protected function ensureDirectoryExists(string $path): void
    {
        $directory = dirname($path);

        if (! $this->fileExists($directory)) {
            if (! $this->mkdir($directory)) {
                throw new DirectoryNotFoundException();
            }
        }
    }

    /**
     * file_exists wrapper
     *
     * @access	protected
     * @param	string	$path	
     * @return	bool
     */
    protected function fileExists(string $path): bool
    {
        return file_exists($path);
    }

    /**
     * mkdir wraper
     *
     * @access	protected
     * @param	string	$directory	
     * @return	bool
     */
    protected function mkdir(string $directory): bool
    {
        return mkdir($directory, 0755, true);
    }

    /**
     * @return	string
     */
    protected function getBasePath(): string
    {
        return $this->appPath . $this->resizedFolder;
    }
}
