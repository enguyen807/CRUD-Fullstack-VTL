<?php
    
namespace Tests\Features;
    
use Illuminate\Http\Response;
use Tests\TestCase;
    
class CustomerFeatureTest extends TestCase 
{
    
    public function testIndexReturnsDataInValidFormat(): void 
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

    public function testCreateReturnsDataInValidFormat(): void 
    {
        $parameters = [
            'first_name' => 'John',
            'last_name'  => 'Smith',
            'birth_date' => '28.07.1986',
            'username'   => 'jsmith',
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

    public function testUpdateReturnsDataInValidFormat(): void 
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

    public function testDeleteReturnsDataInValidFormat(): void 
    {
        $this->json('delete', 'api/customers/17')
            ->seeStatusCode(Response::HTTP_GONE)
            ->seeJsonStructure([
                "status",
                "message"
            ]);
    }
    
}