<!-- Content Header (Page header) -->
<div class="content-header">
    <div class="container-fluid">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0">
                    @php
                        $routeName=\App\Helpers::get_pageTitle();
                        //\App\Helpers::get_pageTitle();
                    @endphp
                    {{ ucwords($routeName) }}
                </h1>
            </div><!-- /.col -->
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item" style=""><a href="{{ route('dashboard.') }}">{{ __('Home') }}</a></li>
                    {{--                    <li class="breadcrumb-item active">{{ ucfirst(trans(\Illuminate\Support\Facades\Request::segment(1))) }}</li>--}}
                    {{--                    <li class="breadcrumb-item active">{{ \Illuminate\Support\Facades\URL::current() }}</li>--}}
                    {{--                    <li class="breadcrumb-item active">{{ \Illuminate\Support\Facades\Route::currentRouteName() }}</li>--}}
                    {{--                    <li class="breadcrumb-item active">{{ \Illuminate\Support\Facades\Route::currentRouteName() }}</li>--}}
                    {{--                    <li class="breadcrumb-item active">@get_pageTitle()</li>--}}
                    {{--                    <li class="breadcrumb-item active">
                                            <?php
                                            \App\Helpers::get_pageTitle();
                                            ?>
                                        </li>--}}
                    <li class="breadcrumb-item active">
                        {{--{{ ucfirst($routeName) }}--}}
                        {{ ucfirst($page_title)  }}
                    </li>
                </ol>
            </div><!-- /.col -->
        </div><!-- /.row -->
    </div><!-- /.container-fluid -->
</div>
<!-- /.content-header -->
