<?php

use Encore\Admin\Auth\Database\Administrator;

class FileUploadTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testFileUploadPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

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

    public function testUploadFile()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateFile()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testDeleteFiles()
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
}
