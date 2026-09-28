<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ExampleTest extends TestCase
{
    use RefreshDatabase;

    public function test_the_public_pages_load_successfully(): void
    {
        $this->get('/')->assertStatus(200);
        $this->get('/vehicles')->assertStatus(200);
        $this->get('/about')->assertStatus(200);
        $this->get('/contact')->assertStatus(200);
        $this->get('/login')->assertStatus(200);
        $this->get('/register')->assertStatus(200);
    }

    public function test_unknown_pages_return_the_404_view(): void
    {
        $this->get('/this-page-does-not-exist')->assertStatus(404);
    }
}
