<?php

namespace App\Livewire\Ai;

use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\Workspace;
use App\Services\GeminiService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Url;
use Livewire\Component;

#[Layout('layouts.app')]
class AiAssistant extends Component
{
    public Workspace $workspace;
    public string $userQuery = '';
    public bool $isThinking = false;

    #[Url(as: 'chat')]
    public ?int $activeConversationId = null;

    public function mount(?Workspace $workspace = null): void
    {
        if (!$workspace || !$workspace->id) {
            $this->workspace = Auth::user()->workspaces()->first() ?? Workspace::first();
        } else {
            $this->workspace = $workspace;
        }

        if ($this->workspace && Auth::user()?->isWorkspaceRequester($this->workspace)) {
            $this->redirect(route('workspace.tickets.my', ['workspace' => $this->workspace->slug]), navigate: true);
            return;
        }

        // If activeConversationId is not set, load the most recent conversation or stay on new chat
        if ($this->activeConversationId) {
            $convo = AiConversation::where('id', $this->activeConversationId)
                ->where('workspace_id', $this->workspace->id)
                ->where('user_id', Auth::id())
                ->first();

            if (!$convo) {
                $this->activeConversationId = null;
            }
        }
    }

    public function startNewChat(): void
    {
        $this->activeConversationId = null;
        $this->userQuery = '';
    }

    public function selectConversation(int $conversationId): void
    {
        $this->activeConversationId = $conversationId;
        $this->userQuery = '';
    }

    public function deleteConversation(int $conversationId): void
    {
        $convo = AiConversation::where('id', $conversationId)
            ->where('workspace_id', $this->workspace->id)
            ->where('user_id', Auth::id())
            ->first();

        if ($convo) {
            $convo->delete();
        }

        if ($this->activeConversationId === $conversationId) {
            $latest = AiConversation::where('workspace_id', $this->workspace->id)
                ->where('user_id', Auth::id())
                ->latest()
                ->first();

            $this->activeConversationId = $latest?->id;
        }
    }

    public function askQuickQuery(string $query): void
    {
        $this->userQuery = $query;
        $this->submitQuery();
    }

    public function submitQuery(): void
    {
        $prompt = trim($this->userQuery);
        if (empty($prompt)) return;

        $user = Auth::user();

        // 1. Ensure or create conversation
        if (!$this->activeConversationId) {
            $title = Str::limit($prompt, 42, '...');
            $conversation = AiConversation::create([
                'workspace_id' => $this->workspace->id,
                'user_id' => $user->id,
                'title' => $title,
            ]);
            $this->activeConversationId = $conversation->id;
        } else {
            $conversation = AiConversation::where('id', $this->activeConversationId)
                ->where('workspace_id', $this->workspace->id)
                ->where('user_id', $user->id)
                ->first();

            if (!$conversation) {
                $conversation = AiConversation::create([
                    'workspace_id' => $this->workspace->id,
                    'user_id' => $user->id,
                    'title' => Str::limit($prompt, 42, '...'),
                ]);
                $this->activeConversationId = $conversation->id;
            }
        }

        // 2. Save User Message
        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => $prompt,
        ]);

        $this->userQuery = '';
        $this->isThinking = true;

        try {
            $gemini = app(GeminiService::class);
            $answer = $gemini->queryTasks($this->workspace, $prompt, $user);

            // 3. Save Assistant Message
            AiMessage::create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => $answer,
            ]);

            $conversation->touch(); // Update updated_at
        } catch (\Throwable $e) {
            AiMessage::create([
                'ai_conversation_id' => $conversation->id,
                'role' => 'assistant',
                'content' => "I encountered an error processing your query: " . $e->getMessage(),
            ]);
        } finally {
            $this->isThinking = false;
        }
    }

    public function render()
    {
        $conversations = AiConversation::where('workspace_id', $this->workspace->id)
            ->where('user_id', Auth::id())
            ->latest('updated_at')
            ->get();

        $activeConversation = $this->activeConversationId
            ? AiConversation::with('messages')->find($this->activeConversationId)
            : null;

        $messages = $activeConversation ? $activeConversation->messages : collect();
        $user = Auth::user();
        $userTier = $user ? $user->getAiHierarchyTier($this->workspace) : 'member';
        $userLabel = $user ? $user->getAiHierarchyLabel($this->workspace) : 'Member';

        return view('livewire.ai.ai-assistant', [
            'conversations' => $conversations,
            'activeConversation' => $activeConversation,
            'messages' => $messages,
            'userTier' => $userTier,
            'userLabel' => $userLabel,
        ]);
    }
}
