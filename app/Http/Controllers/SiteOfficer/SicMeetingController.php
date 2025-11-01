<?php

namespace App\Http\Controllers\SiteOfficer;

use App\Http\Controllers\Controller;
use App\Models\SicMeeting;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SicMeetingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = SicMeeting::with('project:id,name')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_name', fn($row) => $row->project?->name ?? '—')
                ->addColumn('formatted_date', fn($row) => $row->created_at->format('d M Y, h:i A'))
                ->addColumn('project_name', fn($row) => $row->project?->name ?? '—')
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

        return view('Admin.SicMeeting.SicMeeting');
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attribute, $value, $fail) {
                    $exists = Project::whereHas('siteOfficers', function ($q) {
                        $q->where('site_officer_id', Auth::id());
                    })->where('id', $value)->exists();

                    if (!$exists) {
                        $fail('You are not authorized to add SIC meetings for this project.');
                    }
                },
            ],
            'discussed_points' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')?->store('sic_meetings', 'public');

        SicMeeting::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'discussed_points' => $request->discussed_points,
            'photo' => $path,
        ]);

        return response()->json(['success' => 'SIC Meeting added successfully!']);
    }

    public function edit($id)
    {
        $record = SicMeeting::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project_id' => $record->project_id,
            'discussed_points' => $record->discussed_points,
            'photo' => $record->photo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = SicMeeting::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attribute, $value, $fail) {
                    $exists = Project::whereHas('siteOfficers', function ($q) {
                        $q->where('site_officer_id', Auth::id());
                    })->where('id', $value)->exists();

                    if (!$exists) {
                        $fail('You are not authorized to update SIC meetings for this project.');
                    }
                },
            ],
            'discussed_points' => 'required|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $record->project_id = $request->project_id;
        $record->discussed_points = $request->discussed_points;

        if ($request->hasFile('photo')) {
            if ($record->photo && Storage::disk('public')->exists($record->photo)) {
                Storage::disk('public')->delete($record->photo);
            }
            $record->photo = $request->file('photo')->store('sic_meetings', 'public');
        }

        $record->save();

        return response()->json(['success' => 'SIC Meeting updated successfully!']);
    }

    public function show($id)
    {
        $record = SicMeeting::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project_name' => $record->project?->name ?? '—',
            'discussed_points' => $record->discussed_points,
            'photo' => $record->photo,
            'formatted_date' => $record->created_at->format('d M Y, h:i A'),
        ]);
    }

    // ────────────────────────────────
    // GET ASSIGNED PROJECT (for AJAX)
    // ────────────────────────────────
    public function getAssignedProject()
    {
        $projects = Project::whereHas('siteOfficers', function ($q) {
            $q->where('site_officer_id', Auth::id());
        })->select('id', 'name')->get();

        if ($projects->count() === 1) {
            return response()->json([
                'success' => true,
                'project_id' => $projects->first()->id,
                'project_name' => $projects->first()->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No project assigned or multiple found. Contact admin.',
        ]);
    }
}