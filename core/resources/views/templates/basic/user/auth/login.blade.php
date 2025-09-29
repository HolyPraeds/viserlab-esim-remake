@php
    $loginContent = getContent('login_register.content', true);
@endphp
@extends($activeTemplate . 'layouts.auth')
@section('content')
    <div class="account-section">
        <div class="account-left bg-img" data-background-image="{{ frontendImage('login_register', $loginContent?->data_values?->image, '2055x2160') }}">
        </div>
        <div class="account-content">
            <form method="POST" action="{{ route('user.login') }}" class="account-form verify-gcaptcha">
                @csrf

                <a href="{{ route('home') }}" class="account-logo">
                    <img class="fit-image" src="{{ siteLogo('dark') }}" alt="logo">
                </a>

                <div class="account-heading">
                    <h3 class="account-heading__title">{{ __($loginContent?->data_values?->login_subheading) }}</h3>
                    <p class="account-heading__desc">{{ __($loginContent?->data_values?->login_short_description) }}</p>
                </div>

                <div class="row gy-3">
                    <div class="col-12">
                        <label class="form--label required" for="username">
                            @lang('Username or Email')
                        </label>
                        <input class="from-control form--control" type="text" name="username" id="username" placeholder="Username or Email" value="{{ old('username') }}" required>
                    </div>
                    <div class="col-12">
                        <label class="form--label required" for="password">
                            @lang('Password')
                        </label>
                        <div class="input-group input--group input--group-password">
                            <input class="form-control form--control" type="password" name="password" id="password" placeholder="Password">
                            <button class="input-group-text input-group-btn" type="button">
                                <i class="far fa-eye-slash"></i>
                            </button>
                        </div>
                    </div>
                    <div class="col-12">
                        <x-captcha />
                        <div class="flex-between">
                            <div class="form--check">
                                <input class="form-check-input" type="checkbox" name="remember" id="flexCheckChecked">
                                <label class="form-check-label" for="flexCheckChecked" {{ old('remember') ? 'checked' : '' }}>@lang('Remember Me')</label>
                            </div>
                            <a href="{{ route('user.password.request') }}" class="forgot-pass forgot-password">@lang('Forgot password?')</a>
                        </div>
                    </div>
                    <div class="col-12">
                        <button type="submit" id="recaptcha" class="w-100 btn btn--base">
                            @lang('Sign In')
                        </button>
                    </div>
                </div>

                {{-- Social login removed temporarily to avoid LinkedIn config errors --}}
                {{-- @include($activeTemplate . 'partials.social_login') --}}

                <p class="account-external text-center mt-4">
                    @lang('Don\'t have any account?') <a href="{{ route('user.register') }}" class="text--base">@lang('Sign Up')</a>
                </p>
            </form>
        </div>
    </div>
@endsection
