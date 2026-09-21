<?php

namespace App\Livewire\Tickets;

use App\Models\Team;
use App\Models\TicketCategory;
use App\Models\Workspace;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\Layout;
use Livewire\Component;

#[Layout('layouts.app')]
class TicketCategoryManager extends Component
{
    public Workspace $workspace;

    // Create / Edit Modal
    public bool $showCategoryModal = false;
    public ?int $editingCategoryId = null;
    public string $name = '';
    public string $description = '';
    public ?int $defaultTeamId = null;

    public ?string $feedbackMessage = null;

    public function mount(Workspace $workspace): void
    {
        $this->workspace = $workspace;
        abort_unless(Auth::user()->canManageTicketCategories($this->workspace), 403, 'Unauthorized. Access restricted to administrators.');
    }

    public function openCreateModal(): void
    {
        $this->editingCategoryId = null;
        $this->name = '';
        $this->description = '';
        $this->defaultTeamId = null;
        $this->showCategoryModal = true;
    }

    public function openEditModal(int $id): void
    {
        $category = TicketCategory::where('workspace_id', $this->workspace->id)->findOrFail($id);
        $this->editingCategoryId = $category->id;
        $this->name = $category->name;
        $this->description = $category->description ?? '';
        $this->defaultTeamId = $category->default_team_id;
        $this->showCategoryModal = true;
    }

    public function saveCategory(): void
    {
        abort_unless(Auth::user()->canManageTicketCategories($this->workspace), 403);

        $this->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
            'defaultTeamId' => 'nullable|exists:teams,id',
        ]);

        if ($this->editingCategoryId) {
            $cat = TicketCategory::where('workspace_id', $this->workspace->id)->findOrFail($this->editingCategoryId);
            $cat->update([
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'default_team_id' => $this->defaultTeamId,
            ]);
            $this->feedbackMessage = "Category updated successfully.";
        } else {
            TicketCategory::create([
                'workspace_id' => $this->workspace->id,
                'name' => trim($this->name),
                'description' => trim($this->description) ?: null,
                'default_team_id' => $this->defaultTeamId,
            ]);
            $this->feedbackMessage = "Category created successfully.";
        }

        $this->showCategoryModal = false;
    }

    public function deleteCategory(int $id): void
    {
        abort_unless(Auth::user()->canManageTicketCategories($this->workspace), 403);

        $cat = TicketCategory::where('workspace_id', $this->workspace->id)->findOrFail($id);
        $cat->delete();
        $this->feedbackMessage = "Category deleted.";
    }

    public function render()
    {
        $categories = $this->workspace->ticketCategories()
            ->with(['defaultTeam', 'tickets'])
            ->get();

        $teams = $this->workspace->teams()->get();

        return view('livewire.tickets.ticket-category-manager', [
            'categories' => $categories,
            'teams' => $teams,
        ]);
    }
}
