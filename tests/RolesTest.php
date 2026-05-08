<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Role;

class RolesTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testRolesIndex()
    {
        $this->get('admin/auth/roles')
            ->assertOk()
            ->assertSee('Roles')
            ->assertSee('administrator');
    }

    public function testAddRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testAddRoleToUser()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testDeleteRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testEditRole()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
