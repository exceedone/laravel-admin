<?php

use Encore\Admin\Auth\Database\Administrator;

/**
 * Tests for the admin top page (dashboard) and the sidebar menu, logged in as the admin user.
 */
class IndexTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The dashboard shows the environment, extensions and dependencies boxes.
     *
     * @return void
     */
    public function testIndex()
    {
        $this->visit('admin/')
            ->see('Dashboard')
            ->see('Description...')

            ->see('Environment')
            ->see('PHP version')
            ->see('Laravel version')

            ->see('Available extensions')
            ->seeLink('laravel-admin-ext/helpers', 'https://github.com/laravel-admin-extensions/helpers')
            ->seeLink('laravel-admin-ext/backup', 'https://github.com/laravel-admin-extensions/backup')
            ->seeLink('laravel-admin-ext/media-manager', 'https://github.com/laravel-admin-extensions/media-manager')

            ->see('Dependencies')
            ->see('php')
//            ->see('>=7.0.0')
            ->see('laravel/framework');
    }

    /**
     * Each item of the default sidebar menu opens its page.
     *
     * @return void
     */
    public function testClickMenu()
    {
        $this->visit('admin/')
            ->click('Users')
            ->seePageis('admin/auth/users')
            ->click('Roles')
            ->seePageis('admin/auth/roles')
            ->click('Permission')
            ->seePageis('admin/auth/permissions')
            ->click('Menu')
            ->seePageis('admin/auth/menu')
            ->click('Operation log')
            ->seePageis('admin/auth/logs');
    }
}
