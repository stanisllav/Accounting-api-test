<?php

namespace App\Http\Controllers\Transactions;

use App\Filters\TransactionFilter;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Attributes as OA;

class TransactionController extends Controller
{

    #[OA\Get(path: '/api/transactions', summary: 'Returns a paginated set of Transactions', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'page', in: 'query'),
            new OA\Parameter(name: 'amount', in: 'query'),
            new OA\Parameter(name: 'date', in: 'query'),
            new OA\Parameter(name: 'type', in: 'query', schema: new OA\Schema(enum: ['income', 'outcome']))
        ]
    )]
    #[OA\Response(
        response: 200,
        description: 'A paginated set of Transactions',
        content: new OA\JsonContent(
            schema: 'array',
            properties: [
                new OA\Property(property: 'data', type: 'array', items: new OA\Items(ref: '#/components/schemas/Transaction')),
                new OA\Property(property: 'current_page', type: 'integer'),
                new OA\Property(property: 'next_page_url', type: 'string'),
                new OA\Property(property: 'path', type: 'string'),
                new OA\Property(property: 'per_page', type: 'integer'),
                new OA\Property(property: 'prev_page_url', type: 'string'),
                new OA\Property(property: 'to', type: 'integer'),
                new OA\Property(property: 'total', type: 'integer'),
                new OA\Property(property: 'first_page_url', type: 'string'),
                new OA\Property(property: 'from', type: 'integer'),
                new OA\Property(property: 'last_page', type: 'string'),
                new OA\Property(property: 'last_page_url', type: 'string'),
                new OA\Property(property: 'links', type: 'array', items: new OA\Items(type: 'object'))
            ]
        )
    )]
    #[OA\Response(response: 401, description: 'Unauthorized', content: new OA\JsonContent())]
    public function index(Request $request): JsonResponse
    {


        $validated = $request->validate([
            'amount' => 'decimal:2',
            'date' => 'date',
            'type' => 'in:income,outcome'
        ]);
        $validated['author'] = auth()->user()->id;

        $filter = new TransactionFilter($validated);
        $transactions = Transaction::filter($filter);


        $result = $transactions->paginate(10);

        return response()->json($result);
    }


    #[OA\Post(path: '/api/transactions', summary: 'Add new Transaction', security: [['sanctum' => []]], requestBody: new OA\RequestBody(
        content: new OA\MediaType(
            mediaType: 'application/x-www-form-urlencoded',
            schema: new OA\Schema(
                required: ['amount'],
                properties: [
                    new OA\Property(property: 'title', type: 'string'),
                    new OA\Property(property: 'amount', type: 'number', format: 'double', example: '218.50')
                ]
            )
        )
    ),
        tags: ['Transactions'],)]
    #[OA\Response(response: 201, description: 'Created Transaction', content: new OA\JsonContent(ref: '#/components/schemas/Transaction'))]
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'max:255',
            'amount' => 'numeric|required|not_in:0',
        ]);


        $transaction = new Transaction;
        if ($request->has('title')) {
            $transaction->title = $validated['title'];
        }
        $transaction->amount = $validated['amount'];
        $transaction->author()->associate(auth()->user());
        $transaction->save();

        return response()->json($transaction, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     * @throws AuthorizationException
     */
    #[OA\Get(path: '/api/transactions/{id}', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path')
        ])]
    #[OA\Response(response: 200, description: 'Transaction', content: new OA\JsonContent(ref: '#/components/schemas/Transaction'))]
    #[OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent())]
    #[OA\Response(response: 403, description: 'Forbidden', content: new OA\JsonContent())]
    public function show(Transaction $transaction): JsonResponse
    {
        $this->authorize('show', $transaction);
        return response()->json($transaction);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param Transaction $transaction
     * @return JsonResponse
     * @throws AuthorizationException
     */
    #[OA\Delete(path: '/api/transactions/{id}', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'id', in: 'path')
        ])]
    #[OA\Response(response: 204, description: 'Deleted', content: new OA\JsonContent())]
    #[OA\Response(response: 404, description: 'Not found', content: new OA\JsonContent())]
    #[OA\Response(response: 403, description: 'Forbidden', content: new OA\JsonContent())]
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();
        return response()->json(null, 204);
    }

    #[OA\Get(path: '/api/transactions/income-sum', summary: 'Total income', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date'))
        ]
    )]
    #[OA\Response(response: 200, description: 'Total income', content: new OA\JsonContent())]
    public function incomeSum(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);
        $validated['type'] = 'income';
        $validated['author'] = auth()->user()->id;

        $filter = new TransactionFilter($validated);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');
        return response()->json(['sum' => $sum]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/transactions/outcome-sum', summary: 'Total outcome', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date'))
        ]
    )]
    #[OA\Response(response: 200, description: 'Total outcome', content: new OA\JsonContent())]
    public function outcomeSum(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);
        $validated['type'] = 'outcome';
        $validated['author'] = auth()->user()->id;

        $filter = new TransactionFilter($validated);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');
        return response()->json(['sum' => $sum]);
    }

    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/transactions/total-sum', summary: 'Total sum', security: [['sanctum' => []]], tags: ['Transactions'],
        parameters: [
            new OA\Parameter(name: 'date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'start_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date')),
            new OA\Parameter(name: 'end_date', in: 'query', schema: new OA\Schema(type: 'string', format: 'date'))
        ]
    )]
    #[OA\Response(response: 200, description: 'Total sum', content: new OA\JsonContent())]
    public function totalSum(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'date' => 'date',
            'start_date' => 'date',
            'end_date' => 'date'
        ]);
        $validated['author'] = auth()->user()->id;

        $filter = new TransactionFilter($validated);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        return response()->json(['sum' => $sum]);
    }


    /**
     * @param Request $request
     * @return JsonResponse
     */
    #[OA\Get(path: '/api/transactions/balance', summary: 'Get converted sum', security: [['sanctum' => []]], tags: ['Transactions']
    )]
    #[OA\Response(response: 200, description: 'Sum in EUR, and in USD', content: new OA\JsonContent())]
    public function balance(Request $request): JsonResponse
    {

        $filters['author'] = auth()->user()->id;

        $filter = new TransactionFilter(['author' => auth()->user()->id]);
        $transactions = Transaction::filter($filter);

        $sum = $transactions->sum('amount');

        $converted = convertCurrency($sum, 'EUR', 'USD');
        return response()->json(['eur' => $sum, 'usd' => number_format($converted, 2, ".", "")]);
    }
}
