@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('view service type') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$service_type->id)) }}</h3>

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
                <dt class="col-sm-3">{{ __('Service Type ID') }}</dt>
                <dd class="col-sm-9">{{ $service_type->id }}</dd>

                <dt class="col-sm-3">{{ __('Service Type Icon') }}</dt>
                <dd class="col-sm-9">
                    <img class="img-thumbnail" src="{{ $service_type->icon_url }}"
                         alt="No Image" width="80" height="80">
                </dd>

                <dt class="col-sm-3">{{ __('Service Type name') }}</dt>
                <dd class="col-sm-9">{{ $service_type->name }}</dd>

                <dt class="col-sm-3">{{ __('Service Type description') }}</dt>
                <dd class="col-sm-9">{{ $service_type->description }}</dd>

                <dt class="col-sm-3">{{ __('Service Type order queue') }}</dt>
                <dd class="col-sm-9">{{ $service_type->order }}</dd>

                <dt class="col-sm-3 bg-gradient-blue">{{ __('Service Name') }}</dt>
                <dd class="col-sm-9 text-blue">{{ $service_type->service->name }}</dd>

                <dt class="col-sm-3 bg-gradient-blue">{{ __('Service Link') }}</dt>
                <dd class="col-sm-9 text-blue">
                    <a class="text-blue"
                       href="{{ route('dashboard.services.show', $service_type->service)}} ">{{route('dashboard.services.show', $service_type->service)}}
                    </a>
                </dd>

                <dt class="col-sm-3">{{ __('Count products for this service type') }}</dt>
                <dd class="col-sm-9">{{ $count_service_type_products }}</dd>

                @if($count_service_type_products>0)
                    @foreach($service_type_products as $service_type_product)
                        <dt class="col-sm-3 bg-danger">{{ __('Service Type Product name') }}</dt>
                        <dd class="col-sm-9 text-danger">{{ $service_type_product->name }}</dd>
                        <dt class="col-sm-3 bg-danger">{{ __('Service Type Product Link') }}</dt>
                        <dd class="col-sm-9 text-danger">
                            <a class="text-danger"
                               href="{{ route('dashboard.products.show', $service_type_product)}} ">{{route('dashboard.products.show', $service_type_product)}}</a>
                        </dd>
                    @endforeach
                @else
                    <dt class="col-sm-3">{{ __('Service Type Products') }}</dt>
                    <dd class="col-sm-9">{{ $service_type_products }}</dd>
                @endif

                <dt class="col-sm-3">{{ __('Created at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($service_type->created_at)) }}</dd>

                <dt class="col-sm-3">{{ __('Updated at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($service_type->updated_at)) }}</dd>
            </dl>
        </div>
    </div>
@endsection
