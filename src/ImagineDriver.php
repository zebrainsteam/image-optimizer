<?php

declare(strict_types=1);

namespace ImageOptimizer;

use Imagine\Gd\Imagine;

class ImagineDriver
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
     * process.
     *
     * @access	public
     * @param	string	$path       	
     * @param	int   	$width      	
     * @param	int   	$height     	
     * @param	string	$resizedFile	
     * @return	void
     */
    public function process(string $path, int $width, int $height, string $resizedFile): void
    {
        $this->imagine->open($path)
            ->thumbnail(new Box($width, $height), ImageInterface::THUMBNAIL_OUTBOUND)
            ->save($resizedFile)
            ->save($resizedFile . '.webp');
    }
}
