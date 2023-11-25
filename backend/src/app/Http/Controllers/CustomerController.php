<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Laravel\Lumen\Http\ResponseFactory;
use PhpParser\Node\Stmt\TryCatch;

class CustomerController extends Controller
{
    /**
     * Returns all customers
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([ 'data' => Customer::all()], 200);
    }

    /**
     * Create customer
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $customer = Customer::where('username', '=', $request->input('username'))->first();

        if ($customer) {
            return response()->json(['message'=> 'User already exists!'], JsonResponse::HTTP_CONFLICT);
        }

        $customer = Customer::create([
            'first_name' => $request->input('first_name'),
            'last_name'  => $request->input('last_name'),
            'birth_date' => $request->input('birth_date'),
            'username'   => $request->input('username'),
            'password'   => Hash::make($request->input('password')),
        ]);
        return response()->json([ 'data' => $customer ], 200);
    }

    /**
     * Update customer
     * @param  int $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(int $id, Request $request): JsonResponse
    {
        try
        {
            $customer = Customer::findOrFail($id);
            $customer->update([
                'first_name' => $request->input('first_name'),
                'last_name'  => $request->input('last_name'),
                'birth_date' => $request->input('birth_date'),
                'username'   => $request->input('username'),
                'password'   => Hash::make($request->input('password')),
            ]);
    
            return response()->json([ 'data' => $customer ], 200);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json([ 'error' => $e->getMessage() ], 404);
        }

    }
    
    /**
     * Remove customer
     * @param  int  $id
     * @return mixed
     */
    public function destroy(int $id)
    {
        try {
            Customer::findOrFail($id)->delete();
            return response()->json(['status' => 410, 'message' => 'Deleted Successfully'], 410);
        } catch (ModelNotFoundException $e) {
            return response()->json([ 'error' => $e->getMessage() ], 404);
        }

    }       
}
