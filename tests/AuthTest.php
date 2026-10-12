<?php

/**
 * Tests for login and logout of the admin panel.
 */
class AuthTest extends TestCase
{
    /**
     * The login page can be opened without logging in.
     *
     * @return void
     */
    public function testLoginPage()
    {
        $this->visit('admin/auth/login')
            ->see('login');
    }

    /**
     * A guest who opens the admin top page is redirected to the login page.
     *
     * @return void
     */
    public function testVisitWithoutLogin()
    {
        $this->visit('admin')
            ->dontSeeIsAuthenticated('admin')
            ->seePageIs('admin/auth/login');
    }

    /**
     * Logging in with the seeded admin account (admin / admin) opens the dashboard.
     * The dashboard shows the environment, extensions and dependencies boxes, and the
     * sidebar shows the default admin menu.
     *
     * @return void
     */
    public function testLogin()
    {
        $credentials = ['username' => 'admin', 'password' => 'admin'];

        $this->visit('admin/auth/login')
            ->see('login')
            ->submitForm('Login', $credentials)
            ->see('dashboard')
            ->seeCredentials($credentials, 'admin')
            ->seeIsAuthenticated('admin')
            ->seePageIs('admin')
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

        $this
            ->see('<span>Admin</span>')
            ->see('<span>Users</span>')
            ->see('<span>Roles</span>')
            ->see('<span>Permission</span>')
            ->see('<span>Operation log</span>')
            ->see('<span>Menu</span>');
    }

    /**
     * Logging out redirects to the login page and the user is no longer authenticated.
     *
     * @return void
     */
    public function testLogout()
    {
        $this->visit('admin/auth/logout')
            ->seePageIs('admin/auth/login')
            ->dontSeeIsAuthenticated('admin');
    }
}
