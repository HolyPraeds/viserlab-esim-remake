@extends('admin.layouts.app')
@section('panel')
    <div class="row">
        <div class="col-lg-12">
            <div class="card">
                <div class="card-body p-0">
                    <div class="table-responsive--md  table-responsive">
                        <table class="table table--light style--two">
                            <thead>
                                <tr>
                                    <th>@lang('Name')</th>
                                    <th>@lang('Status')</th>
                                    <th>@lang('Action')</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($apis as $api)
                                    <tr>
                                        <td>
                                            <div>

                                                <span class="name">{{ $api->name }}</span>
                                            </div>
                                        </td>

                                        <td>
                                            @php echo $api->statusBadge @endphp
                                        </td>

                                        <td>
                                            <div class="button--group">
                                                <a href="{{ route('admin.api.edit', $api->alias) }}" class="btn btn-sm btn-outline--info ms-1"><i class="las la-pen"></i>@lang('Edit')</a>
                                                @if ($api->status == Status::DISABLE)
                                                    <button class="btn btn-sm btn-outline--success ms-1 confirmationBtn" data-question="@lang('Are you sure to enable this api?')" data-action="{{ route('admin.api.status', $api->id) }}">
                                                        <i class="la la-eye"></i>@lang('Enable')
                                                    </button>
                                                @else
                                                    <button class="btn btn-sm btn-outline--danger ms-1 confirmationBtn" data-question="@lang('Are you sure to disable this api?')" data-action="{{ route('admin.api.status', $api->id) }}">
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
                        </table><!-- table end -->
                    </div>
                </div>
                @if ($apis->hasPages())
                    <div class="card-footer py-4">
                        {{ paginateLinks($apis) }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <x-confirmation-modal />
@endsection
