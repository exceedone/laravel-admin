<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Permission;
use Encore\Admin\Auth\Database\Role;

class PermissionsTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testPermissionsIndex()
    {
        $this->assertTrue(Administrator::first()->isAdministrator());

        $this->visit('admin/auth/permissions')
            ->see('Permissions');
    }

    public function testAddAndDeletePermissions()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testAddPermissionToRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testAddPermissionToUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testAddUserAndAssignPermission()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testPermissionThroughRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testEditPermission()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
