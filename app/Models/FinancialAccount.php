<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Constants\FinancialAccountColumns;
use Illuminate\Support\Facades\DB;


class FinancialAccount extends Model
{
    protected $table;
    use HasFactory;

    protected $fillable = [
        FinancialAccountColumns::NAME,
        FinancialAccountColumns::PARENT_ID,
        FinancialAccountColumns::TYPE,
        FinancialAccountColumns::BALANCE,
        FinancialAccountColumns::INITIAL_BALANCE,
        FinancialAccountColumns::DESCRIPTION,
        FinancialAccountColumns::IS_GROUP,
        FinancialAccountColumns::IS_ACTIVE,
        FinancialAccountColumns::SORT_ORDER,
        FinancialAccountColumns::LEVEL,
    ];

    protected $casts = [
        FinancialAccountColumns::BALANCE => 'integer',
        FinancialAccountColumns::INITIAL_BALANCE => 'integer',
        FinancialAccountColumns::IS_GROUP => 'boolean',
        FinancialAccountColumns::IS_ACTIVE => 'boolean',
    ];

    public function parent()
    {
        return $this->belongsTo(self::class, FinancialAccountColumns::PARENT_ID);
    }

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account', 'financial_accounts');
    }

    public function getIndexData($q,$type,$perPage,$page){
         $wheres = [];
            $params = [];
            
            if (!empty($q)) {
                $wheres[] = "(name LIKE ? OR description LIKE ?)";
                $params[] = "%$q%";
                $params[] = "%$q%";
            }
            
            if (!empty($type)) {
                $wheres[] = "type = ?";
                $params[] = $type;
            }
            
            $whereClause = !empty($wheres) ? "WHERE " . implode(" AND ", $wheres) : "";
            
            // Count total records
            $countSql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
            $countResult = DB::select($countSql, $params);
            $total = $countResult[0]->total;
            
            // Get paginated records
            $offset = ($page - 1) * $perPage;
            $dataSql = "SELECT * FROM {$this->table} $whereClause ORDER BY sort_order ASC LIMIT $perPage OFFSET $offset";
            $records = DB::select($dataSql, $params);

            return [
            'records' => $records,
            'total' => $total
        ];

    }

    public function getById($id){
        $sql = "SELECT * FROM {$this->table} WHERE id = ? LIMIT 1";
        $result = DB::select($sql, [$id]);
        return !empty($result) ? $result[0] : null;
    }

    public function getByType($type, $isActive, $perPage, $page){
            $wheres = ["type = ?"];
            $params = [$type];
            
            if ($isActive !== null) {
                $wheres[] = "is_active = ?";
                $params[] = (int) $isActive;
            }
            
            $whereClause = "WHERE " . implode(" AND ", $wheres);
            
            // Count total records dengan type ini
            $countSql = "SELECT COUNT(*) as total FROM {$this->table} $whereClause";
            $countResult = DB::select($countSql, $params);
            $total = (int) $countResult[0]->total;
            
            // Get paginated records
            $offset = ($page - 1) * $perPage;
            $dataSql = "SELECT * FROM {$this->table} $whereClause ORDER BY sort_order ASC LIMIT $perPage OFFSET $offset";
            $records = DB::select($dataSql, $params);
            return [
            'records' => $records,
            'total' => $total
        ];
    }

    public function getMultipleId($ids){
            $placeholders = implode(',', array_fill(0, count($ids), '?'));
            $sql = "SELECT * FROM {$this->table} WHERE id IN ($placeholders) ORDER BY sort_order ASC";
            return DB::select($sql, $ids);
    }
}