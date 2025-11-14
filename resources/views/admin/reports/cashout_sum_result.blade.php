@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Cashout Summary</h2>

    <p><b>Start:</b> {{ $start }}</p>
    <p><b>End:</b> {{ $end }}</p>

    <h3>Total Cashout: <span class="text-success">Rp {{ number_format($total, 0, ',', '.') }}</span></h3>

    <form action="{{ route('admin.cashout.sum.export') }}" method="POST" class="mt-3">
        @csrf
        <input type="hidden" name="start_date" value="{{ $start }}">
        <input type="hidden" name="end_date" value="{{ $end }}">
        <button class="btn btn-secondary">Export CSV</button>
    </form>

    <hr>

    <h4>Breakdown per Day</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Date</th>
                <th>Total Amount</th>
                <th>Total Transactions</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($breakdown as $row)
                <tr>
                    <td>{{ $row->date }}</td>
                    <td>Rp {{ number_format($row->total_amount, 0, ',', '.') }}</td>
                    <td>{{ $row->count_tx }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <a href="{{ route('admin.cashout.sum.form') }}" class="btn btn-secondary">Back</a>
</div>
@endsection
