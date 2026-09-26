@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <div>
            <a href="{{ route('destination') }}" class="btn btn-outline--base btn--xsm"><i class="las la-arrow-right"></i> @lang('Purchase Now')</a>
            @if (Route::is('user.esim.active'))
                <a href="{{ route('user.esim.expired') }}" class="btn btn--xsm btn-outline--dark"> <i class="las la-clock"></i> @lang('Expired eSIMs')</a>
            @endif
        </div>
    </div>
    <div class="table--responsive mb-3">
        <table class="table table--custom table--responsive-sm">
            <thead>
                <tr>
                    <th>@lang('Plan')</th>
                    <th>@lang('Phone')</th>
                    <th>@lang('Operator')</th>
                    <th>@lang('Price')</th>
                    <th>@lang('Capacity')</th>
                    <th>@lang('Period')</th>
                    <th>@lang('Action')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($esims as $esim)
                    <tr>
                        <td>
                            <span @if(isset($esim->orderItem->plan->name) && strlen($esim->orderItem->plan->name) > 30) data-bs-toggle="tooltip" data-bs-title="{{  __($esim->orderItem->plan->name) }}" @endif>{{ __($esim->orderItem->plan->name ? strLimit(__($esim->orderItem->plan->name), 30) : 'N/A') }}</span>
                        </td>
                        <td>{{ $esim->phone_number }}</td>
                        <td>{{ __($esim->orderItem->plan->operator_name ?? 'N/A') }}</td>
                        @php
                            $payCurrency = strtoupper((string) ($esim->orderItem->order->payment_currency ?? 'EUR'));
                            $paySymbol = match($payCurrency) {
                                'GBP' => '£',
                                'USD' => '$',
                                default => '€',
                            };
                        @endphp
                        <td>{{ $paySymbol . showAmount($esim->orderItem->price, currencyFormat: false) . ' ' . $payCurrency }}</td>
                        <td>{{ $esim->orderItem->plan->capacity. $esim->orderItem->plan->capacity_unit  }}</td>
                        <td>{{ $esim->orderItem->plan->period }} @lang('Days')</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                @if (str_starts_with($esim->qr_code ?? '', 'http'))
                                    <a href="{{ stripPngFromUrl($esim->qr_code) }}" target="_blank" rel="noopener noreferrer" class="btn btn--xsm btn-outline--base text-nowrap" data-bs-toggle="tooltip" data-bs-title="@lang('Open QR Code')">
                                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" x2="21" y1="14" y2="3"/></svg>
                                        @lang('Open QR')
                                    </a>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%">
                            @if (Route::is('user.esim.active'))
                                @lang('You have not purchased any eSIM yet.')
                            @else
                                @lang('There is no expired eSIM exists.')
                            @endif
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($esims->hasPages())
        <div class="pagination-wrapper">
            {{ paginateLinks($esims) }}
        </div>
    @endif

    @if (Route::is('user.esim.active'))
    @endif
@endsection

@if (Route::is('user.esim.active'))
    @push('script')
        <script>
            "use strict";
            (function($) {
            })(jQuery);
        </script>
    @endpush
@endif
