@extends('Admin.layout.app')

@section('title', 'Special Technical Training')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">Special Technical Training</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Special Technical Training</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="container-fluid">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <button class="btn btn-primary btn-sm" id="addNew">+ Add New</button>
            </div>

            <div class="card-body">
                <table id="sttTable" class="table table-bordered table-striped table-hover">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Project</th>
                            <th>Location</th>
                            <th>Contractor</th>
                            <th>Persons</th>
                            <th>Duration</th>
                            <th>Topics</th>
                            <th>Photo</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody></tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="sttModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="sttForm" enctype="multipart/form-data">
            @csrf

            <!-- Hidden fields -->
            <input type="hidden" name="project_id" id="project_id">
            <input type="hidden" id="record_id" name="record_id">

            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Special Technical Training</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">x</button>
                </div>

                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="location" label="Location" placeholder="Enter location" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="contractor_name" label="Contractor Name" placeholder="Enter contractor name" required />
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6">
                            <x-input name="num_persons_attended" label="Number of Persons" type="number" placeholder="e.g. 25" required />
                        </div>
                        <div class="col-md-6">
                            <x-input name="duration_seconds" label="Duration (Seconds)" type="number" placeholder="e.g. 1800" required />
                        </div>
                    </div>

                    <x-textarea name="topics_discussed" label="Topics Discussed" placeholder="Enter topics covered..." rows="4" />

                    <div class="form-group">
                        <label for="photo">Photo <small class="text-muted">(Optional)</small></label>
                        <input type="file" name="photo" id="photo" class="form-control" accept="image/*">
                        <small class="text-muted">Max 2MB | JPG, PNG, JPEG</small>

                        <div class="mt-2 text-center">
                            <img id="previewImage" src="" alt="Photo Preview"
                                 style="display:none; max-width:180px; border-radius:8px; box-shadow:0 2px 8px rgba(0,0,0,0.1);">
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

