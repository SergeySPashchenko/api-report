<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExpenseTypeRequest;
use App\Http\Resources\ExpenseTypeResource;
use App\Models\ExpenseType;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ExpenseTypeController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', ExpenseType::class);

        return ExpenseTypeResource::collection(ExpenseType::all());
    }

    public function store(ExpenseTypeRequest $request): ExpenseTypeResource
    {
        $this->authorize('create', ExpenseType::class);

        return new ExpenseTypeResource(ExpenseType::query()->create($request->validated()));
    }

    public function show(ExpenseType $expenseType): ExpenseTypeResource
    {
        $this->authorize('view', $expenseType);

        return new ExpenseTypeResource($expenseType);
    }

    public function update(ExpenseTypeRequest $request, ExpenseType $expenseType): ExpenseTypeResource
    {
        $this->authorize('update', $expenseType);

        $expenseType->update($request->validated());

        return new ExpenseTypeResource($expenseType);
    }

    public function getExpenseTypes(): JsonResponse
    {
        try {
            $expenseTypes = $this->secureSellerService->getExpenseTypes();

            return response()->json([
                'success' => true,
                'brands' => $expenseTypes,
                'count' => count($expenseTypes),
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch brands',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function syncExpenseTypes(): JsonResponse
    {
        try {
            $expenseTypes = $this->secureSellerService->getExpenseTypes();

            $synced = 0;
            $updated = 0;

            foreach ($expenseTypes as $data) {
                $expenseType = ExpenseType::withTrashed()
                    ->where('ExpenseID', $data['ExpenseID'])
                    ->first();

                if ($expenseType) {
                    $wasRestored = false;

                    if ($expenseType->trashed()) {
                        $expenseType->restore();
                        $wasRestored = true;
                    }

                    $expenseType->fill([
                        'Name' => $data['Name'],
                        'Visible' => $data['Visible'],
                    ]);

                    if ($expenseType->isDirty()) {
                        $expenseType->save();
                        $updated++;
                    } elseif ($wasRestored) {
                        $updated++;
                    }
                } else {
                    ExpenseType::query()->create([
                        'ExpenseID' => $data['ExpenseID'],
                        'Name' => $data['Name'],
                        'Visible' => $data['Visible'],
                    ]);
                    $synced++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Expense types synced successfully',
                'created' => $synced,
                'updated' => $updated,
                'total' => count($expenseTypes),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(ExpenseType $expenseType): JsonResponse
    {
        $this->authorize('delete', $expenseType);

        $expenseType->delete();

        return response()->json();
    }
}
