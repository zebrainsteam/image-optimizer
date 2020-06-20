<?php

namespace ImageOptimizer;

interface DriverInterface
{
    /**
     * Creates optimized images
     *
     * @access	public
     * @param	string	$path       	
     * @param	int   	$width      	
     * @param	int   	$height     	
     * @param	string	$resizedFile	
     * @return	void
     */
    public function process(string $path, int $width, int $height, string $resizedFile): void;
}
