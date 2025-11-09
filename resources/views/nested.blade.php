<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Struktur Nested User & Account</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">

<nav class="navbar navbar-dark bg-dark mb-4">
    <div class="container">
        <span class="navbar-brand mb-0 h1">Laravel Nested Structure</span>
    </div>
</nav>

<div class="container">
    <h2 class="text-center mb-4">Struktur Nested User & Account</h2>

    <table class="table table-bordered table-striped text-center">
        <thead class="table-dark">
            <tr>
                <th>Nama User</th>
                <th>Username</th>
                <th>Email</th>
                <th>Parent Account</th>
                <th>Child Accounts</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($data as $user)
                <tr>
                    <td>{{ $user->name }}</td>
                    <td>{{ $user->userAccount->username ?? '-' }}</td>
                    <td>{{ $user->userAccount->email ?? '-' }}</td>
                    <td>{{ $user->userAccount->parent->username ?? '-' }}</td>
                    <td>
                        @if(isset($user->userAccount->children) && $user->userAccount->children->count() > 0)
                            @foreach($user->userAccount->children as $child)
                                <span class="badge bg-info text-dark">{{ $child->username }}</span>
                            @endforeach
                        @else
                            <span class="text-muted">Tidak ada</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5">Tidak ada data user</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <footer class="text-center mt-4">
        <small>© 2025 Laravel Nested Structure – Created by Nadiya Shabriyyah</small>
    </footer>
</div>

</body>
</html>
