<?php

declare(strict_types=1);

namespace ImageOptimizer;

class ImageOptimized
{
	/**
	 * @var string $originalUrl
	 */
	public $originalUrl;

	/**
	 * @var string $originalUrl
	 */
    public $resizedUrl;

	/**
	 * @var string $originalUrl
	 */
    public $resizedWebpUrl;

	public function __construct(array $data)
	{
		$this->originalUrl = $data['original_url'] ?? null;
		$this->resizedUrl = $data['resized_url'] ?? null;
		$this->resizedWebpUrl = $data['resized_webp_url'] ?? null;
	}
}
