<?php

namespace App\Livewire;

use App\Models\Cv;
use Livewire\Component;
use Livewire\WithFileUploads;

class CvManager extends Component
{
    use WithFileUploads;

    public string $name = '';
    public string $description = '';
    public $file = null;
    public bool $showForm = false;

    protected function rules(): array
    {
        return [
            'name'        => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string', 'max:500'],
            'file'        => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
        ];
    }

    public function saveCV(): void
    {
        $this->validate();

        try {
            $path = $this->file->storeAs(
                'cvs/' . auth()->id(),
                $this->file->getClientOriginalName(),
                'private'
            );

            Cv::create([
                'user_id'     => auth()->id(),
                'name'        => $this->name,
                'description' => $this->description ?: null,
                'file_path'   => $path,
                'file_name'   => $this->file->getClientOriginalName(),
                'file_size'   => $this->file->getSize(),
            ]);

            $this->reset(['name', 'description', 'file', 'showForm']);
            $this->dispatch('notify', message: 'CV uploaded successfully.');
        } catch (\Throwable $e) {
            $this->addError('file', 'Save failed: ' . $e->getMessage());
        }
    }

    public function delete(int $id): void
    {
        $cv = Cv::where('id', $id)->where('user_id', auth()->id())->firstOrFail();
        $cv->deleteFile();
        $cv->delete();
        $this->dispatch('notify', message: 'CV deleted.');
    }

    public function render()
    {
        return view('livewire.cv-manager', [
            'cvs' => Cv::where('user_id', auth()->id())->latest()->get(),
        ]);
    }
}
