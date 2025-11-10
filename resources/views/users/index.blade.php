@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h2 class="mb-0">Users</h2>
                </div>

                <div class="card-body">
                    <form action="{{ route('users.search') }}" method="GET" class="mb-4">
                        <div class="row g-3 align-items-center">
                            <div class="col-auto">
                                <input type="text" name="keyword" class="form-control" placeholder="Search users..." value="{{ $keyword ?? '' }}">
                            </div>
                            <div class="col-auto">
                                <select name="role" class="form-control">
                                    <option value="">All Roles</option>
                                    <option value="user" {{ ($role ?? '') == 'user' ? 'selected' : '' }}>User</option>
                                    <option value="admin" {{ ($role ?? '') == 'admin' ? 'selected' : '' }}>Admin</option>
                                </select>
                            </div>
                            <div class="col-auto">
                                <button type="submit" class="btn btn-primary">Search</button>
                            </div>
                        </div>
                    </form>

                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Name</th>
                                    <th>Username</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse ($users as $user)
                                    <tr>
                                        <td>{{ $user->name }}</td>
                                        <td>{{ $user->username }}</td>
                                        <td>{{ $user->email }}</td>
                                        <td>{{ $user->phone }}</td>
                                        <td>{{ ucfirst($user->role) }}</td>
                                        <td>
                                            <a href="{{ route('users.show', $user) }}" class="btn btn-sm btn-info">View</a>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="6" class="text-center">No users found.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    {{ $users->withQueryString()->links() }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection