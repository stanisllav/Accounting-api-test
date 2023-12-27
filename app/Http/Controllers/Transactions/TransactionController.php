<?php

namespace App\Http\Controllers\Transactions;

use App\Filters\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Http\Requests\Transactions\ShowTransactionRequest;
use App\Http\Requests\Transactions\StoreTransactionRequest;
use App\Http\Resources\TransactionCollection;
use App\Http\Resources\TransactionResource;
use App\Models\Transaction;
use App\Services\TransactionsService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{

    public function __construct()
    {
        $this->authorizeResource(Transaction::class);
    }

    #[OA\Get(path: '/api/transactions', summary: 'Returns a paginated set of Transactions', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query'),
            new OA\Parameter(name: 'amount', in: 'query'),
            new OA\Parameter(name: 'date', in: 'query'),
            new OA\Parameter(name: 'type', in: 'query', schema: new OA\Schema(enum: ['income', 'outcome'])),
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'A paginated set of Transactions',
        content: new OA\JsonContent(
            ref: '#/components/schemas/TransactionCollection'
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent())]
    public function index(ShowTransactionRequest $request): JsonResponse
    {

        $validated = $request->validated();

        $filter = new TransactionFilter($validated + ['author' => $request->user()->id]);
        $transactions = Transaction::filter($filter);

        $result = $transactions->paginate(10);

        return response()->json(TransactionCollection::make($result));
    }

    #[OA\Post(path: '/api/transactions', summary: 'Add new TransactionResource', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        content: new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'amount', type: 'number', format: 'double', example: '218.50'),
                ]
            )
        )
    ),
        tags: ['Transactions'], )]
    #[OA\Response(response: 201, description: 'Created TransactionResource', content: new OA\JsonContent(ref: '#/components/schemas/TransactionResource'))]
    public function store(StoreTransactionRequest $request, TransactionsService $transactionsService): JsonResponse
    {
        $transaction = $transactionsService->createTransaction($request->toDTO());

        return response()->json(TransactionResource::make($transaction), 201);
    }

    /**
     * Display the specified resource.
     *
     * @throws AuthorizationException
     */
    #[OA\Get(path: '/api/transactions/{id}', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path'),
        ])]
    #[OA\Response(response: 200, description: 'TransactionResource', content: new OA\JsonContent(ref: '#/components/schemas/TransactionResource'))]
    #[OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent())]
    #[OA\Response(response: 403, description: 'Forbidden', content: new OA\JsonContent())]
    public function show(Transaction $transaction): JsonResponse
    {
        return response()->json(TransactionResource::make($transaction));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @throws AuthorizationException
     */
    #[OA\Delete(path: '/api/transactions/{id}', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path'),
        ])]
    #[OA\Response(response: 204, description: 'Deleted', content: new OA\JsonContent())]
    #[OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent())]
    #[OA\Response(response: 403, description: 'Forbidden', content: new OA\JsonContent())]
    public function destroy(Transaction $transaction): JsonResponse
    {
        $transaction->delete();
        return response()->json(null, 204);
    }

    #[OA\Get(path: '/api/transactions/income-sum', summary: 'Total income', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ]
    )]
    #[OA\Response(response: 200, description: 'Total income', content: new OA\JsonContent())]
    public function incomeSum(ShowTransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $filter = new TransactionFilter($validated + ['type' => 'income', 'author' => $request->user()->id]);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        return response()->json(['sum' => $sum]);
    }

    #[OA\Get(path: '/api/transactions/outcome-sum', summary: 'Total outcome', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ]
    )]
    #[OA\Response(response: 200, description: 'Total outcome', content: new OA\JsonContent())]
    public function outcomeSum(ShowTransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $filter = new TransactionFilter($validated + ['type' => 'outcome', 'author' => $request->user()->id]);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        return response()->json(['sum' => $sum]);
    }

    #[OA\Get(path: '/api/transactions/total-sum', summary: 'Total sum', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
        ]
    )]
    #[OA\Response(response: 200, description: 'Total sum', content: new OA\JsonContent())]
    public function totalSum(ShowTransactionRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $filter = new TransactionFilter($validated + ['author' => $request->user()->id]);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        return response()->json(['sum' => $sum]);
    }

    #[OA\Get(path: '/api/transactions/balance', summary: 'Get converted sum', security: [['sanctum' => []]], tags: ['Transactions']
    )]
    #[OA\Response(response: 200, description: 'Sum in EUR, and in USD', content: new OA\JsonContent())]
    public function balance(Request $request): JsonResponse
    {

        $filter = new TransactionFilter(['author' => $request->user()->id]);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        $converted = convertCurrency($sum, 'EUR', 'USD');

        return response()->json(['eur' => $sum, 'usd' => number_format($converted, 2, '.', '')]);
    }
}
