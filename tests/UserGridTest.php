<?php

use Encore\Admin\Auth\Database\Administrator;
use Tests\Models\Profile as ProfileModel;
use Tests\Models\User as UserModel;

/**
 * Tests for the grid of the test resource admin/users (Tests\Controllers\UserController),
 * logged in as the admin user. The grid shows 20 rows per page by default.
 */
class UserGridTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The grid page shows the columns, filters and tools.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testIndexPage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Create test users, each with a profile and 5 tags.
     *
     * @return void
     */
    protected function seedsTable($count = 100)
    {
        UserModel::factory()
            ->count($count)
            ->hasTags(5)
            ->hasProfile()
            ->create();
    }

    /**
     * The grid page opens with 100 users, and each user has a profile.
     *
     * @return void
     */
    public function testGridWithData()
    {
        $this->seedsTable();

        $this->visit('admin/users')
            ->see('All users');

        $this->assertCount(100, UserModel::all());
        $this->assertCount(100, ProfileModel::all());
    }

    /**
     * With 65 users, pages 2 and 3 show 20 rows and page 4 shows the remaining 5.
     * The page link "1" goes back to the first page.
     *
     * @return void
     */
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

    /**
     * Filter the grid with an equal filter.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testEqualFilter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * The like filter on username shows exactly the users whose username contains "mi".
     *
     * @return void
     */
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

    /**
     * Filter the grid by a column of a related model.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testFilterRelation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Columns that are not in the table are shown with their display() callbacks, which
     * build the value from the user and the profile.
     *
     * @return void
     */
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

    /**
     * The grid shows the values of a has-many relation (tags).
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testHasManyRelation()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Each row has an edit and a delete action.
     *
     * @return void
     */
    public function testGridActions()
    {
        $this->seedsTable(15);

        $this->visit('admin/users');

        $this->assertCount(15, $this->crawler()->filter('td a i[class*=fa-edit]'));
        $this->assertCount(15, $this->crawler()->filter('td a i[class*=fa-trash]'));
    }

    /**
     * The custom "detail" button added in actions() is shown only for users with an even id
     * (5 of 10 users).
     *
     * @return void
     */
    public function testGridRows()
    {
        $this->seedsTable(10);

        $this->visit('admin/users')
            ->seeInElement('td a[class*=btn]', 'detail');

        $this->assertCount(5, $this->crawler()->filter('td a[class*=btn]'));
    }

    /**
     * The per-page selector offers 10, 20, 30, 50 and 100 with 20 selected, and the
     * per_page query parameter changes the number of rows shown.
     *
     * @return void
     */
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
