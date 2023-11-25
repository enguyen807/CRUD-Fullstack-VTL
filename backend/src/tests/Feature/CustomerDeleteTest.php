<?php

namespace Tests\Feature;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerDeleteTest extends TestCase 
{
    /** @test */
    public function it_can_delete_a_customer()
    {
        $this->json('delete', 'api/customers/17')
            ->seeStatusCode(Response::HTTP_GONE)
            ->seeJsonStructure([
                "status",
                "message"
            ]);        
    }
}