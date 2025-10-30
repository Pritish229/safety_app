@extends('Admin.layout.app')

@section('title', 'Manage Users')

@section('content')
<div class="content-header pb-2">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h4 class="mb-0">Manage Users</h4>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ route('dashboard') }}">Home</a></li>
                    <li class="breadcrumb-item active">Manage Users</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="p-3">
    <div class="card">
        <div class="card-header d-flex justify-content-between">
            @if(auth()->user()->hasPermission('create-users'))
            <button class="btn btn-primary btn-sm my-1" id="addUserBtn">
                <i class="fas fa-plus"></i> Add User
            </button>
            @endif
        </div>

        <div class="card-body">
            <table id="userTable" class="table table-bordered table-striped w-100">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Image</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Joined On</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>

{{-- Add/Edit Modal --}}
<div class="modal fade" id="userModal" tabindex="-1" role="dialog" aria-labelledby="userModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <form id="userForm" enctype="multipart/form-data">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title" id="userModalLabel">Add / Edit User</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body row g-3">
                    <div class="col-md-6">
                        <label>Name</label>
                        <input type="text" name="name" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                    <div class="col-md-6">
                        <label>Password</label>
                        <input type="password" name="password" class="form-control">
                        <small class="text-muted">Leave blank to keep existing password</small>
                    </div>

                    <div class="col-md-6">
                        <label>Gender</label>
                        <select name="gender" class="form-control">
                            <option value="">-- Select --</option>
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    {{-- ✅ Role Dropdown --}}
                    <div class="col-md-6">
                        <label>Assign Role</label>
                        <select name="role_id" id="roleDropdown" class="form-control" required>
                            <option value="">-- Select Role --</option>
                        </select>
                    </div>

                    <div class="col-6">
                        <label>Profile Image</label>
                        <input type="file" name="image" class="form-control">
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary" id="saveBtn">Save</button>
                </div>
            </form>
        </div>
    </div>
</div>

@endsection

@section('scripts')
<script>
    $(function() {

        let table = $('#userTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('admin.users.list') }}",
            columns: [
                { data: 'id', name: 'id' },
                { data: 'image', name: 'image', orderable: false, searchable: false },
                { data: 'name', name: 'name' },
                { data: 'email', name: 'email' },
                { data: 'role', name: 'role' },
                { data: 'created_at', name: 'created_at' },
                { data: 'actions', name: 'actions', orderable: false, searchable: false },
            ]
        });

        // ✅ Load Roles Dropdown
        function loadRoles(selectedId = '') {
            $.get("{{ route('roles.list') }}", function(res) {
                if (res.data) {
                    let roles = res.data.data || res.data; // for DataTables JSON
                    let html = `<option value="">-- Select Role --</option>`;
                    roles.forEach(r => {
                        html += `<option value="${r.id}" ${selectedId == r.id ? 'selected' : ''}>${r.name}</option>`;
                    });
                    $('#roleDropdown').html(html);
                } else {
                    $('#roleDropdown').html('<option value="">No roles available</option>');
                }
            }).fail(() => {
                $('#roleDropdown').html('<option value="">Error loading roles</option>');
            });
        }

        // ✅ Add User
        $('#addUserBtn').on('click', function() {
            $('#userForm')[0].reset();
            $('#userForm').attr('action', "{{ route('admin.users.store') }}");
            loadRoles();
            $('#userModalLabel').text('Add User');
            $('#userModal').modal('show');
        });

        // ✅ Save (Create or Update)
        $('#userForm').on('submit', function(e) {
            e.preventDefault();
            let formData = new FormData(this);
            let url = $('#userForm').attr('action');

            $.ajax({
                url: url,
                method: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: function(res) {
                    if (res.success) {
                        Swal.fire('Success', res.message, 'success');
                        $('#userModal').modal('hide');
                        table.ajax.reload();
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function(err) {
                    Swal.fire('Error', 'Something went wrong.', 'error');
                }
            });
        });

        // ✅ Edit User
        $(document).on('click', '.editUser', function() {
            let id = $(this).data('id');
            $.get("{{ url('/admin/users') }}/" + id + "/edit", function(res) {
                if (res.success) {
                    let u = res.data;
                    $('#userForm').attr('action', "{{ url('/admin/users') }}/" + id);
                    $('input[name="name"]').val(u.name);
                    $('input[name="email"]').val(u.email);
                    $('select[name="gender"]').val(u.gender);
                    loadRoles(u.role_id);
                    $('#userModalLabel').text('Edit User');
                    $('#userModal').modal('show');
                }
            });
        });

        // ✅ View User (SweetAlert)
        $(document).on('click', '.viewUser', function() {
            let id = $(this).data('id');
            $.get("{{ url('/admin/users') }}/" + id, function(res) {
                if (res.success) {
                    let user = res.data;

                    let roles = user.roles && user.roles.length
                        ? user.roles.map(r => r.name).join(', ')
                        : '<em>No role assigned</em>';

                    Swal.fire({
                        title: `<h4 class="mb-2">${user.name}</h4>`,
                        html: `
                            <div class="text-center mb-3">
                                <img src="${user.image 
                                    ? `{{ asset('storage') }}/${user.image}`
                                    : `{{ asset('images/default.png') }}`}" 
                                    class="rounded-circle shadow-sm" 
                                    width="100" height="100"
                                    alt="User Image">
                            </div>
                            <table class="table table-bordered text-start">
                                <tr><th>Email</th><td>${user.email}</td></tr>
                                <tr><th>Gender</th><td>${user.gender ?? '-'}</td></tr>
                                <tr><th>Role(s)</th><td>${roles}</td></tr>
                                <tr><th>Joined On</th><td>${user.joined_on ?? '-'}</td></tr>
                            </table>
                        `,
                        confirmButtonText: 'Close',
                        width: 600,
                    });
                } else {
                    Swal.fire('Error', res.message || 'Failed to load user details.', 'error');
                }
            });
        });

    });
</script>
@endsection
