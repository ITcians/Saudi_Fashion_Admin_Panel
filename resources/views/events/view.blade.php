
@extends('layout.app')
@section('content')

<div class="row">
    <div class="col-xxl-3">
        <div class="card">
            <div class="card-body p-4">
                <div>
                    <div class="row">
                        <div class="col-4">
                            <div class="flex-shrink-0 mx-auto">
                                <div class=" bg-light rounded">

                                    @if ($event->cover_image)
                                        <img class="sq_image" src="{{ asset('upload_cover_image/' . $event->cover_image) }}"
                                            alt="event Image">
                                    @else
                                        <p class="text-center">Image not found</p>
                                    @endif
                                </div>
                            </div>

                            <div class="mt-4 text-center">
                            </div>
                        </div>
                        <div class="col-8">
                            <div class="table-responsive">
                                <table class="table mb-0 table-borderless">
                                    <tbody>
                                        <tr>
                                            <th><span class="fw-medium">Event Title  : </span></th>
                                            <td>{{ ucwords($event->event_name) }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Event Description  : </span></th>
                                            <td>{{ ucwords($event->event_description) }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Event Date  : </span></th>
                                            <td>{{ ucwords($event->event_date) }}</td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Event Hour  : </span></th>
                                            <td>{{ ucwords($event->event_hour) }}
                                            </td>
                                        </tr>
                                        <tr>
                                            <th><span class="fw-medium">Event Status :</span></th>
                                            <td>
                                                <?php 
                                                if ($event->event_status == 200) {
                                                    echo "<span class='badge bg-primary'>Active</span>";
                                                } else {
                                                    echo "<span class='badge bg-danger'>Inactive</span>";
                                                }
                                                ?>
                                                
                                            </td>
                                        </tr>
                                        {{-- <tr>
                                            <th><span class="fw-medium">Created By  : </span></th>
                                            <td>{{ ucwords($event->user->username) }}
                                            </td>
                                        </tr>  --}}
                                        <tr>
                                            <th><span class="fw-medium">Created At  : </span></th>
                                            <td>{{ ucwords($event->created_at) }}
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>






</script>

@endsection()
