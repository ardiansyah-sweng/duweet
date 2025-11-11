<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\FinancialAccount;
use Illuminate\Support\Facades\DB;
use App\Enums\AccountType;
use App\Constants\FinancialAccountColumns as Cols;

class FinancialAccountSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $accounts = [
            // 1. Assets - Cash & Bank (Parent Group)
            [
                Cols::NAME => 'Cash & Bank',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Kas dan rekening bank',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 0,
            ],

            // 2. Assets - Paper Assets (Parent Group)
            [
                Cols::NAME => 'Paper Assets',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Aset surat berharga',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 0,
            ],

            // 3. Assets - Precious Metals (Parent Group)
            [
                Cols::NAME => 'Precious Metals',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Aset logam mulia',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 0,
            ],

            // 4. Assets - Property-Real Estate (Parent Group)
            [
                Cols::NAME => 'Property-Real Estate',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Aset properti dan real estate',
                Cols::SORT_ORDER => 4,
                Cols::LEVEL => 0,
            ],

            // 5. Assets - Vehicles (Parent Group)
            [
                Cols::NAME => 'Vehicles',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Aset kendaraan',
                Cols::SORT_ORDER => 5,
                Cols::LEVEL => 0,
            ],

            // 6. Assets - Electronics & Equipment (Parent Group)
            [
                Cols::NAME => 'Electronics & Equipment',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Aset elektronik dan peralatan',
                Cols::SORT_ORDER => 6,
                Cols::LEVEL => 0,
            ],

            // 7. Income (Parent Group)
            [
                Cols::NAME => 'Pemasukan',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Sumber pemasukan keluarga',
                Cols::SORT_ORDER => 7,
                Cols::LEVEL => 0,
            ],

            // 8. Expenses (Parent Group)
            [
                Cols::NAME => 'Expenses',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Beban rutin keluarga',
                Cols::SORT_ORDER => 8,
                Cols::LEVEL => 0,
            ],

            // 9. Spending (Parent Group)
            [
                Cols::NAME => 'Spending',
                Cols::PARENT_ID => null,
                Cols::TYPE => AccountType::SPENDING->value,
                Cols::IS_GROUP => true,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Pengeluaran tidak rutin',
                Cols::SORT_ORDER => 9,
                Cols::LEVEL => 0,
            ],

            // 10 1.1. BCA Ayah (Child of Cash & Bank)
            [
                Cols::NAME => 'BCA Ayah',
                Cols::PARENT_ID => 1, // Will be updated after insert
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 210798336,
                Cols::INITIAL_BALANCE => 210798336,
                Cols::DESCRIPTION => 'Uang di rekening BCA Ayah',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],
            
            // 11  1.2. BPD Ayah (Child of Cash & Bank)
            [
                Cols::NAME => 'BPD Ayah',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 100000,
                Cols::INITIAL_BALANCE => 100000,
                Cols::DESCRIPTION => 'Uang Serdos Ayah di BPD',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],
            
            // 12 1.3. BPD Istri (Child of Cash & Bank)
            [
                Cols::NAME => 'BPD Istri',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 100000,
                Cols::INITIAL_BALANCE => 100000,
                Cols::DESCRIPTION => 'Uang Serdos Istri di BPD',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 1,
            ],

            // 13 1.4. BPD Syariah Istri (Child of Cash & Bank)
            [
                Cols::NAME => 'BPD Syariah Istri',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 101697979,
                Cols::INITIAL_BALANCE => 101697979,
                Cols::DESCRIPTION => 'Uang Gaji UAD Istri di BPD Syariah',
                Cols::SORT_ORDER => 4,
                Cols::LEVEL => 1,
            ],

            // 14 1.5. Bank Danamon (Child of Cash & Bank)
            [
                Cols::NAME => 'Bank Danamon Istri',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 11096953,
                Cols::INITIAL_BALANCE => 11096953,
                Cols::DESCRIPTION => 'Uang Reksadana Istri di Bank Danamon',
                Cols::SORT_ORDER => 5,
                Cols::LEVEL => 1,
            ],

            // 15 1.6. BSI (Child of Cash & Bank)
            [
                Cols::NAME => 'Bank BSI Istri',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 37376791,
                Cols::INITIAL_BALANCE => 37376791,
                Cols::DESCRIPTION => 'Uang proyek Istri di Bank BSI',
                Cols::SORT_ORDER => 6,
                Cols::LEVEL => 1,
            ],

            // 16 1.7. RDN - IPOT (Child of Cash & Bank)
            [
                Cols::NAME => 'RDN - IPOT',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 243244,
                Cols::INITIAL_BALANCE => 243244,
                Cols::DESCRIPTION => 'Rekening Dana Nasabah IPOT',
                Cols::SORT_ORDER => 7,
                Cols::LEVEL => 1,
            ],

            // 17 1.8. RDN - Mirae (Child of Cash & Bank)
            [
                Cols::NAME => 'RDN - Mirae',
                Cols::PARENT_ID => 1,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 54521423,
                Cols::INITIAL_BALANCE => 54521423,
                Cols::DESCRIPTION => 'Rekening Dana Nasabah Mirae',
                Cols::SORT_ORDER => 8,
                Cols::LEVEL => 1,
            ],

            // 18 2.1. Assets - Paper Asset - Saham KEEN (Child of Paper Asset)
            [
                Cols::NAME => 'Saham KEEN',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Keen Indonesia Tbk (KEEN)',
                Cols::STOCK_SYMBOL => 'KEEN',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 19 2.2. Assets - Paper Asset - Saham PTPS (Child of Paper Asset)
            [
                Cols::NAME => 'Saham PTPS',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Pulau Subur Tbk (PTPS)',
                Cols::STOCK_SYMBOL => 'PTPS',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 20 2.3. Assets - Paper Asset - Saham BBRI (Child of Paper Asset)
            [
                Cols::NAME => 'Saham BBRI',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Bank Rakyat Indonesia Tbk (BBRI)',
                Cols::STOCK_SYMBOL => 'BBRI',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 1,
            ],

            // 21 2.4. Assets - Paper Asset - Saham ESSA (Child of Paper Asset)
            [
                Cols::NAME => 'Saham ESSA',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Essa Industries Tbk (ESSA)',
                Cols::STOCK_SYMBOL => 'ESSA',
                Cols::SORT_ORDER => 4,
                Cols::LEVEL => 1,
            ],

            // 22 2.5. Assets - Paper Asset - Saham MPMX (Child of Paper Asset)
            [
                Cols::NAME => 'Saham MPMX',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Mitra Pinasthika Mustika Tbk (MPMX)',
                Cols::STOCK_SYMBOL => 'MPMX',
                Cols::SORT_ORDER => 5,
                Cols::LEVEL => 1,
            ],

            // 23 2.6. Assets - Paper Asset - Saham RALS (Child of Paper Asset)
            [
                Cols::NAME => 'Saham RALS',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Ramayana Lestari Sentosa Tbk (RALS)',
                Cols::STOCK_SYMBOL => 'RALS',
                Cols::SORT_ORDER => 6,
                Cols::LEVEL => 1,
            ],

            // 24 2.7. Assets - Paper Asset - Saham BEST (Child of Paper Asset)
            [
                Cols::NAME => 'Saham BEST',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Bestprofit Futures Tbk (BEST)',
                Cols::STOCK_SYMBOL => 'BEST',
                Cols::SORT_ORDER => 7,
                Cols::LEVEL => 1,
            ],

            // 25 2.8. Assets - Paper Asset - Saham PNBN (Child of Paper Asset)
            [
                Cols::NAME => 'Saham PNBN',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Bank Pan Indonesia Tbk (PNBN)',
                Cols::STOCK_SYMBOL => 'PNBN',
                Cols::SORT_ORDER => 8,
                Cols::LEVEL => 1,
            ],

            // 26 2.9. Assets - Paper Asset - Saham PWON (Child of Paper Asset)
            [
                Cols::NAME => 'Saham PWON',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0, // Will be updated by AssetSeeder
                Cols::INITIAL_BALANCE => 0, // Will be updated by AssetSeeder
                Cols::DESCRIPTION => 'Saham PT Pakuwon Jati Tbk (PWON)',
                Cols::STOCK_SYMBOL => 'PWON',
                Cols::SORT_ORDER => 9,
                Cols::LEVEL => 1,
            ],

            // 27 2.10. Assets - Paper Asset - RDPU Sucorindo (Child of Paper Asset)
            [
                Cols::NAME => 'RDPU Sucorindo',
                Cols::PARENT_ID => 2,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 207296570,
                Cols::INITIAL_BALANCE => 200000000,
                Cols::DESCRIPTION => 'Reksadana Pasar Uang dari Sucorinvest',
                Cols::SORT_ORDER => 10,
                Cols::LEVEL => 1,
            ],
            
            // 28 3.1. Assets - Precious Metals - Emas Koin (Child of Paper Precious Metals)
            [
                Cols::NAME => 'Emas Koin',
                Cols::PARENT_ID => 3,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Emas Koin',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 29 3.2. Assets - Precious Metals - Emas Perhiasan Cincin (Child of Paper Precious Metals)
            [
                Cols::NAME => 'Emas Cincin Borobudur',
                Cols::PARENT_ID => 3,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Emas Cincin Borobudur Sentra Mas Palembang 6,7 Gr Kadar 92% 07-05-2022',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 30 3.2. Assets - Precious Metals - Emas Gelang (Child of Paper Precious Metals)
            [
                Cols::NAME => 'Emas Gelang',
                Cols::PARENT_ID => 3,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Emas Gelang Sentra Mas Palembang 6,7 Gr Kadar 92% 25-04-2023',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 1,
            ],

            // 31 4.1. Assets - Property-Real Estate - Rumah Geplakan (Child of Property-Real Estate)
            [
                Cols::NAME => 'Rumah Geplakan',
                Cols::PARENT_ID => 4,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Rumah Tinggal di Geplakan',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 32 4.2. Assets - Property-Real Estate - Tanah Ambarketawang (Child of Property-Real Estate)
            [
                Cols::NAME => 'Tanah Ambarketawang',
                Cols::PARENT_ID => 4,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Tanah Ambarketawang',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 33 5.1. Assets - Vehicles - Mobil Brio (Child of Vehicle)
            [
                Cols::NAME => 'Mobil Brio',
                Cols::PARENT_ID => 5,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Mobil Honda Brio',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 34 5.2. Assets - Vehicles - Motor TVS Ronin (Child of Vehicle)
            [
                Cols::NAME => 'Motor TVS Ronin',
                Cols::PARENT_ID => 5,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 25000000,
                Cols::INITIAL_BALANCE => 25000000,
                Cols::DESCRIPTION => 'Motor TVS Ronin',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 35 6.1. Assets - Electronics & Equipment - Laptop Lenovo Ayah (Child of Electonics & Equipment)
            [
                Cols::NAME => 'Laptop Lenovo Ayah',
                Cols::PARENT_ID => 6,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Laptop Lenovo Ayah',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 36 6.2. Assets - Electronics & Equipment - Laptop Lenovo Istri (Child of Electonics & Equipment)
            [
                Cols::NAME => 'Laptop Lenovo Istri',
                Cols::PARENT_ID => 6,
                Cols::TYPE => AccountType::ASSET->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Laptop Lenovo Istri',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],
            
            // 37 7.1. Salary Ayah (Child of Income)
            [
                Cols::NAME => 'Gaji Ayah',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Gaji bulanan Ayah',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

            // 38 7.2. Salary Istri (Child of Income)
            [
                Cols::NAME => 'Gaji Istri',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Gaji bulanan Istri',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 39 7.3. Income Tunjangan (Child of Income)
            [
                Cols::NAME => 'Tunjangan Serdos Ayah',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Sertifikasi Dosen Ayah',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 1,
            ],

            // 40 7.4. Income Tunjangan (Child of Income)
            [
                Cols::NAME => 'Tunjangan Serdos Istri',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Sertifikasi Dosen Istri',
                Cols::SORT_ORDER => 4,
                Cols::LEVEL => 1,
            ],

            // 41 7.5. Income Proyek-Freelance Ayah (Child of Income)
            [
                Cols::NAME => 'Proyek-Freelance Ayah',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Proyek-Freelance Ayah',
                Cols::SORT_ORDER => 5,
                Cols::LEVEL => 1,
            ],

            // 42 7.6. Income Proyek-Freelance Istri (Child of Income)
            [
                Cols::NAME => 'Proyek-Freelance Istri',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Proyek-Freelance Istri',
                Cols::SORT_ORDER => 6,
                Cols::LEVEL => 1,
            ],

            // 43 7.7. Investing Income (Child of Income)
            [
                Cols::NAME => 'Dividen Saham',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Dividen Saham',
                Cols::SORT_ORDER => 7,
                Cols::LEVEL => 1,
            ],

            // 44 7.8. Interest Income (Child of Income)
            [
                Cols::NAME => 'Interest-Bunga',
                Cols::PARENT_ID => 7,
                Cols::TYPE => AccountType::INCOME->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Bunga',
                Cols::SORT_ORDER => 8,
                Cols::LEVEL => 1,
            ],
            
            // 45 8.1. Food & Dining (Child of Expenses)
            [
                Cols::NAME => 'Food & Dining',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Makanan dan minum',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],
            
            // 46 8.2. Transportation (Child of Expenses)
            [
                Cols::NAME => 'Transportation',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya transportasi',
                Cols::SORT_ORDER => 2,
                Cols::LEVEL => 1,
            ],

            // 47 8.3. Education (Child of Expenses)
            [
                Cols::NAME => 'Education',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya Pendidikan',
                Cols::SORT_ORDER => 3,
                Cols::LEVEL => 1,
            ],

            // 48 8.4. Tax (Child of Expenses)
            [
                Cols::NAME => 'Tax',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Pajak',
                Cols::SORT_ORDER => 4,
                Cols::LEVEL => 1,
            ],

            // 49 9.1. Electricity (Child of Expenses)
            [
                Cols::NAME => 'Tagihan Listrik',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya tagihan listrik bulanan',
                Cols::SORT_ORDER => 5,
                Cols::LEVEL => 1,
            ],

            // 50 9.2. Water (Child of Expenses)
            [
                Cols::NAME => 'Tagihan Air PDAM',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya tagihan Air PDAM bulanan',
                Cols::SORT_ORDER => 6,
                Cols::LEVEL => 1,
            ],

            // 51 9.3. Internet & Communication (Child of Expenses)
            [
                Cols::NAME => 'Tagihan Internet dan Komunikasi',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya tagihan Internet dan Komunikasi bulanan',
                Cols::SORT_ORDER => 7,
                Cols::LEVEL => 1,
            ],

            // 52 9.4. Entertainment & Leisure (Child of Expenses)
            [
                Cols::NAME => 'Hiburan dan Liburan',
                Cols::PARENT_ID => 8,
                Cols::TYPE => AccountType::EXPENSES->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Biaya hiburan dan liburan',
                Cols::SORT_ORDER => 8,
                Cols::LEVEL => 1,
            ],

            // 53 10.1. Perawatan Kendaraan (Child of Spending)
            [
                Cols::NAME => 'Vehicle Mintenance',
                Cols::PARENT_ID => 9,
                Cols::TYPE => AccountType::SPENDING->value,
                Cols::IS_GROUP => false,
                Cols::IS_ACTIVE => true,
                Cols::BALANCE => 0,
                Cols::INITIAL_BALANCE => 0,
                Cols::DESCRIPTION => 'Perawatan kendaraan',
                Cols::SORT_ORDER => 1,
                Cols::LEVEL => 1,
            ],

        ];

        // Insert accounts with proper parent_id relationships
        $this->insertAccountsWithRelationships($accounts);
    }

    /**
     * Insert accounts and handle parent-child relationships
     */
    private function insertAccountsWithRelationships(array $accounts): void
    {
        $insertedAccounts = [];
        
        foreach ($accounts as $accountData) {
            // Handle parent_id mapping
            if ($accountData[Cols::PARENT_ID] !== null && isset($insertedAccounts[$accountData[Cols::PARENT_ID] - 1])) {
                $accountData[Cols::PARENT_ID] = $insertedAccounts[$accountData[Cols::PARENT_ID] - 1];
            }

            // Insert account
            $accountId = DB::table(config('db_tables.financial_account'))->insertGetId([
                Cols::NAME => $accountData[Cols::NAME],
                Cols::PARENT_ID => $accountData[Cols::PARENT_ID],
                Cols::TYPE => $accountData[Cols::TYPE],
                Cols::IS_GROUP => $accountData[Cols::IS_GROUP],
                Cols::IS_ACTIVE => $accountData[Cols::IS_ACTIVE],
                Cols::BALANCE => $accountData[Cols::BALANCE],
                Cols::INITIAL_BALANCE => $accountData[Cols::INITIAL_BALANCE],
                Cols::DESCRIPTION => $accountData[Cols::DESCRIPTION],
                Cols::STOCK_SYMBOL => $accountData[Cols::STOCK_SYMBOL] ?? null,
                Cols::SORT_ORDER => $accountData[Cols::SORT_ORDER],
                Cols::LEVEL => $accountData[Cols::LEVEL],
                Cols::CREATED_AT => now(),
                Cols::UPDATED_AT => now(),
            ]);

            $insertedAccounts[] = $accountId;
            
            $this->command->info("Created account: {$accountData[Cols::NAME]} (ID: {$accountId})");
        }
    }
}
