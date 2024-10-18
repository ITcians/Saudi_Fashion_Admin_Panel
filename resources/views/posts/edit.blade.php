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


<div class="container">
    <div class="row ">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h4>Update Record</h4>
                </div>
                <div class="card-body">
                    <form action="{{ url('/admin/post/update/'.$post->id) }}" method="post" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="mb-3">
                            <label for="post">Post</label>
                            <input type="text" id="post" name="post" required class="form-control" value="{{ $post->post }}">
                            @error('post')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="cover">Cover</label>
                            <input type="file" id="cover" name="cover" required class="form-control" {{ $post->cover }}>
                            @error('cover')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="allow_comments">Allow Comments</label>
                            <select id="allow_comments" name="allow_comments" required class="form-control" {{ $post->allow_comments }}>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('allow_comments')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="visibiliy">Visibiliy</label>
                            <select id="visibiliy" name="visibiliy" required class="form-control" {{ $post->visibiliy }}>
                                <option value="1">Yes</option>
                                <option value="0">No</option>
                            </select>
                            @error('visibiliy')
                                <span class="text-danger">{{ $message }}</span>
                            @enderror
                        </div>
                        <div class="mb-3">
                            <label for="is_drafted">Is Drafted</label>
                            <select id="is_drafted" name="is_drafted" required class="form-control" {{ $post->is_drafted }}>
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
                    <select id="created_by" name="created_by" required class="form-control" {{ $post->created_by }}>
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
                        <div class="text-end">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>


@endsection
