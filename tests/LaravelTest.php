<?php

/**
 * Checks that the Laravel application used for the tests boots.
 */
class LaravelTest extends TestCase
{
    /**
     * The Laravel welcome page ("/") returns 200.
     *
     * @return void
     */
    public function testLaravel()
    {
        $this->visit('/')
            ->assertResponseStatus(200)
            ->see('Laravel');
    }
}
