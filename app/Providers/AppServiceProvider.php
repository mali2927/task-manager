<?php

namespace App\Providers;

use App\Models\User;
use App\Models\Workspace;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();

        // Workspace Role Permissions Gates
        Gate::define('invite-workspace-members', function (User $user, Workspace $workspace) {
            return $user->canInviteWorkspaceMembers($workspace);
        });

        Gate::define('remove-workspace-member', function (User $user, Workspace $workspace, User|int $targetUser) {
            return $user->canRemoveWorkspaceMember($workspace, $targetUser);
        });

        Gate::define('update-workspace-member-role', function (User $user, Workspace $workspace, User|int $targetUser) {
            return $user->canUpdateWorkspaceMemberRole($workspace, $targetUser);
        });

        Gate::define('manage-workspace-members', function (User $user, Workspace $workspace) {
            return $user->canManageWorkspaceMembers($workspace);
        });

        \Illuminate\Support\Facades\View::composer('*', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $currentWorkspace = $user->workspaces()->first() ?? \App\Models\Workspace::first();
                if (!$currentWorkspace && $user) {
                    $currentWorkspace = \App\Models\Workspace::create([
                        'name' => $user->name . "'s Workspace",
                        'owner_id' => $user->id,
                    ]);
                    $currentWorkspace->members()->attach($user->id, ['role' => 'owner']);
                    $currentWorkspace->taskStatuses()->create([
                        'name' => 'To Do', 'color' => '#94a3b8', 'type' => 'todo', 'sort_order' => 1, 'is_default' => true,
                    ]);
                    $currentWorkspace->taskStatuses()->create([
                        'name' => 'Done', 'color' => '#10b981', 'type' => 'done', 'sort_order' => 2,
                    ]);
                }
                $userWorkspaces = $user->workspaces()->get();
                $workspaceSpaces = $currentWorkspace ? $currentWorkspace->spaces()->with('projects.lists')->get() : collect();

                $isRequester = $currentWorkspace ? $user->isWorkspaceRequester($currentWorkspace) : false;

                // Determine active navigation module: 'tasks' vs 'helpdesk'
                if ($isRequester) {
                    $activeModule = 'helpdesk';
                } elseif (request()->has('module') && in_array(request('module'), ['tasks', 'helpdesk'])) {
                    $activeModule = request('module');
                    session(['active_module' => $activeModule]);
                } elseif (request()->routeIs('workspace.tickets.*')) {
                    $activeModule = 'helpdesk';
                    session(['active_module' => 'helpdesk']);
                } elseif (request()->routeIs('workspace.tasks*') || request()->routeIs('my-tasks')) {
                    $activeModule = 'tasks';
                    session(['active_module' => 'tasks']);
                } else {
                    $activeModule = session('active_module', 'tasks');
                }

                $view->with('currentWorkspace', $currentWorkspace)
                     ->with('userWorkspaces', $userWorkspaces)
                     ->with('workspaceSpaces', $workspaceSpaces)
                     ->with('activeModule', $activeModule);
            }
        });
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}
