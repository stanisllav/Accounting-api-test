<?php

namespace App\Http\Controllers;

use App\Filters\TransactionFilter;
use App\Models\Transaction;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function index(Request $request): JsonResponse
    {


        $validated = $request->validate([
            'amount' => 'decimal:2',
            'date' => 'date',
            'type' => 'in:income,outcome'
        ]);
        $filter = new TransactionFilter($validated);
        $transactions = Transaction::filter($filter);


        // Filter transactions to include only those belonging to the authenticated user
        $transactions->where('author_id', auth()->user()->id);

        $result = $transactions->paginate(10);

        return response()->json($result);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param Request $request
     * @return JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'title' => 'max:255',
            'amount' => 'decimal:2|required|not_in:0',
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
    public function destroy(Transaction $transaction): JsonResponse
    {
        $this->authorize('delete', $transaction);
        $transaction->delete();
        return response()->json(null, 204);
    }
}
