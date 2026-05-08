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
        return $this->post('admin/files', [
            'file1' => new \Illuminate\Http\UploadedFile(__DIR__.'/AuthTest.php', 'AuthTest.php'),
            'file2' => new \Illuminate\Http\UploadedFile(__DIR__.'/InstallTest.php', 'InstallTest.php'),
            'file3' => new \Illuminate\Http\UploadedFile(__DIR__.'/IndexTest.php', 'IndexTest.php'),
            'file4' => new \Illuminate\Http\UploadedFile(__DIR__.'/LaravelTest.php', 'LaravelTest.php'),
            'file5' => new \Illuminate\Http\UploadedFile(__DIR__.'/routes.php', 'routes.php'),
            'file6' => new \Illuminate\Http\UploadedFile(__DIR__.'/migrations/2016_11_22_093148_create_test_tables.php', '2016_11_22_093148_create_test_tables.php'),
        ]);
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
