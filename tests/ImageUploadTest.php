<?php

use Encore\Admin\Auth\Database\Administrator;
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
        return $this->post('admin/images', [
            'image1' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
            'image2' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
            'image3' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
            'image4' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
            'image5' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
            'image6' => new \Illuminate\Http\UploadedFile(__DIR__.'/assets/test.jpg', 'test.jpg', 'image/jpeg', null, true),
        ]);
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
