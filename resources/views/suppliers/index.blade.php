<div class="flex justify-between mb-4">
    <a href="{{ route('suppliers.export') }}" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700">Export to Excel</a>
    <form action="{{ route('suppliers.import') }}" method="POST" enctype="multipart/form-data" class="flex items-center space-x-2">
        @csrf
        <input type="file" name="file" accept=".xlsx,.xls" required class="border rounded p-1">
        <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded hover:bg-blue-700">Import from Excel</button>
    </form>
</div>
