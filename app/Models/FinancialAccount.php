<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class FinancialAccount extends Model
{
    protected $table = 'financial_accounts';

    protected $fillable = [
        'parent_id',
        'name', 
        'type', 
        'balance', 
        'initial_balance',
        'is_group', 
        'description', 
        'is_active',
        'sort_order',
        'level'
    ];

    protected $casts = [
        'balance' => 'integer',
        'initial_balance' => 'integer',
        'is_group' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
        'level' => 'integer',
    ];

    public $timestamps = true;

    /**
     * Get financial account by ID
     */
    public static function getById(int $id)
    {
        return self::find($id);
    }

    /**
     * Update financial account
     * 
     * @param int $id
     * @param array $data - Allowed fields: name, type, description, is_active, sort_order, parent_id, is_group, level
     * @return bool
     */
    public static function updateAccount(int $id, array $data): bool
    {
        $account = self::find($id);
        
        if (!$account) {
            return false;
        }

        // Only allow specific fields to be updated
        $allowedFields = ['name', 'type', 'description', 'is_active', 'sort_order', 'parent_id', 'is_group', 'level'];
        $updateData = [];

        foreach ($allowedFields as $field) {
            if (array_key_exists($field, $data)) {
                $updateData[$field] = $data[$field];
            }
        }

        if (empty($updateData)) {
            return false;
        }

        return $account->update($updateData);
    }

    /**
     * Update balance and initial_balance
     * 
     * @param int $id
     * @param int $balance
     * @param int|null $initialBalance
     * @return bool
     */
    public static function updateBalance(int $id, int $balance, ?int $initialBalance = null): bool
    {
        $updateData = ['balance' => $balance];
        
        if ($initialBalance !== null) {
            $updateData['initial_balance'] = $initialBalance;
        }

        return self::where('id', $id)->update($updateData) > 0;
    }

    /**
     * Toggle is_active status
     * 
     * @param int $id
     * @param bool $isActive
     * @return bool
     */
    public static function toggleActive(int $id, bool $isActive): bool
    {
        return self::where('id', $id)->update(['is_active' => $isActive]) > 0;
    }

    /**
     * Get all financial accounts by type
     * 
     * @param string $type - IN, EX, SP, LI, AS
     * @param bool|null $isActive - Filter by active status
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getByType(string $type, ?bool $isActive = null)
    {
        $query = self::where('type', $type);
        
        if ($isActive !== null) {
            $query->where('is_active', $isActive);
        }

        return $query->orderBy('sort_order')->orderBy('name')->get();
    }

    /**
     * Get financial account tree (parent-child hierarchy)
     * 
     * @param string|null $type - Filter by type
     * @return \Illuminate\Support\Collection
     */
    public static function getTree(?string $type = null)
    {
        $query = DB::table('financial_accounts as fa')
            ->leftJoin('financial_accounts as parent', 'fa.parent_id', '=', 'parent.id')
            ->select([
                'fa.id',
                'fa.parent_id',
                'fa.name',
                'fa.type',
                'fa.balance',
                'fa.initial_balance',
                'fa.is_group',
                'fa.is_active',
                'fa.level',
                'fa.sort_order',
                'parent.name as parent_name',
            ])
            ->orderBy('fa.level')
            ->orderBy('fa.sort_order')
            ->orderBy('fa.name');

        if ($type) {
            $query->where('fa.type', $type);
        }

        return $query->get();
    }
}