@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <h2 class="mb-0">User Details</h2>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary">Back to List</a>
                    </div>
                </div>

                <div class="card-body">
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Full Name:</div>
                        <div class="col-md-8">{{ $user->name }}</div>
                    </div>

                    @if($user->username)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Username:</div>
                        <div class="col-md-8">{{ $user->username }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Email:</div>
                        <div class="col-md-8">{{ $user->email }}</div>
                    </div>

                    @if($user->phone)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Phone:</div>
                        <div class="col-md-8">{{ $user->phone }}</div>
                    </div>
                    @endif

                    @if($user->address)
                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Address:</div>
                        <div class="col-md-8">{{ $user->address }}</div>
                    </div>
                    @endif

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Role:</div>
                        <div class="col-md-8">{{ ucfirst($user->role) }}</div>
                    </div>

                    <div class="row mb-3">
                        <div class="col-md-4 fw-bold">Member Since:</div>
                        <div class="col-md-8">{{ $user->created_at->format('F j, Y') }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection