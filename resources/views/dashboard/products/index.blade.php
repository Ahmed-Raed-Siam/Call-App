@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('products table') }}

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
            <div class="row p-3">
                <div class="col col-md-6">
                    <div class="dt-buttons btn-group flex-wrap">
                        <button class="btn btn-secondary buttons-copy buttons-html5" tabindex="0"
                                aria-controls="example1" type="button"><span>Copy</span></button>
                        <button class="btn btn-secondary buttons-csv buttons-html5" tabindex="0"
                                aria-controls="example1" type="button"><span>CSV</span></button>
                        <button class="btn btn-secondary buttons-excel buttons-html5" tabindex="0"
                                aria-controls="example1" type="button"><span>Excel</span></button>
                        <button class="btn btn-secondary buttons-pdf buttons-html5" tabindex="0"
                                aria-controls="example1" type="button"><span>PDF</span></button>
                        <button class="btn btn-secondary buttons-print" tabindex="0" aria-controls="example1"
                                type="button"><span>Print</span></button>
                        <div class="btn-group">
                            <button class="btn btn-secondary buttons-collection dropdown-toggle buttons-colvis"
                                    tabindex="0" aria-controls="example1" type="button" aria-haspopup="true"
                                    aria-expanded="false"><span>Column visibility</span></button>
                        </div>
                    </div>
                </div>

                <div class="col col-md-6 flex justify-content-end">
                    <div id="example1_filter" class="dataTables_filter">
                        <form class="m-0"
                              action="{{ route('dashboard.products.index') }}"
                              method="GET">
                            @csrf
                            <div class="input-group">
                                <input type="search" name="search" class="form-control form-control-sm"
                                       placeholder="Keywords"
                                       aria-label="Search for specific product" aria-describedby="basic-addon2">
                                <div class="input-group-append">
                                    <button class="btn btn-sm btn-outline-secondary" type="submit" data-toggle="tooltip"
                                            data-placement="top"
                                            title="{{ __('Search for specific product') }}">
                                        <i class="fas fa-search"></i>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>

                </div>
            </div>

            <table class="table table-bordered table-hover table-responsive table-striped projects">
                <thead>
                <tr>
                    <th style="width: 1%">
                        #
                    </th>
                    <th style="width: 15%">
                        {{ __('Product Name') }}
                    </th>
                    <th style="width: 15%">
                        {{ __('Product Image') }}
                    </th>
                    <th style="width: 20%">
                        {{ __('Product Description') }}
                    </th>
                    <th style="width: 15%">
                        {{ __('Service Type') }}
                    </th>

                    <th style="width: 10%">
                        {{ __('Product Cost') }}
                    </th>
                    <th style="width: 20%">
                        <a class="btn btn-outline-primary m-auto d-flex text-center float-right"
                           href="{{ route('dashboard.products.create') }}"
                           data-toggle="tooltip" data-placement="top"
                           title="{{ __('Add Product') }} {{ $products->count()+1 }}">
                            <i class="fas fa-plus-square p-1"></i>
                            {{ __('Add Product') }}
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($products as $product)
                    <tr>
                        <td>
                            #{{ $counter++ }}
                        </td>
                        <td>
                            <a>
                                {{ $product->name }}
                            </a>
                            <br>
                            <small>
                                {{ __('Created').' '.$product->created_at }}
                            </small>
                        </td>
                        <td>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <img alt="No Image" class="table-avatar" src="{{ $product->image_url }}">
                                </li>
                            </ul>
                        </td>
                        <td>
                            <a>
                                {{ $product->description }}
                            </a>
                        </td>
                        <td>
                            <a>
                                {{ $product->service_type->name }}
                            </a>
                        </td>
                        <td>
                            <a>
                                {{ $product->cost }}
                            </a>
                        </td>

                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm"
                               href="{{ route('dashboard.products.show',$product->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('View Product') }} {{ $counter-1 }}">
                                <i class="fas fa-external-link-alt"></i>
                                {{ __('View') }}
                            </a>
                            <a class="btn btn-info btn-sm"
                               href="{{ route('dashboard.products.edit',$product->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('Edit Product') }} {{ $counter-1 }}">
                                <i class="fas fa-pencil-alt"></i>
                                {{ __('Edit') }}
                            </a>
                            <form class="btn btn-danger btn-sm m-0"
                                  action="{{ route('dashboard.products.destroy', $product) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                <i class="fas fa-trash-alt"></i>
                                <input name="delete" type="submit" class="btn btn-danger btn-sm p-0"
                                       value="Delete"
                                       data-toggle="tooltip" data-placement="top"
                                       title="{{ __('Delete Product') }} {{ $counter-1 }}">
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr class="align-content-center">
                        <td colspan="7">
                            <h1 class="text-center">Not Found</h1>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
        <!-- /.card-body -->

        <div class="card-footer w-100 m-0 pt-sm-2 pr-sm-2 pl-sm-1 bg-light">
            <div class="d-block p-2">
                <ul class="pagination m-auto d-flex justify-content-center float-right ">
                    {!! $products->links('vendor.pagination.custom') !!}
                </ul>
            </div>
            <!-- /.card-footer -->

        </div>
@endsection
