<x-layouts.dashboard.app title="Export">

    <x-dayone.page.header>
        <x-slot name="left">
            <x-dayone.page.title>Export</x-dayone.page.title>
        </x-slot>
    </x-dayone.page.header>


    <div class="d-flex gap-3">
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'facility']) }}">Export Einrichtungen</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'contact']) }}">Export Kontakte</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'product']) }}">Export Produkt</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'appointment']) }}">Export Termine</x-bootstrap.button>
    </div>

</x-layouts.dashboard.app>
