<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FinancialAccount extends Model
{
    protected $table = 'financial_accounts';

    protected $fillable = [
        'name', 'type', 'balance', 'initial_balance',
        'is_group', 'description', 'is_active'
    ];

    public $timestamps = true;

    /**
     * Factory method untuk membuat akun keuangan sekaligus relasi UserFinancialAccount.
     * Menerapkan sebagian business rules dari PRD:
     * - name: required
     * - type: harus salah satu enum valid
     * - initial_balance & balance: >= 0 untuk ASSET, boleh negatif untuk LI jika diperlukan (di sini tetap >=0 untuk sederhana)
     * - parent harus sama type jika diberikan (validasi sederhana di sini)
     * - is_group true => balance & initial_balance dipaksa 0
     */
    public static function createForUser(array $data): self
    {
        $required = ['user_id','name','type','initial_balance'];
        foreach ($required as $field) {
            if (!array_key_exists($field, $data)) {
                throw new \InvalidArgumentException("Missing field: {$field}");
            }
        }

        $validTypes = ['IN','EX','SP','LI','AS'];
        if (!in_array($data['type'], $validTypes, true)) {
            throw new \InvalidArgumentException('Invalid account type');
        }

        $isGroup = (bool)($data['is_group'] ?? false);
        $initial = (int) $data['initial_balance'];

        if ($isGroup) {
            $initial = 0; // group tidak menyimpan saldo langsung
        } elseif ($data['type'] === 'AS' && $initial < 0) {
            throw new \InvalidArgumentException('Asset balance cannot be negative');
        }

        // Parent type check (simplified)
        if (!empty($data['parent_id'])) {
            $parent = self::query()->where('id',$data['parent_id'])->first();
            if ($parent && $parent->type !== $data['type']) {
                throw new \InvalidArgumentException('Parent and child must share the same type');
            }
        }

        $fa = self::create([
            'name'            => $data['name'],
            'type'            => $data['type'],
            'balance'         => $initial,
            'initial_balance' => $initial,
            'is_group'        => $isGroup,
            'description'     => $data['description'] ?? null,
            'is_active'       => true,
        ]);

        // Buat relasi user jika bukan group
        if (!$isGroup) {
            \App\Models\UserFinancialAccount::create([
                'user_id'              => $data['user_id'],
                'financial_account_id' => $fa->id,
                'balance'              => $fa->balance,
                'initial_balance'      => $fa->initial_balance,
                'is_active'            => true,
            ]);
        }

        return $fa;
    }
}