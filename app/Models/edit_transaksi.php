<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Validator;

/**
 * Model for editing / manipulating transactions (transaksi)
 *
 * This model provides basic validation rules and simple CRUD helper
 * methods intended for small controllers or scripts. Adjust the
 * $fillable and rules to match your database schema.
 */
class edit_transaksi extends Model
{
	use HasFactory;

	// If your table is named differently, set it here. By Laravel
	// convention, the model `edit_transaksi` would map to
	// `edit_transaksis` table. Change if necessary.
	protected $table = 'transaksi';

	// Primary key (default 'id'). Change if using a different PK.
	protected $primaryKey = 'id';

	// Allow mass assignment on these columns. Update to match your schema.
	protected $fillable = [
		'account_id',
		'date',
		'description',
		'amount',
		'type', // debit/credit or similar
		'meta', // optional JSON column
	];

	protected $casts = [
		'date' => 'datetime:Y-m-d',
		'amount' => 'decimal:2',
		'meta' => 'array',
	];

	/**
	 * Validation rules for creating/updating a transaksi.
	 * Returns a Validator instance; caller can check ->fails() and ->errors().
	 */
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
			// When updating, allow partial updates (make fields sometimes)
			foreach ($rules as $k => $r) {
				$rules[$k] = 'sometimes|' . $r;
			}
		}

		return Validator::make($data, $rules);
	}

	/**
	 * Create a new transaksi after validating the input.
	 * Returns the model on success or a Validator instance on failure.
	 */
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

