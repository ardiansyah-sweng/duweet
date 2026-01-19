<!DOCTYPE html>
<html>
<head>
    <title>Admin - User Search</title>
</head>
<body>

<h2>Search User (Admin)</h2>

<form method="GET" action="{{ url('/admin/users') }}">
    <input type="text" name="keyword" value="{{ $search }}" placeholder="Cari nama / email">
    <button type="submit">Search</button>
</form>

<table border="1" cellpadding="8" cellspacing="0">
    <tr>
        <th>ID</th>
        <th>Nama</th>
        <th>Email</th>
    </tr>

    @foreach($users as $user)
    <tr>
        <td>{{ $user->id }}</td>
        <td>{{ $user->name }}</td>
        <td>{{ $user->email }}</td>
    </tr>
    @endforeach
</table>

</body>
</html>
