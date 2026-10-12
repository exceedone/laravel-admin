<?php

use Encore\Admin\Auth\Database\Administrator;

/**
 * Tests for the file upload field, using the test resource admin/files
 * (Tests\Controllers\FileController), logged in as the admin user.
 */
class FileUploadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The create page shows the file upload fields.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testFileUploadPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Submit the create form of admin/files with a file attached to each of file1 to file6.
     *
     * @return $this
     */
    protected function uploadFiles()
    {
        return $this->visit('admin/files/create')
            ->attach(__DIR__.'/AuthTest.php', 'file1')
            ->attach(__DIR__.'/InstallTest.php', 'file2')
            ->attach(__DIR__.'/IndexTest.php', 'file3')
            ->attach(__DIR__.'/LaravelTest.php', 'file4')
            ->attach(__DIR__.'/routes.php', 'file5')
            ->attach(__DIR__.'/migrations/2016_11_22_093148_create_test_tables.php', 'file6')
            ->press('Submit');
    }

    /**
     * Uploaded files are stored and their paths are saved.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUploadFile()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Replacing the files of an existing record.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateFile()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Deleting a record also deletes its files.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testDeleteFiles()
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
}
