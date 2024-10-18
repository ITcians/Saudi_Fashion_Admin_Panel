
@extends('layout.app')
@section('content')

@if ($message = Session::get('success'))

<div id="successMessage"
    class="alert alert-success alert-dismissible alert-solid alert-label-icon fade show position-fixed top-0 end-0"
    style="z-index: 9999; margin-top: 25px;" role="alert">
    <i class="ri-check-double-line label-icon"></i><strong>{{ $message }}</strong>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif

@if ($message = Session::get('danger'))
<div id="dangerMessage"
    class="alert alert-danger alert-dismissible alert-solid alert-label-icon fade show position-fixed top-0 end-0"
    style="z-index: 9999; margin-top: 25px;" role="alert">
    <i class="ri-error-warning-line label-icon"></i><strong>{{ $message }}</strong>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="alert" aria-label="Close"></button>
</div>
@endif
@if (count($errors) > 0)

<div class="alert alert-danger">
    <strong>Whoops!</strong> There were some problems with your input.<br><br>
    <ul>
        @foreach ($errors->all() as $error)
        <li>{{ $error }}</li>
        @endforeach
    </ul>
</div>
@endif


    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-9">
                        <h4>Events</h4>
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModaladd">
                            Add Events
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="daterange_table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Event Name</th>
                            <th>Event Date</th>
                            <th>Event Description</th>
                            {{-- <th>Cover Image</th> --}}
                            <th>Event Hour</th>
                            <th>Created By</th>
                            <th>Status</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
      <!--  add Modal -->
      <div class="modal fade" id="exampleModaladd" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
      aria-hidden="true">
      <div class="modal-dialog" role="document">
          <div class="modal-content">
              <div class="modal-header">
                  <h5 class="modal-title" id="exampleModalLabel">Add Record</h5>
                  </button>
              </div>
              <div class="modal-body">
                <form action="{{ url('/admin/event/store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="event_name">Event Name</label>
                        <input type="text" id="event_name" name="event_name" required class="form-control" placeholder="Enter the Event Name">
                        @error('event_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="event_date">Event Date</label>
                        <input type="date" id="event_date" name="event_date" required class="form-control">
                        @error('event_date')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="event_description">Event Description</label>
                        <input type="text" id="event_description" name="event_description" required class="form-control" placeholder="Enter the Description">
                        @error('event_description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="cover_image">Cover Image</label>
                        <input type="file" id="cover_image" name="cover_image" required class="form-control">
                        @error('cover_image')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="event_hour">Event Hour</label>
                        <input type="time" id="event_hour" name="event_hour" required class="form-control">
                        @error('event_hour')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <?php
                        use App\Models\User;
                        $users = User::where('account_status', 200)->get();

                    ?>
                <div class="form-group">
                    <label for="created_by">Created By</label>
                    <select id="created_by" name="created_by" required class="form-control">
                        <option value="">Select User</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}">{{ $user->username }}</option>
                        @endforeach
                    </select>
                    @error('created_by')
                        <span class="text-danger">{{ $message }}</span>
                    @enderror
                </div>


                </div>
              <div class="modal-footer">
                  <button type="button" class="btn btn-secondary"
                  data-bs-dismiss="modal">Close</button>
                  <input type="submit" value="Submit" class="btn btn-primary">
              </div>
              </form>
          </div>
      </div>
  </div>
</body>
<script type="text/javascript">
$(function () {
    var table = $('#daterange_table').DataTable({
        processing: true,
        serverSide: true,
        ajax: "{{ route('events.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'event_name', name: 'event_name' },
            { data: 'event_date', name: 'event_date' },
            { data: 'event_description', name: 'event_description' },
            // { data: 'cover_image', name: 'cover_image' },
            { data: 'event_hour', name: 'event_hour' },
            { data: 'created_by', name: 'created_by' },
            {
                data: 'event_status',
                name: 'event_status',
            },
            { data: 'action', name: 'action', orderable: false, searchable: true }
        ]
    });
});





</script>

@endsection()
