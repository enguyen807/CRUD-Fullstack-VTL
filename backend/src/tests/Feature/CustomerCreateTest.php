<?php

namespace Tests\Feature;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerCreateTest extends TestCase 
{
    /** @test */
    public function it_can_create_a_customer(): void
    {
        $parameters = [
            'first_name' => 'John',
            'last_name'  => 'Smith',
            'birth_date' => '28.07.1986',
            'username'   => 'jsmith234',
            'password'   => 'password'
        ];

        $this->json('post', 'api/customers', $parameters)
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
                        'created_at',
                    ]
                ]
            );
    }
}