<?php

namespace App\Http\Controllers;

use App\Models\ActivityState;
use App\Models\instance as InstanceModel;
use App\Models\instanceLS;
use App\Models\projectB2B;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class AdminController extends Controller
{
    public function index(): View
    {
        $states = ActivityState::query()
            ->orderBy('tracking_type')
            ->orderBy('sort_order')
            ->orderBy('label')
            ->paginate(10, ['*'], 'states_page')
            ->withQueryString();

        return view('admin.index', [
            'users' => User::query()->orderBy('name')->paginate(10, ['*'], 'users_page')->withQueryString(),
            'totalUsers' => User::count(),
            'totalAdmins' => User::where('is_admin', true)->count(),
            'totalInstancels' => instanceLS::count(),
            'totalInstances' => InstanceModel::count(),
            'totalProjects' => projectB2B::count(),
            'states' => $states,
            'stateTypes' => config('activity_states.types', []),
            'stateUsage' => $states->mapWithKeys(fn (ActivityState $state) => [$state->id => $this->stateUsageCount($state)]),
        ]);
    }

    public function storeState(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'tracking_type' => ['required', Rule::in(array_keys(config('activity_states.types', [])))],
            'label' => ['required', 'string', 'max:100'],
        ]);

        $validator->after(function ($validator) use ($request): void {
            if ($request->filled('tracking_type') && $request->filled('label') && ActivityState::where('tracking_type', $request->input('tracking_type'))->where('label', $request->input('label'))->exists()) {
                $validator->errors()->add('label', 'Cet état existe déjà pour ce suivi.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator, 'stateCreate')
                ->withInput()
                ->with('open_state_create', true);
        }

        $typeStatesCount = ActivityState::where('tracking_type', $request->input('tracking_type'))->count();

        ActivityState::create([
            'tracking_type' => $request->input('tracking_type'),
            'label' => trim($request->input('label')),
            'is_active' => true,
            'sort_order' => $typeStatesCount,
        ]);

        return redirect()->route('admin.index')->with('status', 'L’état a été ajouté.');
    }

    public function updateState(Request $request, ActivityState $state): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'label' => ['required', 'string', 'max:100'],
        ]);

        $validator->after(function ($validator) use ($request, $state): void {
            if ($request->filled('label') && ActivityState::where('tracking_type', $state->tracking_type)->where('label', $request->input('label'))->whereKeyNot($state->id)->exists()) {
                $validator->errors()->add('label', 'Cet état existe déjà pour ce suivi.');
            }
        });

        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator, 'stateEdit')
                ->withInput()
                ->with('edit_state_id', $state->id);
        }

        $newLabel = trim($validator->validated()['label']);
        if ($newLabel !== $state->label) {
            DB::transaction(function () use ($state, $newLabel): void {
                $this->replaceStateInRecords($state->tracking_type, $state->label, $newLabel);
                $state->update(['label' => $newLabel]);
            });
        }

        return redirect()->route('admin.index')->with('status', 'L’état a été modifié.');
    }

    public function toggleState(ActivityState $state): RedirectResponse
    {
        if ($state->is_active && ActivityState::where('tracking_type', $state->tracking_type)->where('is_active', true)->count() === 1) {
            return redirect()->route('admin.index')->withErrors(['state' => 'Chaque suivi doit conserver au moins un état actif.']);
        }

        if ($state->is_active && $this->stateUsageCount($state) > 0) {
            return redirect()->route('admin.index')->withErrors(['state' => 'Cet état est utilisé par des fiches ; renommez-le ou mettez à jour les fiches avant de le désactiver.']);
        }

        $state->update(['is_active' => ! $state->is_active]);

        return redirect()->route('admin.index')->with('status', 'La disponibilité de l’état a été mise à jour.');
    }

    public function destroyState(ActivityState $state): RedirectResponse
    {
        if ($this->stateUsageCount($state) > 0) {
            return redirect()->route('admin.index')->withErrors(['state' => 'Cet état est utilisé par des fiches et ne peut pas être supprimé.']);
        }

        if ($state->is_active && ActivityState::where('tracking_type', $state->tracking_type)->where('is_active', true)->count() === 1) {
            return redirect()->route('admin.index')->withErrors(['state' => 'Chaque suivi doit conserver au moins un état actif.']);
        }

        $state->delete();

        return redirect()->route('admin.index')->with('status', 'L’état a été supprimé.');
    }

    private function stateUsageCount(ActivityState $state): int
    {
        return match ($state->tracking_type) {
            'instancels' => instanceLS::where('phase', $state->label)->count(),
            'instances' => InstanceModel::where('etat', $state->label)->count(),
            'projects_b2b' => projectB2B::where('etat', $state->label)->count(),
            default => 0,
        };
    }

    private function replaceStateInRecords(string $trackingType, string $oldLabel, string $newLabel): void
    {
        match ($trackingType) {
            'instancels' => instanceLS::where('phase', $oldLabel)->update(['phase' => $newLabel]),
            'instances' => InstanceModel::where('etat', $oldLabel)->update(['etat' => $newLabel]),
            'projects_b2b' => projectB2B::where('etat', $oldLabel)->update(['etat' => $newLabel]),
            default => null,
        };
    }

    public function storeUser(Request $request): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:12', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator)
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('open_admin_create', true);
        }

        $validated = $validator->validated();

        User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => $validated['password'],
            'is_admin' => $request->boolean('is_admin'),
        ]);

        return redirect()->route('admin.index')->with('status', 'Le compte a été créé.');
    }

    public function updateUser(Request $request, User $user): RedirectResponse
    {
        $validator = Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id)],
            'password' => ['nullable', 'string', 'min:12', 'confirmed'],
            'is_admin' => ['nullable', 'boolean'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('admin.index')
                ->withErrors($validator, 'userEdit')
                ->withInput($request->except(['password', 'password_confirmation']))
                ->with('edit_user_id', $user->id);
        }

        $isAdmin = $request->boolean('is_admin');
        if ($user->is_admin && ! $isAdmin && User::where('is_admin', true)->count() === 1) {
            return redirect()->route('admin.index')
                ->withErrors(['is_admin' => 'Le dernier administrateur ne peut pas être rétrogradé.'], 'userEdit')
                ->withInput()
                ->with('edit_user_id', $user->id);
        }

        $validated = $validator->validated();
        unset($validated['password']);
        $validated['is_admin'] = $isAdmin;

        if ($request->filled('password')) {
            $validated['password'] = $request->input('password');
        }

        $user->fill($validated)->save();

        return redirect()->route('admin.index')->with('status', 'Le compte a été mis à jour.');
    }

    public function destroyUser(User $user): RedirectResponse
    {
        if ($user->is(auth()->user())) {
            return redirect()->route('admin.index')->withErrors(['user' => 'Vous ne pouvez pas supprimer votre propre compte.']);
        }

        if ($user->is_admin && User::where('is_admin', true)->count() === 1) {
            return redirect()->route('admin.index')->withErrors(['user' => 'Le dernier administrateur ne peut pas être supprimé.']);
        }

        $user->delete();

        return redirect()->route('admin.index')->with('status', 'Le compte a été supprimé.');
    }
}