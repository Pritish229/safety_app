@extends('Admin.layout.app')

@section('title', 'Manage Roles')

@section('content')
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6"><h4 class="mb-0">Manage Roles</h4></div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Roles</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="p-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            @if(auth()->user()->hasPermission('create-roles'))
                <button class="btn btn-primary btn-sm my-1" id="addRoleBtn">
                    <i class="fas fa-plus"></i> Add Role
                </button>
            @endif
        </div>

        <div class="card-body">
            <table id="rolesTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Role Name</th>
                        <th>Permissions</th>
                        <th>Created On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Modal (Add / Edit) --}}
<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roleForm">
                @csrf
                <input type="hidden" name="_method" id="formMethod"> {{-- for PUT/POST spoofing --}}
                <div class="modal-header">
                    <h5 class="modal-title" id="roleModalLabel">Add Role</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="form-group">
                        <label>Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="Enter role name" required>
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" class="form-control" rows="2" placeholder="Enter description"></textarea>
                    </div>

                    <div class="form-group">
                        <label>Permissions</label>
                        <div class="row" id="permissionsList"><!-- filled by JS --></div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveRoleBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    $(function () {
        // ---------- DataTable ----------
        const table = $('#rolesTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('roles.list') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'name', name: 'name' },
                { data: 'permissions', name: 'permissions', orderable: false, searchable: false },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false }
            ]
        });

        const csrf = $('meta[name="csrf-token"]').attr('content') || '{{ csrf_token() }}';

        // ---------- Load Permissions ----------
        function loadPermissions(selectedIds = []) {
            $.get("{{ route('roles.permissions') }}", function (res) {
                if (!res.success)
                    return $('#permissionsList').html('<div class="col-12 text-danger">Failed to load permissions</div>');

                let html = '';
                res.data.forEach(p => {
                    const checked = selectedIds.includes(p.id) ? 'checked' : '';
                    html += `
                        <div class="col-md-4 mb-2">
                            <div class="form-check">
                                <input type="checkbox" class="form-check-input" id="perm_${p.id}"
                                       name="permissions[]" value="${p.id}" ${checked}>
                                <label class="form-check-label" for="perm_${p.id}">${p.name}</label>
                            </div>
                        </div>`;
                });
                $('#permissionsList').html(html);
            }).fail(() => {
                $('#permissionsList').html('<div class="col-12 text-danger text-center">Error loading permissions</div>');
            });
        }

        // ---------- Add Modal ----------
        $('#addRoleBtn').on('click', function () {
            $('#roleForm')[0].reset();
            $('#roleModalLabel').text('Add New Role');
            $('#formMethod').removeAttr('value');
            $('#roleForm').removeData('id');
            loadPermissions();
            $('#roleModal').modal('show');
        });

        // ---------- Edit Modal ----------
        $(document).on('click', '.editRole', function () {
            const id = $(this).data('id');
            $.get("{{ url('roles') }}/" + id + "/edit", function (res) {
                if (!res.success)
                    return Swal.fire('Error', 'Failed to load role', 'error');

                $('#roleModalLabel').text('Edit Role');
                $('#roleForm').data('id', id);
                $('#formMethod').val('PUT');

                $('input[name="name"]').val(res.data.name);
                $('textarea[name="description"]').val(res.data.description);
                loadPermissions(res.data.permission_ids);
                $('#roleModal').modal('show');
            }).fail(() => Swal.fire('Error', 'Error loading role data', 'error'));
        });

        // ---------- Form Submit (Create / Update) ----------
        $('#roleForm').on('submit', function (e) {
            e.preventDefault();

            const id = $(this).data('id');
            const method = $('#formMethod').val() || 'POST';
            const url = id
                ? "{{ route('roles.update', ':id') }}".replace(':id', id)
                : "{{ route('roles.store') }}";

            $.ajax({
                url: url,
                type: method,
                data: $(this).serialize(),
                success: function (res) {
                    if (res.success) {
                        Swal.fire({
                            icon: 'success',
                            title: 'Success!',
                            text: res.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        $('#roleModal').modal('hide');
                        table.ajax.reload(null, false);
                    } else {
                        Swal.fire('Error', res.message || 'Operation failed', 'error');
                    }
                },
                error: function (xhr) {
                    const msg = xhr.responseJSON?.message || 'Something went wrong';
                    Swal.fire('Error', msg, 'error');
                }
            });
        });

        // ---------- Delete Role ----------
        $(document).on('click', '.deleteRole', function () {
            const id = $(this).data('id');

            Swal.fire({
                title: 'Are you sure?',
                text: "This action cannot be undone!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    $.ajax({
                        url: "{{ route('roles.destroy', ':id') }}".replace(':id', id),
                        type: 'DELETE',
                        data: { _token: csrf },
                        success: function (res) {
                            if (res.success) {
                                Swal.fire({
                                    icon: 'success',
                                    title: 'Deleted!',
                                    text: res.message,
                                    timer: 1500,
                                    showConfirmButton: false
                                });
                                table.ajax.reload(null, false);
                            } else {
                                Swal.fire('Error', res.message, 'error');
                            }
                        },
                        error: () => Swal.fire('Error', 'Delete failed', 'error')
                    });
                }
            });
        });
    });
</script>
@endsection
