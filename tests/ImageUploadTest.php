<?php

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\File;
use Tests\Models\Image;
use Tests\Models\MultipleImage;

/**
 * Tests for the image upload fields, using the test resources admin/images and
 * admin/multiple-images, logged in as the admin user.
 */
class ImageUploadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The grid of admin/images has no filter.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testDisableFilter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * The create page shows the image upload fields.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testImageUploadPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Submit the create form of admin/images with tests/assets/test.jpg attached to each of image1 to image6.
     *
     * @return $this
     */
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

    /**
     * Uploaded images are stored and their paths are saved.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUploadImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Removing an image from a record deletes the file.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testRemoveImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Replacing the image of an existing record.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Deleting a record also deletes its images.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testDeleteImages()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Deleting several records at once.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testBatchDelete()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Uploading several images to a multiple image field.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUploadMultipleImage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Removing images from a multiple image field.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testRemoveMultipleFiles()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Count the files in a directory under public/ (default: uploads/images).
     *
     * @return int
     */
    protected function fileCountInImageDir($dir = 'uploads/images')
    {
        $file = new FilesystemIterator(public_path($dir), FilesystemIterator::SKIP_DOTS);

        return iterator_count($file);
    }
}
