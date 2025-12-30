<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laravel User Statistics</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <nav class="navbar navbar-dark bg-dark mb-4">
        <div class="container-fluid">
            <a class="navbar-brand" href="/">Laravel User Stats</a>
            <div>
                <a href="/user/count/tanggal" class="btn btn-outline-light btn-sm me-2">Per Tanggal</a>
                <a href="/user/count/bulan" class="btn btn-outline-light btn-sm">Per Bulan</a>
            </div>
        </div>
    </nav>

    <div class="container">
        @yield('content')
    </div>

    <footer class="text-center mt-5 mb-3 text-muted">
        <small>© 2025 Laravel User Statistics – Made with Nadia Salsabila Susanto </small>
    </footer>
</body>
</html>