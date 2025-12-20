<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';

    protected $fillable = [
        'name',
        'type',
        'parent_id',
        'balance',
        'initial_balance',
        'is_group',
        'description',
        'is_active',
        'sort_order',
        'level'
    ];

    // Relasi ke parent account
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    // Relasi ke anak account
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id')
                    ->orderBy('sort_order', 'asc'); 
    }

    // Relasi recursive (nested)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
