<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory;

    /**
     * Set table name from config when model is constructed so it follows project's config.
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->setTable(config('db_tables.financial_account', 'financial_accounts'));
    }

    /**
     * Allow mass assignment for seeding convenience.
     */
    protected $guarded = [];

    protected $casts = [
        'is_group' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Parent account relation
     */
    public function parent()
    {
        return $this->belongsTo(self::class, 'parent_id');
    }

    /**
     * Children accounts relation
     */
    public function children()
    {
        return $this->hasMany(self::class, 'parent_id');
    }

    /**
     * Scope: search by keyword across name and description (case-insensitive, LIKE)
     * Example: FinancialAccount::search('cash')->get();
     */
    public function scopeSearch($query, ?string $term)
    {
        if (empty($term)) {
            return $query;
        }

        $term = trim($term);
        return $query->where(function ($q) use ($term) {
            $q->where('name', 'like', "%{$term}%")
              ->orWhere('description', 'like', "%{$term}%");
        });
    }

    /**
     * Scope: apply common filters passed as array
     * Supported keys: type, is_active, level, parent_id, min_balance, max_balance
     * Example: FinancialAccount::filter(['type' => 'AS','is_active'=>1])->get();
     */
    public function scopeFilter($query, array $filters = [])
    {
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (array_key_exists('is_active', $filters)) {
            $query->where('is_active', (bool) $filters['is_active']);
        }

        if (isset($filters['level'])) {
            $query->where('level', (int) $filters['level']);
        }

        if (isset($filters['parent_id'])) {
            $query->where('parent_id', $filters['parent_id']);
        }

        if (isset($filters['min_balance'])) {
            $query->where('balance', '>=', $filters['min_balance']);
        }

        if (isset($filters['max_balance'])) {
            $query->where('balance', '<=', $filters['max_balance']);
        }

        return $query;
    }
}
