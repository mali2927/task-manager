<?php

namespace Tests\Feature;

use App\Models\Space;
use App\Models\Team;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class WorkspaceTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_create_workspace_and_become_owner(): void
    {
        $user = User::factory()->create();
        $this->actingAs($user);

        $workspace = Workspace::create([
            'name' => 'Stark Industries',
            'owner_id' => $user->id,
        ]);
        $workspace->members()->attach($user->id, ['role' => 'owner', 'job_title' => 'CEO']);

        $this->assertDatabaseHas('workspaces', [
            'id' => $workspace->id,
            'name' => 'Stark Industries',
            'slug' => 'stark-industries',
            'owner_id' => $user->id,
        ]);

        $this->assertTrue($user->isWorkspaceAdmin($workspace));
        $this->assertEquals('owner', $user->roleInWorkspace($workspace));
    }

    public function test_workspace_can_have_teams_and_spaces(): void
    {
        $user = User::factory()->create();
        $workspace = Workspace::create([
            'name' => 'Acme Corp',
            'owner_id' => $user->id,
        ]);

        $team = Team::create([
            'workspace_id' => $workspace->id,
            'name' => 'DevOps',
        ]);
        $team->members()->attach($user->id, ['role' => 'lead']);

        $space = Space::create([
            'workspace_id' => $workspace->id,
            'name' => 'Infrastructure',
            'color' => '#3b82f6',
        ]);

        $this->assertCount(1, $workspace->teams);
        $this->assertCount(1, $workspace->spaces);
        $this->assertEquals('DevOps', $team->name);
        $this->assertEquals('Infrastructure', $space->name);
    }

    public function test_owner_and_admin_can_invite_members_to_workspace(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::create(['name' => 'HQ Lab', 'owner_id' => $owner->id]);
        $workspace->members()->attach($owner->id, ['role' => 'owner']);
        $workspace->members()->attach($admin->id, ['role' => 'admin']);
        $workspace->members()->attach($member->id, ['role' => 'member']);

        // Owner can invite
        $this->assertTrue($owner->canInviteWorkspaceMembers($workspace));

        // Admin can invite
        $this->assertTrue($admin->canInviteWorkspaceMembers($workspace));

        // Standard member CANNOT invite
        $this->assertFalse($member->canInviteWorkspaceMembers($workspace));
    }

    public function test_owner_and_admin_can_remove_members_and_member_cannot(): void
    {
        $owner = User::factory()->create();
        $admin = User::factory()->create();
        $member1 = User::factory()->create();
        $member2 = User::factory()->create();

        $workspace = Workspace::create(['name' => 'HQ Lab', 'owner_id' => $owner->id]);
        $workspace->members()->attach($owner->id, ['role' => 'owner']);
        $workspace->members()->attach($admin->id, ['role' => 'admin']);
        $workspace->members()->attach($member1->id, ['role' => 'member']);
        $workspace->members()->attach($member2->id, ['role' => 'member']);

        // Owner can remove member
        $this->assertTrue($owner->canRemoveWorkspaceMember($workspace, $member1));

        // Admin can remove member
        $this->assertTrue($admin->canRemoveWorkspaceMember($workspace, $member1));

        // Admin cannot remove owner
        $this->assertFalse($admin->canRemoveWorkspaceMember($workspace, $owner));

        // Standard member CANNOT remove any member
        $this->assertFalse($member1->canRemoveWorkspaceMember($workspace, $member2));
        $this->assertFalse($member1->canRemoveWorkspaceMember($workspace, $admin));
        $this->assertFalse($member1->canRemoveWorkspaceMember($workspace, $owner));
    }

    public function test_team_manager_livewire_component_enforces_permissions(): void
    {
        $owner = User::factory()->create();
        $member = User::factory()->create();

        $workspace = Workspace::create(['name' => 'HQ Lab', 'owner_id' => $owner->id]);
        $workspace->members()->attach($owner->id, ['role' => 'owner']);
        $workspace->members()->attach($member->id, ['role' => 'member']);

        // Member cannot invite (throws 403)
        $this->actingAs($member);
        \Livewire\Livewire::test(\App\Livewire\Workspace\TeamManager::class, ['workspace' => $workspace])
            ->set('inviteEmail', 'newuser@example.com')
            ->set('inviteRole', 'member')
            ->call('inviteMember')
            ->assertStatus(403);

        // Member cannot remove (throws 403)
        \Livewire\Livewire::test(\App\Livewire\Workspace\TeamManager::class, ['workspace' => $workspace])
            ->call('removeMember', $owner->id)
            ->assertStatus(403);

        // Owner can invite successfully
        $this->actingAs($owner);
        \Livewire\Livewire::test(\App\Livewire\Workspace\TeamManager::class, ['workspace' => $workspace])
            ->set('inviteEmail', 'newuser@example.com')
            ->set('inviteRole', 'member')
            ->call('inviteMember')
            ->assertStatus(200);

        $this->assertDatabaseHas('workspace_invites', [
            'workspace_id' => $workspace->id,
            'email' => 'newuser@example.com',
        ]);
    }

    public function test_software_engineer_muhammad_ali_cannot_remove_members_or_invite(): void
    {
        $owner = User::factory()->create(['email' => 'alex@example.com']);
        $muhammadAli = User::factory()->create(['email' => 'muhammad.ali@example.com', 'job_title' => 'Software Engineer']);
        $targetUser = User::factory()->create(['email' => 'hamza@example.com']);

        $workspace = Workspace::create(['name' => 'MIS', 'owner_id' => $owner->id]);
        $workspace->members()->attach($owner->id, ['role' => 'owner']);
        $workspace->members()->attach($muhammadAli->id, ['role' => 'member', 'job_title' => 'Software Engineer']);
        $workspace->members()->attach($targetUser->id, ['role' => 'member', 'job_title' => 'Software Engineer']);

        $this->assertEquals('member', $muhammadAli->roleInWorkspace($workspace));
        $this->assertFalse($muhammadAli->canRemoveWorkspaceMember($workspace, $targetUser));
        $this->assertFalse($muhammadAli->canInviteWorkspaceMembers($workspace));
        $this->assertFalse($muhammadAli->canUpdateWorkspaceMemberRole($workspace, $targetUser));

        // Livewire UI call test
        $this->actingAs($muhammadAli);
        \Livewire\Livewire::test(\App\Livewire\Workspace\TeamManager::class, ['workspace' => $workspace])
            ->call('removeMember', $targetUser->id)
            ->assertStatus(403);
    }
}
