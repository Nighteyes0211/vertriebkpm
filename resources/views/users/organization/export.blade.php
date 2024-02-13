<x-layouts.dashboard.app title="Export">

    <x-dayone.page.header>
        <x-slot name="left">
            <x-dayone.page.title>Export</x-dayone.page.title>
        </x-slot>
    </x-dayone.page.header>


    <div class="d-flex gap-3">
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'facility']) }}">Export Facility</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'contact']) }}">Export Contact</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'product']) }}">Export Product</x-bootstrap.button>
        <x-bootstrap.button size="lg" href="{{ route('organization.export.data', ['data' => 'appointment']) }}">Export Appointment</x-bootstrap.button>
    </div>

</x-layouts.dashboard.app>
