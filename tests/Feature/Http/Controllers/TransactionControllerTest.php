<?php

namespace Tests\Feature\Http\Controllers;

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TransactionControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function it_returns_paginated_transactions()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory(25)->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson('api/transactions');

        $response->assertStatus(200)
            ->assertJsonCount(10, 'data'); // Assuming you paginate by 10 items per page
    }

    /** @test */
    public function it_shows_a_specific_transaction()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)
            ->getJson("api/transactions/{$transaction->id}");

        $response->assertStatus(200)
            ->assertJson(['id' => $transaction->id]);
    }

    /** @test */
    public function it_deletes_a_transaction()
    {
        $user = User::factory()->create();
        $transaction = Transaction::factory()->create(['author_id' => $user->id]);

        $response = $this->actingAs($user)
            ->deleteJson("api/transactions/{$transaction->id}");

        $response->assertStatus(204);

        // Ensure the transaction is deleted
        $this->assertDatabaseMissing('transactions', ['id' => $transaction->id]);
    }
}
