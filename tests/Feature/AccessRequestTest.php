<?php

namespace Tests\Feature;

use App\Livewire\AccessRequests\AccessRequestManager;
use App\Livewire\AccessRequests\RequestAccess;
use App\Mail\AccessRequestApprovedMail;
use App\Mail\AccessRequestRejectedMail;
use App\Models\AccessRequest;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\TestCase;

class AccessRequestTest extends TestCase
{
    use RefreshDatabase;

    protected Workspace $workspace;
    protected User $admin;
    protected User $member;

    protected function setUp(): void
    {
        parent::setUp();

        $this->admin = User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@example.com',
        ]);

        $this->workspace = Workspace::create([
            'name' => 'MIS Workspace',
            'slug' => 'mis-workspace',
            'owner_id' => $this->admin->id,
        ]);
        $this->workspace->members()->attach($this->admin->id, ['role' => 'owner']);

        $this->member = User::factory()->create([
            'name' => 'Regular Member',
            'email' => 'member@example.com',
        ]);
        $this->workspace->members()->attach($this->member->id, ['role' => 'member']);
    }

    public function test_public_registration_route_redirects_to_request_access(): void
    {
        $response = $this->get('/register');
        $response->assertRedirect(route('access-requests.create'));
    }

    public function test_public_registration_post_is_blocked(): void
    {
        $response = $this->post('/register', [
            'name' => 'Hacker',
            'email' => 'hacker@example.com',
            'password' => 'password123',
        ]);

        $response->assertStatus(403);
        $this->assertDatabaseMissing('users', ['email' => 'hacker@example.com']);
    }

    public function test_guest_can_submit_access_request(): void
    {
        Livewire::test(RequestAccess::class)
            ->set('name', 'Applicant Doe')
            ->set('email', 'applicant@stmu.edu.pk')
            ->set('department', 'Computer Science')
            ->set('reason', 'Need platform access for task management and course collaboration.')
            ->call('submit')
            ->assertHasNoErrors()
            ->assertSet('isSubmitted', true);

        $this->assertDatabaseHas('access_requests', [
            'name' => 'Applicant Doe',
            'email' => 'applicant@stmu.edu.pk',
            'department' => 'Computer Science',
            'status' => 'pending',
        ]);
    }

    public function test_duplicate_pending_request_is_prevented(): void
    {
        AccessRequest::create([
            'name' => 'Existing Applicant',
            'email' => 'applicant@stmu.edu.pk',
            'reason' => 'Existing reason for access request',
            'status' => 'pending',
        ]);

        Livewire::test(RequestAccess::class)
            ->set('name', 'Existing Applicant')
            ->set('email', 'applicant@stmu.edu.pk')
            ->set('reason', 'Trying to submit a duplicate request')
            ->call('submit')
            ->assertHasErrors(['email']);
    }

    public function test_admin_can_approve_access_request_creating_user_and_sending_email(): void
    {
        Mail::fake();

        $request = AccessRequest::create([
            'name' => 'Approved User',
            'email' => 'approved@stmu.edu.pk',
            'department' => 'MIS Engineering',
            'reason' => 'Valid request for MIS portal access',
            'status' => 'pending',
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AccessRequestManager::class, ['workspace' => $this->workspace])
            ->set('selectedRequestId', $request->id)
            ->set('assignRole', 'member')
            ->call('approveRequest');

        $this->assertDatabaseHas('users', [
            'name' => 'Approved User',
            'email' => 'approved@stmu.edu.pk',
        ]);

        $newUser = User::where('email', 'approved@stmu.edu.pk')->first();
        $this->assertTrue($this->workspace->members()->where('users.id', $newUser->id)->exists());

        $this->assertDatabaseHas('access_requests', [
            'id' => $request->id,
            'status' => 'approved',
            'assigned_role' => 'member',
            'reviewed_by_id' => $this->admin->id,
        ]);

        Mail::assertQueued(AccessRequestApprovedMail::class, function ($mail) use ($newUser) {
            return $mail->user->id === $newUser->id;
        });
    }

    public function test_admin_can_reject_access_request_with_reason(): void
    {
        Mail::fake();

        $request = AccessRequest::create([
            'name' => 'Spam User',
            'email' => 'spam@example.com',
            'reason' => 'Spam reason for access',
            'status' => 'pending',
            'workspace_id' => $this->workspace->id,
        ]);

        $this->actingAs($this->admin);

        Livewire::test(AccessRequestManager::class, ['workspace' => $this->workspace])
            ->set('selectedRequestId', $request->id)
            ->set('rejectionReason', 'Invalid email address provided.')
            ->call('rejectRequest');

        $this->assertDatabaseHas('access_requests', [
            'id' => $request->id,
            'status' => 'rejected',
            'rejection_reason' => 'Invalid email address provided.',
            'reviewed_by_id' => $this->admin->id,
        ]);

        Mail::assertQueued(AccessRequestRejectedMail::class, function ($mail) {
            return $mail->name === 'Spam User';
        });
    }

    public function test_non_admin_cannot_manage_access_requests(): void
    {
        $this->actingAs($this->member);

        $response = $this->get(route('workspace.access-requests', ['workspace' => $this->workspace->slug]));
        $response->assertStatus(403);
    }
}
