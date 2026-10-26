<?php

declare(strict_types=1);

use Illuminate\Support\Facades\Route;

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

it('renders a missing page as an inertia error page with shared data', function (): void {
    $this->get('/missing')
        ->assertNotFound()
        ->assertInertia(fn ($page) => $page
            ->component('Error')
            ->where('status', 404)
            ->where('auth.user', null));
});

it('renders http errors as inertia error pages', function (int $status): void {
    config(['app.debug' => false]);
    Route::get('/test-error', fn () => abort($status))->middleware('web');

    $this->get('/test-error')
        ->assertStatus($status)
        ->assertInertia(fn ($page) => $page->component('Error')->where('status', $status));
})->with([403, 419, 500, 503]);

it('sends an expired form submission back with a toast', function (): void {
    Route::post('/test-error', fn () => abort(419))->middleware('web');

    $this->from(route('home'))
        ->post('/test-error')
        ->assertRedirect(route('home'))
        ->assertInertiaFlash('toasts', [['type' => 'warning', 'message' => 'The page expired. Please try again.']]);
});

it('keeps the debug page for server errors while debugging', function (): void {
    config(['app.debug' => true]);
    Route::get('/test-error', fn () => abort(500))->middleware('web');

    $this->get('/test-error')
        ->assertServerError()
        ->assertHeaderMissing('X-Inertia')
        ->assertDontSee('"component":"Error"', escape: false);
});

it('leaves other http errors to the default renderer', function (): void {
    Route::get('/test-error', fn () => abort(429))->middleware('web');

    $this->get('/test-error')
        ->assertTooManyRequests()
        ->assertDontSee('"component":"Error"', escape: false);
});
