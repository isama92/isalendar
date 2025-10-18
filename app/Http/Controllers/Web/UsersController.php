<?php

namespace App\Http\Controllers\Web;

use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Http\Requests\StoreUserRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Inertia\Inertia;
use Inertia\Response;

class UsersController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('viewAny', User::class);

        $users = User::with('roles:id,name')
            ->withTrashed()
            ->get();

        return Inertia::render('Users/Index', [
            'users' => $users,
            'roles' => $this->getRoles(),
        ]);
    }

    public function show(User $user): Response
    {
        Gate::authorize('view', $user);

        $user->loadMissing('roles:id,name');

        return Inertia::render('Users/Show', [
            'user' => $user,
            'roles' => $this->getRoles(),
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('create', User::class);

        return Inertia::render('Users/Form', [
            'roles' => $this->getRoles(),
        ]);
    }

    public function store(StoreUserRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Generate random password if not provided
        $validated['password'] ??= Str::random(32);

        $user = User::create($validated);

        $user->assignRole($validated['role']);

        $route = match ($request->input('redirect_to', 'index')) {
            'new' => route('users.create'),
            'show' => route('users.show', $user),
            default => route('users.index'),
        };

        return redirect($route)
            ->with('success', __('actions.named.created', ['name' => __('users.user')]));
    }

    public function edit(User $user): Response
    {
        Gate::authorize('update', $user);

        $user->loadMissing('roles:id,name');

        return Inertia::render('Users/Form', [
            'user' => $user,
            'roles' => $this->getRoles(),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user): RedirectResponse
    {
        $validated = $request->validated();

        if (empty($validated['password'])) {
            unset($validated['password']);
        }

        $user->update($validated);

        $user->syncRoles([$validated['role']]);

        $route = match ($request->input('redirect_to', 'show')) {
            'edit' => route('users.edit', $user),
            'index' => route('users.index'),
            default => route('users.show', $user),
        };

        return redirect($route)
            ->with('success', __('actions.named.updated', ['name' => __('users.user')]));
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->trashed()) {
            // TODO: restore in destroy method?
            Gate::authorize('restore', $user);
            $user->restore();

            return redirect()->route('users.index')
                ->with('success', __('actions.named.restored', ['name' => __('users.user')]));
        }

        Gate::authorize('delete', $user);
        $user->delete();

        return redirect()->route('users.index')
            ->with('success', __('actions.named.deleted', ['name' => __('users.user')]));
    }

    /**
     * @return array<array{value: string, label: string}>
     */
    private function getRoles(): array
    {
        return collect(Role::cases())->map(fn (Role $role): array => [
            'value' => $role->value,
            'label' => $role->label(),
        ])->toArray();
    }
}
