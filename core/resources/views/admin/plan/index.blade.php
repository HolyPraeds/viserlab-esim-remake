@extends('admin.layouts.app')
@section('panel')
    <div class="row gy-4">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>
                                        <input type="checkbox" id="selectAll">
                                        <label for="selectAll" class="mb-0 ms-1">@lang('Name')</label>
                                    </th>
                                    <th>@lang('Region')</th>
                                    <th>@lang('Operator')</th>
                                    <th>@lang('Capacity')</th>
                                    <th>@lang('Price') | @lang('Period')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($plans as $plan)
                                    @php
                                        $currency = $currencies->where('api_currency', $plan->price_currency)->first();
                                        if($currency){
                                            $rate = $currency->conversion_rate;
                                            $code = $currency->api_currency;
                                        }else{
                                            $rate = 1;
                                            $code  = __(gs('cur_text'));
                                        }
                                    @endphp
                                    <tr>
                                        <td>
                                            <div>
                                                <input type="checkbox" class="planCheckbox" id="id-{{ $plan->id }}" value="{{ $plan->id }}" />
                                                <label class="ms-1" for="id-{{ $plan->id }}" @if (strlen($plan->name) > 38) title="{{ __($plan->name) }}" @endif>{{ strLimit(__($plan->name), 38) }}</label>
                                            </div>
                                        </td>

                                        <td>
                                            <span @if ($plan->region->status == Status::DISABLE) class="text--warning" title="@lang('Disabled')" @endif>
                                                {{ __($plan->region->name) }}
                                            </span>
                                        </td>

                                        <td>{{ __($plan->operator_name) }}</td>
                                        <td>{{ showPlanCapacity($plan) }}</td>

                                        <td>
                                            <span class="fw-bold">{{ showAmount($plan->converted_price) }}</span>
                                            <br>
                                            <span class="small">{{ $plan->period }} @lang('days')</span>
                                        </td>
                                        <td>
                                            @php
                                                echo $plan->statusBadge;
                                            @endphp
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.plan.detail', $plan->id) }}" class="btn btn-sm btn-outline--primary">
                                                    <i class="las la-desktop"></i>@lang('Details')
                                                </a>
                                                <button class="btn btn-sm btn-outline--info editBtn" data-id="{{ $plan->id }}" data-retail_price="{{ getAmount($plan->retail_price) }}" data-rate="{{ $rate }}" data-code="{{ $code }}"><i class="las la-pencil-alt"></i>@lang('Retail Price')</button>
                                                @if ($plan->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn changeStatusBtn" data-question="@lang('Are you sure to enable this plan?')" data-action="{{ route('admin.plan.change.status') }}" data-status="{{ Status::ENABLE }}" data-id="{{ $plan->id }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn changeStatusBtn" data-question="@lang('Are you sure to disable this plan?')" data-action="{{ route('admin.plan.change.status') }}" data-status="{{ Status::DISABLE }}" data-id="{{ $plan->id }}">
                                                        <i class="la la-eye-slash"></i>@lang('Disable')
                                                    </button>
                                                @endif
                                            </div>
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
                @if ($plans->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($plans) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="offcanvas offcanvas-end" tabindex="-1" id="offcanvasRight" aria-labelledby="offcanvasRightLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="offcanvasRightLabel">@lang('Search')</h5>
            <button type="button" class="close" data-bs-dismiss="offcanvas" aria-label="Close">
                <i class="las la-times"></i>
            </button>
        </div>
        <div class="offcanvas-body">
            <form action="" class="h-100 d-flex flex-column justify-content-between">
                <div>
                    <div class="form-group">
                        <label>@lang('Keyword')</label>
                        <input type="text" name="search" value="{{ request()->search }}" class="form-control">
                    </div>
                    <div class="form-group">
                        <label>@lang('Status')</label>
                        <select class="form-control select2" name="status" data-minimum-results-for-search="-1">
                            <option value="">@lang('All')</option>
                            <option value="{{ Status::ENABLE }}" @selected(request()->status == Status::ENABLE)>@lang('Enabled')</option>
                            <option value="{{ Status::DISABLE }}" @selected(isset(request()->status) && request()->status == Status::DISABLE)>@lang('Disabled')</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label>@lang('Records Per Page')</label>
                        <select class="form-control select2" name="per_page" data-minimum-results-for-search="-1">
                            <option value="20" @selected(request('per_page') == 20 || gs('paginate_number') == 20)>@lang('20 Items')</option>
                            <option value="50" @selected(request('per_page') == 50 || gs('paginate_number') == 50)>@lang('50 Items')</option>
                            <option value="100" @selected(request('per_page') == 100 || gs('paginate_number') == 100)>@lang('100 Items')</option>
                            <option value="150" @selected(request('per_page') == 150)>@lang('150 Items')</option>
                            <option value="200" @selected(request('per_page') == 200)>@lang('200 Items')</option>
                        </select>
                    </div>
                </div>
                <button class="btn btn--primary h-45 w-100 mb-3">@lang('Search')</button>
            </form>
        </div>
    </div>

    <div id="editModal" class="modal fade" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">@lang('Update Retail Price')</h5>
                    <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                        <i class="las la-times"></i>
                    </button>
                </div>
                <form method="POST">
                    @csrf
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Retail Price')</label>
                            <div class="input-group">
                                <input type="number" step="any" name="retail_price" class="form-control" required>
                                <span class="input-group-text currencyCode"></span>
                            </div>
                            <div class="py-3 text-center border border-radius-5 mt-2">
                                <span>@lang('1') {{ __(gs('cur_text')) }}</span> = <span class="rate fw-bold"></span> <span class="currencyCode"></span>. <br>
                                <span>@lang('Retail price in') {{ __(gs('cur_text')) }}</span> <span class="finalConvertedPrice fw-bold"></span>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="btn-group ms-2">
        <button type="button" class="btn btn-sm btn-outline--info bulkActionBtn" data-bs-toggle="dropdown" aria-expanded="false" disabled>
            <i class="las la-ellipsis-v"></i> @lang('Bulk Action')
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="javascript:void(0)" class="dropdown-item bulkActionOption confirmationBtn" data-question="@lang('Are you sure to enable the selected plans?')" data-action="{{ route('admin.plan.change.status') }}" data-status="{{ Status::ENABLE }}">
                    <i class="las la-eye"></i> @lang('Enable')
                </a>
            </li>
            <li>
                <a href="javascript:void(0)" class="dropdown-item bulkActionOption confirmationBtn" data-question="@lang('Are you sure to disable the selected plans?')" data-action="{{ route('admin.plan.change.status') }}" data-status="{{ Status::DISABLE }}">
                    <i class="lar la-eye-slash"></i> @lang('Disable')
                </a>
            </li>
        </ul>
    </div>

    <button class="btn btn-sm btn-outline--primary confirmationBtn" data-question="@lang('Are you sure to fetch plans?')" data-action="{{ route('admin.plan.fetch.all') }}"><i class="las la-plus"></i>@lang('Fetch Plans')</button>

    <button class="btn btn-outline--primary btn-sm" data-bs-toggle="offcanvas" data-bs-target="#offcanvasRight" aria-controls="offcanvasRight"><i class="las la-filter"></i>@lang('Filter')</button>
@endpush

@push('style')
    <style>
        .close {
            background: none !important;
        }

        .border-radius-5{
            border-radius: 5px !important;
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";

            $(document).on('click', '.bulkActionOption', function(e) {
                e.preventDefault();

                let status = $(this).data('status');
                let selectedPlans = [];

                $('.planCheckbox:checked').each(function() {
                    selectedPlans.push($(this).val());
                });

                if (selectedPlans.length === 0) {
                    notify('error', `@lang("Please select at least one plan")`);
                    return;
                }

                showConfirmationModal(status, selectedPlans);
            });

            $(document).on('click', '.changeStatusBtn', function(e) {
                e.preventDefault();
                let status = $(this).data('status');
                let selectedPlans = [$(this).data('id')];
                showConfirmationModal(status, selectedPlans);
            });

            function showConfirmationModal(status, selectedPlans) {
                const modal = $('#confirmationModal');
                modal.find('form').append(`<input type="hidden" name="plans[]" value="${selectedPlans}">`);
                modal.find('form').append(`<input type="hidden" name="status" value="${status}">`);
                modal.find('form').off('submit');
                modal.modal('show');
            }

            $('#selectAll').on('change', function() {
                $('.planCheckbox').prop('checked', $(this).prop('checked'));
            })

            function updateBulkActionState() {
                const anyChecked = $('.planCheckbox:checked').length > 0;
                $('.bulkActionBtn').prop('disabled', !anyChecked);
            }

            $('.planCheckbox, #selectAll').on('change', updateBulkActionState);


            $(document).ready(function() {
                $('#selectAll, .planCheckbox').prop('checked', false);
            });

            const editModal = $('#editModal');
            $('.editBtn').on('click', function(){
                let data = $(this).data();
                editModal.find('input[name="retail_price"]').val(data.retail_price);
                editModal.find('.currencyCode').text(data.code);
                editModal.find('.rate').text(data.rate);
                editModal.find('.finalConvertedPrice').text(parseFloat(data.retail_price / data.rate).toFixed(2));
                editModal.find('form').attr('action', `{{ route('admin.plan.update.retail.price', '') }}/${data.id}`);
                editModal.modal('show');
            });

            editModal.on('input', 'input[name="retail_price"]', function() {
                let rate = parseFloat(editModal.find('.rate').text());
                let retailPrice = parseFloat($(this).val());
                let finalConvertedPrice = (retailPrice / rate).toFixed(2);
                editModal.find('.finalConvertedPrice').text(finalConvertedPrice);
            });

        })(jQuery);
    </script>
@endpush
