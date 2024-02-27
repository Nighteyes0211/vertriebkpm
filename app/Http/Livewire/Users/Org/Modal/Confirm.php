<?php

namespace App\Http\Livewire\Users\Org\Modal;

use Livewire\Component;

class Confirm extends Component
{

    public $modalId = 'delete-modal';

    /**
     * Parent
     */
    public $data, $confirmationEvent = 'confirmed', $message, $eventListener = 'confirmation';

    protected function getListeners()
    {
        return [
            $this->eventListener => 'setData',
        ];
    }

    public function setData($data)
    {
        $this->data = $data;
        $this->emit('openModal', $this->modalId);
    }

    public function render()
    {
        return view('livewire.users.org.modal.confirm');
    }

    public function confirmed()
    {
        $this->emit($this->confirmationEvent, $this->data);
        $this->emit('closeModal', $this->modalId);
    }
}
