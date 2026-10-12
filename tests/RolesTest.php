<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;

/**
 * Tests for the role management page (admin/auth/roles), logged in as the admin user.
 */
class RolesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The roles page lists the seeded administrator role.
     *
     * @return void
     */
    public function testRolesIndex()
    {
        $this->visit('admin/auth/roles')
            ->see('Roles')
            ->see('administrator');
    }

    /**
     * Add a role.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Assign a role to a user.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testAddRoleToUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Delete a role.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testDeleteRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Edit a role and check the saved values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testEditRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
