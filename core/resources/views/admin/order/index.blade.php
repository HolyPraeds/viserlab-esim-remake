@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Order No.')</th>
                                    <th>@lang('Brand')</th>
                                    <th>@lang('User') | @lang('Username')</th>
                                    <th>@lang('Plan')</th>
                                    <th>@lang('Price')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Created At')</th>
                                    <th>@lang('Action')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($orders as $order)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">#{{ $order->order_number }}</span>
                                        </td>
                                        <td>
                                            <span class="badge badge--primary">{{ $order->brandName() }}</span>
                                        </td>
                                        <td>
                                            @php
                                                $accountEmail = $order->user?->email;
                                                $checkoutEmail = trim((string) ($order->deposits->sortByDesc('id')->first()?->detail?->guest_email ?? ''));
                                                if ($checkoutEmail !== '' && $accountEmail && strcasecmp($checkoutEmail, $accountEmail) === 0) {
                                                    $checkoutEmail = '';
                                                }
                                            @endphp
                                            <span class="fw-bold">{{ __($order->user?->fullname ?? '—') }}</span>
                                            <br>
                                            <span class="small">
                                                @if($order->user)
                                                    <a href="{{ appendQuery('search', $order->user->username) }}"><span>@</span>{{ __($order->user->username) }}</a>
                                                @else
                                                    <span class="text-muted">—</span>
                                                @endif
                                            </span>
                                            @if($accountEmail)
                                                <br><span class="small text-muted">{{ $accountEmail }}</span>
                                            @endif
                                            @if($checkoutEmail)
                                                <br><span class="small text--info">Checkout: {{ $checkoutEmail }}</span>
                                            @endif
                                        </td>
                                        <td>{{ __($order->orderItem?->plan?->name ?? '—') }}</td>
                                        <td>
                                            <span class="fw-bold">{{ showAmount($order->total_amount) }}</span>
                                        </td>
                                        <td>
                                            @php echo $order->statusBadge; @endphp
                                        </td>
                                        <td>{{ showDateTime($order->created_at) }} <br> {{ diffForHumans($order->created_at) }}</td>

                                        <td>
                                            @if($order->orderItem?->plan)
                                            <button class="btn btn-sm btn-outline--primary detailBtn"
                                            data-period="{{ $order->orderItem->plan->period }}"
                                            data-capacity="{{ $order->orderItem->plan->capacity }}"
                                            data-retail_price="{{ showAmount($order->orderItem->plan->retail_price, currencyFormat: false) }}"
                                            data-base_currency_amount="{{ showAmount($order->total_amount) }}"
                                            data-price_currency="{{ $order->orderItem->plan->price_currency }}"
                                            data-capacity_unit="{{ $order->orderItem->plan->capacity_unit }}"
                                            data-operator_name="{{ $order->orderItem->plan->operator_name }}"
                                            data-is_reloadable="{{ $order->orderItem->plan->reloadable }}"
                                            data-has_phone_number="{{ $order->orderItem->plan->phone_number }}"
                                            data-plan_name="{{ __($order->orderItem->plan->name) }}">
                                                <i class="las la-desktop"></i>@lang('Details')
                                            </button>
                                            @endif
                                            <button class="btn btn-sm btn-outline--info resendEmailBtn"
                                                data-action="{{ route('admin.order.resend.emails', $order->order_number) }}"
                                                data-order="{{ $order->order_number }}"
                                                data-account="{{ $accountEmail ?? '' }}"
                                                data-checkout="{{ $checkoutEmail ?? '' }}">
                                                <i class="las la-envelope"></i>@lang('Resend email')
                                            </button>
                                        </td>
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
                @if ($orders->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($orders) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div id="detailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Order Detail')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Plan')</span>
                            <span class="plan_name"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Retail Price')</span>
                            <span class="retail_price"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Capacity')</span>
                            <span class="capacity"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Period')</span>
                            <span class="period"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Operator Name')</span>
                            <span class="operator_name"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Reloadable')</span>
                            <span class="is_reloadable"></span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                            <span>@lang('Phone Number')</span>
                            <span class="has_phone_number"></span>
                        </li>
                    </ul>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>

    <div id="resendEmailModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form method="POST" id="resendEmailForm">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Resend purchase email')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <p class="mb-2">@lang('Order'): <strong class="resend-order-number"></strong></p>
                        <p class="mb-1 small">@lang('Account email'): <span class="resend-account-email text-muted">—</span></p>
                        <p class="mb-3 small">@lang('Checkout email'): <span class="resend-checkout-email text-muted">—</span></p>
                        <p class="small text-muted">@lang('Leave empty to resend to account + checkout emails. Or enter another address to send there as well.')</p>
                        <div class="form-group">
                            <label>@lang('Also send to')</label>
                            <input type="email" name="email" class="form-control" placeholder="customer@email.com">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn--dark" data-bs-dismiss="modal">@lang('Close')</button>
                        <button type="submit" class="btn btn--primary">@lang('Send')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' />
@endpush

@push('script')
    <script>
        (function($) {
            'use strict';

            $('.detailBtn').on('click', function() {
                let data = $(this).data();
                let modal = $('#detailModal');
                modal.find('.plan_name').text(data.plan_name);
                modal.find('.retail_price').text(data.retail_price + ' ' + data.price_currency + ' (' + data.base_currency_amount + ')');
                modal.find('.capacity').text(data.capacity + ' ' + data.capacity_unit);
                modal.find('.period').text(data.period + ' @lang('Days')');
                modal.find('.operator_name').text(data.operator_name);
                modal.find('.is_reloadable').text(data.is_reloadable ? '@lang('Yes')' : '@lang('No')');
                modal.find('.has_phone_number').text(data.has_phone_number ? '@lang('Yes')' : '@lang('No')');

                modal.modal('show');
            });

            $('.resendEmailBtn').on('click', function() {
                let btn = $(this);
                let modal = $('#resendEmailModal');
                let account = btn.data('account') || '—';
                let checkout = btn.data('checkout') || '—';
                modal.find('#resendEmailForm').attr('action', btn.data('action'));
                modal.find('.resend-order-number').text('#' + btn.data('order'));
                modal.find('.resend-account-email').text(account);
                modal.find('.resend-checkout-email').text(checkout);
                modal.find('input[name="email"]').val(btn.data('checkout') || '');
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
