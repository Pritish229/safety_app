@extends('Admin.layout.app')

@section('title', 'Projects')

@section('content')
<div class="p-3 mt-4">
    <div class="card shadow-sm rounded-3">
        <div class="card-header  text-white d-flex  align-items-center">
            <button class="btn btn-primary btn-sm" id="addProjectBtn">+ Add Project</button>
        </div>

        <div class="card-body">
            <div class="table-responsive">
                <table id="projectsTable" class="table table-bordered table-striped">
                    <thead class="table-light">
                        <tr>
                            <th>#</th>
                            <th>Code</th>
                            <th>Name</th>
                            <th>Manager</th>
                            <th>Officers</th>
                            <th>Photo</th>
                            <th>Created</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

{{-- ====================== ADD / EDIT MODAL ====================== --}}
<div class="modal fade" id="projectModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-lg" role="document">
        <form id="projectForm" enctype="multipart/form-data">
            @csrf
            <input type="hidden" id="project_id" name="project_id">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="modalTitle">Add Project</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">
                        <span>&times;</span>
                    </button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <label>Project Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6">
                            <label>Project Code</label>
                            <input type="text" name="project_code" class="form-control" placeholder="Auto-generated">
                        </div>
                    </div>

                    <div class="form-group mt-3">
                        <label>Description</label>
                        <textarea name="desc" class="form-control" rows="3"></textarea>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Site Manager <span class="text-danger">*</span></label>
                            <select name="site_manager_id" class="form-select select2" required></select>
                        </div>
                        <div class="col-md-6">
                            <label>Site Officers (Hold Ctrl/Cmd to select multiple)</label>
                            <select name="site_officer_ids[]" class="form-select select2" multiple></select>
                        </div>
                    </div>

                    <div class="row mt-3">
                        <div class="col-md-6">
                            <label>Blood Group</label>
                            <input type="text" name="blood_group" class="form-select" placeholder="e.g. A+">
                        </div>
                        <div class="col-md-6">
                            <label>Photo</label>
                            <input type="file" name="photo" class="form-control" accept="image/*">
                            <div class="mt-2 text-center">
                                <img id="photoPreview" src="" class="img-thumbnail" style="max-height:120px; display:none;">
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="submit" class="btn btn-success">Save</button>
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                </div>
            </div>
        </form>
    </div>
</div>

{{-- ====================== VIEW MODAL ====================== --}}
<div class="modal fade" id="viewModal" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-xl" role="document">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">Project Details</h5>
                <button type="button" class="close text-white" data-dismiss="modal">
                    <span>&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="row">
                    <div class="col-md-4 text-center">
                        <img id="view_photo" src="" class="img-fluid rounded" style="max-height:200px;">
                        <p class="mt-2 text-muted" id="no_photo" style="display:none;">— No photo —</p>
                    </div>
                    <div class="col-md-8">
                        <table class="table table-bordered">
                            <tr><th>Code</th><td id="view_code"></td></tr>
                            <tr><th>Name</th><td id="view_name"></td></tr>
                            <tr><th>Description</th><td id="view_desc"></td></tr>
                            <tr><th>Site Manager</th><td id="view_manager"></td></tr>
                            <tr><th>Site Officers</th><td id="view_officers"></td></tr>
                            <tr><th>Blood Group</th><td id="view_blood"></td></tr>
                            <tr><th>Created</th><td id="view_created"></td></tr>
                        </table>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
{{-- ==== ROUTES FOR JS ==== --}}
<script>
    window.routes = {
        index : '{{ route('admin.projects') }}',
        store : '{{ route('projects.store') }}',
        update: (id) => `{{ route('projects.update', ':id') }}`.replace(':id', id),
        show  : (id) => `{{ route('projects.show', ':id') }}`.replace(':id', id),
    };
</script>

