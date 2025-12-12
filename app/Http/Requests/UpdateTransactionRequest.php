<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTransactionRequest extends FormRequest
{
    public function authorize()
    {
        return true; // adjust auth logic as needed
    }

    public function rules(): array
    {
        return [
            'account_id' => 'sometimes|integer|exists:accounts,id',
            'date' => 'sometimes|date',
            'description' => 'sometimes|nullable|string|max:255',
            'amount' => 'sometimes|numeric|min:0.01',
            'type' => 'sometimes|in:debit,credit',
            'meta' => 'sometimes|array',
        ];
    }
}
