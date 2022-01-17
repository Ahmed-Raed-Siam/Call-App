@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('services table') }}

@endsection
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    <div class="card p-2">
        <div class="card-header">
            <h3 class="card-title">{{ __(ucfirst($page_title)) }}</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-tool" data-card-widget="collapse" title="Collapse">
                    <i class="fas fa-minus"></i>
                </button>
                <button type="button" class="btn btn-tool" data-card-widget="remove" title="Remove">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </div>
        <!-- /.card-header -->

        <div class="card-body p-0">
            <table class="table table-hover table-responsive table-striped projects">
                <thead>
                <tr>
                    <th style="width: 1%">
                        #
                    </th>
                    <th style="width: 20%">
                        {{ __('Service Name') }}
                    </th>
                    <th style="width: 15%">
                        {{ __('Service Icon') }}
                    </th>
                    <th style="width: 25%">
                        {{ __('Service Description') }}
                    </th>
                    <th style="width: 15%">
                        {{ __('Service types') }}
                    </th>
                    <th style="width: 20%">
                        <a class="btn btn-outline-primary m-auto d-flex text-center float-right"
                           href="{{ route('dashboard.services.create') }}"
                           data-toggle="tooltip" data-placement="top"
                           title="{{ __('Add Service') }} {{ $services->count()+1 }}">
                            <i class="fas fa-plus-square p-1"></i>
                            {{ __('Add Service') }}
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($services as $service)
                    <tr>
                        <td>
                            #{{ $counter++ }}
                        </td>
                        <td>
                            <a>
                                {{ $service->name }}
                            </a>
                            <br>
                            <small>
                                {{ __('Created').' '.$service->created_at }}
                            </small>
                        </td>
                        <td>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <img alt="No Image" class="table-avatar" src="{{ $service->icon_url }}">
                                </li>
                            </ul>
                        </td>
                        <td>
                            <a>
                                {{ $service->description }}
                            </a>
                        </td>
                        <td>
                            <a>
                                {{ $service->service_types()->count() }}
                            </a>
                        </td>

                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm"
                               href="{{ route('dashboard.services.show',$service->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('View Service') }} {{ $counter-1 }}">
                                <i class="fas fa-external-link-alt"></i>
                                {{ __('View') }}
                            </a>
                            <a class="btn btn-info btn-sm"
                               href="{{ route('dashboard.services.edit',$service->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('Edit Service') }} {{ $counter-1 }}">
                                <i class="fas fa-pencil-alt"></i>
                                {{ __('Edit') }}
                            </a>
                            <form class="btn btn-danger btn-sm m-0"
                                  action="{{ route('dashboard.services.destroy', ['service' => $service->id]) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                {{ csrf_field() }}
                                <i class="fas fa-trash-alt">
                                </i>
                                <input name="delete" type="submit" class="btn btn-danger btn-sm p-0"
                                       value="Delete"
                                       data-toggle="tooltip" data-placement="top"
                                       title="{{ __('Delete Service') }} {{ $counter-1 }}">
                            </form>
                        </td>
                    </tr>
                @empty
                @endforelse
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->

        <div class="card-footer w-100 m-0 pt-sm-2 pr-sm-2 pl-sm-1 bg-light">
            <div class="d-block p-2">
                <ul class="pagination m-auto d-flex justify-content-center float-right ">
                    {!! $services->links('vendor.pagination.custom') !!}
                </ul>
            </div>
            <!-- /.card-footer -->

        </div>
@endsection
