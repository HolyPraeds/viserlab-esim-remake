@php
    $content = getContent('login_register.content', true);
@endphp

@extends($activeTemplate . 'layouts.auth')
@section('content')
    @if (gs('registration'))
        <div class="account-section">
            <div class="account-left bg-img" data-background-image="{{ brandImage('login_register', frontendImage('login_register', $content?->data_values?->image, '2055x2160')) }}">
            </div>
            <div class="account-content">
                <form action="{{ route('user.register') }}" class="account-form lg-style verify-gcaptcha disableSubmission" method="POST">
                    @csrf
                    <a href="{{ route('home') }}" class="account-logo">
                        <img class="fit-image" src="{{ siteLogo('dark') }}" alt="{{ currentBrand('name') }}">
                    </a>

                    <div class="account-heading">
                        <h3 class="account-heading__title">{{ $content?->data_values?->register_subheading }}</h3>
                        <p class="account-heading__desc">{{ $content?->data_values?->register_short_description }}</p>
                    </div>

                    <div class="row gy-3">
                        @if (session()->get('reference') != null)
                            <div class="col-12">
                                <label for="referenceBy" class="form-label">@lang('Reference by')</label>
                                <input type="text" name="referBy" id="referenceBy" class="form-control form--control" value="{{ session()->get('reference') }}" readonly>
                            </div>
                        @endif

                        <div class="col-sm-6">
                            <label class="form--label required" for="firstname">@lang('First Name')</label>
                            <input type="text" placeholder="First name" class="form-control form--control" id="firstname" name="firstname" value="{{ old('firstname') }}" required>
                        </div>
                        <div class="col-sm-6">
                            <label class="form--label required" for="lastname">@lang('Last Name')</label>
                            <input type="text" placeholder="Last name" class="form-control form--control" id="lastname"  name="lastname" value="{{ old('lastname') }}" required>
                        </div>

                        <div class="col-md-12">
                            <label class="form--label required" for="email">@lang('Email Address')</label>
                            <input id="email" placeholder="Email address" type="email" class="form-control form--control checkUser" name="email" value="{{ old('email') }}" required>
                        </div>

                        <div class="col-sm-6">
                            <label for="password" class="form--label required">@lang('Password')</label>
                            <div class="input-group input--group input--group-password">
                                <input class="form-control form--control @if (gs('secure_password')) secure-password @endif" type="password" placeholder="Password" name="password" id="password" required>
                                <button class="input-group-text input-group-btn" type="button">
                                    <i class="far fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col-sm-6">
                            <label class="form--label required" for="confirm-password">@lang('Confirm Password')</label>
                            <div class="input-group input--group input--group-password">
                                <input type="password" id="confirm-password" class="form-control form--control" name="password_confirmation" required placeholder="Confirm Password">
                                <button class="input-group-text input-group-btn" type="button">
                                    <i class="far fa-eye-slash"></i>
                                </button>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <x-captcha />
                            @if (gs('agree'))
                                <div class="form--check">
                                    <input class="form-check-input" type="checkbox" id="flexCheckChecked" @checked(old('agree')) name="agree" required>
                                    <label class="form-check-label" for="flexCheckChecked">
                                        @lang('I agree with all ')
                                        <a href="{{ route('policy.pages', 'terms-and-conditions') }}" target="_blank" class="text--base">@lang('Terms and Conditions')</a>,
                                        <a href="{{ route('policy.pages', 'privacy-policy') }}" target="_blank" class="text--base">@lang('Privacy Policy')</a>
                                    </label>
                                </div>
                            @endif
                        </div>
                        <div class="col-md-12">
                            <button type="submit" id="recaptcha" class="btn btn--base w-100">
                                @lang('Sign Up')
                            </button>
                        </div>
                    </div>

                    {{-- Social login removed temporarily to avoid LinkedIn config errors --}}
                    {{-- @include($activeTemplate . 'partials.social_login') --}}

                    <p class="account-external text-center mt-4">@lang('Already have an account?')
                        <a href="{{ route('user.login') }}" class="text--base">@lang('Sign in')</a>
                    </p>
                </form>
            </div>
        </div>
    @else
        @include($activeTemplate . 'partials.registration_disabled')
    @endif
@endsection

@if (gs('registration'))
    @if (gs('secure_password'))
        @push('script-lib')
            <script src="{{ asset('assets/global/js/secure_password.js') }}"></script>
        @endpush
    @endif

    @push('script')
        <script>
            "use strict";
            (function($) {

                $('.checkUser').on('focusout', function(e) {
                    var url = '{{ route('user.checkUser') }}';
                    var value = ($(this).val() || '').trim().toLowerCase();
                    $(this).val(value);
                    var token = '{{ csrf_token() }}';

                    var data = {
                        email: value,
                        _token: token
                    }

                    $.post(url, data, function(response) {
                        try {
                            if (typeof response === 'string') {
                                response = JSON.parse(response);
                            }
                        } catch (e) {
                            response = { data: false };
                        }
                        if (response && response.data === true) {
                            $('#existModalCenter').modal('show');
                        }
                    }).fail(function() {
                        // On error, assume not existing to avoid blocking UX
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif
