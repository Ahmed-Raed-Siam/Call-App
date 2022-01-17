@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('view product') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$product->id)) }}</h3>

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
                <dt class="col-sm-3">{{ __('Product ID') }}</dt>
                <dd class="col-sm-9">{{ $product->id }}</dd>

                <dt class="col-sm-3">{{ __('Product Image') }}</dt>
                <dd class="col-sm-9">
                    <img class="img-thumbnail" src="{{ $product->image_url }}"
                         alt="No Image" width="80" height="80">
                </dd>

                <dt class="col-sm-3">{{ __('Product name') }}</dt>
                <dd class="col-sm-9">{{ $product->name }}</dd>

                <dt class="col-sm-3">{{ __('Product description') }}</dt>
                <dd class="col-sm-9">{{ $product->description }}</dd>

                <dt class="col-sm-3">{{ __('Product order queue') }}</dt>
                <dd class="col-sm-9">{{ $product->order }}</dd>

                <dt class="col-sm-3">{{ __('Product cost') }}</dt>
                <dd class="col-sm-9">${{ $product->cost }}</dd>

                <dt class="col-sm-3 bg-gradient-blue">{{ __('Service Type Name') }}</dt>
                <dd class="col-sm-9 text-blue">{{ $product->service_type->name }}</dd>

                <dt class="col-sm-3 bg-gradient-blue">{{ __('Service Type Link') }}</dt>
                <dd class="col-sm-9 text-blue">
                    <a class="text-blue"
                       href="{{ route('dashboard.services.types.show', $product->service_type)}} ">{{route('dashboard.services.types.show', $product->service_type)}}
                    </a>
                </dd>

                <dt class="col-sm-3">{{ __('Created at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($product->created_at)) }}</dd>

                <dt class="col-sm-3">{{ __('Updated at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($product->updated_at)) }}</dd>
            </dl>
        </div>
    </div>
@endsection
