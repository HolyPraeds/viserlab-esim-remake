@extends($activeTemplate . 'layouts.frontend')

@section('content')
    <section class="pt-100 pb-100">
        <div class="container">
            <div class="row justify-content-center">
                <div class="col-lg-8 text-center">
                    <h2 class="mb-3">@lang('Payment Successful')</h2>
                    <p class="mb-4">@lang('Thank you! Your payment was completed successfully. Your eSIM will appear in your account shortly.')</p>
                    <a href="{{ route('user.order.completed') }}" class="btn btn--base me-2">@lang('View Completed Orders')</a>
                    <a href="{{ route('home') }}" class="btn btn--dark">@lang('Back to Home')</a>
                </div>
            </div>
        </div>
    </section>
@endsection

@extends('templates.basic.layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <div class="mb-4">
                        <i class="las la-check-circle text-success" style="font-size: 4rem;"></i>
                    </div>
                    
                    <h2 class="text-success mb-3">@lang('Payment Successful!')</h2>
                    
                    <p class="text-muted mb-4">
                        @lang('Your payment has been processed successfully. Your eSIM will be available in your account shortly.')
                    </p>
                    
                    <div class="d-flex justify-content-center gap-3">
                        @auth
                            <a href="{{ route('user.home') }}" class="btn btn--base">
                                <i class="las la-home"></i> @lang('Go to Dashboard')
                            </a>
                            <a href="{{ route('user.esim.active') }}" class="btn btn-outline--base">
                                <i class="las la-sim-card"></i> @lang('View eSIMs')
                            </a>
                        @else
                            <a href="{{ route('home') }}" class="btn btn--base">
                                <i class="las la-home"></i> @lang('Go to Home')
                            </a>
                        @endauth
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

