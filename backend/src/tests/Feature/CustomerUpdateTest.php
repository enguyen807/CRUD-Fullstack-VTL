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
            'first_name' => 'George',
            'last_name'  => 'Santos',
            'birth_date' => '11.03.1975',
            'username'   => 'gsantos',
            'password'   => 'password'
        ];

        $this->json('put', 'api/customers/4', $parameters)
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