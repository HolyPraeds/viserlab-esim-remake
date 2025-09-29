@extends('Template::layouts.app')

@section('main')
    @include('Template::partials.header')
    @if (request()->routeIs('user.deposit.index') || request()->routeIs('user.deposit.confirm'))
        @include('Template::partials.breadcrumb')
    @endif
    <main class="page-wrapper">
        <section class="dashboard my-60">
            <div class="container">
                <div class="dashboard-inner">

                    @include('Template::partials.dashboard_sidenav')
                    <div class="dashboard-body">
                        <button class="btn btn--sm btn--base d-lg-none mb-4" type="button" data-toggle="offcanvas-sidebar" data-target="#dashboard-offcanvas-sidebar">
                            <i class="fas fa-bars me-1"></i>@lang('Menu')
                        </button>
                        @yield('content')
                    </div>
                </div>
            </div>
        </section>
    </main>
    @include('Template::partials.footer')
@endsection

@push('script')
    <script>
        (function($) {
            'use strict';
            $('#search-form').find('input[name=search]').addClass('form--control');
            $('#search-form').find('button[type=submit]').removeClass('btn--primary').addClass('btn--base btn--xsm');
            $('.scroll-top').remove();
        })(jQuery);
    </script>
@endpush
