<?php

namespace App\Livewire\AccessRequests;

use App\Models\AccessRequest;
use App\Models\User;
use App\Models\Workspace;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.auth')]
class RequestAccess extends Component
{
    public string $name = '';
    public string $email = '';
    public ?int $workspaceId = null;
    public string $department = '';
    public string $reason = '';

    public bool $isSubmitted = false;

    public function mount(): void
    {
        $defaultWorkspace = Workspace::first();
        if ($defaultWorkspace) {
            $this->workspaceId = $defaultWorkspace->id;
        }
    }

    public function submit(): void
    {
        $this->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'department' => 'nullable|string|max:100',
            'reason' => 'required|string|min:10|max:1000',
            'workspaceId' => 'nullable|exists:workspaces,id',
        ]);

        $email = strtolower(trim($this->email));

        if (User::where('email', $email)->exists()) {
            $this->addError('email', 'An account with this email already exists. Please sign in instead.');
            return;
        }

        $existingPending = AccessRequest::where('email', $email)
            ->where('status', 'pending')
            ->exists();

        if ($existingPending) {
            $this->addError('email', 'You already have a pending access request. Our administrators will contact you shortly.');
            return;
        }

        AccessRequest::create([
            'name' => trim($this->name),
            'email' => $email,
            'workspace_id' => $this->workspaceId,
            'department' => trim($this->department) ?: null,
            'reason' => trim($this->reason),
            'status' => 'pending',
            'assigned_role' => 'member',
        ]);

        $this->isSubmitted = true;
    }

    public function render()
    {
        $workspaces = Workspace::all();

        return view('livewire.access-requests.request-access', [
            'workspaces' => $workspaces,
        ]);
    }
}
