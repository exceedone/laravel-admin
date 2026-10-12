<?php

use Encore\Admin\Auth\Database\Administrator;
use Tests\Models\User as UserModel;

/**
 * Tests for the form of the test resource admin/users (Tests\Controllers\UserController),
 * logged in as the admin user.
 */
class UserFormTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();

        $this->be(Administrator::first(), 'admin');
    }

    /**
     * The create page shows the form fields.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testCreatePage()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Submitting the create form saves the user, the profile and the tags.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testSubmitForm()
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
     * The edit page shows the saved values.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testEditForm()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Submitting the edit form updates the user.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateForm()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * Validation rules are applied when updating.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testUpdateFormWithRule()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * The form header shows the expected tools.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testFormHeader()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }

    /**
     * The form footer shows the expected buttons.
     *
     * Disabled: marked incomplete because the test is no longer maintained.
     *
     * @return void
     */
    public function testFormFooter()
    {
        $this->markTestIncomplete(
            'Removed due to unmaintained.'
        );
    }
}
