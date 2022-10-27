<?php

namespace App\Http\Livewire\Pages;

use App\Models\Obj;
use Illuminate\Http\Request;
use Livewire\Component;

class Files extends Component
{
    public function render(Request $request)
    {
        $object = Obj::with('children.objectable', 'ancestorsAndSelf.objectable')->forCurrentTeam()
            ->where('uuid', $request->get('uuid', Obj::forCurrentTeam()->whereNull('parent_id')->first()->uuid))
            ->firstOrFail();

        return view('livewire.pages.files', with([
            'object' => $object,
            'ancestors' => $object->ancestorsAndSelf()->breadthFirst()->get()
        ]));
    }
}
