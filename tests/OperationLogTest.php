<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\OperationLog;

class OperationLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testOperationLogIndex()
    {
        $this->get('admin/auth/logs')
            ->assertOk()
            ->assertSee('Operation log')
            ->assertSee('List')
            ->assertSee('GET')
            ->assertSee('admin/auth/logs');
    }

    public function testGenerateLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->get('admin/auth/menu')->assertOk();
        $this->get('admin/auth/users')->assertOk();
        $this->get('admin/auth/permissions')->assertOk();
        $this->get('admin/auth/roles')->assertOk();
        $this->get('admin/auth/logs')->assertOk();

        $this->assertDatabaseHas($table, ['path' => 'admin/auth/menu', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/users', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/permissions', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/roles', 'method' => 'GET']);

        $this->assertEquals(4, OperationLog::count());
    }

    public function testDeleteLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->get('admin/auth/logs')->assertOk();
        $this->assertEquals(0, OperationLog::count());

        $this->get('admin/auth/users');
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/users', 'method' => 'GET']);

        $this->delete('admin/auth/logs/1');
        $this->assertEquals(0, OperationLog::count());
    }

    public function testDeleteMultipleLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->get('admin/auth/menu');
        $this->get('admin/auth/users');
        $this->get('admin/auth/permissions');
        $this->get('admin/auth/roles');

        $this->assertDatabaseHas($table, ['path' => 'admin/auth/menu', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/users', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/permissions', 'method' => 'GET']);
        $this->assertDatabaseHas($table, ['path' => 'admin/auth/roles', 'method' => 'GET']);
        $this->assertEquals(4, OperationLog::count());

        $this->delete('admin/auth/logs/1,2,3,4');

        $this->assertDatabaseMissing($table, ['path' => 'admin/auth/menu', 'method' => 'GET']);
        $this->assertDatabaseMissing($table, ['path' => 'admin/auth/users', 'method' => 'GET']);
        $this->assertDatabaseMissing($table, ['path' => 'admin/auth/permissions', 'method' => 'GET']);
        $this->assertDatabaseMissing($table, ['path' => 'admin/auth/roles', 'method' => 'GET']);
        $this->assertEquals(0, OperationLog::count());
    }
}
