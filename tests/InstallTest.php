<?php

/**
 * Tests for the result of the "admin:install" command run in TestCase::setUp().
 */
class InstallTest extends TestCase
{
    /**
     * "admin:install" creates the admin directory with its controllers, routes and
     * bootstrap files, and "vendor:publish" publishes the config file and the assets.
     *
     * @return void
     */
    public function testInstalledDirectories()
    {
        $this->assertFileExists(admin_path());

        $this->assertFileExists(admin_path('Controllers'));

        $this->assertFileExists(admin_path('routes.php'));

        $this->assertFileExists(admin_path('bootstrap.php'));

        $this->assertFileExists(admin_path('Controllers/HomeController.php'));

        $this->assertFileExists(admin_path('Controllers/AuthController.php'));

        $this->assertFileExists(admin_path('Controllers/ExampleController.php'));

        $this->assertFileExists(config_path('admin.php'));

        $this->assertFileExists(public_path('vendor/laravel-admin'));
    }
}
