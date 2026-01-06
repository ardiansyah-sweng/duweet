@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3 text-center"> Jumlah User per Tanggal</h2>
    <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th scope="col">Tanggal</th>
                <th scope="col">Total User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usersPerTanggal as $data)
                <tr>
                    <td>{{ \Carbon\Carbon::parse($data->tanggal)->format('d M Y') }}</td>
                    <td>{{ $data->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="2" class="text-center text-muted">Tidak ada data user</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
