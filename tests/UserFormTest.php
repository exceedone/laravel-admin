<?php

use OpenAdmin\Admin\Auth\Database\Administrator;
use Tests\Models\User as UserModel;

class UserFormTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    public function testCreatePage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testSubmitForm()
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

    public function testEditForm()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateForm()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testUpdateFormWithRule()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testFormHeader()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    public function testFormFooter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
