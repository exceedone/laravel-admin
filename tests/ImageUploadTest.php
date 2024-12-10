<?php

use OpenAdmin\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\File;
use Tests\Models\Image;
use Tests\Models\MultipleImage;

class ImageUploadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testDisableFilter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testImageUploadPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    protected function uploadImages()
    {
        return $this->visit('admin/images/create')
            ->attach(__DIR__.'/assets/test.jpg', 'image1')
            ->attach(__DIR__.'/assets/test.jpg', 'image2')
            ->attach(__DIR__.'/assets/test.jpg', 'image3')
            ->attach(__DIR__.'/assets/test.jpg', 'image4')
            ->attach(__DIR__.'/assets/test.jpg', 'image5')
            ->attach(__DIR__.'/assets/test.jpg', 'image6')
            ->press('Submit');
    }

    public function testUploadImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testRemoveImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testDeleteImages()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testBatchDelete()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUploadMultipleImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testRemoveMultipleFiles()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    protected function fileCountInImageDir($dir = 'uploads/images')
    {
        $file = new FilesystemIterator(public_path($dir), FilesystemIterator::SKIP_DOTS);

        return iterator_count($file);
    }
}
