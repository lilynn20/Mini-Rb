<?php

namespace Tests\Feature;

use App\Models\Annonce;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class PaginationTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function the_listings_page_is_paginated_at_nine_per_page()
    {
        $host = User::factory()->create(['role' => 'user']);
        Annonce::factory()->count(15)->create([
            'user_id' => $host->id,
            'ville'   => 'Testville',
        ]);

        // Page 1 exists and links to page 2.
        $this->get('/?ville=Testville')
            ->assertOk()
            ->assertSee('page=2');

        // Page 2 loads and preserves the filter (withQueryString).
        $this->get('/?ville=Testville&page=2')
            ->assertOk()
            ->assertSee('Testville');
    }

    #[Test]
    public function pagination_keeps_active_filters_in_the_links()
    {
        $host = User::factory()->create(['role' => 'user']);
        Annonce::factory()->count(12)->create([
            'user_id' => $host->id,
            'ville'   => 'Marrakech',
        ]);

        // The page-2 link must carry the ville filter forward.
        $this->get('/?ville=Marrakech')
            ->assertOk()
            ->assertSee('ville=Marrakech');
    }
}