@extends('admin_layout.admin')
@section('content')
  <!-- Content Wrapper. Contains page content -->
  <div class="content-wrapper">
    <!-- Content Header (Page header) -->
    <section class="content-header">
      <div class="container-fluid">
        <div class="row mb-2">
          <div class="col-sm-6">
            <h1>Product</h1>
          </div>
          <div class="col-sm-6">
            <ol class="breadcrumb float-sm-right">
              <li class="breadcrumb-item"><a href="#">Home</a></li>
              <li class="breadcrumb-item active">Product</li>
            </ol>
          </div>
        </div>
      </div><!-- /.container-fluid -->
    </section>

    <!-- Main content -->
    <section class="content">
      <div class="container-fluid">
        <div class="row">
          <!-- left column -->
          <div class="col-md-12">
            <!-- jquery validation -->
            <div class="card card-success">
              <div class="card-header">
                <h3 class="card-title">Edit product</h3>
              </div>
              @if (Session::has('status'))
              <div class="alert alert-success">
               {{ session::get('status') }}
              </div>
              @endif
              @if (count($errors) > 0)
              <div class="alert alert-danger">
                <ul>
                  @foreach ($errors->all() as $error)
                      <li>{{ $error }}</li>
                  @endforeach
                </ul>
              </div>
                
              @endif
                {!! Form::open(['action'=>'App\Http\Controllers\ProductController@updateproduct','method'=>'POST','enctype'=>'multipart/form-data']) !!}
                {{ csrf_field() }}
                <div class="card-body">
                  <div class="form-group">
                    {{ Form::hidden('id',$product->id) }}
                    {{ Form::label('','Product name',['for'=>'exampleInputEmail1']) }}
                    {{ Form::text('product_name',$product->product_name,['class'=>'form-control','id'=>'exampleInputEmail1','placeholder'=>'Enter product name']) }}
                  </div>
                  <div class="form-group">
                    {{ Form::label('','Product price',['for'=>'exampleInputEmail1']) }}
                    {{ Form::number('product_price',$product->product_price,['class'=>'form-control','id'=>'exampleInputEmail1','placeholder'=>'Enter product price']) }}
                  </div>
                  <div class="form-group">
                    {{ Form::label('','Product category',['for'=>'exampleInputEmail1']) }}
                    {{ Form::select('product_category',$categories,$product->product_category,['class'=>'form-control select2']) }}
                  
                      {{-- <select class="form-control select2" style="width: 100%;">
                        <option selected="selected">Select</option>
                        @foreach ( $categories as $category)
                        <option> {{ $category->category_name }}</option>
                        @endforeach
                      </select> --}}
                    
                  </div>
                  <label for="exampleInputFile">Product image</label>
                  <div class="input-group">
                    <div class="custom-file">
                      {{ Form::file('product_image',['class'=>'custom-file-input','id'=>'exampleInputFile']) }}
                      {{ Form::label('','Choose file',['class'=>'custom-file-label','for'=>'exampleInputFile']) }}
                    </div>
                    <div class="input-group-append">
                      <span class="input-group-text">Upload</span>
                    </div>
                  </div>
                </div>
                <!-- /.card-body -->
                <div class="card-footer">
                  {!! Form::submit('Save',['class'=>'btn btn-success']) !!}
                </div>
                {!! Form::close() !!}
              {{-- </form> --}}
            </div>
            <!-- /.card -->
            </div>
          <!--/.col (left) -->
          <!-- right column -->
          <div class="col-md-6">

          </div>
          <!--/.col (right) -->
        </div>
        <!-- /.row -->
      </div><!-- /.container-fluid -->
    </section>
    <!-- /.content -->
  </div>
  <!-- /.content-wrapper -->
@stop

@section('scripts')
<script>
    $(function () {
      $.validator.setDefaults({
        submitHandler: function () {
          alert( "Form successful submitted!" );
        }
      });
      $('#quickForm').validate({
        rules: {
          email: {
            required: true,
            email: true,
          },
          password: {
            required: true,
            minlength: 5
          },
          terms: {
            required: true
          },
        },
        messages: {
          email: {
            required: "Please enter a email address",
            email: "Please enter a vaild email address"
          },
          password: {
            required: "Please provide a password",
            minlength: "Your password must be at least 5 characters long"
          },
          terms: "Please accept our terms"
        },
        errorElement: 'span',
        errorPlacement: function (error, element) {
          error.addClass('invalid-feedback');
          element.closest('.form-group').append(error);
        },
        highlight: function (element, errorClass, validClass) {
          $(element).addClass('is-invalid');
        },
        unhighlight: function (element, errorClass, validClass) {
          $(element).removeClass('is-invalid');
        }
      });
    });
    </script>