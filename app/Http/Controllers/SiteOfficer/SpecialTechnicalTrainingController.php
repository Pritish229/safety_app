<?php

namespace App\Http\Controllers\SiteOfficer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\SpecialTechnicalTraining;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class SpecialTechnicalTrainingController extends Controller
{

public function index(Request $request)
{
    if ($request->ajax()) {
        $query = SpecialTechnicalTraining::with('project:id,name')
            ->where('user_id', Auth::id())
            ->select('special_technical_trainings.*'); // Important!

        return DataTables::of($query)
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

    return view('Admin.STT.STT');
}

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attr, $val, $fail) {
                    $exists = Project::whereHas('siteOfficers', fn($q) =>
                        $q->where('site_officer_id', Auth::id())
                    )->where('id', $val)->exists();
                    if (!$exists) $fail('Unauthorized project.');
                },
            ],
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'topics_discussed' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo')?->store('stt_photos', 'public');

        SpecialTechnicalTraining::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'location' => $request->location,
            'contractor_name' => $request->contractor_name,
            'num_persons_attended' => $request->num_persons_attended,
            'duration_seconds' => $request->duration_seconds,
            'topics_discussed' => $request->topics_discussed,
            'photo' => $path,
        ]);

        return response()->json(['success' => 'Record added successfully!']);
    }

    public function edit($id)
    {
        $record = SpecialTechnicalTraining::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project_id' => $record->project_id,
            'location' => $record->location,
            'contractor_name' => $record->contractor_name,
            'num_persons_attended' => $record->num_persons_attended,
            'duration_seconds' => $record->duration_seconds,
            'topics_discussed' => $record->topics_discussed,
            'photo' => $record->photo,
        ]);
    }

    public function update(Request $request, $id)
    {
        $record = SpecialTechnicalTraining::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'project_id' => [
                'required',
                'exists:projects,id',
                function ($attr, $val, $fail) {
                    $exists = Project::whereHas('siteOfficers', fn($q) =>
                        $q->where('site_officer_id', Auth::id())
                    )->where('id', $val)->exists();
                    if (!$exists) $fail('Unauthorized project.');
                },
            ],
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'topics_discussed' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $record->fill($request->only([
            'project_id', 'location', 'contractor_name',
            'num_persons_attended', 'duration_seconds', 'topics_discussed'
        ]));

        if ($request->hasFile('photo')) {
            if ($record->photo) Storage::disk('public')->delete($record->photo);
            $record->photo = $request->file('photo')->store('stt_photos', 'public');
        }

        $record->save();

        return response()->json(['success' => 'Record updated successfully!']);
    }

    public function show($id)
    {
        $record = SpecialTechnicalTraining::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project_name' => $record->project?->name ?? '—',
            'location' => $record->location,
            'contractor_name' => $record->contractor_name,
            'num_persons_attended' => $record->num_persons_attended,
            'duration_seconds' => $record->duration_seconds,
            'topics_discussed' => $record->topics_discussed,
            'photo' => $record->photo,
            'formatted_date' => $record->created_at->format('d M Y, h:i A'),
        ]);
    }

    // GET ASSIGNED PROJECT
    public function getAssignedProject()
    {
        $projects = Project::whereHas('siteOfficers', fn($q) =>
            $q->where('site_officer_id', Auth::id())
        )->select('id', 'name')->get();

        if ($projects->count() === 1) {
            return response()->json([
                'success' => true,
                'project_id' => $projects->first()->id,
                'project_name' => $projects->first()->name,
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'No project assigned or multiple found.',
        ]);
    }
}