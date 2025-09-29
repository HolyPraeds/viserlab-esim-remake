@extends($activeTemplate . 'layouts.master')

@section('content')
    <div class="notice"></div>
    @php
        $kyc = getContent('kyc.content', true);
    @endphp
    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="alert alert--danger" role="alert">
            <div class="alert__icon"><i class="las la-times"></i></div>
            <div class="alert__content">
                <h6 class="alert__title">@lang('KYC Documents Rejected')</h6>
                <p class="alert__desc">
                    {{ __($kyc?->data_values?->reject) }} <a class="alert__link" href="{{ route('user.kyc.form') }}">@lang('Click Here to Re-submit Documents')</a>.
                </p>
            </div>
        </div>
    @elseif(auth()->user()->kv == Status::KYC_UNVERIFIED)
        <div class="alert alert--info" role="alert">
            <div class="alert__icon"><i class="las la-check"></i></div>
            <div class="alert__content">
                <h6 class="alert__title">@lang('KYC Verification required')</h6>
                <p class="alert__desc">{{ __($kyc?->data_values?->required) }} <a class="alert__link" href="{{ route('user.kyc.form') }}">@lang('Click Here to Submit Documents')</a></p>
            </div>
        </div>
    @elseif(auth()->user()->kv == Status::KYC_PENDING)
        <div class="alert alert--warning" role="alert">
            <div class="alert__icon"><i class="las la-hourglass-start"></i></div>
            <div class="alert__content">
                <h6 class="alert__title">@lang('KYC Verification pending')</h6>
                <p class="alert__desc">
                    {{ __($kyc?->data_values?->pending) }} <a class="alert__link" href="{{ route('user.kyc.data') }}">@lang('See KYC Data')</a>
                </p>
            </div>
        </div>
    @endif

    @if (auth()->user()->kv == Status::KYC_UNVERIFIED && auth()->user()->kyc_rejection_reason)
        <div class="modal fade" id="kycRejectionReason">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('KYC Document Rejection Reason')</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>{{ auth()->user()->kyc_rejection_reason }}</p>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <div class="row g-3 g-sm-4">
        <div class="col-6 col-md-3">
            <a class="dashboard-widget primary" href="{{ route('user.esim.active') }}">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Total eSIMs')</span>
                    <span class="dashboard-widget__arrow">
                        <i class="las la-arrow-up"></i>
                    </span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ $widget['total_esim'] }}</h4>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a class="dashboard-widget success" href="{{ route('user.esim.active') }}">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Active eSIMs')</span>
                    <span class="dashboard-widget__arrow">
                        <i class="las la-arrow-up"></i>
                    </span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ $widget['active_esim'] }}</h4>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a class="dashboard-widget danger" href="{{ route('user.esim.expired') }}">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Expired eSIMs')</span>
                    <span class="dashboard-widget__arrow">
                        <i class="las la-arrow-up"></i>
                    </span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ $widget['expired_esim'] }}</h4>
                </div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a class="dashboard-widget warning" href="{{ route('user.order.pending') }}">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Pending Orders')</span>
                    <span class="dashboard-widget__arrow">
                        <i class="las la-arrow-up"></i>
                    </span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ $widget['pending_orders'] }}</h4>
                </div>
            </a>
        </div>
    </div>

    <div class="row mt-3">
        <div class="col-lg-12">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                <h5>@lang('Recent Transactions')</h5>
                <div class="mb-3">
                    <a href="{{ route('user.transactions') }}" class="btn btn--xsm btn--dark"><i class="fa-solid fa-angles-right"></i> @lang('View All')</a>
                </div>
            </div>
            <div class="table--responsive mb-3">
                <table class="table table--custom table--responsive-sm">
                    <thead>
                        <tr>
                            <th>@lang('Trx')</th>
                            <th>@lang('Transacted')</th>
                            <th>@lang('Amount')</th>
                            <th>@lang('Post Balance')</th>
                            <th>@lang('Details')</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions as $trx)
                            <tr>
                                <td>
                                    <strong>{{ $trx->trx }}</strong>
                                </td>

                                <td>
                                    {{ showDateTime($trx->created_at) }}<br>{{ diffForHumans($trx->created_at) }}
                                </td>

                                <td>
                                    <span class="fw-bold @if ($trx->trx_type == '+') text--success @else text--danger @endif">
                                        {{ $trx->trx_type }} {{ showAmount($trx->amount) }}
                                    </span>
                                </td>

                                <td>
                                    {{ showAmount($trx->post_balance) }}
                                </td>

                                <td>{{ __($trx->details) }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td class="text-muted text-center" colspan="100%">{{ __($emptyMessage) }}</td>
                            </tr>
                        @endforelse

                    </tbody>
                </table>
            </div>
        </div>
    </div>
@endsection
