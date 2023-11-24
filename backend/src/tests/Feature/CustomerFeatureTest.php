<?php
    
namespace Tests\Features;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerFeatureTest extends TestCase {
    
    public function testIndexReturnsDataInValidFormat() {
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