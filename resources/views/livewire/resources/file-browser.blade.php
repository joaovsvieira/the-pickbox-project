<div>
    <!-- header -->
    <div class="flex flex-wrap items-center justify-between mb-4">
        <!-- search -->
        <div class="flex-grow sm:mr-3 mt-4 sm:mt-0 order-3 sm:order-1 w-full sm:w-auto">
            <input type="search" placeholder="Search files and folders" class="w-full px-3 h-12 border-2 border-gray-200 rounded-lg">
        </div>

        <!-- buttons -->
        <div class="order-2">
            <div>
                <button wire:click="$set('creatingNewFolder', true)" class="bg-gray-200 px-6 h-12 rounded-lg mr-2">
                    New folder
                </button>
                <button wire:click="$set('showingFileUploadForm', true)" class="bg-blue-600 text-white px-6 h-12 rounded-lg font-bold">
                    Upload files
                </button>
            </div>
        </div>
    </div>

    <!-- content -->
    <div class="border-2 border-gray-200 rounded-lg">
        <!-- breadcrumb -->
        <div class="py-2 px-3">
            <div class="flex items-center">
                @foreach($ancestors as $ancestor)
                    <a href="{{ route('files', ['uuid' => $ancestor->uuid]) }}" class="font-bold text-gray-400">
                        {{ $ancestor->objectable->name }}
                    </a>

                    @if(!$loop->last)
                        <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" class="w-5 h-5 text-gray-300 mx-1">
                            <path fill-rule="evenodd" d="M16.28 11.47a.75.75 0 010 1.06l-7.5 7.5a.75.75 0 01-1.06-1.06L14.69 12 7.72 5.03a.75.75 0 011.06-1.06l7.5 7.5z" clip-rule="evenodd" />
                        </svg>
                    @endif
                @endforeach
            </div>
        </div>

        <!-- contents -->
        <div class="overflow-auto">
            <table class="w-full">
                <thead class="bg-gray-100">
                <tr>
                    <th class="text-left py-2 px-3">Name</th>
                    <th class="text-left py-2 px-3 w-2/12">Size</th>
                    <th class="text-left py-2 px-3 w-2/12">Created</th>
                    <th class="py-2 w-2/12"></th>
                </tr>
                </thead>

                <tbody>
                    @if($creatingNewFolder)
                        <tr class="border-gray-100 border-b-2 hover:bg-gray-100">
                            <td class="p-3">
                                <form wire:submit.prevent="createFolder" class="flex items-center">
                                    <input wire:model.defer="newFolderState.name" type="text" class="w-full px-3 h-10 border-2 border-gray-200 rounded-lg mr-2">

                                    <button type="submit" class="bg-blue-600 text-white px-6 h-10 rounded-lg mr-2">
                                        Create
                                    </button>

                                    <button wire:click="$set('creatingNewFolder', false)" class="bg-gray-200 px-6 h-10 rounded-lg">
                                        Cancel
                                    </button>
                                </form>
                            </td>
                            <td></td>
                            <td></td>
                            <td></td>
                        </tr>
                    @endif

                    @foreach($object->children as $child)
                        <tr class="@if(!$loop->last) border-gray-100 border-b-2 @endif hover:bg-gray-100">
                            <td class="py-2 px-3 flex items-center">
                                @if($child->objectable_type == 'file')
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m2.25 0H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                                    </svg>
                                @else
                                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-6 h-6 text-blue-700 mr-2">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12.75V12A2.25 2.25 0 014.5 9.75h15A2.25 2.25 0 0121.75 12v.75m-8.69-6.44l-2.12-2.12a1.5 1.5 0 00-1.061-.44H4.5A2.25 2.25 0 002.25 6v12a2.25 2.25 0 002.25 2.25h15A2.25 2.25 0 0021.75 18V9a2.25 2.25 0 00-2.25-2.25h-5.379a1.5 1.5 0 01-1.06-.44z" />
                                    </svg>
                                @endif

                                @if($renamingObject === $child->id)
                                    <form wire:submit.prevent="renameObject" class="flex items-center ml-2 flex-grow">
                                        <input wire:model.defer="renamingObjectState.name" type="text" class="w-full px-3 h-10 border-2 border-gray-200 rounded-lg mr-2">

                                        <button type="submit" class="bg-blue-600 text-white px-6 h-10 rounded-lg mr-2">
                                            Rename
                                        </button>

                                        <button wire:click="$set('renamingObject', null)" class="bg-gray-200 px-6 h-10 rounded-lg">
                                            Cancel
                                        </button>
                                    </form>
                                @else
                                    @if($child->objectable_type == 'file')
                                        <span wire:click="download({{ $child->objectable }})" class="cursor-pointer">{{ $child->objectable->name }}</span>
                                    @else
                                        <a href="{{ route('files', ['uuid' => $child->uuid]) }}" class="p-2 font-bold text-blue-700 flex-grow">
                                            {{ $child->objectable->name }}
                                        </a>
                                    @endif
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                @if($child->objectable_type == 'file')
                                    {{ $child->objectable->sizeForHumans() }}
                                @else
                                    &mdash;
                                @endif
                            </td>
                            <td class="py-2 px-3">
                                {{ $child->created_at }}
                            </td>
                            <td class="py-2 px-3">
                                <div class="flex justify-end items-center">
                                    <ul class="flex items-center">
                                        <li class="mr-4">
                                            <button wire:click="$set('renamingObject', {{ $child->id }})" class="text-gray-400 font bold">Rename</button>
                                        </li>
                                        <li>
                                            <button wire:click="$set('confirmingObjectDeletion', {{ $child->id }})" class="text-red-400 font bold">Delete</button>
                                        </li>
                                    </ul>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        @if($object->children->count() === 0)
            <div class="p-3 text-gray-700">
                This folder is empty
            </div>
        @endif
    </div>

    <!-- deletion modal -->
    <x-jet-dialog-modal wire:model="confirmingObjectDeletion">
        <x-slot name="title">
            {{ __('Delete') }}
        </x-slot>

        <x-slot name="content">
            {{ __('Are you sure you want to delete this?') }}
        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="$set('confirmingObjectDeletion', null)" wire:loading.attr="disabled">
                {{ __('Nevermind') }}
            </x-jet-secondary-button>

            <x-jet-danger-button wire:click="deleteObject" class="ml-2">
                {{ __('Delete') }}
            </x-jet-danger-button>
        </x-slot>
    </x-jet-dialog-modal>

    <!-- upload modal -->
    <x-jet-modal wire:model="showingFileUploadForm">
        <div
            wire:ignore
            class="m-3 border-dashed border-2"
            x-data="{
                initFilepond () {
                    const pond = FilePond.create(this.$refs.filepond, {
                        onprocessfile: (error, file) => {
                            pond.removeFile(file.id)

                            if (pond.getFiles().length === 0) {
                                @this.set('showingFileUploadForm', false)
                            }
                        },
                        allowRevert: false,
                        server: {
                            process: (fieldName, file, metadata, load, error, progress, abort, transfer, options) => {
                                @this.upload('upload', file, load, error, progress)
                            }
                        }
                    })
                }
            }"
            x-init="initFilepond"
        >
            <div>
                <input type="file" x-ref="filepond" multiple>
            </div>
        </div>
    </x-jet-modal>
</div>
