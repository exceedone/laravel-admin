<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\OperationLog;

/**
 * Tests for the operation log (admin/auth/logs), logged in as the admin user.
 * Requests to admin/auth/logs* are excluded from logging in tests/config/admin.php,
 * so visiting the log page itself does not add a log.
 */
class OperationLogTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The operation log page can be opened.
     *
     * @return void
     */
    public function testOperationLogIndex()
    {
        $this->visit('admin/auth/logs')
            ->see('Operation log')
            ->see('List')
            ->see('GET')
            ->see('admin/auth/logs');
    }

    /**
     * Visiting admin pages records one GET log for each page, and the log page is not recorded.
     *
     * @return void
     */
    public function testGenerateLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->visit('admin/auth/menu')
            ->seePageIs('admin/auth/menu')
            ->visit('admin/auth/users')
            ->seePageIs('admin/auth/users')
            ->visit('admin/auth/permissions')
            ->seePageIs('admin/auth/permissions')
            ->visit('admin/auth/roles')
            ->seePageIs('admin/auth/roles')
            ->visit('admin/auth/logs')
            ->seePageIs('admin/auth/logs')
            ->seeInDatabase($table, ['path' => 'admin/auth/menu', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/users', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/permissions', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/roles', 'method' => 'GET']);

        $this->assertEquals(4, OperationLog::count());
    }

    /**
     * Visiting a page records a log, and deleting it by id removes it.
     *
     * @return void
     */
    public function testDeleteLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->visit('admin/auth/logs')
            ->seePageIs('admin/auth/logs')
            ->assertEquals(0, OperationLog::count());

        $this->visit('admin/auth/users');

        $this->seeInDatabase($table, ['path' => 'admin/auth/users', 'method' => 'GET']);

        $this->delete('admin/auth/logs/1')
            ->assertEquals(0, OperationLog::count());
    }

    /**
     * Several logs can be deleted at once by passing comma-separated ids.
     *
     * @return void
     */
    public function testDeleteMultipleLogs()
    {
        $table = config('admin.database.operation_log_table');

        $this->visit('admin/auth/menu')
            ->visit('admin/auth/users')
            ->visit('admin/auth/permissions')
            ->visit('admin/auth/roles')
            ->seeInDatabase($table, ['path' => 'admin/auth/menu', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/users', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/permissions', 'method' => 'GET'])
            ->seeInDatabase($table, ['path' => 'admin/auth/roles', 'method' => 'GET'])
            ->assertEquals(4, OperationLog::count());

        $this->delete('admin/auth/logs/1,2,3,4')
            ->notSeeInDatabase($table, ['path' => 'admin/auth/menu', 'method' => 'GET'])
            ->notSeeInDatabase($table, ['path' => 'admin/auth/users', 'method' => 'GET'])
            ->notSeeInDatabase($table, ['path' => 'admin/auth/permissions', 'method' => 'GET'])
            ->notSeeInDatabase($table, ['path' => 'admin/auth/roles', 'method' => 'GET'])

            ->assertEquals(0, OperationLog::count());
    }
}
