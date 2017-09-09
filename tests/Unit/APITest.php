<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APITest extends TestCase
{
    /**
     * A basic test example.
     *
     * @return void
     */
    public function testVehiclesPost()
    {
        $response = $this->json('POST', '/vehicles', ['modelYear' => 2015,'manufacturer'=> 'Audi','model'=>'A3']);

        $response
            ->assertStatus(200)
            ->assertJson([]);

    }
}
