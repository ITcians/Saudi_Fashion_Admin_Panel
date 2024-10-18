
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
                        <h4>Products</h4>
                    </div>
                    <div class="col-3 text-end">
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModaladd">
                            Add Products
                        </button>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table class="table table-bordered" id="daterange_table">
                    <thead>
                        <tr>
                            <th>Id</th>
                            <th>Title</th>
                            <th>Description</th>
                            <th>Care Advice</th>
                            <th>Material</th>
                            <th>Price</th>
                            <th>Quantity</th>
                            {{-- <th>Created By</th> --}}
                            {{-- <th>Category Id</th> --}}
                            {{-- <th>Sub Category Id</th> --}}
                            {{-- <th>Status</th> --}}
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
                <form action="{{ url('/admin/product/store') }}" method="post" enctype="multipart/form-data">
                    @csrf
                    <div class="form-group">
                        <label for="title">Title</label>
                        <input type="text" id="title" name="title" required class="form-control" placeholder="Enter the product title">
                        @error('title')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <input type="text" id="description" name="description" required class="form-control" placeholder="Enter the description">
                        @error('description')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="care_advice">Care Advice</label>
                        <input type="text" id="care_advice" name="care_advice" required class="form-control" placeholder="Enter the care advice">
                        @error('care_advice')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="material">Material</label>
                        <input type="text" id="material" name="material" required class="form-control" placeholder="Enter the useable material">
                        @error('material')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="price">Price</label>
                        <input type="number" id="price" name="price" required class="form-control" placeholder="Enter the selling price">
                        @error('price')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <div class="form-group">
                        <label for="quantity">Quantity</label>
                        <input type="number" id="quantity" name="quantity" required class="form-control" placeholder="Enter the avaiable stock quantity" >
                        @error('quantity')
                            <span class="text-danger">{{ $message }}</span>
                        @enderror
                    </div>
                    <?php
                    use App\Models\User;
                    $users = User::where('account_status',200)->get();

                ?>
            <div class="form-group">
                <label for="created_by">User</label>
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
                    <?php
                    use App\Models\CategoryModel;
                    $categorys = CategoryModel::all();

                ?>
            <div class="form-group">
                <label for="category_id">Category</label>
                <select id="category_id" name="category_id" required class="form-control">
                    <option value="">Select Category</option>
                    @foreach($categorys as $category)
                        <option value="{{ $category->id }}">{{ $category->category }}</option>
                    @endforeach
                </select>
                @error('category_id')
                    <span class="text-danger">{{ $message }}</span>
                @enderror
                </div>
                <?php
                use App\Models\SubCategoryModel;
                $subcategory = SubCategoryModel::all();

            ?>
        <div class="form-group">
            <label for="sub_category_id">Sub Category</label>
            <select id="sub_category_id" name="sub_category_id" required class="form-control">
                <option value="">Select Category</option>
                @foreach($subcategory as $category)
                    <option value="{{ $category->id }}">{{ $category->sub_category }}</option>
                @endforeach
            </select>
            @error('sub_category_id')
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
        ajax: "{{ route('products.index') }}",
        columns: [
            { data: 'id', name: 'id' },
            { data: 'title', name: 'title' },
            { data: 'description', name: 'description' },
            { data: 'care_advice', name: 'care_advice' },
            { data: 'material', name: 'material' },
            { data: 'price', name: 'price' },
            { data: 'quantity', name: 'quantity' },
            // { data: 'created_by', name: 'created_by' },
            // { data: 'category_id', name: 'category_id' },
            // { data: 'sub_category_id', name: 'sub_category_id' },
            // {
            //             data: 'status',
            //             name: 'status',
            //         },
            { data: 'action', name: 'action', orderable: true, searchable: true }
        ]
    });
});





</script>

@endsection()
