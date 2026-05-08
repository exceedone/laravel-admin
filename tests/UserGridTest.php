<?php

use Encore\Admin\Auth\Database\Administrator;
use Symfony\Component\DomCrawler\Crawler;
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

    /** Helper: create a DomCrawler from a TestResponse. */
    private function crawlerFrom(\Illuminate\Testing\TestResponse $response): Crawler
    {
        return new Crawler($response->getContent());
    }

    public function testGridWithData()
    {
        $this->seedsTable();

        $this->get('admin/users')
            ->assertOk()
            ->assertSee('All users');

        $this->assertCount(100, UserModel::all());
        $this->assertCount(100, ProfileModel::all());
    }

    public function testGridPagination()
    {
        $this->seedsTable(65);

        $this->get('admin/users')->assertOk()->assertSee('All users');

        $response = $this->get('admin/users?page=2')->assertOk();
        $this->assertCount(20, $this->crawlerFrom($response)->filter('td a i[class*=fa-edit]'));

        $response = $this->get('admin/users?page=3')->assertOk();
        $this->assertCount(20, $this->crawlerFrom($response)->filter('td a i[class*=fa-edit]'));

        $response = $this->get('admin/users?page=4')->assertOk();
        $this->assertCount(5, $this->crawlerFrom($response)->filter('td a i[class*=fa-edit]'));

        $response = $this->get('admin/users?page=1')->assertOk();
        $this->assertCount(20, $this->crawlerFrom($response)->filter('td a i[class*=fa-edit]'));
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

        $this->get('admin/users')->assertOk()->assertSee('All users');

        $this->assertCount(50, UserModel::all());
        $this->assertCount(50, ProfileModel::all());

        $users = UserModel::where('username', 'like', '%mi%')->get();

        $response = $this->get('admin/users?username=mi')->assertOk();
        $crawler  = $this->crawlerFrom($response);

        // Table rows minus header row should equal matching users count.
        $this->assertCount($crawler->filter('table tr')->count() - 1, $users);

        foreach ($users as $user) {
            $response->assertSee($user->username);
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

        $this->get('admin/users')
            ->assertOk()
            ->assertSee('Column1 not in table')
            ->assertSee('Column2 not in table')
            ->assertSee("full name:{$user->profile->first_name} {$user->profile->last_name}")
            ->assertSee("{$user->email}#{$user->profile->color}");
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

        $response = $this->get('admin/users')->assertOk();
        $crawler  = $this->crawlerFrom($response);

        $this->assertCount(15, $crawler->filter('td a i[class*=fa-edit]'));
        $this->assertCount(15, $crawler->filter('td a i[class*=fa-trash]'));
    }

    public function testGridRows()
    {
        $this->seedsTable(10);

        $response = $this->get('admin/users')->assertOk()->assertSee('detail');
        $crawler  = $this->crawlerFrom($response);

        $this->assertCount(5, $crawler->filter('td a[class*=btn]'));
    }

    public function testGridPerPage()
    {
        $this->seedsTable(98);

        $response = $this->get('admin/users')->assertOk();
        $crawler  = $this->crawlerFrom($response);

        $this->assertCount(1, $crawler->filter('select[class*=per-page][name=per-page]'));
        $response->assertSee('>10<', false)
            ->assertSee('>20<', false)
            ->assertSee('>30<', false)
            ->assertSee('>50<', false)
            ->assertSee('>100<', false);

        $this->assertStringContainsString(
            'per_page=20',
            $crawler->filter('select option[selected]')->attr('value') ?? ''
        );

        $perPage  = rand(1, 98);
        $response = $this->get('admin/users?per_page='.$perPage)->assertOk();
        $crawler  = $this->crawlerFrom($response);

        $this->assertStringContainsString((string) $perPage, $crawler->filter('select option[selected]')->text());
        // +1 for header row
        $this->assertCount($perPage + 1, $crawler->filter('tr'));
    }
}

