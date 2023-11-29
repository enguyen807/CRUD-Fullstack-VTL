<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class CustomerController extends Controller
{
    /**
     * Returns all customers
     * @return JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([ 'data' => Customer::all()], JsonResponse::HTTP_OK);
    }

    /**
     * Create customer
     * @param  Request  $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validate = Validator::make(json_decode($request->getContent(),true), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|min:6|max:50|unique:customers,username',
            'birth_date' => 'required|before:today|after:1900-01-01',
            'password' => 'required|min:6|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        ]);

        if ($validate->fails()) {
            if ($request->json('username') && $validate->errors()->has('username')) {
                return response()->json(['message'=> 'User already exists!'], JsonResponse::HTTP_CONFLICT);
            }

            return response()->json(['error' => $validate->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        $customer = Customer::create([
            'first_name' => $request->json('first_name'),
            'last_name'  => $request->json('last_name'),
            'birth_date' => $request->json('birth_date'),
            'username'   => $request->json('username'),
            'password'   => Hash::make($request->json('password')),
        ]);
        return response()->json([ 'data' => $customer ], JsonResponse::HTTP_CREATED);
    }

    /**
     * Update customer
     * @param  int $id
     * @param  Request  $request
     * @return JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $validate = Validator::make(json_decode($request->getContent(),true), [
            'first_name' => 'required|string|max:50',
            'last_name' => 'required|string|max:50',
            'username' => 'required|string|min:6|max:50|unique:customers,username,' . $id,
            'birth_date' => 'required|before:today|after:1900-01-01',
            'password' => 'sometimes|required|min:6|max:50|regex:/^(?=.*?[A-Z])(?=.*?[a-z])(?=.*?[0-9])(?=.*?[#?!@$%^&*-]).{8,}$/',
        ]);

        if ($validate->fails()) {
            if (
                $request->json('username') && 
                $validate->errors()->has('username') 
                ) {
                return response()->json(['message'=> 'User already exists!'], JsonResponse::HTTP_CONFLICT);
            }
            return response()->json(['error' => $validate->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
        }

        try
        {
            $customer = Customer::findOrFail($id);
            $customer->update([
                'first_name' => $request->json('first_name'),
                'last_name'  => $request->json('last_name'),
                'birth_date' => $request->json('birth_date'),
                'username'   => $request->json('username'),
                'password'   => Hash::make($request->json('password')),
            ]);
    
            return response()->json([ 'data' => $customer ], JsonResponse::HTTP_OK);
        }
        catch (ModelNotFoundException $e)
        {
            return response()->json([ 'error' => $e->getMessage() ], JsonResponse::HTTP_NOT_FOUND);
        }

    }
    
    /**
     * Remove customer
     * @param  int  $id
     * @return mixed
     */
    public function destroy(Request $request)
    {
        try {
            $validate = Validator::make(json_decode($request->getContent(), true), [
                'ids' => 'required|array',
                'ids.*' => 'integer'
            ]);

            if ($validate->fails()) {
                return response()->json(['error' => $validate->errors()], JsonResponse::HTTP_UNPROCESSABLE_ENTITY);
            }

            DB::table('customers')->whereIn('id', $request->json('ids'))->delete();

            return response()->json(['status' => JsonResponse::HTTP_OK, 'message' => 'Deleted Successfully'], JsonResponse::HTTP_OK);
        } catch (ModelNotFoundException $e) {
            return response()->json([ 'error' => $e->getMessage() ], JsonResponse::HTTP_NOT_FOUND);
        }

    }       
}
