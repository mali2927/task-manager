<?php

namespace Tests\Feature;

use App\Livewire\Ai\AiAssistant;
use App\Models\AiConversation;
use App\Models\AiMessage;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class AiAssistantTest extends TestCase
{
    use RefreshDatabase;

    public function test_ai_assistant_page_renders_with_chat_history(): void
    {
        $user = User::factory()->create(['name' => 'Alex Rivers', 'job_title' => 'Founder & CEO']);
        $workspace = Workspace::create(['name' => 'Acme Labs', 'owner_id' => $user->id]);

        $conversation = AiConversation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'title' => 'Project Kickoff Consultation',
        ]);

        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'user',
            'content' => 'What is the sprint status?',
        ]);

        AiMessage::create([
            'ai_conversation_id' => $conversation->id,
            'role' => 'assistant',
            'content' => 'All sprint milestones are currently on track.',
        ]);

        $this->actingAs($user);

        Livewire::test(AiAssistant::class, ['workspace' => $workspace])
            ->assertSee('Project Kickoff Consultation')
            ->assertSee('Chat History')
            ->assertSee('New Chat');
    }

    public function test_ai_assistant_creates_new_chat_and_persists_messages(): void
    {
        $user = User::factory()->create(['name' => 'Khubaib Ahmed', 'job_title' => 'Director MIS']);
        $workspace = Workspace::create(['name' => 'MIS Hub', 'owner_id' => $user->id]);

        $this->actingAs($user);

        Livewire::test(AiAssistant::class, ['workspace' => $workspace])
            ->set('userQuery', 'Who is working on the Admission Project?')
            ->call('submitQuery')
            ->assertSet('userQuery', '');

        $this->assertDatabaseHas('ai_conversations', [
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
        ]);

        $convo = AiConversation::where('workspace_id', $workspace->id)->first();
        $this->assertNotNull($convo);

        $this->assertDatabaseHas('ai_messages', [
            'ai_conversation_id' => $convo->id,
            'role' => 'user',
            'content' => 'Who is working on the Admission Project?',
        ]);

        $this->assertDatabaseHas('ai_messages', [
            'ai_conversation_id' => $convo->id,
            'role' => 'assistant',
        ]);
    }

    public function test_ai_assistant_can_delete_conversation(): void
    {
        $user = User::factory()->create(['name' => 'Sarah Chen', 'job_title' => 'Frontend Lead']);
        $workspace = Workspace::create(['name' => 'Acme Labs', 'owner_id' => $user->id]);

        $conversation = AiConversation::create([
            'workspace_id' => $workspace->id,
            'user_id' => $user->id,
            'title' => 'Temporary Exploration',
        ]);

        $this->actingAs($user);

        Livewire::test(AiAssistant::class, ['workspace' => $workspace])
            ->call('deleteConversation', $conversation->id);

        $this->assertDatabaseMissing('ai_conversations', [
            'id' => $conversation->id,
        ]);
    }

    public function test_ai_assistant_displays_role_hierarchy_badge(): void
    {
        $director = User::factory()->create(['name' => 'Khubaib Ahmed', 'job_title' => 'Director MIS']);
        $engineer = User::factory()->create(['name' => 'Hamza Shoulat', 'job_title' => 'Software Engineer']);

        $workspace = Workspace::create(['name' => 'MIS Labs', 'owner_id' => $director->id]);
        $workspace->members()->attach($engineer->id, ['role' => 'member', 'job_title' => 'Software Engineer']);

        // Director view
        $this->actingAs($director);
        Livewire::test(AiAssistant::class, ['workspace' => $workspace])
            ->assertSee('Director / Executive')
            ->assertSee('Full Access');

        // Engineer view
        $this->actingAs($engineer);
        Livewire::test(AiAssistant::class, ['workspace' => $workspace])
            ->assertSee('Member')
            ->assertSee('Personal Scope');
    }
}
