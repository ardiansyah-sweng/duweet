<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;

class FinancialAccount extends Model
{
    /**
     * Set table name from config so it follows project convention
     */
    protected $table;

    protected $fillable = [];

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
        $this->fillable = FinancialAccountColumns::getFillable();
    }

    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function children()
    {
        return $this->hasMany(self::class, FinancialAccountColumns::PARENT_ID)->orderBy(FinancialAccountColumns::SORT_ORDER);
    }

    /**
     * Scope: simple search on name and description
     */
    public function scopeSearch(Builder $query, ?string $term): Builder
    {
        if (!$term) {
            return $query;
        }

        $term = trim($term);

        // Use raw SQL with bindings for the search to execute as a single DML-style clause.
        // This keeps the query safe from injection while allowing raw SQL expression.
        $like = "%{$term}%";

        return $query->whereRaw(
            '(' . FinancialAccountColumns::NAME . ' LIKE ? OR ' . FinancialAccountColumns::DESCRIPTION . ' LIKE ?)',
            [$like, $like]
        );
    }

    /**
     * Scope: apply common filters
     */
    public function scopeApplyFilters(Builder $query, array $filters): Builder
    {
        return $query
            ->when(isset($filters['type']) && $filters['type'] !== null, function ($q) use ($filters) {
                $q->where(FinancialAccountColumns::TYPE, $filters['type']);
            })
            ->when(isset($filters['is_active']) && $filters['is_active'] !== null, function ($q) use ($filters) {
                $q->where(FinancialAccountColumns::IS_ACTIVE, (bool) $filters['is_active']);
            })
            ->when(isset($filters['parent_id']) && $filters['parent_id'] !== null, function ($q) use ($filters) {
                $q->where(FinancialAccountColumns::PARENT_ID, $filters['parent_id']);
            })
            ->when(isset($filters['level']) && $filters['level'] !== null, function ($q) use ($filters) {
                $q->where(FinancialAccountColumns::LEVEL, $filters['level']);
            });
    }
}
