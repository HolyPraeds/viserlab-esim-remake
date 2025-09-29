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
                                    <th>@lang('Name')</th>
                                    <th>@lang('Total Plan')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($regions as $region)
                                    <tr>
                                        <td>
                                            <div class="user gap-2">
                                                <div class="d-flex align-items-center gap-3 flex-wrap justify-content-end justify-content-md-start">
                                                    <span class="thumb d-none d-lg-block">
                                                        <img src="{{ getImage(getFilePath('regionImage') . '/' . $region->region_image, getFileSize('regionImage')) }}" alt="image">
                                                    </span>
                                                </div>
                                                <span>{{ $region->name }}</span>
                                            </div>
                                        </td>
                                        <td>
                                            <a href="{{ route('admin.plan.all') . '?region=' . $region->slug }}">
                                                <span class="badge badge--primary">{{ $region->plans_count }}</span>
                                            </a>
                                        </td>
                                        <td>@php echo $region->statusBadge @endphp</td>
                                        <td>
                                            <div class="button--group">
                                                <button type="button" class="btn btn-sm btn-outline--primary editRegionBtn" data-region="{{ json_encode($region) }}"><i class="las la-pen"></i>@lang('Edit')</button>

                                                @if ($region->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success confirmationBtn" data-question="@lang('Are you sure to enable this region?')" data-action="{{ route('admin.destination.region.change.status', $region->id) }}" data-status="{{ Status::ENABLE }}" data-id="{{ $region->id }}"><i class="la la-eye"></i>@lang('Enable')</button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger confirmationBtn" data-question="@lang('Are you sure to disable this region?')" data-action="{{ route('admin.destination.region.change.status', $region->id) }}" data-status="{{ Status::DISABLE }}" data-id="{{ $region->id }}"><i class="la la-eye-slash"></i>@lang('Disable')</button>
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
                @if ($regions->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($regions) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="modal fade" id="regionModal" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <form method="POST" enctype="multipart/form-data">
                    @csrf
                    <div class="modal-header">
                        <h5 class="modal-title">@lang('Edit Region')</h5>
                        <button type="button" class="close" data-bs-dismiss="modal">
                            <i class="las la-times"></i>
                        </button>
                    </div>
                    <div class="modal-body">
                        <div class="form-group">
                            <label>@lang('Region Image')</label>
                            <x-image-uploader type="regionImage" name="region_image" class="w-100" :required="false" />
                        </div>
                        <div class="form-group">
                            <label>@lang('Region Name')</label>
                            <input type="text" class="form-control" name="name" disabled>
                        </div>
                        <div class="form-group">
                            <label>@lang('Countries List')</label>
                            <!-- <textarea class="form-control" rows="4" name="countries_list" disabled></textarea> -->
                            <div class="form-control" name="countries_list" style="height: auto; min-height: 100px; white-space: pre-wrap;" disabled>
                            
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="submit" class="btn btn--primary h-45 w-100">@lang('Update')</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection

@push('breadcrumb-plugins')
    <button class="btn btn-outline--primary confirmationBtn" data-question="@lang('Are you sure to fetch regions?')" data-action="{{ route('admin.destination.region.fetch.all') }}"><i class="las la-plus"></i>@lang('Fetch Regions')</button>
    <x-search-form />
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            $('.editRegionBtn').on('click', function() {
                let modal = $('#regionModal');
                let region = $(this).data('region');
                let url =    `{{ route('admin.destination.region.update', ':id') }}`.replace(':id', region.id);

                modal.find('form').attr('action', url);
                modal.find('[name=name]').val(region.name);
                let countries = JSON.parse(region.countries_list_name);
                let readable = countries.join(", ");
                modal.find('[name=countries_list]').text(readable);

                let imagePath = region.region_image ?
                    `{{ asset(getFilePath('regionImage')) }}/` + region.region_image :
                    `{{ getImage(null, getFileSize('regionImage')) }}`;
                $('.image-upload-preview').css('background-image', `url(${imagePath})`);

                modal.modal('show');
            });
        })(jQuery);
    </script>
@endpush
