<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;

class FinancialAccount extends Model
{
    protected string $table;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account');
    }

    protected $fillable = FinancialAccountColumns::getFillable();

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
