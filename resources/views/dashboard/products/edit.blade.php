@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('edit product') }}
@endsection
@csrf
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    {{--simple error tracing--}}
    @include('dashboard.simple error tracing.simple_error_tracing')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$product->id)) }}
                <small>Created at{{ date_format($product->created_at, 'jS M Y') }} / Updated
                    at{{ date_format($product->updated_at, 'jS M Y') }}</small>
            </h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="POST" action="{{ route('dashboard.products.update',$product->id) }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                <!-- Product Name input -->
                <div class="form-group">
                    <label for="InputProductName">{{ __('Name') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="InputProductName"
                           placeholder="Enter username" value="{{ $product->name }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product input -->
                <div class="form-group">
                    <label for="InputServiceType">{{ __('Select Service Type:') }}</label>
                    <select name="service_type_id" id="InputServiceType"
                            class="form-control custom-select @error('service_type_id') is-invalid @enderror">
                        <option selected="selected" disabled>{{ __('Select one') }}</option>
                        @foreach($service_types as $service_type)
                            <option value="{{ $service_type->id }}"
                                    @if( $product->service_type_id  === $service_type->id) selected="selected" @endif >{{ $service_type->name }}</option>
                        @endforeach
                    </select>
                    @error('service_type_id')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Description input -->
                <div class="form-group">
                    <label for="InputProductDescription">{{ __('Description') }}</label>
                    <input name="description" type="text"
                           class="form-control @error('description') is-invalid @enderror"
                           id="InputProductDescription"
                           placeholder="Enter description" value="{{ $product->description }}">
                    @error('description')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Image input -->
                <div class="form-group">
                    <label for="InputProductImage">{{ __('Product Image') }}</label>
                    <div class="input-group">
                        <img alt="No Image" class="table-avatar img-thumbnail" width="30%" height="30%"
                             id="service-icon-image-img-tag"
                             src="{{ $product->image_url }}">
                    </div>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="image" type="file"
                                   class="custom-file-input @error('image') is-invalid @enderror"
                                   id="InputProductImage" value="{{ $product->image }}">
                            <label class="custom-file-label"
                                   for="InputProductImage">{{ __('Choose Product Image') }}</label>
                        </div>
                    </div>
                    @error('image')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Cost input -->
                <div class="form-group">
                    <label for="InputProductCost">{{ __('Cost') }}</label>
                    <input name="cost" type="number" class="form-control @error('cost') is-invalid @enderror"
                           id="InputProductCost"
                           placeholder="Cost" min="1" value="{{ $product->cost }}">
                    @error('cost')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Order input -->
                <div class="form-group">
                    <label for="InputOrder">{{ __('Order') }}</label>
                    <input name="order" type="number" class="form-control @error('order') is-invalid @enderror"
                           id="InputOrder"
                           placeholder="Order" value="{{ $product->order }}">
                    @error('order')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <!-- /.card-body -->

            <div class=" card-footer">
                <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="Save Changes" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
@section('js-script')
    <script>
        function readURL(input) {
            if (input.files && input.files[0]) {
                let reader = new FileReader();

                reader.onload = function (e) {
                    $('#service-icon-image-img-tag').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#InputProductImage").change(function () {
            readURL(this);
        });
    </script>
@endsection
