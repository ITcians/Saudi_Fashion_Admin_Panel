
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
                        <h4>Color Record</h4>
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModaladd">
                            ADD Color
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="daterange_table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Color Name</th>
                            <th>Color Code</th>
                            <th>Product</th>
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
                <form action="{{ url('/admin/color/store') }}" method="post" enctype="multipart/form-data">
                    @csrf   
                    <div class="form-group">
                        <label  for="color_name">Color Name </label>
                        <input type="text" id="color_name" name="color_name" required class="form-control" placeholder="Enter the color name">
                        @error('color_name')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="color_code">Color Code </label>
                        <input type="text" id="color_code" name="color_code" required class="form-control" placeholder="Enter the color">
                        @error('color_code')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <?php
                    use App\Models\ProductModel;
                    $products = ProductModel::all();

                ?>
            <div class="form-group">
                <label for="product_id">Product </label>
                <select id="product_id" name="product_id" required class="form-control">
                    <option value="">Select Category</option>
                    @foreach($products as $product)
                        <option value="{{ $product->id }}">{{ $product->title }}</option>
                    @endforeach
                </select>
                @error('product_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
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
        ajax: "{{ route('colors.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'color_name', name: 'color_name' },
            { data: 'color_code', name: 'color_code' },
            { data: 'product_id', name: 'product_id' },
            { data: 'action', name: 'action', orderable: false, searchable: true }
        ]
    });
});





</script>

@endsection()