<!-- View Modal -->
<div class="modal fade" id="viewModal" tabindex="-1">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title">View Special Technical Training</h5>
                <button type="button" class="close text-white" data-dismiss="modal">x</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th width="25%">Project</th>
                                <td id="view_project"></td>
                            </tr>
                            <tr>
                                <th>Location</th>
                                <td id="view_location"></td>
                            </tr>
                            <tr>
                                <th>Contractor</th>
                                <td id="view_contractor"></td>
                            </tr>
                            <tr>
                                <th>No. of Persons</th>
                                <td id="view_persons"></td>
                            </tr>
                            <tr>
                                <th>Duration</th>
                                <td id="view_duration"></td>
                            </tr>
                            <tr>
                                <th>Topics Discussed</th>
                                <td id="view_topics"></td>
                            </tr>
                            <tr>
                                <th>Date & Time</th>
                                <td id="view_date"></td>
                            </tr>
                            <tr>
                                <th>Photo</th>
                                <td>
                                    <img id="view_photo" src="" alt="Training Photo"
                                         style="max-width:250px; border-radius:8px; border:1px solid #ddd; display:none;">
                                    <span id="no_photo" class="text-muted" style="display:none;">— No photo —</span>
                                </td>
                            </tr>
                        </tbody>
                    </table>
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
<script>
    const storageUrl = "{{ asset('storage') }}";

    $(function () {
        // 1. FETCH ASSIGNED PROJECT
        $.get("{{ route('get.assigned.project') }}", function (res) {
            if (res.success && res.project_id) {
                $('#project_id').val(res.project_id);
            } else {
                toastr.warning(res.message || 'No project assigned.');
                $('#addNew').prop('disabled', true);
            }
        }).fail(() => {
            toastr.error('Failed to load project.');
            $('#addNew').prop('disabled', true);
        });

        // 2. DATATABLE
        let table = $('#sttTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('special.technical.training') }}",
            columns: [
                { data: 'DT_RowIndex', orderable: false, searchable: false },
                { data: 'project_name', name: 'project_name' },
                { data: 'location', name: 'location' },
                { data: 'contractor_name', name: 'contractor_name' },
                { data: 'num_persons_attended', name: 'num_persons_attended' },
                { data: 'duration_seconds', name: 'duration_seconds' },
                { data: 'topics_discussed', name: 'topics_discussed', defaultContent: '—' },
                {
                    data: 'photo',
                    orderable: false,
                    searchable: false,
                    render: p => p ? `<img src="${storageUrl}/${p}" width="60" class="rounded">` : '—'
                },
                { data: 'formatted_date', name: 'formatted_date' },
                {
                    data: 'action',
                    orderable: false,
                    searchable: false,
                    render: (d, t, r) => `
                        <button class="btn btn-info btn-sm viewBtn" data-id="${r.id}"><i class="fas fa-eye"></i></button>
                        <button class="btn btn-primary btn-sm editBtn" data-id="${r.id}"><i class="fas fa-edit"></i></button>
                    `
                }
            ],
            responsive: true,
            pageLength: 10,
            lengthMenu: [10, 25, 50, 100]
        });

        // 3. ADD NEW
        $('#addNew').click(function () {
            $('#sttForm')[0].reset();
            $('#record_id').val('');
            $('#previewImage').hide();
            $('#sttModal .modal-title').text('Add Special Technical Training');
            $('#sttModal').modal('show');
        });

        // 4. EDIT
        $(document).on('click', '.editBtn', function () {
            const id = $(this).data('id');
            $.get("{{ route('special.technical.training') }}/" + id + "/Edit", function (data) {
                $('#record_id').val(data.id);
                $('#project_id').val(data.project_id);
                $('#location').val(data.location);
                $('#contractor_name').val(data.contractor_name);
                $('#num_persons_attended').val(data.num_persons_attended);
                $('#duration_seconds').val(data.duration_seconds);
                $('#topics_discussed').val(data.topics_discussed);

                if (data.photo) {
                    $('#previewImage').attr('src', storageUrl + '/' + data.photo).show();
                } else {
                    $('#previewImage').hide();
                }

                $('#sttModal .modal-title').text('Edit Special Technical Training');
                $('#sttModal').modal('show');
            });
        });

        // 5. VIEW
        $(document).on('click', '.viewBtn', function () {
            const id = $(this).data('id');
            $.get("{{ route('special.technical.training') }}/" + id)
                .done(function (data) {
                    $('#view_project').text(data.project_name || '—');
                    $('#view_location').text(data.location);
                    $('#view_contractor').text(data.contractor_name);
                    $('#view_persons').text(data.num_persons_attended);
                    $('#view_duration').text(data.duration_seconds + ' seconds');
                    $('#view_topics').text(data.topics_discussed || '—');
                    $('#view_date').text(data.formatted_date);

                    if (data.photo) {
                        $('#view_photo').attr('src', storageUrl + '/' + data.photo).show();
                        $('#no_photo').hide();
                    } else {
                        $('#view_photo').hide();
                        $('#no_photo').show();
                    }

                    $('#viewModal').modal('show');
                })
                .fail(() => toastr.error('Failed to load record.'));
        });

        // 6. IMAGE PREVIEW
        $('#photo').on('change', function (e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = ev => $('#previewImage').attr('src', ev.target.result).show();
                reader.readAsDataURL(file);
            }
        });

        // 7. SAVE / UPDATE
        $('#sttForm').submit(function (e) {
            e.preventDefault();
            const id = $('#record_id').val();
            const formData = new FormData(this);

            const url = id
                ? "{{ route('special.technical.training') }}/" + id + "/Update"
                : "{{ route('special.technical.training.store') }}";

            $.ajax({
                url, type: "POST", data: formData,
                contentType: false, processData: false,
                success: res => {
                    $('#sttModal').modal('hide');
                    table.ajax.reload();
                    toastr.success(res.success || 'Saved successfully!');
                },
                error: xhr => {
                    const errors = xhr.responseJSON?.errors;
                    if (errors) {
                        $.each(errors, (k, v) => toastr.error(v[0]));
                    } else {
                        toastr.error('Something went wrong.');
                    }
                }
            });
        });
    });
</script>
@endsection