<?php

declare(strict_types=1);

it('renders the dark class for a dark appearance cookie', function (): void {
    $this->withUnencryptedCookie('appearance', 'dark')
        ->get(route('home'))
        ->assertOk()
        ->assertSee("const appearance = 'dark'", escape: false)
        ->assertSee('class="dark"', escape: false);
});

it('falls back to system for an unknown appearance cookie', function (): void {
    $this->withUnencryptedCookie('appearance', '\\')
        ->get(route('home'))
        ->assertOk()
        ->assertSee("const appearance = 'system'", escape: false);
});
