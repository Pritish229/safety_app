<?php

namespace App\Http\Controllers\SiteOfficer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\PepTalk;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class PepTalkController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = PepTalk::with('project:id,name')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_name', fn($row) => $row->project?->name ?? '—')
                ->addColumn('formatted_date', fn($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('photo', function ($row) {
                    return $row->photo
                        ? '<img src="' . asset('storage/' . $row->photo) . '" width="60" class="rounded">'
                        : '—';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <button class="btn btn-info btn-sm viewBtn" data-id="' . $row->id . '">
                        <i class="fas fa-eye"></i>
                    </button>
                    <button class="btn btn-primary btn-sm editBtn" data-id="' . $row->id . '">
                        <i class="fas fa-edit"></i>
                    </button>
                ';
                })
                ->rawColumns(['photo', 'action'])
                ->make(true);
        }

        // ✅ Fetch assigned projects
        $projects = Project::whereHas('siteOfficers', function ($q) {
            $q->where('site_officer_id', Auth::id());
        })
            ->select('id', 'name')
            ->get();

        return view('Admin.PepTalk.pep_talk', compact('projects'));
    }


    /**
     * Store a new Pep Talk
     */
    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'topics_discussed' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ✅ Verify project belongs to this site officer
        $hasAccess = Project::whereHas('siteOfficers', fn($q) => $q->where('site_officer_id', Auth::id()))
            ->where('id', $request->project_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized project access.'], 403);
        }

        $path = $request->file('photo') ? $request->file('photo')->store('pep_talk_photos', 'public') : null;

        PepTalk::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'location' => $request->location,
            'contractor_name' => $request->contractor_name,
            'num_persons_attended' => $request->num_persons_attended,
            'duration_seconds' => $request->duration_seconds,
            'topics_discussed' => $request->topics_discussed,
            'photo' => $path,
        ]);

        return response()->json(['success' => 'Pep Talk record added successfully!']);
    }

    /**
     * Edit a Pep Talk
     */
    public function edit($id)
    {
        $record = PepTalk::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($record);
    }

    /**
     * Update Pep Talk
     */
    public function update(Request $request, $id)
    {
        $record = PepTalk::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'topics_discussed' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // ✅ Ensure project is accessible
        $hasAccess = Project::whereHas('siteOfficers', fn($q) => $q->where('site_officer_id', Auth::id()))
            ->where('id', $request->project_id)
            ->exists();

        if (!$hasAccess) {
            return response()->json(['error' => 'Unauthorized project access.'], 403);
        }

        // Handle image replacement
        if ($request->hasFile('photo')) {
            if ($record->photo && Storage::disk('public')->exists($record->photo)) {
                Storage::disk('public')->delete($record->photo);
            }

            $record->photo = $request->file('photo')->store('pep_talk_photos', 'public');
        }

        // Update fields
        $record->update([
            'project_id' => $request->project_id,
            'location' => $request->location,
            'contractor_name' => $request->contractor_name,
            'num_persons_attended' => $request->num_persons_attended,
            'duration_seconds' => $request->duration_seconds,
            'topics_discussed' => $request->topics_discussed,
            'photo' => $record->photo,
        ]);

        return response()->json(['success' => 'Pep Talk record updated successfully!']);
    }

    public function show($id)
    {
        $record = PepTalk::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project' => $record->project?->name ?? '—',
            'location' => $record->location,
            'contractor_name' => $record->contractor_name,
            'num_persons_attended' => $record->num_persons_attended,
            'duration_seconds' => $record->duration_seconds,
            'topics_discussed' => $record->topics_discussed,
            'photo' => $record->photo ? asset('storage/' . $record->photo) : null,
            'formatted_date' => $record->created_at->format('d M Y, h:i A'),
        ]);
    }

    public function getAssignedProject()
    {
        $user = Auth::user();

        // Only for site officers
        if ($user->hasRole('site_officer')) {
            $project = Project::whereHas('siteOfficers', function ($q) use ($user) {
                $q->where('site_officer_id', $user->id);
            })->first();

            if ($project) {
                return response()->json([
                    'success' => true,
                    'project_id' => $project->id,
                    'project_name' => $project->name,
                ]);
            } else {
                return response()->json(['success' => false, 'message' => 'No assigned project found.']);
            }
        }

        return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
    }
}
