<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data User & Financial Account</title>
    <style>
        body {
            font-family: "Segoe UI", sans-serif;
            background-color: #fdfcff;
            padding: 40px;
        }
        table {
            border-collapse: collapse;
            width: 90%;
            margin: auto;
            background-color: #fff;
            border-radius: 8px;
            overflow: hidden;
            box-shadow: 0 4px 12px rgba(0,0,0,0.08);
        }
        th, td {
            padding: 12px 16px;
            border-bottom: 1px solid #eee;
            text-align: left;
            vertical-align: top;
        }
        th {
            background-color: #e8e5ff;
            color: #333;
        }
        tr:hover {
            background-color: #f8f6ff;
        }
        h1 {
            text-align: center;
            color: #4b4b6b;
            margin-bottom: 24px;
        }
    </style>
</head>
<body>
    <h1>Daftar User dan Akun Finansial</h1>
    <table>
        <thead>
            <tr>
                <th>ID User</th>
                <th>Nama</th>
                <th>Email</th>
                <th>Nomor Akun</th>
                <th>Saldo</th>
                <th>Tanggal Dibuat</th>
                <th>Terakhir Diperbarui</th>
            </tr>
        </thead>
        <tbody>
            @foreach($users as $user)
                <tr>
                    <td>{{ $user->id }}</td>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @forelse($user->userFinancialAccounts as $acc)
                            {{ $acc->account_number }}<br>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>
                        @forelse($user->userFinancialAccounts as $acc)
                            Rp{{ number_format($acc->balance, 0, ',', '.') }}<br>
                        @empty
                            -
                        @endforelse
                    </td>
                    <td>{{ $user->created_at }}</td>
                    <td>{{ $user->updated_at }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</body>
</html>
