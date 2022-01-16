@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('create users') }}
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
        <form method="POST" action="{{ route('dashboard.users.store') }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            <div class="card-body">
                <!-- Username input -->
                <div class="form-group">
                    <label for="inputUsername">{{ __('Username') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputUsername"
                           placeholder="Enter username" value="{{ old('name') }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <!-- User Avatar Image input -->
                <div class="form-group">
                    <label for="inputUserAvatarImage">{{ __('User Avatar Image') }}</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="avatar_url" type="file"
                                   class="custom-file-input @error('avatar_url') is-invalid @enderror"
                                   id="inputUserAvatarImage" value="{{ old('avatar_url') }}">
                            <label class="custom-file-label"
                                   for="inputUserAvatarImage">{{ __('Choose User Avatar Image') }}</label>
                        </div>
                        {{--                        <div class="input-group-append">--}}
                        {{--                            <span class="input-group-text">Upload</span>--}}
                        {{--                        </div>--}}
                    </div>
                    @error('avatar_url')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <!-- User Role input -->
                <div class="form-group">
                    <label>{{ __('Select Roles :') }}</label><br>
                    <div class="row @error('role_id') is-invalid @enderror">
                        @foreach($roles as $role)
                            <div class="col-6">
                                <label class="custom-radio">
                                    {{--                                    <input type="checkbox" name="roles[]" class="form-check-input"--}}
                                    {{--                                           value="{{ $role->id }}"--}}
                                    {{--                                           @if( is_array(old('roles')) && in_array($role->id, old('roles'), false)) checked @endif>--}}
                                    {{--                                    <input type="radio" name="role_id" value="{{ $role->id }}" @if( is_array(old('role_id')) && in_array($role->id, old('role_id'), false)) checked @endif>--}}
                                    <input type="radio" name="role_id" value="{{ $role->id }}"
                                           @if( (int)old('role_id') === $role->id ) checked @endif>
                                    {{ $role->name }}
                                </label>
                            </div>
                        @endforeach
                    </div>
                    @error('role_id')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Email input -->
                <div class="form-group">
                    <label for="InputEmail">{{ __('Email Address') }}</label>
                    <input name="email" type="email" class="form-control @error('email') is-invalid @enderror"
                           id="InputEmail" placeholder="Enter email" value="{{ old('email') }}">
                    @error('email')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div class="form-group mt-4" x-data="{ StartCountryCode:'' , start:'' }">
                    <label for="mobile_number" class="block text-sm font-medium text-gray-700">{{ __('Mobile Number') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        </div>
                        <input type="text" name="phone_number" id="mobile_number"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-2.5 pr-12 sm:text-sm border-gray-300 rounded-md"
                               x-bind:placeholder="start + StartCountryCode +' xxx xxx xxx'"
                               x-bind:value="start+StartCountryCode">
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <label for="countryCode" class="sr-only">{{ __('Country Code') }}</label>
                            <x-country-code-select :selected="old('country_code')"/>
                        </div>
                    </div>
                    @error('country_code')
                    <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                    @enderror
                    @error('phone_number')
                    <span class="flex items-center mt-1 font-medium ml-1 text-pink-600 text-sm">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Password input -->
                <div class="form-group">
                    <label for="InputPassword">{{ __('Password') }}</label>
                    <input name="password" type="password" class="form-control @error('password') is-invalid @enderror"
                           id="InputPassword"
                           placeholder="Password">
                    @error('password')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
                <!-- Confirm Password input -->
                <div class="form-group">
                    <label for="InputConfirmPassword">{{ __('Confirm Password') }}</label>
                    <input name="password_confirmation" type="password"
                           class="form-control @error('password_confirmation') is-invalid @enderror"
                           id="InputConfirmPassword"
                           placeholder="Retype password">
                    @error('password')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- /.card-body -->

            <div class="card-footer">
                <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Create new User') }}" class="btn btn-success float-right">
            </div>
        </form>
    </div>
@endsection
