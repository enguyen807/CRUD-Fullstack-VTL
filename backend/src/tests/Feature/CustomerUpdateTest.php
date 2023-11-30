<?php

namespace Tests\Feature;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerUpdateTest extends TestCase 
{
    /** @test */
    public function it_can_update_a_customer()
    {
        $parameters = [
            'first_name' => $this->faker->firstname,
            'last_name'  => $this->faker->lastname,
            'birth_date' => $this->faker->dateTimeBetween('1950-01-01', '1999-01-01')->format('d.m.Y'),
            'username'   => $this->faker->unique()->userName,
            'password'   => $this->faker->password
        ];

        $this->json('put', 'api/customers/4', $parameters, ['Content-Type' => 'text/plain'])
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJsonStructure(
                [
                    'data' => 
                    [
                        'id',
                        'first_name',
                        'last_name',
                        'birth_date',
                        'username',
                    ]
                ]
            );        
    }
}