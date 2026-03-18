<div>
    {{-- Upload button --}}
    <div class="flex justify-between items-center mb-5">
        <p class="text-sm text-gray-500">Upload PDF, DOC or DOCX — max 5 MB per file.</p>
        <button wire:click="$toggle('showForm')"
                class="inline-flex items-center gap-1.5 px-4 py-2 bg-indigo-600 text-white text-sm font-medium rounded-lg hover:bg-indigo-700 transition">
            + Upload CV
        </button>
    </div>

    {{-- Upload form --}}
    @if($showForm)
    <div class="bg-white rounded-xl border border-indigo-100 shadow-sm p-6 mb-6">
        <h3 class="text-sm font-semibold text-gray-700 mb-4">New CV Version</h3>
        <form @submit.prevent class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Name *</label>
                    <input wire:model="name" type="text" placeholder="e.g. Senior DevOps Engineer"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Description</label>
                    <input wire:model="description" type="text" placeholder="e.g. Focused on Kubernetes & CI/CD"
                           class="w-full rounded-lg border-gray-300 text-sm shadow-sm focus:ring-indigo-500 focus:border-indigo-500" />
                    @error('description') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File *</label>
                <input wire:model="file" type="file" accept=".pdf,.doc,.docx"
                       class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-medium file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100 cursor-pointer" />
                <div wire:loading wire:target="file" class="text-xs text-gray-400 mt-1">Uploading...</div>
                @error('file') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
            </div>

            <div class="flex gap-3 justify-end">
                <button type="button" wire:click="$toggle('showForm')"
                        class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                <button type="button" @click="$wire.saveCV()"
                        class="px-4 py-2 text-sm font-medium text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 transition">
                    <span wire:loading.remove wire:target="saveCV">Save CV</span>
                    <span wire:loading wire:target="saveCV">Saving...</span>
                </button>
            </div>
        </form>
    </div>
    @endif

    {{-- CV list --}}
    @if($cvs->isEmpty())
        <div class="bg-white rounded-xl border border-dashed border-gray-300 py-16 text-center">
            <p class="text-gray-400 text-sm">No CVs uploaded yet.</p>
            <p class="text-gray-300 text-xs mt-1">Upload your first CV to start tracking which version you send to each job.</p>
        </div>
    @else
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
            @foreach($cvs as $cv)
                <div class="bg-white rounded-xl border border-gray-100 shadow-sm p-5 flex flex-col gap-3">
                    <div class="flex items-start gap-3">
                        <div class="shrink-0 w-10 h-10 rounded-lg bg-red-50 flex items-center justify-center">
                            <svg class="w-5 h-5 text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                        </div>
                        <div class="flex-1 min-w-0">
                            <p class="font-semibold text-gray-800 text-sm truncate">{{ $cv->name }}</p>
                            @if($cv->description)
                                <p class="text-xs text-gray-500 mt-0.5">{{ $cv->description }}</p>
                            @endif
                            <p class="text-xs text-gray-400 mt-1">{{ $cv->file_name }} · {{ $cv->file_size_formatted }}</p>
                        </div>
                    </div>

                    <div class="text-xs text-gray-400">
                        Uploaded {{ $cv->created_at->diffForHumans() }}
                        @if($cv->applications_count ?? $cv->applications()->count())
                            · Used in <span class="font-medium text-gray-600">{{ $cv->applications()->count() }}</span> application(s)
                        @endif
                    </div>

                    <div class="flex gap-2 pt-1 border-t border-gray-50">
                        <a href="{{ route('cvs.download', $cv) }}"
                           class="flex-1 text-center py-1.5 text-xs font-medium text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition">
                            Download
                        </a>
                        <button wire:click="delete({{ $cv->id }})"
                                wire:confirm="Delete this CV? Applications using it will not be deleted."
                                class="flex-1 text-center py-1.5 text-xs font-medium text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition">
                            Delete
                        </button>
                    </div>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Notification toast --}}
    <div x-data="{ show: false, msg: '' }"
         x-on:notify.window="msg = $event.detail.message; show = true; setTimeout(() => show = false, 3000)"
         x-show="show" x-transition
         class="fixed bottom-5 right-5 bg-gray-800 text-white text-sm px-4 py-2 rounded-lg shadow-lg z-50">
        <span x-text="msg"></span>
    </div>
</div>
