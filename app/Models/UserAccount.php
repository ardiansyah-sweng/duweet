<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;

class UserAccount extends Model
{
    use HasFactory;

    protected $table = 'user_accounts';

    /**
     * This table does not use created_at/updated_at timestamps.
     *
     * @var bool
     */
    public $timestamps = false;

    protected $casts = [
        UserAccountColumns::IS_ACTIVE => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Get the fillable attributes for the model.
     * Uses centralized definition from UserAccountColumns constant class.
     *
     * @return array<string>
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Get financial accounts for this user account
     */
    public function financialAccounts()
    {
        return $this->hasMany(UserFinancialAccount::class, 'user_account_id');
    }

    /**
     * Hapus satu UserAccount berdasarkan ID dengan raw query
     * 
     * @param int $id
     * @return array
     */
    public static function deleteUserAccountRaw($id)
    {
        try {
            $deleteQuery = "DELETE FROM user_accounts WHERE " . UserAccountColumns::ID . " = ?";
            DB::delete($deleteQuery, [$id]);
            return [
                'success' => true,
                'message' => 'UserAccount berhasil dihapus'
            ];
        } catch (\Exception $e) {
            return [
                'success' => false,
                'message' => 'Gagal menghapus UserAccount: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get user accounts that don't have financial accounts setup
     */
    public static function getAccountsWithoutFinancialSetup()
    {
        return self::leftJoin('user_financial_accounts as ufa', 'ufa.user_account_id', '=', 'user_accounts.id')
            ->whereNull('ufa.id')
            ->select(
                'user_accounts.id',
                UserAccountColumns::ID_USER,
                UserAccountColumns::USERNAME,
                UserAccountColumns::EMAIL,
                UserAccountColumns::IS_ACTIVE,
                UserAccountColumns::VERIFIED_AT
            )
            ->orderBy(UserAccountColumns::ID, 'desc')
            ->get();
    }

    /**
     * Get user accounts without active financial accounts
     */
    public static function getAccountsWithoutActiveFinancial()
    {
        return self::leftJoin('user_financial_accounts as ufa_active', function ($join) {
                $join->on('ufa_active.user_account_id', '=', 'user_accounts.id')
                    ->where('ufa_active.is_active', 1);
            })
            ->whereNull('ufa_active.id')
            ->select(
                'user_accounts.id',
                UserAccountColumns::ID_USER,
                UserAccountColumns::USERNAME,
                UserAccountColumns::EMAIL,
                UserAccountColumns::IS_ACTIVE,
                UserAccountColumns::VERIFIED_AT
            )
            ->orderBy(UserAccountColumns::ID, 'desc')
            ->get();
    }

    /**
     * Get all user accounts with their financial account status
     */
    public static function getAllAccountsWithFinancialStatus()
    {
        return self::with(['user:id,name,email', 'financialAccounts:id,name,type,balance'])
            ->select('id', UserAccountColumns::ID_USER, UserAccountColumns::USERNAME, UserAccountColumns::EMAIL, UserAccountColumns::IS_ACTIVE)
            ->orderBy(UserAccountColumns::ID, 'desc')
            ->get();
    }
}
