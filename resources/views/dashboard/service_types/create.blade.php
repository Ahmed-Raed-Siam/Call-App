@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('create service type') }}
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
        <form method="POST" action="{{ route('dashboard.services.types.store') }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <!-- Service Type Name input -->
                <div class="form-group">
                    <label for="inputServiceTypeName">{{ __('Name') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputServiceTypeName"
                           placeholder="Enter service type name" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service input -->
                <div class="form-group">
                    <label for="inputService">{{ __('Select Service:') }}</label>
                    <select name="service_id" id="inputService"
                            class="form-control custom-select @error('service_id') is-invalid @enderror">
                        <option selected="selected" disabled>{{ __('Select one') }}</option>
                        @foreach($services as $service)
                            <option value="{{ $service->id }}"
                                    @if( (int)old('service_id')  === $service->id) selected="selected" @endif >{{ $service->name }}</option>
                        @endforeach
                    </select>
                    @error('service_id')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Type Description input -->
                <div class="form-group">
                    <label for="inputServiceTypeDescription">{{ __('Description') }}</label>
                    <input name="description" type="text"
                           class="form-control @error('description') is-invalid @enderror"
                           id="inputServiceTypeDescription"
                           placeholder="Enter description" value="{{ old('description') }}">
                    @error('description')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Type Icon input -->
                <div class="form-group">
                    <label for="inputServiceTypeIcon">{{ __('Service Type Icon') }}</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="icon_path" type="file"
                                   class="custom-file-input @error('icon_path') is-invalid @enderror"
                                   id="inputServiceTypeIcon" value="{{ old('icon_path') }}">
                            <label class="custom-file-label"
                                   for="inputServiceTypeIcon">{{ __('Choose Service Type Icon') }}</label>
                        </div>
                    </div>
                    @error('icon_path')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Type Order input -->
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
                <a href="{{ route('dashboard.services.types.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Create new Service Type') }}" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
