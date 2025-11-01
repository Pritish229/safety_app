@extends('Admin.layout.app')

@section('title', 'Pep Talk')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Pep Talk</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Pep Talk</li>
                </ol>
            </div>
        </div>
    </div>
</div>

<div class="content">
    <div class="">
        <div class="card">
            <div class="card-header">
                <button class="btn btn-sm btn-primary my-1" id="addNew">+ Add New</button>
            </div>

            <div class="p-3">
                <table id="pepTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
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
                </table>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit Modal -->
<div class="modal fade" id="pepModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <form id="pepForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">Add Pep Talk</h5>
                    <button type="button" class="close text-white" data-dismiss="modal">×</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="record_id" name="record_id">
                    <input type="hidden" id="project_id" name="project_id"> <!-- ✅ Auto-filled project ID -->

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
                <h5 class="modal-title">View Pep Talk</h5>
                <button type="button" class="close text-white" data-dismiss="modal">×</button>
            </div>

            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr><th width="25%">Location</th><td id="view_location"></td></tr>
                            <tr><th>Contractor</th><td id="view_contractor"></td></tr>
                            <tr><th>No. of Persons</th><td id="view_persons"></td></tr>
                            <tr><th>Duration</th><td id="view_duration"></td></tr>
                            <tr><th>Topics Discussed</th><td id="view_topics"></td></tr>
                            <tr><th>Date & Time</th><td id="view_date"></td></tr>
                            <tr>
                                <th>Photo</th>
                                <td>
                                    <img id="view_photo" src="" alt="Pep Talk Photo"
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
$(function() {

    // 🧩 Fetch assigned project for logged-in site officer
    $.get("{{ route('get.assigned.project') }}", function(response) {
        if (response.success) {
            $('#project_id').val(response.project_id);
            console.log('Assigned project:', response.project_name);
        } else {
            toastr.warning(response.message);
        }
    }).fail(() => toastr.error('Failed to fetch project ID.'));

    // ✅ DataTable setup
    let table = $('#pepTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('pep.talk') }}",
        columns: [
            { data: 'DT_RowIndex', name: 'DT_RowIndex' },
            { data: 'location', name: 'location' },
            { data: 'contractor_name', name: 'contractor_name' },
            { data: 'num_persons_attended', name: 'num_persons_attended' },
            { data: 'duration_seconds', name: 'duration_seconds' },
            { data: 'topics_discussed', name: 'topics_discussed' },
            { data: 'photo', name: 'photo', orderable: false, searchable: false },
            { data: 'formatted_date', name: 'formatted_date' },
            { data: 'action', name: 'action', orderable: false, searchable: false },
        ]
    });

    // 🟢 Add New
    $('#addNew').click(function() {
        $('#pepForm')[0].reset();
        $('#record_id').val('');
        $('#previewImage').hide();
        $('#pepModal').modal('show');
        $('.modal-title').text('Add Pep Talk');
    });

    // ✏️ Edit Record
    $(document).on('click', '.editBtn', function() {
        let id = $(this).data('id');
        $.get("{{ route('pep.talk') }}/" + id + "/Edit", function(data) {
            $('#record_id').val(data.id);
            $('#location').val(data.location);
            $('#contractor_name').val(data.contractor_name);
            $('#num_persons_attended').val(data.num_persons_attended);
            $('#duration_seconds').val(data.duration_seconds);
            $('#topics_discussed').val(data.topics_discussed);
            if (data.photo) {
                $('#previewImage').attr('src', "{{ asset('storage') }}/" + data.photo).show();
            } else {
                $('#previewImage').hide();
            }
            $('#pepModal').modal('show');
            $('.modal-title').text('Edit Pep Talk');
        });
    });

    // 👁️ View Record
    $(document).on('click', '.viewBtn', function() {
        let id = $(this).data('id');
        $.get("{{ route('pep.talk') }}/" + id)
        .done(function(data) {
            $('#view_location').text(data.location);
            $('#view_contractor').text(data.contractor_name);
            $('#view_persons').text(data.num_persons_attended);
            $('#view_duration').text(data.duration_seconds + ' seconds');
            $('#view_topics').text(data.topics_discussed || '—');
            $('#view_date').text(data.formatted_date);
            if (data.photo) {
                $('#view_photo').attr('src', "{{ asset('storage') }}/" + data.photo).show();
                $('#no_photo').hide();
            } else {
                $('#view_photo').hide();
                $('#no_photo').show();
            }
            $('#viewModal').modal('show');
        })
        .fail(() => toastr.error('Unable to fetch details.'));
    });

    // 💾 Save / Update
    $('#pepForm').submit(function(e) {
        e.preventDefault();
        let id = $('#record_id').val();
        let formData = new FormData(this);

        let url = id
            ? "{{ route('pep.talk') }}/" + id + "/Update"
            : "{{ route('pep.talk.store') }}";

        $.ajax({
            type: "POST",
            url: url,
            data: formData,
            cache: false,
            contentType: false,
            processData: false,
            success: function(res) {
                $('#pepModal').modal('hide');
                $('#pepForm')[0].reset();
                table.ajax.reload();
                toastr.success(res.success);
            },
            error: function(err) {
                toastr.error('Error saving record');
                console.error(err.responseText);
            }
        });
    });

    // 🖼️ Preview Image
    $('#photo').change(function(e) {
        let reader = new FileReader();
        reader.onload = function(event) {
            $('#previewImage').attr('src', event.target.result).show();
        }
        reader.readAsDataURL(e.target.files[0]);
    });

});
</script>
@endsection
