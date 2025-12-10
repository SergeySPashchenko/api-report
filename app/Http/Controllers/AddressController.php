<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Http\Resources\AddressResource;
use App\Models\Address;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class AddressController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $addresses = Address::query()
            ->with('customer')
            ->latest()
            ->paginate(15);

        return AddressResource::collection($addresses);
    }

    public function store(AddressRequest $request): AddressResource
    {
        $address = Address::query()->create($request->validated());

        return new AddressResource($address);
    }

    public function show(Address $address): AddressResource
    {
        $address->load('customer');

        return new AddressResource($address);
    }

    public function update(AddressRequest $request, Address $address): AddressResource
    {
        $address->update($request->validated());

        return new AddressResource($address);
    }

    public function destroy(Address $address): JsonResponse
    {
        $address->delete();

        return response()->json(['message' => 'Address deleted successfully']);
    }
}
