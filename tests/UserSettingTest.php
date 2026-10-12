<?php

use Encore\Admin\Auth\Database\Administrator;
use Illuminate\Support\Facades\File;

/**
 * Tests for the user setting page (admin/auth/setting), logged in as the admin user.
 */
class UserSettingTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The setting page shows the current user's values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testVisitSettingPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
    /**
     * Updating the name.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateName()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Updating the avatar image.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateAvatar()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Updating the password fails when the confirmation does not match.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdatePasswordConfirmation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Updating the password, then logging in with the new password.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdatePassword()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
