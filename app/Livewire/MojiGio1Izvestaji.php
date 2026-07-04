<?php

namespace App\Livewire;

use App\Models\GodisnjIzvestaj;
use Livewire\Component;

class MojiGio1Izvestaji extends Component
{
    public function render()
    {
        return view('livewire.moji-gio1-izvestaji', [
            'izvestaji' => GodisnjIzvestaj::forTeam()
                ->orderByDesc('godina')
                ->get(),
        ]);
    }
}
