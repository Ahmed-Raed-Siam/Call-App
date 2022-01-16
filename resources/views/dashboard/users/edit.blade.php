@extends('dashboard.layout.master')

@section('page-title')
    {{ $page_title=ucwords('edit user') }}
@endsection
@csrf
@section('content')
    {{--Update Status--}}
    @include('dashboard.status.status')
    {{--simple error tracing--}}
    @include('dashboard.simple error tracing.simple_error_tracing')
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title">{{ ucfirst(trans($page_title.' '.$user->id)) }}
                <small>Created at{{ date_format($user->created_at, 'jS M Y') }} / Updated
                    at{{ date_format($user->updated_at, 'jS M Y') }}</small>
            </h3>
        </div>
        <!-- /.card-header -->
        <!-- form start -->
        <form method="POST" action="{{ route('dashboard.users.update',$user->id) }}" class="form-group mb-0"
              enctype="multipart/form-data">
            @csrf
            @method('PUT')

            <div class="card-body">

                <!-- Username input -->
                <div class="form-group">
                    <label for="inputUsername">{{ __('Username') }}</label>
                    <input name="name" type="text" class="form-control @error('name') is-invalid @enderror"
                           id="inputUsername"
                           placeholder="Enter username" value="{{ $user->name }}">
                    @error('name')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- User Avatar Image input -->
                <div class="form-group">
                    <label for="inputUserAvatarImage">{{ __('User Avatar Image') }}</label>
                    <div class="input-group">
                        <img alt="No Image" class="table-avatar img-thumbnail" width="30%" height="30%"
                             id="user-avatar-image-img-tag"
                             src="{{ $user->user_photo_url }}">
                    </div>
                    <div class="input-group">
                        <div class="custom-file">
                            <input name="avatar_url" type="file"
                                   class="custom-file-input @error('avatar_url') is-invalid @enderror"
                                   id="inputUserAvatarImage" value="{{ $user->avatar_url }}">
                            <label class="custom-file-label"
                                   for="inputUserAvatarImage">{{ __('Choose User Avatar Image') }}</label>
                        </div>
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
                                    <input type="radio" name="role_id" value="{{ $role->id }}"
                                           @if( $user->role_id === $role->id ) checked @endif>
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
                    <input name="email" type="email @error('email') is-invalid @enderror" class="form-control"
                           id="InputEmail" placeholder="Enter email"
                           value="{{ $user->email }}">
                    @error('email')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>

                <!-- Mobile Number -->
                <div class="form-group mt-4" x-data="{ StartCountryCode:'{{$user->phone_number}}' , start:'' }">
                    <label for="mobile_number"
                           class="block text-sm font-medium text-gray-700">{{ __('Mobile Number') }}</label>
                    <div class="mt-1 relative rounded-md shadow-sm">
                        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        </div>
                        <input type="text" name="phone_number" id="mobile_number"
                               class="focus:ring-indigo-500 focus:border-indigo-500 block w-full pl-2.5 pr-12 sm:text-sm border-gray-300 rounded-md"
                               x-bind:placeholder="StartCountryCode +' xxx xxx xxx'"
                               x-bind:value="start+StartCountryCode">
                        <div class="absolute inset-y-0 right-0 flex items-center">
                            <label for="countryCode" class="sr-only">{{ __('Country Code') }}</label>
                            {{--                            <x-country-code-select :selected="$user->county_code"/>--}}
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
                           placeholder="Password" value="{{ $user->password }}">
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
                           placeholder="Retype password" value="{{ $user->password }}">
                    @error('password_confirmation')
                    <span class="text-sm text-danger">{{ $message }}</span>
                    @enderror
                </div>
            </div>
            <!-- /.card-body -->

            <div class=" card-footer">
                <a href="{{ route('dashboard.users.index') }}" class="btn btn-secondary">{{ __('Cancel') }}</a>
                <input type="submit" value="{{ __('Save Changes') }}" class="btn btn-success float-right">
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
                    $('#user-avatar-image-img-tag').attr('src', e.target.result);
                }

                reader.readAsDataURL(input.files[0]);
            }
        }

        $("#inputUserAvatarImage").change(function () {
            readURL(this);
        });
    </script>
@endsection
