<?php

namespace App\Http\Livewire\Users\Org\Index;

use App\Models\FacilityType as ModelsFacilityType;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class FacilityType extends DataTableComponent
{
    protected $listeners = ['recordDeletionConfirmed' => 'delete'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setAdditionalSelects(['facility_types.id as id']);
        $this->setDefaultSort('facility_types.name', 'asc');
    }

    public function builder(): Builder
    {
        return ModelsFacilityType::available();
    }

    public function columns() : array
    {
        return [
            Column::make('Name')
                ->sortable()
                ->searchable(),
            ButtonGroupColumn::make('Aktionen')
                ->buttons([
                    LinkColumn::make('Edit') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Bearbeiten')
                        ->location(fn($row) => route('organization.facility-type.edit', $row))
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('facility-type:edit') ? 'd-none' : '';
                            return [
                                'class' => "btn {$hideClass} btn-sm btn-info",
                            ];
                        }),
                    LinkColumn::make('Delete') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Löschen')
                        ->location(fn($row) => "#")
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('facility-type:delete') ? 'd-none' : '';
                            return [
                                'class' => "btn {$hideClass} btn-sm btn-danger",
                                'wire:click' => "deleteConfirmation({$row->id})"
                            ];
                        }),
                ]),
        ];
    }


    public function deleteConfirmation($record_id)
    {
        $this->emit('confirmDelete', $record_id);
        $this->emit('openModal', 'delete-modal');
    }

    public function delete(int $record_id)
    {

        $record = ModelsFacilityType::find($record_id);
        $record->softDelete();

    }
}
