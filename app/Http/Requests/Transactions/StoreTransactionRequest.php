<?php

namespace App\Http\Requests\Transactions;

use App\DTO\TransactionDTO;
use Illuminate\Contracts\Validation\ValidationRule;

class StoreTransactionRequest extends TransactionRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array|string>
     */
    public function rules(): array
    {
        return array_merge(
            parent::rules(),
            [
                'title' => 'max:255',
                'amount' => 'numeric|required|not_in:0',
            ]);
    }

    /**
     * return a DTO
     */
    public function toDTO(): TransactionDTO
    {
        return new TransactionDTO(
            title: $this->input('title'),
            amount: $this->input('amount'),
            author: $this->user()
        );
    }
}
