@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('create services') }}
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
        <form method="POST" action="{{ route('dashboard.services.store') }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <!-- Service Name input -->
                <div class="form-group">
                    <label for="inputServiceName">{{ __('Name') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputServiceName"
                           placeholder="Enter service name" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Description input -->
                <div class="form-group">
                    <label for="inputServiceDescription">{{ __('Description') }}</label>
                    <input name="description" type="text"
                           class="form-control @error('description') is-invalid @enderror"
                           id="inputServiceDescription"
                           placeholder="Enter description" value="{{ old('description') }}">
                    @error('description')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Icon input -->
                <div class="form-group">
                    <label for="inputServiceIcon">{{ __('Service Icon') }}</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="icon_path" type="file"
                                   class="custom-file-input @error('icon_path') is-invalid @enderror"
                                   id="inputServiceIcon" value="{{ old('icon_path') }}">
                            <label class="custom-file-label"
                                   for="inputServiceIcon">{{ __('Choose Service Icon') }}</label>
                        </div>
                    </div>
                    @error('icon_path')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Order input -->
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
                <a href="{{ route('dashboard.services.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Create new Service') }}" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
