<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use App\Constants\UserFinancialAccountColumns;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_accounts';
    
    protected $fillable = [
        'user_id',
        'financial_account_id',
        'balance',
        'initial_balance',
        'is_active'
    ];

    public $timestamps = false;

    /**
     * Relasi ke User
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke FinancialAccount
     */
    public function financialAccount()
    {
        return $this->belongsTo(FinancialAccount::class, 'financial_account_id');
    }

    /**
     * Query untuk menghitung total Liquid Assets (Admin)
     * Menghitung sum balance dari semua user yang memiliki financial account bertipe Asset (AS)
     * 
     * @param int|null $userId - Optional: filter by specific user
     * @return float
     */
    public static function getLiquidAssets($userId = null)
    {
        $query = DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->where('fa.type', 'AS') // Asset type sesuai PRD
            ->where('ufa.is_active', true)
            ->where('fa.is_active', true);

        // Filter by user if specified
        if ($userId) {
            $query->where('ufa.user_id', $userId);
        }

        return $query->sum('ufa.balance') ?? 0;
    }

    /**
     * Query untuk mendapatkan detail Liquid Assets per User
     * 
     * @return \Illuminate\Support\Collection
     */
    public static function getLiquidAssetsPerUser()
    {
        return DB::table('user_financial_accounts as ufa')
            ->join('users as u', 'ufa.user_id', '=', 'u.id')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'u.email',
                DB::raw('SUM(ufa.balance) as total_liquid_assets'),
                DB::raw('SUM(ufa.initial_balance) as total_initial_balance'),
                DB::raw('SUM(ufa.balance - ufa.initial_balance) as balance_change')
            )
            ->where('fa.type', 'AS')
            ->where('ufa.is_active', true)
            ->where('fa.is_active', true)
            ->groupBy('u.id', 'u.name', 'u.email')
            ->orderByDesc('total_liquid_assets')
            ->get();
    }

    /**
     * Query untuk mendapatkan detail Liquid Assets dengan breakdown per account
     * 
     * @param int|null $userId - Optional: filter by specific user
     * @return \Illuminate\Support\Collection
     */
    public static function getLiquidAssetsDetail($userId = null)
    {
        $query = DB::table('user_financial_accounts as ufa')
            ->join('users as u', 'ufa.user_id', '=', 'u.id')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                'u.id as user_id',
                'u.name as user_name',
                'fa.id as account_id',
                'fa.name as account_name',
                'fa.type as account_type',
                'fa.level as account_level',
                'ufa.balance as current_balance',
                'ufa.initial_balance',
                DB::raw('(ufa.balance - ufa.initial_balance) as balance_change')
            )
            ->where('fa.type', 'AS')
            ->where('ufa.is_active', true)
            ->where('fa.is_active', true);

        if ($userId) {
            $query->where('ufa.user_id', $userId);
        }

        return $query->orderBy('u.id')
            ->orderBy('fa.level')
            ->orderBy('fa.name')
            ->get();
    }

    /**
     * Query untuk mendapatkan summary Liquid Assets
     * 
     * @return object
     */
    public static function getLiquidAssetsSummary()
    {
        return DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->select(
                DB::raw('COUNT(DISTINCT ufa.user_id) as total_users'),
                DB::raw('COUNT(DISTINCT ufa.financial_account_id) as total_accounts'),
                DB::raw('SUM(ufa.initial_balance) as total_initial_balance'),
                DB::raw('SUM(ufa.balance) as total_current_balance'),
                DB::raw('SUM(ufa.balance - ufa.initial_balance) as total_balance_change'),
                DB::raw('ROUND((SUM(ufa.balance) - SUM(ufa.initial_balance)) / NULLIF(SUM(ufa.initial_balance), 0) * 100, 2) as change_percentage')
            )
            ->where('fa.type', 'AS')
            ->where('ufa.is_active', true)
            ->where('fa.is_active', true)
            ->first();
    }

    /**
     * Update balance pada user_financial_account
     * 
     * @param int $userId
     * @param int $financialAccountId
     * @param float $newBalance
     * @return bool
     */
    public static function updateBalance($userId, $financialAccountId, $newBalance)
    {
        return DB::table('user_financial_accounts')
            ->where('user_id', $userId)
            ->where('financial_account_id', $financialAccountId)
            ->update([
                'balance' => $newBalance,
            ]);
    }

    /**
     * Update balance dengan increment/decrement
     * 
     * @param int $userId
     * @param int $financialAccountId
     * @param float $amount - Positive untuk increment, negative untuk decrement
     * @return bool
     */
    public static function adjustBalance($userId, $financialAccountId, $amount)
    {
        if ($amount > 0) {
            return DB::table('user_financial_accounts')
                ->where('user_id', $userId)
                ->where('financial_account_id', $financialAccountId)
                ->increment('balance', $amount);
        } else {
            return DB::table('user_financial_accounts')
                ->where('user_id', $userId)
                ->where('financial_account_id', $financialAccountId)
                ->decrement('balance', abs($amount));
        }
    }

    /**
     * Update multiple fields pada user_financial_account
     * 
     * @param int $userId
     * @param int $financialAccountId
     * @param array $data - Array of fields to update
     * @return bool
     */
    public static function updateAccount($userId, $financialAccountId, array $data)
    {
        return DB::table('user_financial_accounts')
            ->where('user_id', $userId)
            ->where('financial_account_id', $financialAccountId)
            ->update($data);
    }

    /**
     * Toggle status is_active
     * 
     * @param int $userId
     * @param int $financialAccountId
     * @param bool $isActive
     * @return bool
     */
    public static function toggleActive($userId, $financialAccountId, $isActive)
    {
        return DB::table('user_financial_accounts')
            ->where('user_id', $userId)
            ->where('financial_account_id', $financialAccountId)
            ->update([
                'is_active' => $isActive,
            ]);
    }

    /**
     * Recalculate balance untuk financial_accounts parent (group accounts)
     * Digunakan setelah update balance child accounts
     * 
     * @param int $parentAccountId
     * @return bool
     */
    public static function recalculateParentBalance($parentAccountId)
    {
        $totalBalance = DB::table('financial_accounts')
            ->where('parent_id', $parentAccountId)
            ->where('is_active', true)
            ->sum('balance');

        return DB::table('financial_accounts')
            ->where('id', $parentAccountId)
            ->update([
                'balance' => $totalBalance,
                'updated_at' => now(),
            ]);
    }

    /**
     * Get user financial account untuk validasi sebelum update
     * 
     * @param int $userId
     * @param int $financialAccountId
     * @return object|null
     */
    public static function getUserFinancialAccount($userId, $financialAccountId)
    {
        return DB::table('user_financial_accounts as ufa')
            ->join('financial_accounts as fa', 'ufa.financial_account_id', '=', 'fa.id')
            ->where('ufa.user_id', $userId)
            ->where('ufa.financial_account_id', $financialAccountId)
            ->select(
                'ufa.*',
                'fa.name as account_name',
                'fa.type as account_type',
                'fa.is_group',
                'fa.parent_id'
            )
            ->first();
    }
}