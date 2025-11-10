<form action="{{ route('users.search') }}" method="GET">
    <input type="text" name="keyword" placeholder="Cari user berdasarkan nama" value="{{ $keyword ?? '' }}">
    <button type="submit">Cari</button>
</form>

@if(isset($users) && $users->count() > 0)
    <ul>
        @foreach($users as $user)
            <li>{{ $user->name }} ({{ $user->email }})</li>
        @endforeach
    </ul>
@else
    <p>Tidak ada user ditemukan.</p>
@endif
    