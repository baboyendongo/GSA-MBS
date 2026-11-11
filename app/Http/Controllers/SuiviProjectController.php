<?php

namespace App\Http\Controllers;

use App\Models\instanceLS;
use App\Models\instance as InstanceModel;
use App\Models\ActivityState;
use App\Models\projectB2B;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class SuiviProjectController extends Controller
{
    public function index(Request $request)
    {
        $search = trim($request->query('search', ''));
        $query = instanceLS::query();

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                foreach (['date', 'evenement', 'operation', 'ligne', 'codeclient', 'clients', 'ville', 'phase', 'responsable', 'delai', 'date_cloture', 'observation'] as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        $instancels = $query
            ->orderByDesc('date')
            ->orderByDesc('id')
            ->paginate(10)
            ->withQueryString();

        return view('instancels.index', [
            'instancels' => $instancels,
            'totalInstancels' => instanceLS::count(),
            'states' => $this->stateOptions('instancels'),
            'activeInstancels' => instanceLS::whereNull('date_cloture')->count(),
            'closedInstancels' => instanceLS::whereNotNull('date_cloture')->count(),
        ]);
    }

    public function instanceIndex(Request $request)
    {
        $search = trim($request->query('search', ''));
        $query = InstanceModel::query();

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                foreach (['date', 'instance', 'client', 'ligne', 'responsable', 'etat', 'delai', 'date_cloture', 'observation'] as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        return view('instances.index', [
            'instances' => $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString(),
            'states' => $this->stateOptions('instances'),
        ]);
    }

    public function dashboard()
    {
        return view('dashboard', [
            'totalInstancels' => instanceLS::count(),
            'totalInstances' => InstanceModel::count(),
            'activeInstancels' => instanceLS::whereNull('date_cloture')->count(),
            'closedInstancels' => instanceLS::whereNotNull('date_cloture')->count(),
            'totalProjectsB2B' => projectB2B::count(),
            'recentInstancels' => instanceLS::query()->orderByDesc('date')->orderByDesc('id')->limit(6)->get(),
            'recentInstances' => InstanceModel::query()->orderByDesc('date')->orderByDesc('id')->limit(6)->get(),
            'recentProjectsB2B' => projectB2B::query()->orderByDesc('date')->orderByDesc('id')->limit(5)->get(),
        ]);
    }

    public function projectB2BIndex(Request $request)
    {
        $search = trim($request->query('search', ''));
        $query = projectB2B::query();

        if ($search !== '') {
            $query->where(function ($query) use ($search): void {
                foreach (['date', 'project', 'client', 'Action', 'responsable', 'etat', 'delai', 'date_cloture', 'observation'] as $column) {
                    $query->orWhere($column, 'like', '%'.$search.'%');
                }
            });
        }

        return view('projects-b2b.index', [
            'projects' => $query->orderByDesc('date')->orderByDesc('id')->paginate(10)->withQueryString(),
            'states' => $this->stateOptions('projects_b2b'),
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'evenement' => ['required', 'string', 'max:255'],
            'ligne' => ['nullable', 'string', 'max:255'],
            'codeclient' => ['nullable', 'string', 'max:255'],
            'clients' => ['nullable', 'string', 'max:255'],
            'ville' => ['nullable', 'string', 'max:255'],
            'phase' => ['nullable', 'string', $this->activeStateRule('instancels')],
            'responsable' => ['nullable', 'string', 'max:255'],
            'delai' => ['nullable', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['phase'] ?? null) !== 'Archivé') {
            unset($validated['date_cloture']);
        }

        instanceLS::create($validated + ['operation' => '']);

        return redirect()->route('instancels.index')->with('status', 'L’Instancel a été ajouté.');
    }

    public function editInstancel(string $id)
    {
        return view('instancels.edit', [
            'instancel' => instanceLS::findOrFail($id),
            'states' => $this->stateOptions('instancels'),
        ]);
    }

    public function updateInstancel(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'evenement' => ['required', 'string', 'max:255'],
            'ligne' => ['nullable', 'string', 'max:255'],
            'clients' => ['nullable', 'string', 'max:255'],
            'phase' => ['nullable', 'string', $this->activeStateRule('instancels')],
            'responsable' => ['nullable', 'string', 'max:255'],
            'delai' => ['nullable', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('instancels.index')
                ->withErrors($validator, 'instancelEdit')
                ->withInput()
                ->with('edit_record_id', $id);
        }

        $validated = $validator->validated();

        $instancel = instanceLS::findOrFail($id);
        if (($validated['phase'] ?? null) !== 'Archivé') {
            if ($instancel->phase === 'Archivé') {
                $validated['date_cloture'] = null;
            } else {
                unset($validated['date_cloture']);
            }
        }

        $instancel->fill($validated)->save();

        return redirect()->route('instancels.index')->with('status', 'L’Instancel a été modifié.');
    }

    public function destroyInstancel(string $id)
    {
        instanceLS::findOrFail($id)->delete();

        return redirect()->route('instancels.index')->with('status', 'L’Instancel a été supprimé.');
    }

    public function storeInstance(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'instance' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'ligne' => ['nullable', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'etat' => ['nullable', 'string', $this->activeStateRule('instances')],
            'delai' => ['nullable', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string'],
        ]);

        if (($validated['etat'] ?? null) !== 'Archivé') {
            unset($validated['date_cloture']);
        }

        InstanceModel::create($validated);

        return redirect()->route('instances.index')->with('status', 'L’instance de travail a été ajoutée.');
    }

    public function editInstance(string $id)
    {
        return view('instances.edit', [
            'instance' => InstanceModel::findOrFail($id),
            'states' => $this->stateOptions('instances'),
        ]);
    }

    public function updateInstance(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'instance' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'ligne' => ['nullable', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'etat' => ['nullable', 'string', $this->activeStateRule('instances')],
            'delai' => ['nullable', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('instances.index')
                ->withErrors($validator, 'instanceEdit')
                ->withInput()
                ->with('edit_record_id', $id);
        }

        $validated = $validator->validated();

        $instance = InstanceModel::findOrFail($id);
        if (($validated['etat'] ?? null) !== 'Archivé') {
            if ($instance->etat === 'Archivé') {
                $validated['date_cloture'] = null;
            } else {
                unset($validated['date_cloture']);
            }
        }

        $instance->fill($validated)->save();

        return redirect()->route('instances.index')->with('status', 'L’instance a été modifiée.');
    }

    public function destroyInstance(string $id)
    {
        InstanceModel::findOrFail($id)->delete();

        return redirect()->route('instances.index')->with('status', 'L’instance a été supprimée.');
    }

    public function storeProjectB2B(Request $request)
    {
        $validated = $request->validate([
            'date' => ['required', 'date'],
            'project' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'Action' => ['required', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'etat' => ['required', 'string', $this->activeStateRule('projects_b2b')],
            'delai' => ['required', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
        ]);

        if (($validated['etat'] ?? null) !== 'Archivé') {
            unset($validated['date_cloture']);
        }

        projectB2B::create($validated + [
            'date_cloture' => $validated['date_cloture'] ?? null,
            'observation' => $validated['observation'] ?? null,
        ]);

        return redirect()->route('projects-b2b.index')->with('status', 'Le projet B2B a été ajouté.');
    }

    public function editProjectB2B(string $id)
    {
        return view('projects-b2b.edit', [
            'project' => projectB2B::findOrFail($id),
            'states' => $this->stateOptions('projects_b2b'),
        ]);
    }

    public function updateProjectB2B(Request $request, string $id)
    {
        $validator = Validator::make($request->all(), [
            'date' => ['required', 'date'],
            'project' => ['required', 'string', 'max:255'],
            'client' => ['required', 'string', 'max:255'],
            'Action' => ['required', 'string', 'max:255'],
            'responsable' => ['required', 'string', 'max:255'],
            'etat' => ['required', 'string', $this->activeStateRule('projects_b2b')],
            'delai' => ['required', 'date'],
            'date_cloture' => ['nullable', 'date'],
            'observation' => ['nullable', 'string', 'max:255'],
        ]);

        if ($validator->fails()) {
            return redirect()->route('projects-b2b.index')
                ->withErrors($validator, 'projectEdit')
                ->withInput()
                ->with('edit_record_id', $id);
        }

        $validated = $validator->validated();

        $project = projectB2B::findOrFail($id);
        if (($validated['etat'] ?? null) !== 'Archivé') {
            if ($project->etat === 'Archivé') {
                $validated['date_cloture'] = null;
            } else {
                unset($validated['date_cloture']);
            }
        }

        $project->fill($validated)->save();

        return redirect()->route('projects-b2b.index')->with('status', 'Le projet B2B a été modifié.');
    }

    public function destroyProjectB2B(string $id)
    {
        projectB2B::findOrFail($id)->delete();

        return redirect()->route('projects-b2b.index')->with('status', 'Le projet B2B a été supprimé.');
    }
    private function stateOptions(string $trackingType)
    {
        return ActivityState::query()
            ->where('tracking_type', $trackingType)
            ->where('is_active', true)
            ->orderBy('sort_order')
            ->orderBy('label')
            ->pluck('label');
    }

    private function activeStateRule(string $trackingType)
    {
        return Rule::exists('activity_states', 'label')
            ->where(fn ($query) => $query->where('tracking_type', $trackingType)->where('is_active', true));
    }

}