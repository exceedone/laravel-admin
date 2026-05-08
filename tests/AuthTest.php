<?php

class AuthTest extends TestCase
{
    public function testLoginPage()
    {
        $this->get('admin/auth/login')
            ->assertOk()
            ->assertSee('login');
    }

    public function testVisitWithoutLogin()
    {
        $this->assertGuest('admin');

        $this->get('admin')
            ->assertRedirect();
    }

    public function testLogin()
    {
        $credentials = ['username' => 'admin', 'password' => 'admin'];

        $this->get('admin/auth/login')
            ->assertOk()
            ->assertSee('login');

        $this->post('admin/auth/login', $credentials)
            ->assertRedirect();

        $this->assertAuthenticated('admin');

        $this->get('admin')
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
            ->assertSee('laravel/framework')
            ->assertSee('<span>Admin</span>', false)
            ->assertSee('<span>Users</span>', false)
            ->assertSee('<span>Roles</span>', false)
            ->assertSee('<span>Permission</span>', false)
            ->assertSee('<span>Operation log</span>', false)
            ->assertSee('<span>Menu</span>', false);
    }

    public function testLogout()
    {
        $this->get('admin/auth/logout')
            ->assertRedirect('admin/auth/login');

        $this->assertGuest('admin');
    }
}
}
