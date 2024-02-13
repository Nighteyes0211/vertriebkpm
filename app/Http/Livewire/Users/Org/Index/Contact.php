<?php

namespace App\Http\Livewire\Users\Org\Index;

use App\Enum\RoleEnum;
use App\Models\Contact as ModelsContact;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Rappasoft\LaravelLivewireTables\DataTableComponent;
use Rappasoft\LaravelLivewireTables\Views\Column;
use Rappasoft\LaravelLivewireTables\Views\Columns\ButtonGroupColumn;
use Rappasoft\LaravelLivewireTables\Views\Columns\LinkColumn;

class Contact extends DataTableComponent
{
    protected $listeners = ['recordDeletionConfirmed' => 'delete'];

    public function configure(): void
    {
        $this->setPrimaryKey('id');
        $this->setAdditionalSelects(['contacts.id as id']);
        $this->setDefaultSort('created_at', 'desc');
    }

    public function builder(): Builder
    {
        return ModelsContact::available()->when(auth()->user()->is_internal == false, fn($query) => $query->where('is_internal', false) );
    }

    public function columns() : array
    {
        return [
            Column::make('Anrede', 'salutation')
                ->sortable()
                ->searchable(),
            Column::make('Vorname', 'first_name')
                ->sortable()
                ->searchable(),
            Column::make('Nachname', 'last_name')
                ->sortable()
                ->searchable(),
            Column::make('E-Mail Adresse', 'email')
                ->sortable()
                ->searchable(),
            Column::make('Telefon', 'telephone')
                ->sortable()
                ->searchable(),
            Column::make('Mobilnummer', 'mobile')
                ->sortable()
                ->searchable(),
            Column::make('Ort', 'location')
                ->sortable()
                ->searchable(),
            Column::make('Einrichtungen', 'id')
                ->format(function ($value, $row, Column $column) { return $row->facilities()->available()->get()->pluck('name')->join(' ,'); })
                ->sortable()
                ->searchable(),
            Column::make('Zugeordneter Benutzer', 'user_id')
                ->format(function ($value, $row, Column $column) { return $row->user->fullName(); })
                ->sortable()
                ->searchable(),
            ButtonGroupColumn::make('Aktion')
                ->attributes(function ($row) {
                    return [
                        'class' => 'space-x-2',
                    ];
                })
                ->buttons([
                    LinkColumn::make('Edit') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Bearbeiten')
                        ->location(fn($row) => route('organization.contact.edit', $row->id))
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('contact:edit') ? 'd-none' : '';
                            return [
                                'class' => "btn {$hideClass} btn-sm btn-info",
                            ];
                        }),
                    LinkColumn::make('Delete') // make() has no effect in this case but needs to be set anyway
                        ->title(fn ($row) => 'Löschen')
                        ->location(fn($row) => "#")
                        ->attributes(function ($row) {
                            $hideClass = !Auth::user()->can('contact:delete') ? 'd-none' : '';
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

        $record = ModelsContact::find($record_id);
        $record->softDelete();

    }
}
