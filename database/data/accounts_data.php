<?php

return [
    // ASSETS (AS)
    [
        'name' => 'Cash & Bank',
        'type' => 'AS',
        'is_group' => true,
        'level' => 0,
        'sort_order' => 1,
        'children' => [
            [
                'name' => 'Cash',
                'type' => 'AS',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Dompet', 'type' => 'AS', 'level' => 2, 'sort_order' => 1, 'initial_balance' => 50000],
                    ['name' => 'Kas Kecil', 'type' => 'AS', 'level' => 2, 'sort_order' => 2, 'initial_balance' => 100000],
                ]
            ],
            [
                'name' => 'Bank Account',
                'type' => 'AS',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'BCA Tabungan', 'type' => 'AS', 'level' => 2, 'sort_order' => 1, 'initial_balance' => 5000000],
                    ['name' => 'Mandiri Tabungan', 'type' => 'AS', 'level' => 2, 'sort_order' => 2, 'initial_balance' => 3000000],
                    ['name' => 'BNI Tabungan', 'type' => 'AS', 'level' => 2, 'sort_order' => 3, 'initial_balance' => 2000000],
                    ['name' => 'BRI Simpedes', 'type' => 'AS', 'level' => 2, 'sort_order' => 4, 'initial_balance' => 1500000],
                ]
            ],
            [
                'name' => 'E-Wallet',
                'type' => 'AS',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 3,
                'children' => [
                    ['name' => 'GoPay', 'type' => 'AS', 'level' => 2, 'sort_order' => 1, 'initial_balance' => 200000],
                    ['name' => 'OVO', 'type' => 'AS', 'level' => 2, 'sort_order' => 2, 'initial_balance' => 150000],
                    ['name' => 'Dana', 'type' => 'AS', 'level' => 2, 'sort_order' => 3, 'initial_balance' => 100000],
                    ['name' => 'ShopeePay', 'type' => 'AS', 'level' => 2, 'sort_order' => 4, 'initial_balance' => 75000],
                ]
            ]
        ]
    ],

    // INCOME (IN)
    [
        'name' => 'Income',
        'type' => 'IN',
        'is_group' => true,
        'level' => 0,
        'sort_order' => 2,
        'children' => [
            ['name' => 'Salary', 'type' => 'IN', 'level' => 1, 'sort_order' => 1, 'description' => 'Gaji pokok bulanan'],
            ['name' => 'Bonus', 'type' => 'IN', 'level' => 1, 'sort_order' => 2, 'description' => 'Bonus kerja, THR, dll'],
            ['name' => 'Freelance', 'type' => 'IN', 'level' => 1, 'sort_order' => 3, 'description' => 'Penghasilan freelance'],
            ['name' => 'Investment Return', 'type' => 'IN', 'level' => 1, 'sort_order' => 4, 'description' => 'Dividen, bunga, capital gain'],
            ['name' => 'Business Income', 'type' => 'IN', 'level' => 1, 'sort_order' => 5, 'description' => 'Penghasilan usaha'],
            ['name' => 'Other Income', 'type' => 'IN', 'level' => 1, 'sort_order' => 6, 'description' => 'Penghasilan lain-lain'],
        ]
    ],

    // EXPENSES (EX)
    [
        'name' => 'Expenses',
        'type' => 'EX',
        'is_group' => true,
        'level' => 0,
        'sort_order' => 3,
        'children' => [
            [
                'name' => 'Housing',
                'type' => 'EX',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'Rent/Mortgage', 'type' => 'EX', 'level' => 2, 'sort_order' => 1],
                    ['name' => 'Utilities', 'type' => 'EX', 'level' => 2, 'sort_order' => 2],
                    ['name' => 'Internet', 'type' => 'EX', 'level' => 2, 'sort_order' => 3],
                    ['name' => 'Home Maintenance', 'type' => 'EX', 'level' => 2, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Transportation',
                'type' => 'EX',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Fuel', 'type' => 'EX', 'level' => 2, 'sort_order' => 1],
                    ['name' => 'Public Transport', 'type' => 'EX', 'level' => 2, 'sort_order' => 2],
                    ['name' => 'Online Transport', 'type' => 'EX', 'level' => 2, 'sort_order' => 3],
                    ['name' => 'Vehicle Maintenance', 'type' => 'EX', 'level' => 2, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Food & Dining',
                'type' => 'EX',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 3,
                'children' => [
                    ['name' => 'Groceries', 'type' => 'EX', 'level' => 2, 'sort_order' => 1],
                    ['name' => 'Restaurant', 'type' => 'EX', 'level' => 2, 'sort_order' => 2],
                    ['name' => 'Food Delivery', 'type' => 'EX', 'level' => 2, 'sort_order' => 3],
                    ['name' => 'Coffee & Snacks', 'type' => 'EX', 'level' => 2, 'sort_order' => 4],
                ]
            ],
            [
                'name' => 'Healthcare',
                'type' => 'EX',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 4,
                'children' => [
                    ['name' => 'Medical', 'type' => 'EX', 'level' => 2, 'sort_order' => 1],
                    ['name' => 'Pharmacy', 'type' => 'EX', 'level' => 2, 'sort_order' => 2],
                    ['name' => 'Insurance', 'type' => 'EX', 'level' => 2, 'sort_order' => 3],
                ]
            ]
        ]
    ],

    // SPENDING (SP)
    [
        'name' => 'Spending',
        'type' => 'SP',
        'is_group' => true,
        'level' => 0,
        'sort_order' => 4,
        'children' => [
            ['name' => 'Shopping', 'type' => 'SP', 'level' => 1, 'sort_order' => 1],
            ['name' => 'Entertainment', 'type' => 'SP', 'level' => 1, 'sort_order' => 2],
            ['name' => 'Subscription', 'type' => 'SP', 'level' => 1, 'sort_order' => 3],
            ['name' => 'Hobbies', 'type' => 'SP', 'level' => 1, 'sort_order' => 4],
            ['name' => 'Travel', 'type' => 'SP', 'level' => 1, 'sort_order' => 5],
            ['name' => 'Gifts', 'type' => 'SP', 'level' => 1, 'sort_order' => 6],
        ]
    ],

    // LIABILITY (LI)
    [
        'name' => 'Liability',
        'type' => 'LI',
        'is_group' => true,
        'level' => 0,
        'sort_order' => 5,
        'children' => [
            [
                'name' => 'Credit Card',
                'type' => 'LI',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 1,
                'children' => [
                    ['name' => 'BCA Credit Card', 'type' => 'LI', 'level' => 2, 'sort_order' => 1, 'initial_balance' => -2000000],
                    ['name' => 'Mandiri Credit Card', 'type' => 'LI', 'level' => 2, 'sort_order' => 2, 'initial_balance' => -1500000],
                ]
            ],
            [
                'name' => 'Loans',
                'type' => 'LI',
                'is_group' => true,
                'level' => 1,
                'sort_order' => 2,
                'children' => [
                    ['name' => 'Car Loan', 'type' => 'LI', 'level' => 2, 'sort_order' => 1, 'initial_balance' => -50000000],
                    ['name' => 'Personal Loan', 'type' => 'LI', 'level' => 2, 'sort_order' => 2, 'initial_balance' => -10000000],
                ]
            ],
            ['name' => 'Mortgage', 'type' => 'LI', 'level' => 1, 'sort_order' => 3, 'initial_balance' => -200000000],
        ]
    ]
];
