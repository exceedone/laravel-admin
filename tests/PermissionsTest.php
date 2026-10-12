<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;

/**
 * Tests for the permission management page (admin/auth/permissions), logged in as the admin user.
 */
class PermissionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The seeded admin user has the administrator role, and the permissions page can be opened.
     *
     * @return void
     */
    public function testPermissionsIndex()
    {
        $this->assertTrue(Administrator::first()->isAdministrator());

        $this->visit('admin/auth/permissions')
            ->see('Permissions');
    }

    /**
     * Add permissions and delete them.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddAndDeletePermissions()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Add a permission to a role.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddPermissionToRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Add a permission to a user.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddPermissionToUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Create a user, assign a permission, and check what the user can access.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddUserAndAssignPermission()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * A user gets the permissions of their role.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testPermissionThroughRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Edit a permission and check the saved values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testEditPermission()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
