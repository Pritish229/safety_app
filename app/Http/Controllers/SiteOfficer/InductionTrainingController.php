<?php

namespace App\Http\Controllers\SiteOfficer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\InductionTraining;
use App\Models\Project;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InductionTrainingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = InductionTraining::with('project:id,name')
                ->where('user_id', Auth::id())
                ->latest()
                ->get();

            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('project_name', fn($row) => $row->project?->name ?? '—')
                ->addColumn('formatted_date', fn($row) => Carbon::parse($row->created_at)->format('d M Y, h:i A'))
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

        // Get projects assigned to this site officer via pivot
        $projects = Project::whereHas('siteOfficers', function ($q) {
            $q->where('site_officer_id', Auth::id());
        })->select('id', 'name')->get();

        return view('Admin.InductionTraining.induction_training', compact('projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $path = $request->file('photo') ? $request->file('photo')->store('induction_photos', 'public') : null;

        InductionTraining::create([
            'user_id' => Auth::id(),
            'project_id' => $request->project_id,
            'location' => $request->location,
            'contractor_name' => $request->contractor_name,
            'num_persons_attended' => $request->num_persons_attended,
            'duration_seconds' => $request->duration_seconds,
            'notes' => $request->notes,
            'photo' => $path,
        ]);

        return response()->json(['success' => 'Record added successfully!']);
    }

    public function edit($id)
    {
        $training = InductionTraining::where('user_id', Auth::id())->findOrFail($id);
        return response()->json($training);
    }

    public function update(Request $request, $id)
    {
        $training = InductionTraining::where('user_id', Auth::id())->findOrFail($id);

        $request->validate([
            'project_id' => 'required|exists:projects,id',
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        $training->fill([
            'project_id' => $request->project_id,
            'location' => $request->location,
            'contractor_name' => $request->contractor_name,
            'num_persons_attended' => $request->num_persons_attended,
            'duration_seconds' => $request->duration_seconds,
            'notes' => $request->notes,
        ]);

        if ($request->hasFile('photo')) {
            if ($training->photo && Storage::disk('public')->exists($training->photo)) {
                Storage::disk('public')->delete($training->photo);
            }

            $training->photo = $request->file('photo')->store('induction_photos', 'public');
        }

        $training->save();

        return response()->json(['success' => 'Record updated successfully!']);
    }

    public function show($id)
    {
        $record = InductionTraining::with('project:id,name')
            ->where('user_id', Auth::id())
            ->findOrFail($id);

        return response()->json([
            'id' => $record->id,
            'project_name' => $record->project?->name ?? '—',
            'location' => $record->location,
            'contractor_name' => $record->contractor_name,
            'num_persons_attended' => $record->num_persons_attended,
            'duration_seconds' => $record->duration_seconds,
            'notes' => $record->notes,
            'photo' => $record->photo,
            'formatted_date' => $record->created_at->format('d M Y, h:i A'),
        ]);
    }
}
