<?php

declare(strict_types=1);

it('renders api errors as json', function (): void {
    $this->get('/api/missing')
        ->assertNotFound()
        ->assertHeader('Content-Type', 'application/json');
});

it('renders errors as json when the request expects json', function (): void {
    $this->getJson('/missing')
        ->assertNotFound()
        ->assertJsonStructure(['message']);
});
