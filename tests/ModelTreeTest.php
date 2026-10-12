<?php

use Tests\Models\Tree;

/**
 * Tests for the ModelTree trait, using Tests\Models\Tree (a model on the admin menu table).
 */
class ModelTreeTest extends TestCase
{
    public function setUp(): void
    {
        parent::setUp();
    }

    /**
     * selectOptions() with a query callback and a root text returns the root text as the
     * first option, followed by one option for each row that matches the query.
     *
     * @return void
     */
    public function testSelectOptions()
    {
        $rootText = 'Root Text';

        $options = Tree::selectOptions(function ($query) {
            return $query->where('uri', '');
        }, $rootText);

        $count = Tree::query()->where('uri', '')->count();

        $this->assertEquals(array_shift($options), $rootText);
        $this->assertEquals(count($options), $count);
    }
}
