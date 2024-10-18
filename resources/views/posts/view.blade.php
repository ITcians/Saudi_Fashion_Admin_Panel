

    {{-- previous code  --}}

    

{{-- previous code --}}

@extends('layout.app')
@section('title', 'Post Detail')
@section('content')

    <!-- start page title -->
    <div class="row">
        <div class="col-12">
            <div class="page-title-box d-sm-flex align-items-center justify-content-between">
                <h4 class="mb-sm-0">Post Detail</h4>
                <div class="page-title-right">
                    <ol class="breadcrumb m-0">
                        <li class="breadcrumb-item"><a href="javascript: void(0);">Dashboard</a></li>
                        <li class="breadcrumb-item active">Post Detail</li>
                    </ol>
                </div>
            </div>
        </div>
    </div>
  
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body">
                    <div class="row gx-lg-5">
                        <div class="col-xl-4 col-md-8 mx-auto">
                            <div class="product-img-slider sticky-side-div">
                                <div class="swiper product-thumbnail-slider p-2 rounded bg-light">
                                    <div class="swiper-wrapper">
                                        @if ($post->cover)
                                            <img src="{{ asset('upload_image/' . $post->cover) }}"
                                            alt="Post Image" class="post-img" height="500px">
                                            @else
                                            <p class="text-center">cover not found</p>
                                            @endif

                                    </div>
                                    <div class="swiper-button-next"></div>
                                    <div class="swiper-button-prev"></div>
                                </div>
                            </div>
                        </div>

                        <div class="col-xl-8">
                            <div class="mt-xl-0 mt-5">
                                <div class="d-flex">
                                    <div class="flex-grow-1">
                                        <h4>{{ $post->post }}</h4>
                                        <div class="hstack gap-3 flex-wrap">
                                            {{-- <div><a href="#" class="text-primary d-block">Member</a></div> --}}
                                            <div class="text-muted">Post Created  : <span
                                                    class="text-body fw-medium">{{ $post->user->username }}:
                                                    </span></div>
                                            <div class="vr"></div>
                                            <div class="text-muted">Published : <span
                                                    class="text-body fw-medium">{{ ucwords($post->created_at) }}</span></div>
                                        </div>
                                    </div>
                                    <div class="flex-shrink-0">

                                    </div>
                                </div>




                                <div class="tab-content border  p-4 mt-4" id="nav-tabContent">
                                    <div class="table-responsive">
                                        <table class="table mb-0">
                                            <tbody>
                                                <tr>
                                                    <th scope="row" style="width: 200px;">Comments</th>
                                                    <td>
                                                        <?php
                                                        if ($post->allow_comments == 1) {
                                                            echo '<span class="badge bg-success">Enable</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Disable</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Visibilty</th>
                                                    <td>
                                                        <?php
                                                        if ($post->visibiliy == 1) {
                                                            echo '<span class="badge bg-success">Enable</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Disable</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Status</th>
                                                    <td>
                                                        <?php
                                                        if ($post->status == 200) {
                                                            echo '<span class="badge bg-success">Enable</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Disable</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Is Drafted</th>
                                                    <td>
                                                        <?php
                                                            if ($post->is_drafted == 1) {
                                                            echo '<span class="badge bg-success">Enable</span>';
                                                        } else {
                                                            echo '<span class="badge bg-danger">Disable</span>';
                                                        }
                                                        ?>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <th scope="row">Created At</th>
                                                    <td>{{ ucwords($post->created_at) }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>

                            </div>
                        </div>
                        <!-- product-content -->
                    </div>
                </div>
                <!-- end col -->
            </div>
            <!-- end row -->
        </div>
        <!-- end card body -->
    </div>

    
    <div class="row">
        <div class="col-xl-12">
            <div class="card">
                <div class="card-body">
                    <!-- Nav tabs -->
                    <ul class="nav nav-tabs mb-3" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#postcomments" role="tab" aria-selected="false">
                                Post Comments
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#flag" role="tab" aria-selected="false">
                                Flag Posts
                            </a>
                        </li>
                    </ul>
                    <!-- Tab panes -->
                    <div class="tab-content text-muted">
                        <div class="tab-pane" id="postcomments" role="tabpanel">
                            <h6>Post Comments</h6>
                            <div class="table-responsive">
                                <table id="deposit_request_table" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Comment</th>
                                            <th>Post_ID</th>
                                            <th>User_Id</th>
                                            <th>Reply To User</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($comments as $comment)
                                        <tr>
                                            <td>{{ $comment->id }}</td>
                                            <td>{{ $comment->comment }}</td>
                                            <td>{{ $comment->post->post }}</td>
                                            <td>{{ $comment->user->username }}</td>
                                            <td>{{ $comment->user->username }}</td>
                                            <td>{{ $comment->created_at }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $comments->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div><!-- end tab-content -->
    
                    <div class="tab-content text-muted">
                        <div class="tab-pane" id="flag" role="tabpanel">
                            <h6>Flag Post</h6>
                            <div class="table-responsive">
                                <table id="deposit_request_table" class="table table-bordered nowrap table-striped align-middle" style="width:100%">
                                    <thead>
                                        <tr>
                                            <th>ID</th>
                                            <th>Post</th>
                                            <th>Flagged By User</th>
                                            <th>Reason</th>
                                            <th>Created At</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        @foreach ($flags as $flag)
                                        <tr>
                                            <td>{{ $flag->id }}</td>
                                            <td>{{ $flag->post->post }}</td>
                                            <td>{{ $flag->user->username }}</td>
                                            <td>{{ $flag->reason }}</td>
                                            <td>{{ $flag->created_at }}</td>
                                        </tr>
                                        @endforeach
                                    </tbody>
                                </table>
                                {{ $flags->links('pagination::bootstrap-5') }}
                            </div>
                        </div>
                    </div><!-- end tab-content -->
                </div><!-- end card-body -->
            </div><!-- end card -->
        </div><!-- end col -->
    </div><!-- end row -->
    


    @endsection
