<x-layouts.dashboard.app title="Export">

    <x-dayone.page.header>
        <x-slot name="left">
            <x-dayone.page.title>Export</x-dayone.page.title>
        </x-slot>
    </x-dayone.page.header>


    <div class="d-flex gap-3">
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'facility']) }}">Einrichtungen exportieren</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'contact']) }}">Kontakte exportieren</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'product']) }}">Produkte exportieren</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'appointment']) }}">Termine exportieren</x-bootstrap.button>
    </div>

</x-layouts.dashboard.app>
