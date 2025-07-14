@extends('layouts.app')

@section('content')
<div class="max-w-lg mx-auto mt-10">
    <h1 class="text-2xl font-bold mb-4">Test Email (Superadmin only)</h1>
    @if(session('success'))
        <div class="bg-green-100 text-green-800 p-2 rounded mb-4">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="bg-red-100 text-red-800 p-2 rounded mb-4">
            @foreach($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif
    <form method="POST" action="{{ route('admin.test-email.send') }}">
        @csrf
        <label class="block mb-2">Destination Email</label>
        <input type="email" name="to" class="border p-2 w-full mb-4" required>
        <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send Test Email</button>
    </form>
</div>
@endsection
