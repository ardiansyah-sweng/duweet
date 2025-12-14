@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h1 class="mb-4">Daftar User (Admin)</h1>

    <form method="GET" action="{{ route('admin.users.index') }}" class="row g-3 mb-3">
        <div class="col-md-4">
            <input type="text" name="search" class="form-control"
                   placeholder="Cari nama atau email..." value="{{ $search }}">
        </div>
        <div class="col-md-3">
            <select name="sort" class="form-select">
                <option value="created_at"  @selected($sort=='created_at')>Terbaru</option>
                <option value="-created_at" @selected($sort=='-created_at')>Terlama</option>
                <option value="name"        @selected($sort=='name')>Nama A - Z</option>
                <option value="-name"       @selected($sort=='-name')>Nama Z - A</option>
            </select>
        </div>
        <div class="col-md-2">
            <button class="btn btn-primary w-100">Cari</button>
        </div>
        <div class="col-md-2">
            <a href="{{ route('admin.users.index') }}" class="btn btn-secondary w-100">Reset</a>
        </div>
    </form>

    <div class="card shadow-sm">
        <div class="card-body p-0">
            <table class="table table-striped mb-0">
                <thead class="table-dark">
                    <tr>
                        <th>ID</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Dibuat</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $user)
                        <tr>
                            <td>{{ $user->id }}</td>
                            <td>{{ $user->name }}</td>
                            <td>{{ $user->email }}</td>
                            <td>{{ $user->created_at->format('d M Y H:i') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-3">Tidak ada data.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="mt-3">
        {{ $users->withQueryString()->links() }}
    </div>
</div>
@endsection
