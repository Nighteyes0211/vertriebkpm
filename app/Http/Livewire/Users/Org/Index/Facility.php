<?php

namespace App\Http\Livewire\Users\Org\Index;

use App\Enum\Facility\StatusEnum;
use App\Enum\RoleEnum;
use App\Models\FacilityStatus;
use App\Models\Facilty;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;
use Rappasoft\LaravelLivewireTables\Views\Filters\MultiSelectFilter;

class Facility extends DataTableComponent
{
    protected $listeners = ['recordDeletionConfirmed' => 'delete'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setAdditionalSelects(['facilties.id as id']);
        $this->setDefaultSort('facilties.created_at', 'desc');
    }

    public function builder(): Builder
    {
        return Facilty::available()->when(auth()->user()->is_internal == false, fn($query) => $query->where('is_internal', false));
    }

    public function columns() : array
    {
        return [
            Column::make('Einrichtungs','name')
                ->sortable()
                ->searchable(),
            Column::make('Telefon','telephone')
                ->sortable()
                ->searchable(),
            Column::make('E-mail','email')
                ->sortable()
                ->searchable(),
            Column::make('Status', 'name')
                ->format(function ($value, $row, Column $column) {
                    $text = '';
                    foreach (FacilityStatus::available()->get() as $status) {
                        if ($row->statuses()->where('facility_statuses.id', $status->id)->exists()) {
                            $text .= "<span class='badge $status->color m-1'>$status->name</span>";
                        } else {
                            $text .= "<span class='badge badge-light m-1'>$status->name</span>";
                        }
                    }
                    return $text;
                })
                ->html()
                ->searchable(),
            ButtonGroupColumn::make('Aktionen')
                ->buttons([
                    LinkColumn::make('Edit') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Bearbeiten')
                        ->location(fn($row) => route('organization.facility.edit', $row))
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('facility:edit') ? 'd-none' : '';
                            return [
                                'class' => "btn {$hideClass} btn-sm btn-info",
                            ];
                        }),
                    LinkColumn::make('Delete') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Löschen')
                        ->location(fn($row) => "#")
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('facility:delete') ? 'd-none' : '';
                            return [
                                'class' => "btn {$hideClass} btn-sm btn-danger",
                                'wire:click' => "deleteConfirmation({$row->id})"
                            ];
                        }),
                ]),
        ];
    }

    public function filters(): array
    {
        return [
            MultiSelectFilter::make('Status')
                ->options( FacilityStatus::available()->get()->pluck('name', 'name')->toArray() )
                ->filter(function ($query, $value) {
                    return $query->whereHas('statuses', function ($query) use ($value) {
                        return $query->whereIn('facility_statuses.name', $value);
                    });
                })
        ];
    }

    public function deleteConfirmation($record_id)
    {
        $this->emit('confirmDelete', $record_id);
        $this->emit('openModal', 'delete-modal');
    }

    public function delete(int $record_id)
    {

        $record = Facilty::find($record_id);
        $record->softDelete();

    }
}
