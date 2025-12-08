@extends('layouts.app')

@section('content')
<div class="container">
    <h2>Query Sum Cashout by Period</h2>
    
    <form action="{{ route('admin.cashout.sum.result') }}" method="POST">
        @csrf

        <div class="mt-3">
            <label>Start Date</label>
            <input type="date" name="start_date" class="form-control" required>
        </div>

        <div class="mt-3">
            <label>End Date</label>
            <input type="date" name="end_date" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary mt-3">Submit</button>
    </form>
</div>
@endsection
