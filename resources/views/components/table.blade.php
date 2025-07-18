@props(['headers', 'rows', 'empty' => 'No records found.', 'actions' => null])

<div class="overflow-x-auto rounded-lg shadow">
    <table class="min-w-full divide-y divide-gray-200 bg-white">
        <thead>
            <tr>
                @foreach ($headers as $header)
                    <th class="px-4 py-2 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">{{ $header }}</th>
                @endforeach
                @if($actions)
                    <th class="px-4 py-2"></th>
                @endif
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr class="hover:bg-gray-50">
                    @foreach ($row as $cell)
                        <td class="px-4 py-2 whitespace-nowrap">{!! $cell !!}</td>
                    @endforeach
                    @if($actions)
                        <td class="px-4 py-2 whitespace-nowrap">{!! $actions($row) !!}</td>
                    @endif
                </tr>
            @empty
                <tr>
                    <td colspan="{{ count($headers) + ($actions ? 1 : 0) }}" class="px-4 py-6 text-center text-gray-400">{{ $empty }}</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
