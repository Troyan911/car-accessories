<?php

namespace Tests\Browser;

use Laravel\Dusk\Browser;
use Tests\DuskTestCase;

class ExampleTest extends DuskTestCase
{
    /**
     * A basic browser test example.
     */
    public function testBasicExample(): void
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/')
                ->assertSee('Laravel');
        });
    }

    /**
     * Test registration fields are displayed.
     *
     * @return void
     */
    public function testRegistrationFieldsAreDisplayed()
    {
        $this->browse(function (Browser $browser) {
            $browser->visit('/register')
                ->assertUrlIs('/register') // Confirm you're on the registration page
                ->assertPresent('input[name=name]') // Check for the name input
                ->assertPresent('input[name=email]') // Check for the email input
                ->assertPresent('input[name=password]') // Check for the password input
                ->assertPresent('input[name=password_confirmation]'); // Check for the password confirmation input
        });
    }
}
