<?php

use Encore\Admin\Auth\Database\Administrator;
use Tests\Models\Profile as ProfileModel;
use Tests\Models\User as UserModel;

class UserGridTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testIndexPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    protected function seedsTable($count = 100)
    {
        UserModel::factory()
            ->count($count)
            ->hasTags(5)
            ->hasProfile()
            ->create();
    }

    public function testGridWithData()
    {
        $this->seedsTable();

        $this->visit('admin/users')
            ->see('All users');

        $this->assertCount(100, UserModel::all());
        $this->assertCount(100, ProfileModel::all());
    }

    public function testGridPagination()
    {
        $this->seedsTable(65);

        $this->visit('admin/users')
            ->see('All users');

        $this->visit('admin/users?page=2');
        $this->assertCount(20, $this->crawler()->filter('td a i[class*=fa-edit]'));

        $this->visit('admin/users?page=3');
        $this->assertCount(20, $this->crawler()->filter('td a i[class*=fa-edit]'));

        $this->visit('admin/users?page=4');
        $this->assertCount(5, $this->crawler()->filter('td a i[class*=fa-edit]'));

        $this->click(1)->seePageIs('admin/users?page=1');
        $this->assertCount(20, $this->crawler()->filter('td a i[class*=fa-edit]'));
    }

    public function testEqualFilter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testLikeFilter()
    {
        $this->seedsTable(50);

        $this->visit('admin/users')
            ->see('All users');

        $this->assertCount(50, UserModel::all());
        $this->assertCount(50, ProfileModel::all());

        $users = UserModel::where('username', 'like', '%mi%')->get();

        $this->visit('admin/users?username=mi');

        $this->assertCount($this->crawler()->filter('table tr')->count() - 1, $users);

        foreach ($users as $user) {
            $this->seeInElement('td', $user->username);
        }
    }

    public function testFilterRelation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testDisplayCallback()
    {
        $this->seedsTable(1);

        $user = UserModel::with('profile')->find(1);

        $this->visit('admin/users')
            ->seeInElement('th', 'Column1 not in table')
            ->seeInElement('th', 'Column2 not in table')
            ->seeInElement('td', "full name:{$user->profile->first_name} {$user->profile->last_name}")
            ->seeInElement('td', "{$user->email}#{$user->profile->color}");
    }

    public function testHasManyRelation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testGridActions()
    {
        $this->seedsTable(15);

        $this->visit('admin/users');

        $this->assertCount(15, $this->crawler()->filter('td a i[class*=fa-edit]'));
        $this->assertCount(15, $this->crawler()->filter('td a i[class*=fa-trash]'));
    }

    public function testGridRows()
    {
        $this->seedsTable(10);

        $this->visit('admin/users')
            ->seeInElement('td a[class*=btn]', 'detail');

        $this->assertCount(5, $this->crawler()->filter('td a[class*=btn]'));
    }

    public function testGridPerPage()
    {
        $this->seedsTable(98);

        $this->visit('admin/users')
            ->seeElement('select[class*=per-page][name=per-page]')
            ->seeInElement('select option', 10)
            ->seeInElement('select option[selected]', 20)
            ->seeInElement('select option', 30)
            ->seeInElement('select option', 50)
            ->seeInElement('select option', 100);

        $this->assertEquals('http://localhost:8000/admin/users?per_page=20', $this->crawler()->filter('select option[selected]')->attr('value'));

        $perPage = rand(1, 98);

        $this->visit('admin/users?per_page='.$perPage)
            ->seeInElement('select option[selected]', $perPage)
            ->assertCount($perPage + 1, $this->crawler()->filter('tr'));
    }
}
