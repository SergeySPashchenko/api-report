<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\ExpensesRequest;
use App\Http\Resources\ExpensesResource;
use App\Models\Expenses;
use App\Models\ExpenseType;
use App\Models\Product;
use App\Services\SecureSellerService;
use Exception;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

final class ExpensesController extends Controller
{
    use AuthorizesRequests;

    public function __construct(
        private SecureSellerService $secureSellerService
    ) {}

    public function index(): AnonymousResourceCollection
    {
        $this->authorize('viewAny', Expenses::class);

        return ExpensesResource::collection(Expenses::all());
    }

    public function store(ExpensesRequest $request): ExpensesResource
    {
        $this->authorize('create', Expenses::class);

        return new ExpensesResource(Expenses::query()->create($request->validated()));
    }

    public function show(Expenses $expenses): ExpensesResource
    {
        $this->authorize('view', $expenses);

        return new ExpensesResource($expenses);
    }

    public function update(ExpensesRequest $request, Expenses $expenses): ExpensesResource
    {
        $this->authorize('update', $expenses);

        $expenses->update($request->validated());

        return new ExpensesResource($expenses);
    }

    public function getExpenses(): JsonResponse
    {
        try {
            /** @var Request $request */
            $request = request();
            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');

            if (empty($dateStart) && empty($dateEnd)) {
                $dateStart = date('Y-m-d', strtotime('-1 day'));
                $dateEnd = $dateStart;
            }

            if (! empty($dateStart) && empty($dateEnd)) {
                $dateEnd = $dateStart;
            }

            if (empty($dateStart) && ! empty($dateEnd)) {
                $dateStart = $dateEnd;
            }

            /** @var string $dateStart */
            /** @var string $dateEnd */
            $expenses = $this->secureSellerService->getExpenses($dateStart, $dateEnd);

            return response()->json([
                'success' => true,
                'expenses' => $expenses,
                'count' => count($expenses),
            ]);

        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to fetch expenses',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function syncExpenses(): JsonResponse
    {
        try {
            /** @var Request $request */
            $request = request();
            $dateStart = $request->input('date_start');
            $dateEnd = $request->input('date_end');

            if (empty($dateStart) && empty($dateEnd)) {
                $dateStart = date('Y-m-d', strtotime('-1 day'));
                $dateEnd = $dateStart;
            }

            if (! empty($dateStart) && empty($dateEnd)) {
                $dateEnd = $dateStart;
            }

            if (empty($dateStart) && ! empty($dateEnd)) {
                $dateStart = $dateEnd;
            }

            /** @var string $dateStart */
            /** @var string $dateEnd */
            $expenses = $this->secureSellerService->getExpenses($dateStart, $dateEnd);

            $synced = 0;
            $updated = 0;

            foreach ($expenses as $data) {
                // Find relationships
                $product = Product::query()
                    ->where('ProductID', $data['ProductID'] ?? 0)
                    ->withoutGlobalScope('user_access')
                    ->first();

                $expenseType = ExpenseType::query()
                    ->where('ExpenseID', $data['ExpenseID'] ?? 0)
                    ->withTrashed()
                    ->first();

                // Use 'id' from data as external_id for unique matching
                $expense = Expenses::withTrashed()
                    ->where('external_id', $data['id'])
                    ->first();

                if ($expense) {
                    $wasRestored = false;

                    if ($expense->trashed()) {
                        $expense->restore();
                        $wasRestored = true;
                    }

                    $expense->fill([
                        // Map internal fields
                        'external_id' => $data['id'],
                        'ExpenseID' => $data['ExpenseID'],
                        'ExpenseDate' => $data['ExpenseDate'],
                        'Expense' => $data['Expense'],
                        'ProductID' => $data['ProductID'],
                        'product_id' => $product?->id,
                        'brand_id' => $product?->brand_id,
                        'expense_type_id' => $expenseType?->id,
                    ]);

                    if ($expense->isDirty()) {
                        $expense->save();
                        $updated++;
                    } elseif ($wasRestored) {
                        $updated++;
                    }
                } else {
                    Expenses::query()->create([
                        'external_id' => $data['id'],
                        'ExpenseID' => $data['ExpenseID'],
                        'ExpenseDate' => $data['ExpenseDate'],
                        'Expense' => $data['Expense'],
                        'ProductID' => $data['ProductID'],
                        'product_id' => $product?->id,
                        'brand_id' => $product?->brand_id,
                        'expense_type_id' => $expenseType?->id,
                    ]);
                    $synced++;
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'Expenses synced successfully',
                'created' => $synced,
                'updated' => $updated,
                'total' => count($expenses),
            ]);
        } catch (Exception $exception) {
            return response()->json([
                'success' => false,
                'message' => 'Sync failed',
                'error' => $exception->getMessage(),
            ], 500);
        }
    }

    public function destroy(Expenses $expenses): JsonResponse
    {
        $this->authorize('delete', $expenses);

        $expenses->delete();

        return response()->json();
    }
}
