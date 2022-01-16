@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('edit service') }}
@endsection
@csrf
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    {{--simple error tracing--}}
    @include('dashboard.simple error tracing.simple_error_tracing')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$service->id)) }}
                <small>Created at{{ date_format($service->created_at, 'jS M Y') }} / Updated
                    at{{ date_format($service->updated_at, 'jS M Y') }}</small>
            </h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="POST" action="{{ route('dashboard.services.update',$service->id) }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">
                <!-- Service Name input -->
                <div class="form-group">
                    <label for="inputServiceName">{{ __('Name') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputServiceName"
                           placeholder="Enter service name" value="{{ $service->name }}">
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
                           placeholder="Enter description" value="{{ $service->description }}">
                    @error('description')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Service Icon input -->
                <div class="form-group">
                    <label for="inputServiceIcon">{{ __('Service Icon') }}</label>
                    <div class="input-group">
                        <img alt="No Image" class="table-avatar img-thumbnail" width="30%" height="30%"
                             id="service-icon-image-img-tag"
                             src="{{ $service->icon_url }}">
                    </div>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="icon_path" type="file"
                                   class="custom-file-input @error('icon_path') is-invalid @enderror"
                                   id="inputServiceIcon" value="{{ $service->icon_path }}">
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
                           placeholder="Order" value="{{ $service->order }}">
                    @error('order')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

            </div>
            <!-- /.card-body -->

            <div class=" card-footer">
                <a href="{{ route('dashboard.services.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
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

        $("#inputServiceIcon").change(function () {
            readURL(this);
        });
    </script>
@endsection
