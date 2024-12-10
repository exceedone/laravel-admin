<?php

use OpenAdmin\Admin\Auth\Database\Administrator;

class UsersTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Administrator::first();

        $this->be($this->user, 'admin');
    }

    public function testUsersIndexPage()
    {
        $this->visit('admin/auth/users')
            ->see('Administrator');
    }

    public function testCreateUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testResetPassword()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
