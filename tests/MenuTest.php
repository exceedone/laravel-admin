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
        $this->visit('admin/auth/menu')
            ->see('Menu')
            ->see('Auth')
            ->see('Users')
            ->see('Roles')
            ->see('Permission')
            ->see('Menu')
            ->see('Submit');
    }

    public function testAddMenu()
    {
        $client_mock = \Mockery::mock('overload:\ExmentDB');
        $client_mock->shouldReceive('transaction')->once();

        $item = ['parent_id' => '0', 'title' => 'Test', 'uri' => 'test'];

        $this->visit('admin/auth/menu')
            ->seePageIs('admin/auth/menu')
            ->see('Menu')
            ->submitForm('Submit', $item)
            ->seePageIs('admin/auth/menu');

//        $this->expectException(\Laravel\BrowserKitTesting\HttpException::class);
//
//        $this->visit('admin')
//            ->see('Test')
//            ->click('Test');
    }

    public function testDeleteMenu()
    {
        $this->delete('admin/auth/menu/8')
            ->assertEquals(7, Menu::count());
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

        $this->visit('admin/auth/menu/5/edit')
            ->see('Menu')
            ->submitForm('Submit', ['parent_id' => 5]);
    }
}
