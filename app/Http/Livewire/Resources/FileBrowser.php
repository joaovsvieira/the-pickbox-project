<?php

namespace App\Http\Livewire\Resources;

use App\Models\File;
use App\Models\Obj;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;
use Livewire\WithFileUploads;

class FileBrowser extends Component
{
    use WithFileUploads;
    use AuthorizesRequests;

    public $upload;
    public $object;
    public $ancestors;
    public $renamingObject;
    public $creatingNewFolder = false;
    public $showingFileUploadForm = false;
    public $confirmingObjectDeletion;

    public $newFolderState = [
        'name' => ''
    ];

    public $renamingObjectState = [
        'name' => ''
    ];

    public function deleteObject()
    {
        Obj::forCurrentTeam()->find($this->confirmingObjectDeletion)->delete();

        $this->confirmingObjectDeletion = null;

        $this->object = $this->object->fresh();
    }

    public function updatedUpload($upload)
    {
        $object = $this->currentTeam->objects()->make(['parent_id' => $this->object->id]);

        $object->objectable()->associate(
            $this->currentTeam->files()->create([
                'name' => $upload->getClientOriginalName(),
                'size' => $upload->getSize(),
                'path' => $upload->storePublicly(
                    'files', [
                        'disk' => 'local'
                    ])
            ])
        );

        $object->save();

        $this->object = $this->object->fresh();
    }

    public function renameObject()
    {
        $this->validate([
            'renamingObjectState.name' => 'required|max:255'
        ]);

        Obj::forCurrentTeam()->find($this->renamingObject)->objectable->update($this->renamingObjectState);

        $this->object = $this->object->fresh();

        $this->renamingObject = null;
    }

    public function updatingRenamingObject($id)
    {
        if ($id === null) {
            $this->renamingObjectState = [
                'name' => null
            ];
        }

        if ($object = Obj::forCurrentTeam()->find($id)) {
            $this->renamingObjectState = [
                'name' => $object->objectable->name
            ];
        }
    }

    public function createFolder()
    {
        $this->validate([
            'newFolderState.name' => 'required|max:255'
        ]);

        $object = $this->currentTeam->objects()->make(['parent_id' => $this->object->id]);
        $object->objectable()->associate($this->currentTeam->folders()->create($this->newFolderState));
        $object->save();

        $this->newFolderState = [
            'name' => ''
        ];

        $this->object = $this->object->fresh();
    }

    public function getCurrentTeamProperty()
    {
        return auth()->user()->currentTeam;
    }

    public function download(File $file)
    {
        $this->authorize('download', $file);
        return Storage::disk('local')->download($file->path, $file->name);
    }

    public function mount($object, $ancestors)
    {
        $this->object = $object;
        $this->ancestors = $ancestors;
    }

    public function render()
    {
        return view('livewire.resources.file-browser');
    }
}
