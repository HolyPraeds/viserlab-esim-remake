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
                                    <th>@lang('Username')</th>
                                    <th>@lang('Plan Name')</th>
                                    <th>@lang('Paid Price')</th>
                                    <th>@lang('Retail Price')</th>
                                    <th>@lang('Activated At')</th>
                                    <th>@lang('Expire At')</th>
                                    <th>@lang('Action')</th>

                                </tr>
                            </thead>
                            <tbody>
                                @forelse($esims as $esim)
                                    <tr>
                                        <td>
                                            <span class="fw-bold">{{ __($esim->user->fullname) }}</span>
                                            <br>
                                            <span class="small">
                                                <a href="{{ appendQuery('search', $esim->user->username) }}"><span>@</span>{{ __($esim->user->username) }}</a>
                                            </span>
                                        </td>
                                        <td>{{ __($esim->orderItem->order->plan->name ?? 'N/A') }}</td>
                                        <td class="fw-bold">{{ showAmount($esim->orderItem->paid_price) }}</td>
                                        <td class="fw-bold">{{ showAmount($esim->orderItem->price) }}</td>
                                        <td>{{ showDateTime($esim->created_at) }} <br> {{ diffForHumans($esim->created_at) }}</td>
                                        <td>{{ showDateTime($esim->expiry_date) }} <br> {{ diffForHumans($esim->expiry_date) }}</td>

                                        <td>
                                            <button class="btn btn-sm btn-outline--primary ms-1 detailBtn" data-username="{{ __($esim->user->username) }}" data-plan="{{ __($esim->orderItem->order->plan->name ?? 'N/A') }}"
                                                data-purchase="{{ $esim->orderItem->purchase_id }}" data-serial="{{ $esim->serial_number }}" data-phone="{{ $esim->phone_number }}"
                                                data-activated="{{ showDateTime($esim->created_at, 'd-M-Y, h:i A') }}" data-expiry="{{ showDateTime($esim->expiry_date, 'd-M-Y, h:i A') }}">
                                                <i class="las la-desktop"></i> @lang('Details')
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
                @if ($esims->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($esims) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="detailModal" tabindex="-1" role="dialog" aria-labelledby="detailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailModalLabel">@lang('eSIM Details')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <div class="modal-body ">
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Username')</strong> <span class="modalUsername"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Plan Name')</strong> <span class="modalPlan"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Purchase Id')</strong> <span class="modalPurchase"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Serial Number')</strong> <span class="modalSerial"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Phone Number')</strong> <span class="modalPhone"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Activated At')</strong> <span class="modalActivated"></span></li>
                        <li class="list-group-item d-flex justify-content-between align-items-center"><strong>@lang('Expires At')</strong> <span class="modalExpiry"></span></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
@endsection

@push('breadcrumb-plugins')
    <x-search-form dateSearch='yes' />
@endpush

@push('style')
    <style>
        .form-switch .form-check-input {
            width: 3em;
            height: 1.5em;
        }

        .form-check-input:checked {
            background-color: #28c76f;
            border-color: #28c76f;
        }

        .form-check-input:focus {
            border-color: #28C76F !important;
            box-shadow: #28C76F 0px 0px 0px 0px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $('.detailBtn').on('click', function() {
                const modal = $('#detailModal');

                modal.find('.modalUsername').text($(this).data('username'));
                modal.find('.modalPlan').text($(this).data('plan'));
                modal.find('.modalPurchase').text($(this).data('purchase'));
                modal.find('.modalSerial').text($(this).data('serial'));
                modal.find('.modalPhone').text($(this).data('phone'));
                modal.find('.modalActivated').text($(this).data('activated'));
                modal.find('.modalExpiry').text($(this).data('expiry'));

                modal.modal('show');
            });

            $(document).on('click', '.bulkActionOption', function(e) {
                e.preventDefault();

                let status = $(this).data('status');
                let selectedEsims = [];

                $('.esimCheckbox:checked').each(function() {
                    selectedEsims.push($(this).val());
                });

                if (selectedEsims.length === 0) {
                    notify('error', 'Please select at least one eSIM');
                    return;
                }

                showConfirmationModal(selectedEsims, status);
            });

            $(document).on('click', '.changeStatusBtn', function(e) {
                e.preventDefault();
                let status = $(this).data('status');
                let selectedEsims = [$(this).data('id')];
                showConfirmationModal(selectedEsims, status);
            });

            function showConfirmationModal(selectedEsims, status) {
                const modal = $('#confirmationModal');
                modal.find('form').append(`<input type="hidden" name="esims" value="${selectedEsims}">`);
                modal.find('form').append(`<input type="hidden" name="status" value="${status}">`);
                modal.find('form').off('submit');
                modal.modal('show');
            }

            $('#selectAll').on('change', function() {
                $('.esimCheckbox').prop('checked', $(this).prop('checked'));
                updateBulkActionState();
            });

            function updateBulkActionState() {
                const anyChecked = $('.esimCheckbox:checked').length > 0;
                $('.bulkActionBtn').prop('disabled', !anyChecked);
            }

            $('.esimCheckbox').on('change', updateBulkActionState);

            $(document).ready(function() {
                $('#selectAll, .esimCheckbox').prop('checked', false);
                updateBulkActionState();
            });
        })(jQuery);
    </script>
@endpush
