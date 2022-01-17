@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('view service') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$service->id)) }}</h3>

            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <div class="card-body p-0">
            <dl class="card-body row">
                <dt class="col-sm-3">{{ __('Service ID') }}</dt>
                <dd class="col-sm-9">{{ $service->id }}</dd>

                <dt class="col-sm-3">{{ __('Service Icon') }}</dt>
                <dd class="col-sm-9">
                    <img class="img-thumbnail" src="{{ $service->icon_url }}"
                         alt="No Image" width="80" height="80">
                </dd>

                <dt class="col-sm-3">{{ __('Service name') }}</dt>
                <dd class="col-sm-9">{{ $service->name }}</dd>

                <dt class="col-sm-3">{{ __('Service description') }}</dt>
                <dd class="col-sm-9">{{ $service->description }}</dd>

                <dt class="col-sm-3">{{ __('Service order queue') }}</dt>
                <dd class="col-sm-9">{{ $service->order }}</dd>

                <dt class="col-sm-3">{{ __('Count service types for this service') }}</dt>
                <dd class="col-sm-9">{{ $count_service_types }}</dd>

                @if($count_service_types>0)
                    @foreach($service_types as $service_type)
                        <dt class="col-sm-3 bg-danger">{{ __('Service Type name') }}</dt>
                        <dd class="col-sm-9 text-danger">{{ $service_type->name }}</dd>
                        <dt class="col-sm-3 bg-danger">{{ __('Service Type Link') }}</dt>
                        <dd class="col-sm-9 text-danger">
                            <a class="text-danger"
                               href="{{ route('dashboard.services.types.show', $service_type)}} ">{{route('dashboard.services.types.show', $service_type)}}</a>
                        </dd>
                    @endforeach
                @else
                    <dt class="col-sm-3">{{ __('Service types') }}</dt>
                    <dd class="col-sm-9">{{ $service_types }}</dd>
                @endif

                <dt class="col-sm-3">{{ __('Created at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($service->created_at)) }}</dd>

                <dt class="col-sm-3">{{ __('Updated at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($service->updated_at)) }}</dd>
            </dl>
        </div>
    </div>
@endsection
