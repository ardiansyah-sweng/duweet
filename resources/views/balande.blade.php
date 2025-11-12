<!DOCTYPE html>
<html>
<head>
    <title>Total Balance</title>
</head>
<body>
    <h1>Total Balance User: {{ $user_id }}</h1>
    <p>Saldo Total: {{ number_format($total_balance, 2) }}</p>
</body>
</html>
