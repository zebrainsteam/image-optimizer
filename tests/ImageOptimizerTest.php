<?php

namespace ImageOptimizer\Tests;

use Mockery\Adapter\Phpunit\MockeryTestCase;
use ImageOptimizer\{ImageOptimizer, ImagineDriver};
use Imagine\Gd\Imagine;
use Mockery;
use ImageOptimizer\Exception\{DirectoryNotFoundException, ImageNotFoundException};

class ImageOptimizerTest extends MockeryTestCase
{
    protected $resizedFolder;

    protected $appPath;

    public function setUp(): void
    {
        $this->resizedFolder = '/resized_images';
        $this->appPath = '/var/www/html';
    }

    /**
     * @test
     */
    public function exception_is_thrown_if_file_not_exists()
    {
        $driver = Mockery::mock(ImagineDriver::class);

        $service = $this->getService($driver);

        $this->fakeFileCheck($service, ['/var/www/html/image.png' => false]);

        $this->expectException(ImageNotFoundException::class);

        $service->getOptimizedImage('/image.png', 150, 100);
    }

    /**
     * @test
     */
    public function exception_is_thrown_if_folder_cannot_be_created()
    {
        $driver = Mockery::mock(ImagineDriver::class);

        $service = $this->getService($driver);

        $this->fakeFileCheck($service, [
            '/var/www/html/image.png' => true,
            '/var/www/html/resized_images/image_150x100.png' => false,
            '/var/www/html/resized_images' => false,
        ]);

        $service->shouldReceive('mkdir')
            ->with('/var/www/html/resized_images')
            ->andReturn(false);

        $this->expectException(DirectoryNotFoundException::class);

        $service->getOptimizedImage('/image.png', 150, 100);
    }

    /**
     * @test
     */
    public function driver_gets_called()
    {
        $driver = Mockery::mock(ImagineDriver::class);

        $service = $this->getService($driver);

        $this->fakeFileCheck($service, [
            '/var/www/html/image.png' => true,
            '/var/www/html/resized_images/image_150x100.png' => false,
            '/var/www/html/resized_images' => false,
        ]);

        $service->shouldReceive('mkdir')
            ->with('/var/www/html/resized_images')
            ->andReturn(true);

        $driver->shouldReceive('process')->once();

        $image = $service->getOptimizedImage('/image.png', 150, 100);

        $this->assertEquals('/image.png', $image->originalUrl);
        $this->assertEquals('/resized_images/image_150x100.png', $image->resizedUrl);
        $this->assertEquals('/resized_images/image_150x100.png.webp', $image->resizedWebpUrl);
    }

    /**
     * @test
     */
    public function driver_is_not_called_if_file_exists()
    {
        $driver = Mockery::mock(ImagineDriver::class);

        $service = $this->getService($driver);

        $this->fakeFileCheck($service, [
            '/var/www/html/image.png' => true,
            '/var/www/html/resized_images/image_150x100.png' => true,
        ]);

        $service->shouldNotReceive('mkdir');

        $driver->shouldNotReceive('process');

        $image = $service->getOptimizedImage('/image.png', 150, 100);

        $this->assertEquals('/image.png', $image->originalUrl);
        $this->assertEquals('/resized_images/image_150x100.png', $image->resizedUrl);
        $this->assertEquals('/resized_images/image_150x100.png.webp', $image->resizedWebpUrl);
    }

    protected function getService($driver)
    {
        return Mockery::mock(ImageOptimizer::class, [$driver, $this->resizedFolder, $this->appPath])
            ->makePartial()
            ->shouldAllowMockingProtectedMethods();
    }

    protected function fakeFileCheck($service, array $expectations): void
    {
        foreach ($expectations as $file => $return) {
            $service->shouldReceive('fileExists')
                ->with($file)
                ->andReturn($return);
        }
    }
}