<script>
$(function () {
    let managers = [], officers = [];

    const table = $('#projectsTable').DataTable({
        processing: true,
        serverSide: false,
        ajax: {
            url: window.routes.index,
            data: d => d.ajax = true
        },
        columns: [
            { data: 'DT_RowIndex', orderable: false, searchable: false },
            { data: 'project_code' },
            { data: 'name' },
            { data: 'site_manager' },
            {
                data: 'site_officers',
                orderable: false,
                render: data => data.length
                    ? data.map(o => `<span class="badge badge-info mr-1">${o.name}</span>`).join('')
                    : '<span class="text-muted">—</span>'
            },
            {
                data: 'photo',
                orderable: false,
                render: p => p ? `<img src="${p}" width="50" class="rounded img-thumbnail">` : '<span class="text-muted">—</span>'
            },
            { data: 'created_at' },
            {
                data: null,
                orderable: false,
                render: data => `
                    <button class="btn btn-info btn-sm viewBtn" data-id="${data.id}"><i class="fas fa-eye"></i></button>
                    <button class="btn btn-primary btn-sm editBtn" data-id="${data.id}"><i class="fas fa-edit"></i></button>
                `
            }
        ]
    });

    // === LOAD USERS FOR DROPDOWNS ===
    $.get(window.routes.index, { load_users: true }, res => {
        managers = res.managers || [];
        officers = res.officers || [];
    }).fail(() => Swal.fire('Error', 'Failed to load users.', 'error'));

    // === INITIALIZE SELECT2 ===
    function initializeSelect2() {
        // Manager dropdown
        const $manager = $('select[name="site_manager_id"]');
        $manager.empty().append('<option value="">-- Select Manager --</option>');
        managers.forEach(u => $manager.append(`<option value="${u.id}">${u.name}</option>`));
        $manager.select2({
            theme: 'bootstrap',
            width: '100%',
            dropdownParent: $('#projectModal'),
            placeholder: 'Select Manager',
        });

        // Officers dropdown (multiple)
        const $officers = $('select[name="site_officer_ids[]"]');
        $officers.empty();
        officers.forEach(u => $officers.append(`<option value="${u.id}">${u.name}</option>`));
        $officers.select2({
            width: '100%',
            dropdownParent: $('#projectModal'),
            placeholder: 'Select Officers',
            closeOnSelect: false,
        });
    }

    // === ADD MODAL ===
    $('#addProjectBtn').on('click', () => {
        $('#projectForm')[0].reset();
        $('#project_id').val('');
        $('#photoPreview').hide();
        $('#modalTitle').text('Add Project');

        $('select[name="site_manager_id"]').empty();
        $('select[name="site_officer_ids[]"]').empty();
        initializeSelect2();

        $('#projectModal').modal('show');
    });

    // === EDIT MODAL ===
    $(document).on('click', '.editBtn', function () {
        const id = $(this).data('id');
        $.get(window.routes.show(id), data => {
            $('#project_id').val(data.id);
            $('input[name="name"]').val(data.name);
            $('input[name="project_code"]').val(data.project_code);
            $('textarea[name="desc"]').val(data.desc || '');
            $('input[name="blood_group"]').val(data.blood_group || '');

            $('#photoPreview').hide();
            if (data.photo) $('#photoPreview').attr('src', data.photo).show();

            $('#modalTitle').text('Edit Project');
            $('#projectModal').modal('show');

            $('#projectModal').one('shown.bs.modal', () => {
                initializeSelect2();

                $('select[name="site_manager_id"]').val(data.site_manager_id).trigger('change');

                const officerIds = data.site_officers ? data.site_officers.map(o => o.id) : [];
                $('select[name="site_officer_ids[]"]').val(officerIds).trigger('change');
            });
        });
    });

    // === VIEW MODAL ===
    $(document).on('click', '.viewBtn', function () {
        const id = $(this).data('id');
        $.get(window.routes.show(id), data => {
            $('#view_code').text(data.project_code || '—');
            $('#view_name').text(data.name);
            $('#view_desc').text(data.desc || '—');
            $('#view_manager').text(data.site_manager || '—');

            const officersHtml = data.site_officers && data.site_officers.length
                ? data.site_officers.map(o => `<span class="badge badge-info mr-1">${o.name}</span>`).join('')
                : '<span class="text-muted">—</span>';
            $('#view_officers').html(officersHtml);

            $('#view_blood').text(data.blood_group || '—');
            $('#view_created').text(data.created_at);

            if (data.photo) {
                $('#view_photo').attr('src', data.photo).show();
                $('#no_photo').hide();
            } else {
                $('#view_photo').hide();
                $('#no_photo').show();
            }

            $('#viewModal').modal('show');
        });
    });

    // === IMAGE PREVIEW ===
    $('input[name="photo"]').on('change', function () {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = e => $('#photoPreview').attr('src', e.target.result).show();
            reader.readAsDataURL(file);
        } else {
            $('#photoPreview').hide();
        }
    });

    // === FORM SUBMIT ===
    $('#projectForm').on('submit', function (e) {
        e.preventDefault();

        const id = $('#project_id').val();
        const url = id ? window.routes.update(id) : window.routes.store;

        const fd = new FormData(this);
        if (id) fd.append('_method', 'POST');

        $.ajax({
            url,
            type: 'POST',
            data: fd,
            processData: false,
            contentType: false,
            success: res => {
                $('#projectModal').modal('hide');
                table.ajax.reload(null, false);
                Swal.fire('Success', res.message || 'Project saved!', 'success');
            },
            error: xhr => {
                let msg = 'Error.';
                if (xhr.responseJSON?.errors) {
                    msg = Object.values(xhr.responseJSON.errors).flat().join('<br>');
                } else if (xhr.responseJSON?.message) {
                    msg = xhr.responseJSON.message;
                }
                Swal.fire('Error', msg, 'error');
            }
        });
    });
});
</script>
@endsection
