@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="show-filter mb-3 text-end">
                <button type="button" class="btn btn-outline--primary showFilterBtn btn-sm"><i class="las la-filter"></i> @lang('Filter')</button>
            </div>
            <div class="card responsive-filter-card mb-4">
                <div class="card-body">
                    <form>
                        <div class="d-flex flex-wrap gap-4">
                            <div class="flex-grow-1">
                                <label>@lang('Keyword')</label>
                                <input type="search" name="search" placeholder="@lang('Search by Name / Code')" value="{{ request()->search }}" class="form-control">
                            </div>

                            <div class="flex-grow-1">
                                <label>@lang('Status')</label>
                                <select class="form-control select2" name="status" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="{{ Status::ENABLE }}" @selected(request()->status == Status::ENABLE)>@lang('Enabled')</option>
                                    <option value="{{ Status::DISABLE }}" @selected(isset(request()->status) && request()->status == Status::DISABLE)>@lang('Disabled')</option>
                                </select>
                            </div>

                            <div class="flex-grow-1">
                                <label>@lang('Is Featured')</label>
                                <select class="form-control select2" name="is_featured" data-minimum-results-for-search="-1">
                                    <option value="">@lang('All')</option>
                                    <option value="{{ Status::YES }}" @selected(request()->is_featured == Status::YES)>@lang('Yes')</option>
                                    <option value="{{ Status::NO }}" @selected(isset(request()->is_featured) && request()->is_featured == Status::NO)>@lang('No')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1">
                                <label>@lang('Records Per Page')</label>
                                <select class="form-control select2" name="per_page" data-minimum-results-for-search="-1">
                                    <option value="20" @selected(request('per_page') == 20 || gs('paginate_number') == 20)>@lang('20 Items')</option>
                                    <option value="50" @selected(request('per_page') == 50 || gs('paginate_number') == 50)>@lang('50 Items')</option>
                                    <option value="100" @selected(request('per_page') == 100 || gs('paginate_number') == 100)>@lang('100 Items')</option>
                                    <option value="150" @selected(request('per_page') == 150)>@lang('150 Items')</option>
                                    <option value="200" @selected(request('per_page') == 200)>@lang('200 Items')</option>
                                </select>
                            </div>
                            <div class="flex-grow-1 align-self-end">
                                <button class="btn btn--primary w-100 h-45"><i class="fas fa-filter"></i> @lang('Filter')</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th><input type="checkbox" id="selectAll"></th>
                                    <th class="text-start">@lang('Name')</th>
                                    <th>@lang('Code')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Featured')</th>
                                    <th>@lang('Not Featured')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($countries as $country)
                                    @php
                                        $country->image_with_full_path = getImage(getFilePath('countryFlag') . '/' . $country->image, getFileSize('countryFlag'));
                                    @endphp
                                    <tr>
                                        <td>
                                            <input type="checkbox" class="countryCheckbox" value="{{ $country->id }}">
                                        </td>

                                        <td>
                                            <div class="user">
                                                <div class="d-flex align-items-center gap-2 flex-wrap justify-content-end justify-content-md-start">
                                                    @if ($country->image)
                                                        <span class="thumb d-none d-lg-block">
                                                            <img src="{{ $country->image }}" alt="image">
                                                        </span>
                                                    @else
                                                        <span class="country-code-avatar">
                                                            {{ __($country->code ?? 'N/A') }}
                                                        </span>
                                                    @endif
                                                    <span>{{ $country->name }}</span>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $country->code }}</td>
                                        <td>@php echo $country->statusBadge @endphp</td>
                                        <td>
                                            <div class="form-check form-switch pl-35 m-0 d-inline-block">
                                                <input class="form-check-input toggle-feature" type="checkbox" role="switch" id="countrySwitch{{ $country->id }}" data-id="{{ $country->id }}" {{ $country->is_featured ? 'checked' : '' }}>
                                            </div>
                                        </td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary countryEditBtn" data-country="{{ json_encode($country) }}">
                                                    <i class="las la-pen"></i>@lang('Edit')
                                                </button>

                                                <a href="{{ route('admin.plan.all') . '?country=' . $country->slug }}" class="btn btn-sm btn-outline--info">
                                                    <i class="lar la-chart-bar"></i>@lang('Plans') ({{ $country->plans_count }})
                                                </a>

                                                @if ($country->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn changeStatusBtn" data-question="@lang('Are you sure to enable this country?')" data-action="{{ route('admin.destination.country.change.status') }}" data-status="{{ Status::ENABLE }}" data-id="{{ $country->id }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn changeStatusBtn" data-question="@lang('Are you sure to disable this country?')" data-action="{{ route('admin.destination.country.change.status') }}" data-status="{{ Status::DISABLE }}" data-id="{{ $country->id }}">
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

                @if ($countries->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($countries) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="countryModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Edit Country')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Flag')</label>
                            <x-image-uploader type="countryFlag" name="country_image" class="w-100" :required="false" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Country Name')</label>
                            <input type="text" class="form-control" name="name" disabled>
                        </div>
                        <div class="form-group">
                            <label>@lang('Country Code')</label>
                            <input type="text" class="form-control" name="code" disabled>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary w-100 h-45">@lang('Submit')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <div class="btn-group ms-2">
        <button type="button" class="btn btn-outline--info btn-sm bulkActionBtn" data-bs-toggle="dropdown" aria-expanded="false" disabled>
            <i class="las la-ellipsis-v"></i> @lang('Bulk Action')
        </button>
        <ul class="dropdown-menu">
            <li>
                <a href="javascript:void(0)" class="dropdown-item bulkActionOption confirmationBtn" data-question="@lang('Are you sure to enable selected countries?')" data-action="{{ route('admin.destination.country.change.status') }}" data-status="{{ Status::ENABLE }}">
                    <i class="las la-eye"></i> @lang('Enable')
                </a>
            </li>

            <li>
                <a  href="javascript:void(0)" class="dropdown-item bulkActionOption confirmationBtn" data-question="@lang('Are you sure to disable selected countries?')" data-action="{{ route('admin.destination.country.change.status') }}" data-status="{{ Status::DISABLE }}"><i class="lar la-eye-slash"></i> @lang('Disable')</a>
            </li>
        </ul>
    </div>

    <button class="btn btn-outline--primary btn-sm confirmationBtn" data-question="@lang('Are you sure to fetch countries from API?')" data-action="{{ route('admin.destination.country.fetch.all') }}">
        <i class="las la-plus"></i>@lang('Fetch Countries')
    </button>
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

        .country-code-avatar {
            width: 40px;
            height: 40px;
            display: flex;
            justify-content: center;
            align-items: center;
            border: 1px solid #000;
            border-radius: 50px;
            background: #000000;
            color: #fff;
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
                let selectedCountries = [];

                $('.countryCheckbox:checked').each(function() {
                    selectedCountries.push($(this).val());
                });

                if (selectedCountries.length === 0) {
                    notify('error', 'Please select at least one country.');
                    return;
                }

                showConfirmationModal(selectedCountries, status);
            });

            $(document).on('click', '.changeStatusBtn', function(e) {
                e.preventDefault();
                let status = $(this).data('status');
                let selectedCountries = [$(this).data('id')];
                showConfirmationModal(selectedCountries, status);
            });

            function showConfirmationModal(selectedCountries, status) {
                const modal = $('#confirmationModal');
                modal.find('form').append(`<input type="hidden" name="countries" value="${selectedCountries}">`);
                modal.find('form').append(`<input type="hidden" name="status" value="${status}">`);
                modal.find('form').off('submit');
                modal.modal('show');
            }

            $('#selectAll').on('change', function() {
                $('.countryCheckbox').prop('checked', $(this).prop('checked'));
            });

            function updateBulkActionState() {
                const anyChecked = $('.countryCheckbox:checked').length > 0;
                $('.bulkActionBtn').prop('disabled', !anyChecked);
            }

            $('.countryCheckbox, #selectAll').on('change', updateBulkActionState);


            $(document).ready(function() {
                $('#selectAll, .countryCheckbox').prop('checked', false);
            });

            $('.toggle-feature').on('change', function() {

                const countryId = $(this).data('id');
                const data = {
                    "_token": "{{ csrf_token() }}"
                };

                $.post("{{ route('admin.destination.country.toggle.feature', '') }}/" + countryId, data,
                    function(data, textStatus) {
                        if (textStatus == 'success') {
                            notify('success', data.message);
                        }
                    }
                );
            });


            $('.countryEditBtn').on('click', function() {
                let modal = $('#countryModal');
                let data = $(this).data('country');
                let _url = `{{ route('admin.destination.country.update', ':id') }}`.replace(':id', data.id);

                modal.find('form').attr('action', _url);
                modal.find('[name=name]').val(data.name);
                modal.find('[name=code]').val(data.code);
                modal.find('.image-upload-preview').css('background-image', `url(${data.image_with_full_path})`);
                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
