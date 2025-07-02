@extends('layouts.app')

@section('title', 'Profile')

@section('content')
<div class="section-header">
    <h1>{{ __('Profile') }}</h1>
</div>
<div class="section-body">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Update Profile') }}</h4>
                </div>
                <div class="card-body">
                    @if (session('success'))
                        <div class="alert alert-success">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="alert alert-danger">
                            {{ session('error') }}
                        </div>
                    @endif

                    <form method="post" action="{{ route('profile.update') }}" class="needs-validation" novalidate="" enctype="multipart/form-data">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('App Name') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="app_name" class="form-control" value="{{ config('app.name') }}" required>
                                @error('app_name')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('JWT Key') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="jwt_key" class="form-control" value="{{ $jwtKey }}" required>
                                @error('jwt_key')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('Footer Copyrights Text') }}</label>
                            <div class="col-sm-9">
                                <input type="text" name="footer_copyrights_text" class="form-control" value="{{ $footerCopyrightsText }}" required>
                                @error('footer_copyrights_text')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('Full Logo') }}</label>
                            <div class="col-sm-9">
                                <input type="file" name="full_file" class="form-control" accept="image/*">
                                <small class="text-muted">{{ __('Leave it blank if you don\'t want to change it') }}</small>
                                @error('full_file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('Half Logo') }}</label>
                            <div class="col-sm-9">
                                <input type="file" name="half_file" class="form-control" accept="image/*">
                                <small class="text-muted">{{ __('Leave it blank if you don\'t want to change it') }}</small>
                                @error('half_file')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary">{{ __('Save Changes') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h4>{{ __('Change Password') }}</h4>
                </div>
                <div class="card-body">
                    <form method="post" action="{{ route('profile.change-password') }}" class="needs-validation" novalidate="">
                        @csrf
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('Current Password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" name="oldpassword" class="form-control" required>
                                @error('oldpassword')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('New Password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" name="newpassword" class="form-control" required>
                                @error('newpassword')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <label class="col-sm-3 col-form-label">{{ __('Confirm New Password') }}</label>
                            <div class="col-sm-9">
                                <input type="password" name="confirmpassword" class="form-control" required>
                                @error('confirmpassword')
                                    <div class="invalid-feedback">
                                        {{ $message }}
                                    </div>
                                @enderror
                            </div>
                        </div>
                        <div class="form-group row">
                            <div class="col-sm-12 text-right">
                                <button type="submit" class="btn btn-primary">{{ __('Change Password') }}</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection