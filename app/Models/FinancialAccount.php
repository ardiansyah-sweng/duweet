<?php

namespace App\Models;

use App\Constants\FinancialAccountColumns;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    
    protected $table;

    protected $fillable = [];


   
    public static function searchWithFilters(?string $searchTerm = null, array $filters = [], int $limit = 15, int $offset = 0): array
    {
        $table = config('db_tables.financial_account', 'financial_accounts');
        
        $sql = "SELECT * FROM {$table} WHERE 1=1 ";
        $params = [];

        // Filter search term
        if (!empty($searchTerm)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?) ";
            $params[] = "%{$searchTerm}%";
            $params[] = "%{$searchTerm}%";
        }

        // Filter type
        if (!empty($filters['type'])) {
            $sql .= " AND type = ? ";
            $params[] = $filters['type'];
        }

        // Filter is_active
        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $sql .= " AND is_active = ? ";
            $params[] = (int) $filters['is_active'];
        }

        // Filter parent_id
        if (!empty($filters['parent_id'])) {
            $sql .= " AND parent_id = ? ";
            $params[] = $filters['parent_id'];
        }

        // Filter level
        if (!empty($filters['level'])) {
            $sql .= " AND level = ? ";
            $params[] = (int) $filters['level'];
        }

        // Sort
        $sortBy = $filters['sort_by'] ?? 'sort_order';
        $order = strtoupper($filters['order'] ?? 'asc') === 'DESC' ? 'DESC' : 'ASC';
        $sql .= " ORDER BY {$sortBy} {$order} ";

        // Limit dan offset
        $sql .= " LIMIT ? OFFSET ? ";
        $params[] = $limit;
        $params[] = $offset;

        // Execute
        $results = DB::select($sql, $params);
        return array_map(fn($row) => (array) $row, $results);
    }

   
    public static function countWithFilters(?string $searchTerm = null, array $filters = []): int
    {
        $table = config('db_tables.financial_account', 'financial_accounts');
        
        $sql = "SELECT COUNT(*) as total FROM {$table} WHERE 1=1 ";
        $params = [];

        // Filter search term
        if (!empty($searchTerm)) {
            $sql .= " AND (name LIKE ? OR description LIKE ?) ";
            $params[] = "%{$searchTerm}%";
            $params[] = "%{$searchTerm}%";
        }

        // Filter type
        if (!empty($filters['type'])) {
            $sql .= " AND type = ? ";
            $params[] = $filters['type'];
        }

        // Filter is_active
        if (isset($filters['is_active']) && $filters['is_active'] !== null) {
            $sql .= " AND is_active = ? ";
            $params[] = (int) $filters['is_active'];
        }

        // Filter parent_id
        if (!empty($filters['parent_id'])) {
            $sql .= " AND parent_id = ? ";
            $params[] = $filters['parent_id'];
        }

        // Filter level
        if (!empty($filters['level'])) {
            $sql .= " AND level = ? ";
            $params[] = (int) $filters['level'];
        }

        $result = DB::select($sql, $params);
        return $result[0]->total ?? 0;
    }
}
