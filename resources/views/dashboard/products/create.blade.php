@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('create product') }}
@endsection
@csrf
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    {{--simple error tracing--}}
    @include('dashboard.simple error tracing.simple_error_tracing')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst($page_title) }}</h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="POST" action="{{ route('dashboard.products.store') }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <!-- Product Name input -->
                <div class="form-group">
                    <label for="inputProductName">{{ __('Name') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputProductName"
                           placeholder="Enter product name" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Type input -->
                <div class="form-group">
                    <label for="inputServiceType">{{ __('Select Service Type:') }}</label>
                    <select name="service_type_id" id="inputServiceType"
                            class="form-control custom-select @error('service_type_id') is-invalid @enderror">
                        <option selected="selected" disabled>{{ __('Select one') }}</option>
                        @foreach($service_types as $service_type)
                            <option value="{{ $service_type->id }}"
                                    @if( (int)old('service_type_id')  === $service_type->id) selected="selected" @endif >{{ $service_type->name }}</option>
                        @endforeach
                    </select>
                    @error('service_type_id')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Description input -->
                <div class="form-group">
                    <label for="inputProductDescription">{{ __('Description') }}</label>
                    <input name="description" type="text"
                           class="form-control @error('description') is-invalid @enderror"
                           id="inputProductDescription"
                           placeholder="Enter description" value="{{ old('description') }}">
                    @error('description')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Image input -->
                <div class="form-group">
                    <label for="inputProductIcon">{{ __('Product Image') }}</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="image" type="file"
                                   class="custom-file-input @error('image') is-invalid @enderror"
                                   id="inputProductIcon" value="{{ old('image') }}">
                            <label class="custom-file-label"
                                   for="inputProductIcon">{{ __('Choose Product Image') }}</label>
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
                           placeholder="Cost" min="1" value="1">
                    @error('cost')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Product Order input -->
                <div class="form-group">
                    <label for="InputOrder">{{ __('Order') }}</label>
                    <input name="order" type="number" class="form-control @error('order') is-invalid @enderror"
                           id="InputOrder"
                           placeholder="Order" value="0">
                    @error('order')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>

            <!-- /.card-body -->

            <div class="card-footer">
                <a href="{{ route('dashboard.products.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Create new Product') }}" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
