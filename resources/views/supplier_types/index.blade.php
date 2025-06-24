@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto py-8">
    <h1 class="text-2xl font-bold mb-4">Supplier Types</h1>
    <table class="min-w-full bg-white border rounded">
        <thead>
            <tr>
                <th class="px-4 py-2 border-b">Name</th>
            </tr>
        </thead>
        <tbody>
            @foreach($types as $type)
                <tr>
                    <td class="px-4 py-2 border-b flex items-center gap-2">
                        @if($type->icon)
                            @if(Str::startsWith($type->icon, 'fa'))
                                <i class="{{ $type->icon }} text-xl"></i>
                            @else
                                <img src="{{ $type->icon }}" alt="icon" class="h-6 w-6 object-contain inline-block" />
                            @endif
                        @endif
                        {{ $type->name }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
