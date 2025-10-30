<?php

namespace App\Http\Controllers\Admin;

use Carbon\Carbon;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;

class ManageUserController extends Controller
{
    public function index()
    {
        return view('Admin.ManageUser.ManageUser');
    }

    // ✅ CREATE USER
    public function store(Request $request)
    {
        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', 'unique:users'],
            'password' => ['required', 'min:8'],
            'gender'   => ['nullable', Rule::in(['male', 'female', 'other'])],
            'image'    => ['nullable', 'image', 'max:2048'],
            'role_id'  => ['required', 'exists:roles,id'],
        ]);

        $data = $request->only(['name', 'email', 'gender']);
        $data['password'] = Hash::make($request->password);

        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('images', 'public');
        }

        $user = User::create($data);

        DB::table('role_user')->insert([
            'user_id'     => $user->id,
            'role_id'     => $request->role_id,
            'created_at'  => now(),
            'updated_at'  => now(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'User created successfully with role!',
        ]);
    }

    // ✅ FETCH ALL USERS (DataTable)
    public function listAll(Request $request)
    {
        if ($request->ajax()) {
            $users = User::with('roles')->select('users.*');

            return DataTables::of($users)
                ->addColumn('image', function ($user) {
                    $imgPath = $user->image
                        ? asset('storage/' . $user->image)
                        : asset('images/default.png');
                    return '<img src="' . $imgPath . '" width="40" height="40" class="rounded-circle"/>';
                })
                ->addColumn('role', function ($user) {
                    return $user->roles->pluck('name')->implode(', ') ?: '-';
                })
                ->addColumn('created_at', function ($user) {
                    return $user->created_at ? $user->created_at->format('d M Y') : '-';
                })
                ->addColumn('actions', function ($user) {
                    return '
                        <button class="btn btn-sm btn-info viewUser" data-id="' . $user->id . '">
                            <i class="fas fa-eye"></i>
                        </button>
                        <button class="btn btn-sm btn-primary editUser" data-id="' . $user->id . '">
                            <i class="fas fa-edit"></i>
                        </button>';
                })
                ->rawColumns(['image', 'actions'])
                ->make(true);
        }

        return view('Admin.ManageUser.ManageUser');
    }

    // ✅ VIEW USER DETAILS
    public function show($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'bio' => $user->bio,
                'roles' => $user->roles,
                'role_id' => $user->roles->first()->id ?? null,
                'image' => $user->image,
                'created_at' => $user->created_at,
                'updated_at' => $user->updated_at,
                'joined_on' => optional($user->created_at)->format('d M Y, h:i A'),
                'last_updated' => optional($user->updated_at)->format('d M Y, h:i A'),
            ]
        ]);
    }

    // ✅ EDIT (FETCH USER DATA)
    public function edit($id)
    {
        $user = User::with('roles')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'gender' => $user->gender,
                'bio' => $user->bio,
                'role_id' => $user->roles->first()->id ?? null,
                'image' => $user->image,
            ]
        ]);
    }

    // ✅ UPDATE USER DETAILS
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'gender'   => ['nullable', Rule::in(['male', 'female', 'other'])],
            'password' => ['nullable', 'min:8'],
            'image'    => ['nullable', 'image', 'max:2048'],
            'role_id'  => ['required', 'exists:roles,id'],
        ]);

        // Update basic fields
        $user->name   = $request->name;
        $user->email  = $request->email;
        $user->gender = $request->gender;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        if ($request->hasFile('image')) {
            if ($user->image && Storage::disk('public')->exists($user->image)) {
                Storage::disk('public')->delete($user->image);
            }
            $user->image = $request->file('image')->store('images', 'public');
        }

        $user->save();

        // === ROLE LOGIC: Check role_user table ===
        $roleId = $request->role_id;

        $exists = DB::table('role_user')
            ->where('user_id', $user->id)
            ->exists();

        if ($exists) {
            // UPDATE existing record
            DB::table('role_user')
                ->where('user_id', $user->id)
                ->update([
                    'role_id'     => $roleId,
                    'updated_at'  => now(),
                ]);
        } else {
            // INSERT new record
            DB::table('role_user')->insert([
                'user_id'     => $user->id,
                'role_id'     => $roleId,
                'created_at'  => now(),
                'updated_at'  => now(),
            ]);
        }

        return response()->json([
            'success' => true,
            'message' => 'User and role updated successfully!',
        ]);
    }

    // ✅ Simple roles list for dropdown
    public function getRoles()
    {
        $roles = Role::select('id', 'name')->get();
        return response()->json(['success' => true, 'data' => $roles]);
    }
}
