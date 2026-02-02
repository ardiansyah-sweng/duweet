SELECT account_type, SUM(balance) AS total_balance
FROM users
GROUP BY account_type;