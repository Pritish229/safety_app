@extends('Admin.layout.app')

@section('title', 'Induction Training')

@section('content')
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1>Induction Training</h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="{{ url('/') }}">Home</a></li>
                    <li class="breadcrumb-item active">Induction Training</li>
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
                <table id="inductionTable" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Location</th>
                            <th>Contractor</th>
                            <th>Persons</th>
                            <th>Duration</th>
                            <th>Notes</th>
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
<div class="modal fade" id="inductionModal" tabindex="-1">
    <div class="modal-dialog">
        <form id="inductionForm" enctype="multipart/form-data">
            @csrf
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Add Induction Training</h5>
                    <button type="button" class="close" data-dismiss="modal">&times;</button>
                </div>

                <div class="modal-body">
                    <input type="hidden" id="record_id" name="record_id">

                    <x-input name="location" label="Location" placeholder="Enter location" required />

                    <x-input name="contractor_name" label="Contractor Name" placeholder="Enter contractor name" required />

                    <x-input name="num_persons_attended" label="Number of Persons" type="number" placeholder="Enter total persons" required />

                    <x-input name="duration_seconds" label="Duration (Seconds)" type="number" placeholder="Enter duration" required />

                    <x-textarea name="notes" label="Notes" placeholder="Enter additional notes" rows="3" />

                    <div class="form-group">
                        <label for="photo" class="form-label">Photo</label>
                        <input type="file" name="photo" id="photo" class="form-control">
                        <small>Maximum 2MB JPG,PNG,JPEG</small>

                        <!-- 🖼️ Image Preview -->
                        <div class="mt-2">
                            <img id="previewImage" src="" alt="Image Preview"
                                style="display:none; width:100px; height:100px; border-radius:5px; object-fit:cover; border:1px solid #ddd;">
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
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">View Induction Training</h5>
                <button type="button" class="close" data-dismiss="modal">&times;</button>
            </div>

            <div class="modal-body">
                <table class="table table-bordered">
                    <tbody>
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
                            <th>Notes</th>
                            <td id="view_notes"></td>
                        </tr>
                        <tr>
                            <th>Date</th>
                            <td id="view_date"></td>
                        </tr>
                        <tr>
                            <th>Photo</th>
                            <td><img id="view_photo" src="" style="width:150px; border-radius:5px; border:1px solid #ddd; display:none;"></td>
                        </tr>
                    </tbody>
                </table>
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

        // ✅ DataTable
        let table = $('#inductionTable').DataTable({
            processing: true,
            serverSide: true,
            ajax: "{{ route('induction.training') }}",
            columns: [{
                    data: 'DT_RowIndex',
                    name: 'DT_RowIndex'
                },
                {
                    data: 'location',
                    name: 'location'
                },
                {
                    data: 'contractor_name',
                    name: 'contractor_name'
                },
                {
                    data: 'num_persons_attended',
                    name: 'num_persons_attended'
                },
                {
                    data: 'duration_seconds',
                    name: 'duration_seconds'
                },
                {
                    data: 'notes',
                    name: 'notes'
                },
                {
                    data: 'photo',
                    name: 'photo',
                    orderable: false,
                    searchable: false
                },
                {
                    data: 'formatted_date',
                    name: 'formatted_date'
                },
                {
                    data: 'action',
                    name: 'action',
                    orderable: false,
                    searchable: false
                },
            ]
        });

        // 🟢 Add New
        $('#addNew').click(function() {
            $('#inductionForm')[0].reset();
            $('#record_id').val('');
            $('#previewImage').hide();
            $('#photo').val('');
            $('#inductionModal').modal('show');
            $('.modal-title').text('Add Induction Training');
        });

        // ✏️ Edit Record
        $(document).on('click', '.editBtn', function() {
            let id = $(this).data('id');
            $.get("{{ route('induction.training') }}/" + id + "/Edit", function(data) {
                $('#record_id').val(data.id);
                $('#location').val(data.location);
                $('#contractor_name').val(data.contractor_name);
                $('#num_persons_attended').val(data.num_persons_attended);
                $('#duration_seconds').val(data.duration_seconds);
                $('#notes').val(data.notes);

                if (data.photo) {
                    $('#previewImage').attr('src', "{{ asset('storage') }}/" + data.photo).show();
                } else {
                    $('#previewImage').hide();
                }

                $('#inductionModal').modal('show');
                $('.modal-title').text('Edit Induction Training');
            });
        });

        // 👁️ View Record (Modal)
        $(document).on('click', '.viewBtn', function() {
            let id = $(this).data('id');
            $.get("{{ route('induction.training') }}/" + id)
                .done(function(data) {
                    $('#view_location').text(data.location);
                    $('#view_contractor').text(data.contractor_name);
                    $('#view_persons').text(data.num_persons_attended);
                    $('#view_duration').text(data.duration_seconds + ' seconds');
                    $('#view_notes').text(data.notes ? data.notes : '—');
                    $('#view_date').text(data.formatted_date);

                    if (data.photo) {
                        $('#view_photo').attr('src', "{{ asset('storage') }}/" + data.photo).show();
                    } else {
                        $('#view_photo').hide();
                    }

                    $('#viewModal').modal('show');
                })
                .fail(function(xhr) {
                    toastr.error('Unable to fetch details.');
                    console.error(xhr.responseText);
                });
        });

        // 💾 Save / Update
        $('#inductionForm').submit(function(e) {
            e.preventDefault();
            let id = $('#record_id').val();
            let formData = new FormData(this);

            let url = id ?
                "{{ route('induction.training') }}/" + id + "/Update" :
                "{{ route('induction.training.store') }}";

            $.ajax({
                type: "POST",
                url: url,
                data: formData,
                cache: false,
                contentType: false,
                processData: false,
                success: function(res) {
                    $('#inductionModal').modal('hide');
                    $('#inductionForm')[0].reset();
                    table.ajax.reload();
                    toastr.success(res.success);
                },
                error: function(err) {
                    toastr.error('Error saving record');
                }
            });
        });

        // 🖼️ Image Preview
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