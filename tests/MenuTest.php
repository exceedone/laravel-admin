<?php

use Encore\Admin\Auth\Database\Administrator;
use Encore\Admin\Auth\Database\Menu;

/**
 * Tests for the menu management page (admin/auth/menu), logged in as the admin user.
 * The default menu seeded by "admin:install" has 7 items (ids 1 to 7).
 */
class MenuTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The menu page shows the default menu items and the form to add a new item.
     *
     * @return void
     */
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

    /**
     * Submitting the add form on the menu page redirects back to the menu page.
     *
     * Form saving runs inside ExmentDB::transaction(), which this package does not define,
     * so ExmentDB is mocked. The mock does not run the callback, so the item is not actually
     * saved; this test only checks the submit and the redirect.
     *
     * @return void
     */
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

    /**
     * Deleting menu id 8, which does not exist in the default menu, keeps the 7 default items.
     *
     * @return void
     */
    public function testDeleteMenu()
    {
        $this->delete('admin/auth/menu/8')
            ->assertEquals(7, Menu::count());
    }

    /**
     * Edit a menu item and check the saved values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testEditMenu()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Open the detail page of a menu item.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testShowPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Intended to check that a menu item cannot be set as its own parent.
     *
     * Note: the InvalidArgumentException currently comes from browser-kit, because the edit
     * page has no form with a "Submit" button ("Could not find a form that has submit button
     * [Submit]"). The parent check in ModelTree is not reached.
     *
     * @return void
     */
    public function testEditMenuParent()
    {
        $this->expectException(InvalidArgumentException::class);

        $this->visit('admin/auth/menu/5/edit')
            ->see('Menu')
            ->submitForm('Submit', ['parent_id' => 5]);
    }
}
