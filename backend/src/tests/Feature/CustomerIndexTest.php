<?php

namespace Tests\Feature;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerIndexTest extends TestCase 
{
    /** @test */
    public function it_can_get_all_customers()
    {
        $this->json('get', 'api/customers')
            ->seeStatusCode(Response::HTTP_OK)
            ->seeJsonStructure(
                [
                    'data' => [
                        '*' => [
                            'id',
                            'first_name',
                            'last_name',
                            'birth_date',
                            'username',
                            'created_at',
                        ]
                    ]
                ]
            );        
    }
}