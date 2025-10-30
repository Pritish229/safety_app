<?php

namespace App\Http\Controllers\Admin;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Yajra\DataTables\Facades\DataTables;

class ManageRoleController extends Controller
{
    public function index()
    {
        return view('Admin.ManageUser.ManageRole');
    }

    /** JSON list for DataTables */
    public function rolelist(Request $request)
    {
        if ($request->ajax()) {
            $roles = Role::with('permissions')->select('roles.*');

            return DataTables::of($roles)
                ->addColumn('permissions', function ($role) {
                    return $role->permissions->pluck('name')->map(function ($name) {
                        return '<span class="badge bg-primary me-1">' . e($name) . '</span>';
                    })->implode(' ') ?: '<span class="text-muted">No permissions</span>';
                })
                ->editColumn('created_at', fn($r) => $r->created_at?->format('d M Y') ?? '-')
                ->addColumn('actions', function ($role) {
                    
                    $edit = auth()->user()->hasPermission('edit-roles')
                        ? '<button class="btn btn-sm btn-primary editRole" data-id="'.$role->id.'"><i class="fas fa-edit"></i></button>'
                        : '';
                    $delete = auth()->user()->hasPermission('delete-roles')
                        ? '<button class="btn btn-sm btn-danger deleteRole" data-id="'.$role->id.'"><i class="fas fa-trash"></i></button>'
                        : '';

                    return  $edit . $delete;
                })
                ->rawColumns(['permissions', 'actions'])
                ->make(true);
        }
    }

    /** All permissions (JSON) */
    public function getPermissions()
    {
        try {
            $permissions = Permission::select('id', 'name')->get();
            return response()->json(['success' => true, 'data' => $permissions]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    /** Store new role */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|unique:roles,name',
            'description'=> 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::create($request->only('name', 'description'));
        $role->permissions()->sync($request->input('permissions', []));

        return response()->json(['success' => true, 'message' => 'Role created successfully!']);
    }

    /** JSON for edit modal */
    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        return response()->json([
            'success' => true,
            'data'    => [
                'id'          => $role->id,
                'name'        => $role->name,
                'description' => $role->description,
                'permission_ids' => $role->permissions->pluck('id')->toArray(),
            ],
        ]);
    }

    /** Update role */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name'        => 'required|unique:roles,name,'.$id,
            'description'=> 'nullable|string',
            'permissions' => 'nullable|array',
            'permissions.*'=> 'exists:permissions,id',
        ]);

        $role = Role::findOrFail($id);
        $role->update($request->only('name', 'description'));
        $role->permissions()->sync($request->input('permissions', []));

        return response()->json(['success' => true, 'message' => 'Role updated successfully!']);
    }

    /** Delete role */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);
        $role->permissions()->detach();
        $role->delete();

        return response()->json(['success' => true, 'message' => 'Role deleted successfully!']);
    }
}