<?php

use Encore\Admin\Auth\Database\Administrator;

class IndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testIndex()
    {
        $this->get('admin/')
            ->assertOk()
            ->assertSee('Dashboard')
            ->assertSee('Description...')
            ->assertSee('Environment')
            ->assertSee('PHP version')
            ->assertSee('Laravel version')
            ->assertSee('Available extensions')
            ->assertSee('https://github.com/laravel-admin-extensions/helpers')
            ->assertSee('https://github.com/laravel-admin-extensions/backup')
            ->assertSee('https://github.com/laravel-admin-extensions/media-manager')
            ->assertSee('Dependencies')
            ->assertSee('php')
            ->assertSee('laravel/framework');
    }

    public function testClickMenu()
    {
        // Navigate directly to each menu-linked page (replaces BrowserKit click+seePageIs)
        $this->get('admin/auth/users')->assertOk();
        $this->get('admin/auth/roles')->assertOk();
        $this->get('admin/auth/permissions')->assertOk();
        $this->get('admin/auth/menu')->assertOk();
        $this->get('admin/auth/logs')->assertOk();
    }
}
