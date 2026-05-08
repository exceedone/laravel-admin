<?php

class LaravelTest extends TestCase
{
    public function testLaravel()
    {
        $this->get('/')
            ->assertStatus(200)
            ->assertSee('Laravel');
    }
}
