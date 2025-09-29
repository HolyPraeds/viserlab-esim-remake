@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="d-flex flex-column justify-content-between align-items-start mb-4">
        <h5 class="mb-2">@lang('Referral Link')</h5>
        <div class="form-group w-100">
            <div class="input-group">
                <input type="text" class="form-control form--control inputLink" value="{{ $link }}" readonly>
                <button type="button" class="btn btn--xsm btn--base copyBtn" title="@lang('Copy')">
                    <i class="fas fa-copy"></i> @lang('Copy')
                </button>
            </div>
        </div>
    </div>
    <div class="row g-3 g-sm-4 mb-4">
        <div class="col-6 col-md-6">
            <div class="dashboard-widget primary">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Total Referrals')</span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ $widget['total_referrals'] }}</h4>
                </div>
            </div>
        </div>
        <div class="col-6 col-md-6">
            <div class="dashboard-widget success">
                <div class="dashboard-widget__top">
                    <span class="dashboard-widget__label">@lang('Referral Earnings')</span>
                </div>
                <div class="dashboard-widget__content">
                    <span class="dashboard-widget__dot"></span>
                    <h4 class="dashboard-widget__title">{{ showAmount($widget['total_earning']) }}</h4>
                </div>
            </div>
        </div>
    </div>

    <h5 class="my-3">@lang('Referred Users')</h5>
    <div class="table--responsive">
        <table class="table table--custom table--responsive-sm">
            <thead>
                <tr>
                    <th width="200px">@lang('Name')</th>
                    <th>@lang('Email')</th>
                    <th>@lang('Date')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($referrals as $referral)
                    <tr>
                        <td>{{ __($referral->fullname) }}</td>
                        <td>{{ showEmailAddress($referral->email) }}</td>
                        <td>{{ showDateTime($referral->created_at, 'd M, Y') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            {{ __($emptyMessage) }}
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.copyBtn').on('click', function() {
                var input = $(this).siblings('.inputLink');
                input.select();
                document.execCommand("copy");
                $('.copyBtn').html(`<i class="fas fa-check"></i> @lang('Copied')`);
                setTimeout(function() {
                    $('.copyBtn').html(`<i class="fas fa-copy"></i> @lang('Copy')`);
                }, 1000);
            });
        })(jQuery);
    </script>
@endpush
