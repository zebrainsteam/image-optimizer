<?php

namespace ImageOptimizer;

class ImageOptimized
{

	public $originalUrl;

    public $resizedUrl;

    public $resizedWebpUrl;

	public function __construct(array $data)
	{
		$this->originalUrl = $data['original_url'];
		$this->resizedUrl = $data['resized_url'];
		$this->resizedWebpUrl = $data['resized_webp_url'];
	}
}