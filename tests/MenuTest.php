<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;

class MenuTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testMenuIndex()
    {
        $this->get('admin/auth/menu')
            ->assertOk()
            ->assertSee('Menu')
            ->assertSee('Auth')
            ->assertSee('Users')
            ->assertSee('Roles')
            ->assertSee('Permission')
            ->assertSee('Menu')
            ->assertSee('Submit');
    }

    public function testAddMenu()
    {
        $item = ['parent_id' => '0', 'title' => 'Test', 'uri' => 'test'];

        $this->get('admin/auth/menu')->assertOk()->assertSee('Menu');

        $this->post('admin/auth/menu', $item)
            ->assertRedirect();

        $this->get('admin/auth/menu')->assertOk();
    }

    public function testDeleteMenu()
    {
        $this->delete('admin/auth/menu/8');

        $this->assertEquals(7, Menu::count());
    }

    public function testEditMenu()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testShowPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testEditMenuParent()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->withoutExceptionHandling();

        $this->get('admin/auth/menu/5/edit')->assertOk()->assertSee('Menu');

        $this->put('admin/auth/menu/5', ['parent_id' => 5]);
    }
}
