
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
                        <h4>Posts</h4>
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModaladd">
                            Add Post
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="daterange_table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Post</th>
                            <th>Allow Comments</th>
                            <th>Visibiliy</th>
                            <th>is_drafted</th>
                            <th>created_by</th>
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
                <form action="{{ url('/admin/post/store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="mb-3">
                        <label for="post">Post</label>
                        <input type="text" id="post" name="post" required class="form-control" placeholder="Enter the Post Name">
                        @error('post')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="cover">Cover</label>
                        <input type="file" id="cover" name="cover" required class="form-control">
                        @error('cover')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="allow_comments">Allow Comments</label>
                        <select id="allow_comments" name="allow_comments" required class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('allow_comments')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="visibiliy">Visibiliy</label>
                        <select id="visibiliy" name="visibiliy" required class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('visibiliy')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="mb-3">
                        <label for="is_drafted">Is Drafted</label>
                        <select id="is_drafted" name="is_drafted" required class="form-control">
                            <option value="1">Yes</option>
                            <option value="0">No</option>
                        </select>
                        @error('is_drafted')
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
        ajax: "{{ route('posts.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'post', name: 'post' },
            {
              data: 'allow_comments',
              name: 'allow_comments',
              render: function(data) {
                  return data == 1 ? 'Yes' : 'No';
              }
            },
            {
              data: 'visibiliy',
              name: 'visibiliy',
              render: function(data) {
                  return data == 1 ? 'Yes' : 'No';
              }
            },
            {
              data: 'is_drafted',
              name: 'is_drafted',
              render: function(data) {
                  return data == 1 ? 'Yes' : 'No';
              }
            },


            { data: 'created_by', name: 'created_by' },
            {
                data: 'status',
                name: 'status',
            },
            { data: 'action', name: 'action', orderable: false, searchable: true }
        ]
    });
});





</script>


@endsection()
