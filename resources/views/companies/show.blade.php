@if($company->holding)
    <div class="mb-2">
        <strong>Holding:</strong> {{ $company->holding->name }}
    </div>
@endif
