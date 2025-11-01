<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    /** --------------------------------------------------------------
     *  INDEX – returns the view **or** JSON for DataTables / load_users
     *  -------------------------------------------------------------- */
    public function index(Request $request)
    {
        $user = Auth::user();

        // ----- Load users for Select2 (called once) -----------------
        if ($request->has('load_users')) {
            $managers = User::role('site_manager')->select('id', 'name')->get();
            $officers = User::role('site_officer')->select('id', 'name')->get();

            return response()->json([
                'managers' => $managers,
                'officers' => $officers,
            ]);
        }

        // ----- DataTables JSON ---------------------------------------
        if ($request->ajax()) {
            $query = Project::with(['siteManager:id,name', 'siteOfficers:id,name']);

            // Role-based filtering
            if ($user->hasRole('admin')) {
                // admin sees everything
            } elseif ($user->hasRole('site_manager')) {
                $query->where('site_manager_id', $user->id);
            } elseif ($user->hasRole('site_officer')) {
                $query->whereHas('siteOfficers', fn($q) => $q->where('site_officer_id', $user->id));
            } else {
                return response()->json(['data' => []]);
            }

            $projects = $query->get()->map(function ($p, $i) {
                return [
                    'DT_RowIndex'   => $i + 1,
                    'id'            => $p->id,
                    'project_code'  => $p->project_code,
                    'name'          => $p->name,
                    'site_manager'  => $p->siteManager?->name ?? '—',
                    'site_officers' => $p->siteOfficers->map(fn($o) => ['id' => $o->id, 'name' => $o->name])->toArray(),
                    'photo'         => $p->photo ? asset('storage/' . $p->photo) : null,
                    'created_at'    => $p->created_at->format('d M Y'),
                ];
            });

            return response()->json(['data' => $projects]);
        }

        // ----- Blade view (only for admins) -------------------------
        if (! $user->hasRole('admin')) {
            abort(403);
        }

        $siteManagers = User::role('site_manager')->select('id', 'name')->get();
        $siteOfficers = User::role('site_officer')->select('id', 'name')->get();

        return view('Admin.Projects.AddProject', compact('siteManagers', 'siteOfficers'));
    }

    /** --------------------------------------------------------------
     *  STORE
     *  -------------------------------------------------------------- */
    public function store(Request $request)
    {
        if (! $request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Only admin can create projects.'], 403);
        }

        $validated = $request->validate([
            'name'            => 'required|string|max:255',
            'desc'            => 'nullable|string',
            'project_code'    => 'nullable|string|unique:projects,project_code',
            'photo'           => 'nullable|image|max:2048',
            'blood_group'     => 'nullable|string|max:20',
            'site_manager_id' => 'required|exists:users,id',
            'site_officer_ids'=> 'nullable|array',
            'site_officer_ids.*'=> 'exists:users,id',
        ]);

        // Auto-generate code if empty
        if (empty($validated['project_code'])) {
            do {
                $next = Project::max('id') + 1;
                $code = 'PROJECT-' . str_pad($next, 6, '0', STR_PAD_LEFT);
            } while (Project::where('project_code', $code)->exists());

            $validated['project_code'] = $code;
        }

        // Store photo
        if ($request->hasFile('photo')) {
            $validated['photo'] = $request->file('photo')->store('project_photos', 'public');
        }

        $project = Project::create($validated);
        $project->siteOfficers()->sync($validated['site_officer_ids'] ?? []);

        return response()->json([
            'message' => 'Project created successfully.',
            'data'    => $project->load(['siteManager:id,name', 'siteOfficers:id,name']),
        ], 201);
    }

    /** --------------------------------------------------------------
     *  SHOW (for view / edit modal)
     *  -------------------------------------------------------------- */
    public function show($id)
    {
        $project = Project::with(['siteManager:id,name', 'siteOfficers:id,name'])->findOrFail($id);
        $user    = auth()->user();

        $canView = $user->hasRole('admin')
            || ($user->hasRole('site_manager') && $project->site_manager_id == $user->id)
            || ($user->hasRole('site_officer') && $project->siteOfficers->pluck('id')->contains($user->id));

        if (! $canView) {
            return response()->json(['message' => 'Access denied.'], 403);
        }

        return response()->json([
            'id'             => $project->id,
            'project_code'   => $project->project_code,
            'name'           => $project->name,
            'desc'           => $project->desc,
            'photo'          => $project->photo ? asset('storage/' . $project->photo) : null,
            'blood_group'    => $project->blood_group,
            'site_manager'   => $project->siteManager?->name,
            'site_manager_id'=> $project->site_manager_id,
            'site_officers'  => $project->siteOfficers->map(fn($u) => ['id' => $u->id, 'name' => $u->name])->toArray(),
            'created_at'     => $project->created_at->format('d M Y'),
        ]);
    }

    /** --------------------------------------------------------------
     *  UPDATE (PUT)
     *  -------------------------------------------------------------- */
    public function update(Request $request, $id)
    {
        if (! $request->user()->hasRole('admin')) {
            return response()->json(['message' => 'Only admin can update projects.'], 403);
        }

        $project = Project::findOrFail($id);

        $validated = $request->validate([
            'name'            => 'sometimes|required|string|max:255',
            'desc'            => 'nullable|string',
            'project_code'    => ['sometimes', 'string', Rule::unique('projects', 'project_code')->ignore($id)],
            'photo'           => 'nullable|image|max:2048',
            'blood_group'     => 'nullable|string|max:20',
            'site_manager_id' => 'sometimes|required|exists:users,id',
            'site_officer_ids'=> 'nullable|array',
            'site_officer_ids.*'=> 'exists:users,id',
        ]);

        // Replace photo if a new one is uploaded
        if ($request->hasFile('photo')) {
            if ($project->photo && Storage::disk('public')->exists($project->photo)) {
                Storage::disk('public')->delete($project->photo);
            }
            $validated['photo'] = $request->file('photo')->store('project_photos', 'public');
        }

        $project->update($validated);
        $project->siteOfficers()->sync($request->input('site_officer_ids', []));

        return response()->json([
            'message' => 'Project updated successfully.',
            'data'    => $project->load(['siteManager:id,name', 'siteOfficers:id,name']),
        ]);
    }

    /** --------------------------------------------------------------
     *  DESTROY
     *  -------------------------------------------------------------- */
    public function destroy($id)
    {
        if (! auth()->user()->hasRole('admin')) {
            return response()->json(['message' => 'Only admin can delete projects.'], 403);
        }

        $project = Project::findOrFail($id);

        if ($project->photo && Storage::disk('public')->exists($project->photo)) {
            Storage::disk('public')->delete($project->photo);
        }

        $project->delete();

        return response()->json(['message' => 'Project deleted successfully.']);
    }
}