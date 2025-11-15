<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <title>Tambah Transaksi</title>
</head>
<body>
    <h1>Tambah Transaksi</h1>

    @if(session('success'))
        <p style="color:green;">{{ session('success') }}</p>
    @endif

    <form action="{{ route('transactions.store') }}" method="POST">
        @csrf

        <label>Account:</label>
        <select name="account_id">
            @foreach($accounts as $acct)
                <option value="{{ $acct->id }}">{{ $acct->name }}</option>
            @endforeach
        </select>
        <br><br>

        <label>Tipe Transaksi:</label>
        <select name="type">
            <option value="IN">IN - Pemasukan</option>
            <option value="EX">EX - Pengeluaran</option>
            <option value="SP">SP - Simpanan</option>
            <option value="LI">LI - Liabilitas</option>
            <option value="AS">AS - Aset</option>
        </select>
        <br><br>

        <label>Jumlah:</label>
        <input type="number" name="amount" step="0.01" required>
        <br><br>

        <label>Deskripsi:</label>
        <input type="text" name="description">
        <br><br>

        <label>Tanggal Transaksi:</label>
        <input type="date" name="transaction_date" required>
        <br><br>

        <button type="submit">Simpan Transaksi</button>
    </form>
</body>
</html>
