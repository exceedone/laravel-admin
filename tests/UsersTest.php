<?php

use Encore\Admin\Auth\Database\Administrator;

/**
 * Tests for the admin user management page (admin/auth/users), logged in as the admin user.
 */
class UsersTest extends TestCase
{
    protected $user;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = Administrator::first();

        $this->be($this->user, 'admin');
    }

    /**
     * The users page lists the seeded admin user.
     *
     * @return void
     */
    public function testUsersIndexPage()
    {
        $this->visit('admin/auth/users')
            ->see('Administrator');
    }

    /**
     * Create a user, assign a role, and log in as the new user.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testCreateUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Edit a user and check the saved values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Reset a user's password, then log in with the new password.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testResetPassword()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
