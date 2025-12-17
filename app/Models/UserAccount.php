<?php

namespace App\Models;

use App\Constants\UserAccountColumns;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash; // Tambahan: Import Hash

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
        UserAccountColumns::IS_ACTIVE   => 'boolean',
        UserAccountColumns::VERIFIED_AT => 'datetime',
    ];

    protected $hidden = [
        UserAccountColumns::PASSWORD,
    ];

    /**
     * Get the fillable attributes for the model.
     */
    public function getFillable()
    {
        return UserAccountColumns::getFillable();
    }

    /**
     * Override primary key name using centralized constant.
     */
    public function getKeyName()
    {
        return UserAccountColumns::getPrimaryKey();
    }

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, UserAccountColumns::ID_USER);
    }

    /**
     * Insert UserAccount baru dengan Raw Query (DB::insert)
     * Logika hashing dan default value dilakukan di sini.
     * * @param array $data Data yang sudah divalidasi
     * @return bool
     */
    public static function insertUserAccountRaw(array $data)
    {
        if (isset($data[UserAccountColumns::PASSWORD])) {
            $data[UserAccountColumns::PASSWORD] = Hash::make($data[UserAccountColumns::PASSWORD]);
        }

        $data[UserAccountColumns::VERIFIED_AT] = now();
        $data[UserAccountColumns::IS_ACTIVE]   = true;

        return DB::table('user_accounts')->insert($data);
    }

    /**
     * Hapus satu UserAccount berdasarkan ID dengan raw query
     * * @param int $id
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
}