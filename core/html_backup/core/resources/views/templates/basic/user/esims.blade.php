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
                    <th>@lang('Expiry Date')</th>
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
                        <td>{{ showAmount($esim->orderItem->price) }}</td>
                        <td>{{ $esim->orderItem->plan->capacity. $esim->orderItem->plan->capacity_unit  }}</td>
                        <td>{{ $esim->orderItem->plan->period }} @lang('Days')</td>
                        <td>{{ showDateTime($esim->expiry_date, 'd M, Y') }}</td>
                        <td>
                            <div class="d-flex align-items-center justify-content-end gap-2">
                                <button type="button" class="btn btn--xsm btn-outline--base qrCodeBtn text-nowrap" data-id="{{ $esim->id }}" data-url="{{ route('user.esim.get.qr', $esim->id) }}" data-bs-toggle="tooltip" data-bs-title="View QR Code">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-qr-code-icon lucide-qr-code">
                                        <rect width="5" height="5" x="3" y="3" rx="1" />
                                        <rect width="5" height="5" x="16" y="3" rx="1" />
                                        <rect width="5" height="5" x="3" y="16" rx="1" />
                                        <path d="M21 16h-3a2 2 0 0 0-2 2v3" />
                                        <path d="M21 21v.01" />
                                        <path d="M12 7v3a2 2 0 0 1-2 2H7" />
                                        <path d="M3 12h.01" />
                                        <path d="M12 3h.01" />
                                        <path d="M12 16v.01" />
                                        <path d="M16 12h1" />
                                        <path d="M21 12v.01" />
                                        <path d="M12 21v-1" />
                                    </svg>
                                </button>
                                <button class="btn btn--xsm btn-outline--base checkCapacity text-nowrap" type="button" data-id="{{ $esim->id }}" data-bs-toggle="tooltip" data-bs-title="Details">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="lucide lucide-card-sim-icon lucide-card-sim">
                                        <path d="M12 14v4" />
                                        <path d="M14.172 2a2 2 0 0 1 1.414.586l3.828 3.828A2 2 0 0 1 20 7.828V20a2 2 0 0 1-2 2H6a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2z" />
                                        <path d="M8 14h8" />
                                        <rect x="8" y="10" width="8" height="8" rx="1" />
                                    </svg>
                                </button>
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
        <div class="modal custom--modal qr--modal fade" id="qrCodeModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-sm">
                <div class="modal-content">
                    <div class="modal-body">
                        <button type="button" class="btn btn--xsm btn--close" data-bs-dismiss="modal" aria-label="@lang('Close')"></button>
                        <div class="modal-heading modal-heading--active d-none">
                            <h6 class="modal-heading__title">@lang('Scan QR Code')</h6>
                            <p class="modal-heading__desc">@lang('Scan this code to activate your eSIM')</p>
                            <img src="" class="modalQr img-fluid esim-qr-img" alt="QR Code">
                        </div>

                        <div class="modal-heading modal-heading--pending d-none">
                            <h6 class="modal-heading__title">@lang('Pending QR Code')</h6>
                            <p class="modal-heading__desc qrWaiting">@lang('Your eSIM is still pending for activation. Please wait.')</p>
                            <img src="{{ asset($activeTemplateTrue . 'images/icon/pending-plan-2.png') }}" class="pending-img" alt="QR Code">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Capacity Info Modal -->
        <div class="modal custom--modal fade" id="capacityModal" tabindex="-1" aria-labelledby="capacityModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h6 class="modal-title mb-0">@lang('eSIM Capacity Info')</h6>
                        <button type="button" class="btn btn--xsm btn--close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <ul class="info-list">
                            <li class="info-list__item">
                                <span class="info-list__label">@lang('Phone Number')</span>
                                <span class="info-list__value modalPhone"></span>
                            </li>
                            <li class="info-list__item">
                                <span class="info-list__label">@lang('Remaining Capacity')</span>
                                <span class="info-list__value modalCapacity"></span>
                            </li>
                            <li class="info-list__item">
                                <span class="info-list__label">@lang('Plan Expiry Date')</span>
                                <span class="info-list__value modalPlanExpiry"></span>
                            </li>
                            <li class="info-list__item">
                                <span class="info-list__label">@lang('eSIM Expiry Date')</span>
                                <span class="info-list__value modalEsimExpiry"></span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endif
@endsection

@if (Route::is('user.esim.active'))
    @push('script')
        <script>
            "use strict";
            (function($) {
                $('.qrCodeBtn').on('click', function() {
                    const url = $(this).data('url');

                    $.ajax({
                        url: url,
                        method: 'GET',
                        success: function(res) {
                            $('#qrCodeModal').find('.modal-heading--active, .modal-heading--pending').addClass('d-none');
                            $('.modalQr').attr('src', '').addClass('d-none');
                            if (res.status === 'PENDING') {
                                $('#qrCodeModal').find('.modal-heading--pending').removeClass('d-none');
                            } else if (res.status === 'ACTIVE') {
                                $('#qrCodeModal').find('.modal-heading--active').removeClass('d-none');
                                $('.modalQr').attr('src', res.qr).removeClass('d-none');
                            }
                            $('#qrCodeModal').modal('show');
                        },
                        error: function() {
                            notify('error', `@lang('Something went wrong. Please try again later')`);
                        }
                    });
                });


                $('.checkCapacity').on('click', function() {
                    let esimId = $(this).data('id');
                    $.ajax({
                        url: "{{ route('user.esim.check.capacity') }}",
                        method: 'POST',
                        data: {
                            esim_id: esimId,
                            _token: '{{ csrf_token() }}'
                        },
                        success: function(response) {
                            if (response.status === 'success') {
                                let data = response.data;

                                $('.modalPhone').text(data.phone);
                                $('.modalCapacity').text(data.remaining);
                                $('.modalPlanExpiry').text(data.plan_expiry);
                                $('.modalEsimExpiry').text(data.esim_expiry);

                                $('#capacityModal').modal('show');
                            } else {
                                notify('error', response.message);
                            }
                        },
                        error: function() {
                            notify('error', `@lang('Something went wrong. Please try again later')`);
                        }
                    });
                });
            })(jQuery);
        </script>
    @endpush
@endif
