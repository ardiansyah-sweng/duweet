@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <h2 class="mb-3 text-center"> Jumlah User per Bulan</h2>
    <table class="table table-bordered table-striped table-hover text-center">
        <thead class="table-dark">
            <tr>
                <th scope="col">Bulan</th>
                <th scope="col">Tahun</th>
                <th scope="col">Total User</th>
            </tr>
        </thead>
        <tbody>
            @forelse($usersPerBulan as $data)
                <tr>
                    <td>{{ \DateTime::createFromFormat('!m', intval($data->bulan))->format('F') }}</td>
                    <td>{{ $data->tahun }}</td>
                    <td>{{ $data->total }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="text-center text-muted">Tidak ada data user</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection