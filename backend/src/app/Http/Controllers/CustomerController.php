<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

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
        $fields = $this->validate($request, [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|min:6|max:50|unique:customers,username',
            'birth_date' => 'required|date_format:d.m.Y|before:today|after:1900-01-01',
            'password' => 'required|min:6|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        ]);

        $customer = Customer::where('username', '=', $fields['username'])->first();

        if ($customer) {
            return response()->json(['message'=> 'User already exists!'], JsonResponse::HTTP_CONFLICT);
        }

        $customer = Customer::create([
            'first_name' => $fields['first_name'],
            'last_name'  => $fields['last_name'],
            'birth_date' => $fields['birth_date'],
            'username'   => $fields['username'],
            'password'   => Hash::make($fields['password']),
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
        $fields = $this->validate($request, [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|min:6|max:50|unique:customers,username',
            'birth_date' => 'required|date_format:d.m.Y|before:today|after:1900-01-01',
            'password' => 'required|min:6|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        ]);

        try
        {
            $customer = Customer::findOrFail($id);
            $customer->update([
                'first_name' => $fields('first_name'),
                'last_name'  => $fields('last_name'),
                'birth_date' => $fields('birth_date'),
                'username'   => $fields('username'),
                'password'   => Hash::make($fields('password')),
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
