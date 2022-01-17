@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('users table') }}

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
                    <th style="width: 15%">
                        {{ __('Username') }}
                    </th>
                    <th style="width: 12%">
                        {{ __('Photo') }}
                    </th>
                    <th style="width: 15%">
                        {{ __('Email Address') }}
                        <small>
                            {{ __('Email Verified at') }}
                        </small>
                    </th>
                    <th style="width: 5%">
                        {{ __('Phone Number') }}
                    </th>
                    <th style="width: 20%">
                        <a class="btn btn-outline-primary m-auto d-flex text-center float-right"
                           href="{{ route('dashboard.users.create') }}"
                           data-toggle="tooltip" data-placement="top"
                           title="{{ __('Add User') }} {{ $users->count()+1 }}">
                            <i class="fas fa-plus-square p-1"></i>
                            {{ __('Add User') }}
                        </a>
                    </th>
                </tr>
                </thead>
                <tbody>
                @forelse ($users as $user)
                    <tr>
                        <td>
                            #{{ $counter++ }}
                        </td>
                        <td>
                            <a>
                                {{ $user->name }}
                            </a>
                            <br/>
                            <small>
                                {{ __('Created').' '.$user->created_at }}
                            </small>
                        </td>
                        <td>
                            <ul class="list-inline">
                                <li class="list-inline-item">
                                    <img alt="No Image" class="table-avatar" src="{{ $user->user_photo_url }}" width="50" height="50">
                                </li>
                            </ul>
                        </td>
                        <td>
                            <a>
                                {{ $user->email }}
                            </a>
                            <br/>
                            <small>
                                {{ __('Verified at').' '.$user->email_verified_at }}
                            </small>
                        </td>
                        <td>
                            {{ $user->phone_number  }}
                        </td>
                        <td class="project-actions text-right">
                            <a class="btn btn-primary btn-sm"
                               href="{{ route('dashboard.users.show',$user->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('View User') }} {{ $counter-1 }}">
                                {{--                                title="{{ __('View User') }} {{ $user->name }}">--}}
                                <i class="fas fa-external-link-alt"></i>
                                {{ __('View') }}
                            </a>
                            <a class="btn btn-info btn-sm"
                               href="{{ route('dashboard.users.edit',$user->id) }}"
                               data-toggle="tooltip" data-placement="top"
                               title="{{ __('Edit User') }} {{ $counter-1 }}">
                                <i class="fas fa-pencil-alt"></i>
                                {{ __('Edit') }}
                            </a>
                            <form class="btn btn-danger btn-sm m-0"
                                  action="{{ route('dashboard.users.destroy', ['user' => $user->id]) }}"
                                  method="POST">
                                @csrf
                                @method('DELETE')
                                {{ csrf_field() }}
                                <i class="fas fa-trash-alt">
                                </i>
                                <input name="delete" type="submit" class="btn btn-danger btn-sm p-0"
                                       value="Delete"
                                       data-toggle="tooltip" data-placement="top"
                                       title="{{ __('Delete User') }} {{ $counter-1 }}">
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
                    {!! $users->links('vendor.pagination.custom') !!}
                </ul>
            </div>
            <!-- /.card-footer -->

        </div>
@endsection
