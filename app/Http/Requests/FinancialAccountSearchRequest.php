<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FinancialAccountSearchRequest extends FormRequest
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
     * Supported params: q, type, is_active, level, parent_id, min_balance, max_balance, per_page, sort
     */
    public function rules(): array
    {
        return [
            'q' => 'nullable|string|max:255',
            'type' => 'nullable|in:AS,LI,IN,EX,SP',
            'is_active' => 'nullable|boolean',
            'level' => 'nullable|integer|min:0|max:10',
            'parent_id' => 'nullable|integer|exists:'.config('db_tables.financial_account','financial_accounts').',id',
            'min_balance' => 'nullable|numeric',
            'max_balance' => 'nullable|numeric',
            'per_page' => 'nullable|integer|min:1|max:200',
            'sort' => 'nullable|string|max:100',
        ];
    }
}
