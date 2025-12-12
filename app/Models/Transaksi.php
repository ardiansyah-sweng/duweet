<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

class Transaksi extends Model
{
    use HasFactory;

    protected $table = 'transaksi';

    protected $fillable = [
        'account_id', 'user_id', 'date', 'description', 'amount', 'type', 'meta'
    ];

    protected $casts = [
        'date' => 'datetime:Y-m-d',
        'amount' => 'decimal:2',
        'meta' => 'array',
    ];

    public function account()
    {
        return $this->belongsTo(Account::class, 'account_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public static function validator(array $data, $updating = false)
    {
        $rules = [
            'account_id' => 'required|integer|exists:accounts,id',
            'date' => 'required|date',
            'description' => 'nullable|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'type' => 'required|in:debit,credit',
            'meta' => 'nullable|array',
        ];

        if ($updating) {
            foreach ($rules as $k => $r) {
                $rules[$k] = 'sometimes|' . $r;
            }
        }

        return Validator::make($data, $rules);
    }


    public static function createValidated(array $data)
	{
		$validator = self::validator($data);
		if ($validator->fails()) {
			return $validator;
		}

		return self::create($data);
	}

	/**
	 * Update a transaksi safely. Returns the model or Validator on failure.
	 */
	public function updateValidated(array $data)
	{
		$validator = self::validator($data, true);
		if ($validator->fails()) {
			return $validator;
		}

		$this->fill($data);
		$this->save();

		return $this;
	}

	/**
	 * Simple helper to find by id or fail gracefully (return null).
	 */
	public static function findOrNull($id)
	{
		return self::find($id);
	}
}
