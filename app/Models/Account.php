<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $table = 'accounts';
    protected $fillable = [
        'code',
        'name',
        'account_type',
        'parent_id'
    ];

    // Relasi ke parent account
    public function parent()
    {
        return $this->belongsTo(Account::class, 'parent_id');
    }

    // Relasi ke semua anak account
    public function children()
    {
        return $this->hasMany(Account::class, 'parent_id');
    }

    // Relasi recursive untuk mengambil semua level anak (nested)
    public function childrenRecursive()
    {
        return $this->children()->with('childrenRecursive');
    }
}
