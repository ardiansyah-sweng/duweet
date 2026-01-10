<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;

class UserFinancialAccount extends Model
{
    use HasFactory;

    protected $table = 'user_financial_account';

    protected $fillable = [
        'user_id',
        'account_number',
        'balance',
        'parent_account_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function parentAccount()
    {
        return $this->belongsTo(self::class, 'parent_account_id');
    }

    public function children()
    {
        return $this->hasMany(self::class, 'parent_account_id');
    }

    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
