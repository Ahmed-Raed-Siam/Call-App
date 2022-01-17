@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('view user') }}
@endsection
@section('content')
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$user->id)) }}</h3>

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
                <dt class="col-sm-3">{{ __('User ID') }}</dt>
                <dd class="col-sm-9">{{ $user->id }}</dd>

                <dt class="col-sm-3">{{ __('User photo') }}</dt>
                <dd class="col-sm-9">
                    <img class="img-thumbnail" src="{{ $user->user_photo_url }}"
                         alt="No Image" width="80" height="80">
                </dd>

                <dt class="col-sm-3">{{ __('User name') }}</dt>
                <dd class="col-sm-9">{{ $user->name }}</dd>

                <dt class="col-sm-3">{{ __('User role') }}</dt>
                <dd class="col-sm-9">{{ ucfirst($role) }}</dd>

                <dt class="col-sm-3">{{ __('User email') }}</dt>
                <dd class="col-sm-9">{{ $user->email }}</dd>

                <dt class="col-sm-3">{{ __('User phone number') }}</dt>
                <dd class="col-sm-9">{{ $user->phone_number }}</dd>

                <dt class="col-sm-3">{{ __('Created at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($user->created_at)) }}</dd>

                <dt class="col-sm-3">{{ __('Updated at') }}</dt>
                <dd class="col-sm-9">{{ date('F d, Y', strtotime($user->updated_at)) }}</dd>
            </dl>
        </div>
    </div>
@endsection
