<?php

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\File;

class UserSettingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testVisitSettingPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
    public function testUpdateName()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateAvatar()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdatePasswordConfirmation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdatePassword()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
