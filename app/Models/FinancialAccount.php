<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Enums\AccountType;
use App\Constants\FinancialAccountColumns as Cols;
use App\Constants\AssetColumns as AssetCols;

class FinancialAccount extends Model
{
    protected $table;
    
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        $this->table = config('db_tables.financial_account');
        $this->fillable = Cols::getFillable();
    }
    
    protected $fillable = [];

    protected $casts = [
        Cols::TYPE => AccountType::class,
        Cols::IS_GROUP => 'boolean',
        Cols::IS_ACTIVE => 'boolean',
        Cols::BALANCE => 'integer',
        Cols::INITIAL_BALANCE => 'integer',
        Cols::SORT_ORDER => 'integer',
        Cols::LEVEL => 'integer',
    ];

    // ================================
    // RELATIONSHIPS
    // ================================
    
    /**
     * Get the parent account
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(self::class, Cols::PARENT_ID);
    }
    
    /**
     * Get direct children accounts
     */
    public function children(): HasMany
    {
        return $this->hasMany(self::class, Cols::PARENT_ID)
                    ->where(Cols::IS_ACTIVE, true)
                    ->orderBy(Cols::SORT_ORDER);
    }
    
    /**
     * Get all descendants (recursive)
     */
    public function allDescendants(): HasMany
    {
        return $this->children()->with('allDescendants');
    }

    /**
     * Get assets related to this account
     */
    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class, AssetCols::ACCOUNT_ID);
    }

    // ================================
    // SCOPES
    // ================================
    
    /**
     * Scope for asset accounts (type starts with 'A')
     */
    public function scopeAssetAccounts($query)
    {
        return $query->where(Cols::TYPE, 'like', 'A%');
    }
    
    /**
     * Scope for active accounts
     */
    public function scopeActiveAccounts($query)
    {
        return $query->where(Cols::IS_ACTIVE, true);
    }
    
    /**
     * Scope for group accounts
     */
    public function scopeGroupAccounts($query)
    {
        return $query->where(Cols::IS_GROUP, true);
    }
    
    /**
     * Scope for leaf accounts (non-group)
     */
    public function scopeLeafAccounts($query)
    {
        return $query->where(Cols::IS_GROUP, false);
    }

    /**
     * Scope for specific account type
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where(Cols::TYPE, $type);
    }

    /**
     * Scope for accounts by level
     */
    public function scopeByLevel($query, int $level)
    {
        return $query->where(Cols::LEVEL, $level);
    }

    // ================================
    // BALANCE CALCULATION METHODS
    // ================================
    
    /**
     * Get total balance for this account (including all descendants)
     */
    public function getTotalBalance(): int
    {
        if (!$this->is_group) {
            return $this->balance;
        }
        
        // For group accounts, sum all leaf descendants
        return $this->getLeafDescendants()->sum(Cols::BALANCE);
    }
    
    /**
     * Get all leaf (non-group) descendants
     */
    public function getLeafDescendants()
    {
        $descendants = collect();
        
        foreach ($this->children as $child) {
            if ($child->is_group) {
                $descendants = $descendants->merge($child->getLeafDescendants());
            } else {
                $descendants->push($child);
            }
        }
        
        return $descendants;
    }
    
    /**
     * Get balance breakdown by account type
     */
    public function getBalanceBreakdown(): array
    {
        $breakdown = [];
        
        foreach ($this->children as $child) {
            $breakdown[$child->{Cols::NAME}] = [
                'account_id' => $child->{Cols::ID},
                'account_name' => $child->{Cols::NAME},
                'account_type' => $child->{Cols::TYPE}->value,
                'is_group' => $child->{Cols::IS_GROUP},
                'balance' => $child->getTotalBalance(),
                'initial_balance' => $child->{Cols::INITIAL_BALANCE},
                'children' => $child->{Cols::IS_GROUP} ? $child->getBalanceBreakdown() : null
            ];
        }
        
        return $breakdown;
    }

    /**
     * Calculate gain/loss for this account
     */
    public function getGainLoss(): array
    {
        $currentBalance = $this->getTotalBalance();
        $initialBalance = $this->is_group 
            ? $this->getLeafDescendants()->sum(Cols::INITIAL_BALANCE)
            : $this->initial_balance;
        
        $gainLoss = $currentBalance - $initialBalance;
        $gainLossPercentage = $initialBalance > 0 
            ? ($gainLoss / $initialBalance) * 100 
            : 0;

        return [
            'current_balance' => $currentBalance,
            'initial_balance' => $initialBalance,
            'gain_loss' => $gainLoss,
            'gain_loss_percentage' => round($gainLossPercentage, 2),
            'performance_status' => $gainLoss >= 0 ? 'profit' : 'loss'
        ];
    }
    
    // ================================
    // STATIC METHODS FOR ASSET QUERIES
    // ================================
    
    /**
     * Get total asset balance (all asset types)
     */
    public static function getTotalAssetBalance(): int
    {
        return static::assetAccounts()
                    ->activeAccounts()
                    ->leafAccounts()
                    ->sum(Cols::BALANCE);
    }
    
    /**
     * Get asset balance by type
     */
    public static function getAssetBalanceByType(): array
    {
        return static::assetAccounts()
                    ->activeAccounts()
                    ->groupAccounts()
                    ->with('children')
                    ->get()
                    ->mapWithKeys(function ($account) {
                        return [$account->name => $account->getTotalBalance()];
                    })
                    ->toArray();
    }

    /**
     * Get asset accounts hierarchy
     */
    public static function getAssetHierarchy(): array
    {
        $rootAssets = static::assetAccounts()
                           ->activeAccounts()
                           ->byLevel(0)
                           ->with(['allDescendants' => function ($query) {
                               $query->where(Cols::IS_ACTIVE, true)
                                     ->orderBy(Cols::SORT_ORDER);
                           }])
                           ->orderBy(Cols::SORT_ORDER)
                           ->get();

        return $rootAssets->map(function ($account) {
            return [
                'id' => $account->id,
                'name' => $account->name,
                'type' => $account->type->value,
                'is_group' => $account->is_group,
                'level' => $account->level,
                'balance' => $account->getTotalBalance(),
                'gain_loss' => $account->getGainLoss(),
                'children' => $account->is_group ? $account->getBalanceBreakdown() : null
            ];
        })->toArray();
    }

    // ================================
    // UTILITY METHODS
    // ================================

    /**
     * Get account path (for breadcrumb navigation)
     */
    public function getAccountPath(): array
    {
        $path = [];
        $current = $this;
        
        while ($current) {
            array_unshift($path, [
                'id' => $current->id,
                'name' => $current->name,
                'level' => $current->level
            ]);
            $current = $current->parent;
        }
        
        return $path;
    }

    /**
     * Check if account is liquid (cash and bank accounts)
     */
    public function isLiquid(): bool
    {
        return $this->type->value === 'ACB'; // Asset Cash & Bank
    }

    /**
     * Get formatted balance
     */
    public function getFormattedBalance(bool $includeCurrency = true): string
    {
        $balance = number_format($this->getTotalBalance(), 0, ',', '.');
        return $includeCurrency ? "Rp {$balance}" : $balance;
    }
}
