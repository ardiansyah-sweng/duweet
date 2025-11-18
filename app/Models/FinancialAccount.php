<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FinancialAccount extends Model
{
    use HasFactory;

    protected $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account');
    }

    public $timestamps = false;

    /**
     * Get the fillable attributes for the model.
     *
     * @return array<string>
     */
    public function getFillable()
    {
        return FinancialAccountColumns::getFillable();
    }

    /**
     * Relasi ke Transactions
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class, FinancialAccountColumns::ID);
    }
}
