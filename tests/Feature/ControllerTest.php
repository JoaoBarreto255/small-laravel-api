<?php

namespace Tests\Feature;

use Tests\TestCase;

class ControllerTest extends TestCase
{
    /**
     * Run test on entire api.
     */
    public function testBasicEntireApi(): void
    {
        // verify root uri.
        $this->get('/')
            ->assertNoContent();

        // reset storage.
        $this->post('/reset')
            ->assertOk()
            ->assertContent('OK');

        // Seek for non-existent account
        $this->get('/balance?account_id=1234')
            ->assertNotFound()
            ->assertContent('0');

        // Create account
        $this->postJson('/event', [
            "type" => "deposit",
            "destination" => "100",
            "amount" => 10,
        ])
        ->assertCreated()
        ->assertJson([
            "destination" => ["id" =>"100", "balance" =>10]
        ]);

        // deposit into existent account.
        $this->postJson('/event', [
            "type" => "deposit",
            "destination" => "100",
            "amount" => 10,
        ])
        ->assertCreated()
        ->assertJson([
            "destination" => ["id" =>"100", "balance" => 20]
        ]);

        // Seek for non-existent account
        $this->get('/balance?account_id=100')
            ->assertOk()
            ->assertContent('20');

        // withdraw from existent account.
        $this->postJson('/event', [
            "type" => "withdraw",
            "origin" => "100",
            "amount" => 5,
        ])
        ->assertCreated()
        ->assertJson([
            "origin" => ["id" =>"100", "balance" => 15]
        ]);

        // withdraw from non-existent account.
        $this->postJson('/event', [
            "type" => "withdraw",
            "origin" => "1234",
            "amount" => 5,
        ])
        ->assertNotFound()
        ->assertContent('0');

        // transfer from one account into another.
        $this->postJson('/event', [
            "type" => "transfer",
            "origin" => "100",
            "destination" => "300",
            "amount" => 15,
        ])
        ->assertCreated()
        ->assertJson([
            "origin" => ["id" =>"100", "balance" => 0],
            "destination" => ["id" =>"300", "balance" => 15],
        ]);

        // reset storage.
        $this->post('/reset')
            ->assertOk()
            ->assertContent('OK');
    }
}
