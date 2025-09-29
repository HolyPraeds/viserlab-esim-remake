@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <div class="d-flex align-items-center flex-wrap gap-2">
            <x-search-form />

            @if (Route::is('user.deposit.history'))
                <div class="deposit-button">
                    <a href="{{ route('user.deposit.index') }}" class="btn btn-outline--base"> <i class="las la-money-bill-wave"></i> @lang('Deposit Now')</a>
                </div>
            @endif
        </div>
    </div>
    <div class="table--responsive mb-3">
        <table class="table table--custom table--responsive-sm">
            <thead>
                <tr>
                    <th>@lang('Gateway | Transaction')</th>
                    <th>@lang('Initiated')</th>
                    <th>@lang('Amount')</th>
                    <th>@lang('Conversion')</th>
                    <th>@lang('Status')</th>
                    <th>@lang('Details')</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($deposits as $deposit)
                    <tr>
                        <td>
                            <div>
                                <span class="fw-bold">
                                    <span class="text--base">
                                        @if ($deposit->method_code < 5000)
                                            {{ __($deposit->gateway->name) }}
                                        @else
                                            @lang('Google Pay')
                                        @endif
                                    </span>
                                </span>
                                <br>
                                <small> {{ $deposit->trx }} </small>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ showDateTime($deposit->created_at) }}
                                <br>
                                {{ diffForHumans($deposit->created_at) }}
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ showAmount($deposit->amount) }} + <span class="text--danger" data-bs-toggle="tooltip" title="@lang('Processing Charge')">{{ showAmount($deposit->charge) }} </span>
                                <br>
                                <strong data-bs-toggle="tooltip" title="@lang('Amount with charge')">
                                    {{ showAmount($deposit->amount + $deposit->charge) }}
                                </strong>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ showAmount(1) }} = {{ showAmount($deposit->rate, currencyFormat: false) }} {{ __($deposit->method_currency) }}
                                <br>
                                <strong>{{ showAmount($deposit->final_amount, currencyFormat: false) }} {{ __($deposit->method_currency) }}</strong>
                            </div>
                        </td>
                        <td> @php echo $deposit->statusBadge @endphp </td>
                        @php
                            $details = [];
                            if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000) {
                                foreach (isset($deposit->detail) ? $deposit->detail : [] as $key => $info) {
                                    $details[] = $info;
                                    if ($info->type == 'file') {
                                        $details[$key]->value = route('user.download.attachment', encrypt(getFilePath('verify') . '/' . $info->value));
                                    }
                                }
                            }
                        @endphp

                        <td>
                            @if ($deposit->method_code >= 1000 && $deposit->method_code <= 5000)
                                <button type="button" class="btn btn--icon btn--xsm btn-outline--base text-nowrap detailBtn" data-info="{{ json_encode($details) }}" @if ($deposit->status == Status::PAYMENT_REJECT) data-admin_feedback="{{ $deposit->admin_feedback }}" @endif>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-monitor">
                                        <rect x="2" y="3" width="20" height="14" rx="2" ry="2" />
                                        <line x1="8" y1="21" x2="16" y2="21" />
                                        <line x1="12" y1="17" x2="12" y2="21" />
                                    </svg>
                                </button>
                            @else
                                <button class="btn btn--icon btn--xsm btn-outline--base text-nowrap" type="button" data-bs-toggle="tooltip" data-bs-title="Automatically processed">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-circle-check-big-icon lucide-circle-check-big">
                                        <path d="M21.801 10A10 10 0 1 1 17 3.335" />
                                        <path d="m9 11 3 3L22 4" />
                                    </svg>
                                </button>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="100%" class="text-center">{{ __($emptyMessage) }}</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if ($deposits->hasPages())
        <div class="pagination-wrapper">
            {{ paginateLinks($deposits) }}
        </div>
    @endif

    {{-- APPROVE MODAL --}}
    <div id="detailModal" class="modal custom--modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Details')</h5>
                    <span type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </span>
                </div>
                <div class="modal-body">
                    <ul class="list-group list-group-flush userData mb-2">
                    </ul>
                    <div class="feedback"></div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn--dark btn--xsm" data-bs-dismiss="modal">@lang('Close')</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.detailBtn').on('click', function() {
                var modal = $('#detailModal');

                var userData = $(this).data('info');
                var html = '';
                if (userData) {
                    userData.forEach(element => {
                        if (element.type != 'file') {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>${element.name}</span>
                                <span">${element.value}</span>
                            </li>`;
                        } else {
                            html += `
                            <li class="list-group-item d-flex justify-content-between align-items-center px-0">
                                <span>${element.name}</span>
                                <span"><a href="${element.value}"><i class="fa-regular fa-file"></i> @lang('Attachment')</a></span>
                            </li>`;
                        }
                    });
                }

                modal.find('.userData').html(html);

                if ($(this).data('admin_feedback') != undefined) {
                    var adminFeedback = `
                        <div class="my-3">
                            <strong>@lang('Admin Feedback')</strong>
                            <p>${$(this).data('admin_feedback')}</p>
                        </div>
                    `;
                } else {
                    var adminFeedback = '';
                }

                modal.find('.feedback').html(adminFeedback);


                modal.modal('show');
            });

            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[title], [data-title], [data-bs-title]'))
            tooltipTriggerList.map(function(tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl)
            });

        })(jQuery);
    </script>
@endpush
