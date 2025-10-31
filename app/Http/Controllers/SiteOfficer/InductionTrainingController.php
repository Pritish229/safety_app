<?php

namespace App\Http\Controllers\SiteOfficer;

use Carbon\Carbon;
use Illuminate\Http\Request;
use App\Models\InductionTraining;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class InductionTrainingController extends Controller
{
    public function index(Request $request)
    {
        if ($request->ajax()) {
            $data = InductionTraining::where('user_id', Auth::id())->latest()->get();
            return DataTables::of($data)
                ->addIndexColumn()
                ->addColumn('formatted_date', function ($row) {
                    return Carbon::parse($row->created_at)->format('d M Y, h:i A');
                })
                ->addColumn('photo', function ($row) {
                    if ($row->photo) {
                        return '<img src="' . asset('storage/' . $row->photo) . '" width="60" class="rounded">';
                    }
                    return '—';
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

        return view('Admin.InductionTraining.induction_training');
    }

    public function store(Request $request)
    {
        $request->validate([
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
            'location' => 'required|string|max:255',
            'contractor_name' => 'required|string|max:255',
            'num_persons_attended' => 'required|integer|min:1',
            'duration_seconds' => 'required|integer|min:1',
            'notes' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // 🔄 Update basic fields
        $training->location = $request->location;
        $training->contractor_name = $request->contractor_name;
        $training->num_persons_attended = $request->num_persons_attended;
        $training->duration_seconds = $request->duration_seconds;
        $training->notes = $request->notes;

        // 🖼️ Handle image upload or retention
        if ($request->hasFile('photo')) {
            // 🗑️ Delete old image if exists
            if ($training->photo && Storage::disk('public')->exists($training->photo)) {
                Storage::disk('public')->delete($training->photo);
            }

            // 📸 Store new image
            $path = $request->file('photo')->store('induction_photos', 'public');
            $training->photo = $path;
        } else {
            // 🚫 Keep old image (do nothing)
            // Explicitly skip overwriting the existing photo
        }

        $training->save();

        return response()->json(['success' => 'Record updated successfully!']);
    }
    public function show($id)
    {
        $record = InductionTraining::where('user_id', Auth::id())->findOrFail($id);

        return response()->json([
            'id' => $record->id,
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