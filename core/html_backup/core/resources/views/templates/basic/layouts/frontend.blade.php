@extends('Template::layouts.app')
@section('main')
    @include('Template::partials.header')
    <main class="page-wrapper">
        @if (!request()->routeIs('home'))
            @include('Template::partials.breadcrumb')
        @endif

        @yield('content')
    </main>
    @include('Template::partials.footer')
@endsection
