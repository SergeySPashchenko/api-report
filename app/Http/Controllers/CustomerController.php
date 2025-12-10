<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\CustomerRequest;
use App\Http\Resources\CustomerResource;
use App\Models\Customer;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class CustomerController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $customers = Customer::query()
            ->withCount(['orders', 'addresses'])
            ->latest()
            ->paginate(15);

        return CustomerResource::collection($customers);
    }

    public function store(CustomerRequest $request): CustomerResource
    {
        $customer = Customer::query()->create($request->validated());

        return new CustomerResource($customer);
    }

    public function show(Customer $customer): CustomerResource
    {
        $customer->loadCount(['orders', 'addresses']);

        return new CustomerResource($customer);
    }

    public function update(CustomerRequest $request, Customer $customer): CustomerResource
    {
        $customer->update($request->validated());

        return new CustomerResource($customer);
    }

    public function destroy(Customer $customer): JsonResponse
    {
        $customer->delete();

        return response()->json(['message' => 'Customer deleted successfully']);
    }
}
