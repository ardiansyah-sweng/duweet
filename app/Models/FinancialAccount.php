<?php

namespace App\Models;

use App\Enums\AccountType; // Ambil Enum yang sudah ada
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    use HasFactory; // <-- PENTING! Agar ::factory() bisa dipanggil

    /**
     * The attributes that are mass assignable.
     */
    protected $fillable = [
        'parent_id',
        'name',
        'type',
        'balance',
        'initial_balance',
        'is_group',
        'description',
        'is_active',
        'color',
        'icon',
        'sort_order',
        'level',
    ];

    /**
     * The attributes that should be cast.
     */
    protected $casts = [
        'type' => AccountType::class, // <-- Sesuai PRD
        'is_group' => 'boolean',
        'is_active' => 'boolean',
        'balance' => 'integer',
        'initial_balance' => 'integer',
    ];
}