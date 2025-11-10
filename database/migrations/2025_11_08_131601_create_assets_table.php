<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Constants\AssetColumns;

return new class extends Migration
{
    protected string $table;

    public function __construct()
    {
        $this->table = config('db_tables.asset');
    }
    
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create($this->table, function (Blueprint $table) {
            // Primary Key
            $table->id(AssetColumns::ID);
            
            // Foreign Key to financial_accounts
            $table->foreignId(AssetColumns::ACCOUNT_ID)
                  ->constrained(config('db_tables.financial_account'))
                  ->onDelete('cascade')
                  ->comment('Reference to financial_accounts table for asset type');

            // Asset acquisition information
            $table->date(AssetColumns::ACQUISITION_DATE)
                  ->comment('Date when the asset was acquired/purchased');

            $table->date(AssetColumns::SOLD_DATE)
                  ->nullable()
                  ->comment('Date when the asset was sold/disposed');

            // Asset pricing information
            $table->integer(AssetColumns::BOUGHT_PRICE)
                  ->default(0)
                  ->comment('Purchase price of the asset');

            $table->integer(AssetColumns::SOLD_PRICE)
                  ->default(0)
                  ->comment('Sale price of the asset');

            // Asset quantity information
            $table->integer(AssetColumns::BUY_QTY)
                  ->default(0)
                  ->comment('Quantity of asset purchased');

            $table->integer(AssetColumns::SELL_QTY)
                  ->default(0)
                  ->comment('Quantity of asset sold');

            // Asset characteristics
            $table->boolean(AssetColumns::IS_LIQUID)
                  ->default(false)
                  ->comment('Whether the asset is liquid (easily convertible to cash)');

            $table->boolean(AssetColumns::IS_PRODUCTIVE)
                  ->default(false)
                  ->comment('Whether the asset is productive (generates income or appreciates)');

            // Measurement unit for the asset
            $table->string(AssetColumns::MEASUREMENT, 50)
                  ->nullable()
                  ->comment('Unit of measurement (e.g., lot, gram, unit, m², piece)');

            // Derived/calculated fields
            $table->string(AssetColumns::HOLDING_PERIOD)
                  ->nullable()
                  ->comment('Holding period in days (derived from acquisition_date to current date)');

            // Asset status
            $table->boolean(AssetColumns::IS_SOLD)
                  ->default(false)
                  ->comment('Whether the asset has been sold/disposed');

            // Timestamps
            $table->timestamps();

            // Indexes for performance
            $table->index(AssetColumns::ACCOUNT_ID, 'idx_assets_account_id');
            $table->index(AssetColumns::ACQUISITION_DATE, 'idx_assets_acquisition_date');
            $table->index(AssetColumns::SOLD_DATE, 'idx_assets_sold_date');
            $table->index([AssetColumns::IS_LIQUID, AssetColumns::IS_PRODUCTIVE], 'idx_assets_characteristics');
            $table->index(AssetColumns::IS_SOLD, 'idx_assets_is_sold');
            $table->index(AssetColumns::HOLDING_PERIOD, 'idx_assets_holding_period');
            $table->index([AssetColumns::BOUGHT_PRICE, AssetColumns::SOLD_PRICE], 'idx_assets_pricing');
            $table->index([AssetColumns::BUY_QTY, AssetColumns::SELL_QTY], 'idx_assets_quantity');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists($this->table);
    }
};
