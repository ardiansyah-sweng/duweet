-- ============================================
-- SQL Query untuk Filter Financial Account
-- ============================================

-- 1. Melihat data users
SELECT id, name, email FROM users LIMIT 5;

-- 2. Melihat semua financial accounts
SELECT id, name, type, is_active, is_group FROM financial_accounts LIMIT 10;

-- 3. Melihat relasi user dengan financial accounts
SELECT 
    u.id as user_id,
    u.name as user_name,
    fa.id as account_id,
    fa.name as account_name,
    fa.type as account_type,
    ufa.balance as user_balance
FROM user_financial_accounts ufa
JOIN users u ON ufa.user_id = u.id
JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
LIMIT 10;

-- ============================================
-- QUERY FILTER: Financial Account by User & Type
-- ============================================

-- 4. Filter: Semua accounts untuk user tertentu (ganti 1 dengan user_id yang ada)
SELECT 
    fa.id,
    fa.name,
    fa.type,
    fa.is_active,
    ufa.balance as user_balance,
    ufa.initial_balance as user_initial_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.is_active = 1;

-- 5. Filter: Assets (AS) untuk user tertentu
SELECT 
    fa.id,
    fa.name,
    fa.type,
    ufa.balance as user_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.type = 'AS'
  AND fa.is_active = 1;

-- 6. Filter: Income (IN) untuk user tertentu
SELECT 
    fa.id,
    fa.name,
    fa.type,
    ufa.balance as user_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.type = 'IN'
  AND fa.is_active = 1;

-- 7. Filter: Multiple types (AS, IN) untuk user tertentu
SELECT 
    fa.id,
    fa.name,
    fa.type,
    ufa.balance as user_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.type IN ('AS', 'IN')
  AND fa.is_active = 1;

-- 8. Filter dengan label type (formatted)
SELECT 
    fa.id,
    fa.name,
    fa.type,
    CASE fa.type
        WHEN 'AS' THEN 'Asset (Aset)'
        WHEN 'IN' THEN 'Income (Pendapatan)'
        WHEN 'EX' THEN 'Expenses (Pengeluaran)'
        WHEN 'SP' THEN 'Spending (Belanja)'
        WHEN 'LI' THEN 'Liability (Kewajiban)'
        ELSE fa.type
    END as type_label,
    fa.is_group,
    fa.is_active,
    ufa.balance as user_balance,
    ufa.initial_balance as user_initial_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.is_active = 1
ORDER BY fa.type, fa.name;

-- 9. Count accounts by type untuk user tertentu
SELECT 
    fa.type,
    COUNT(*) as total_accounts,
    SUM(ufa.balance) as total_balance
FROM financial_accounts fa
INNER JOIN user_financial_accounts ufa ON fa.id = ufa.financial_account_id
WHERE ufa.user_id = 1
  AND fa.is_active = 1
GROUP BY fa.type
ORDER BY fa.type;

-- 10. Filter semua user dengan accounts mereka, grouped by type
SELECT 
    u.id as user_id,
    u.name as user_name,
    fa.type,
    COUNT(fa.id) as total_accounts,
    SUM(ufa.balance) as total_balance
FROM users u
INNER JOIN user_financial_accounts ufa ON u.id = ufa.user_id
INNER JOIN financial_accounts fa ON ufa.financial_account_id = fa.id
WHERE fa.is_active = 1
GROUP BY u.id, u.name, fa.type
ORDER BY u.id, fa.type;
