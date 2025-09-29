@extends($activeTemplate . 'layouts.master')
@section('content')
    <div class="d-flex justify-content-between align-items-center flex-wrap gap-2 mb-3">
        <h5 class="mb-0">{{ __($pageTitle) }}</h5>
        <a href="{{ route('ticket.index') }}" class="btn btn--sm btn--base"><i class="fas fa-list"></i> @lang('My Support Ticket')</a>
    </div>
    <div class="card custom--card">
        <div class="card-body">
            <form action="{{ route('ticket.store') }}" class="disableSubmission" method="post" enctype="multipart/form-data">
                @csrf
                <div class="row gy-3">
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Subject')</label>
                        <input type="text" name="subject" value="{{ old('subject') }}" class="form-control form--control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form--label required">@lang('Priority')</label>
                        <select name="priority" class="form-select form--select select2" data-minimum-results-for-search="-1" required>
                            <option value="3">@lang('High')</option>
                            <option value="2">@lang('Medium')</option>
                            <option value="1">@lang('Low')</option>
                        </select>
                    </div>
                    <div class="col-12">
                        <label class="form--label required">@lang('Message')</label>
                        <textarea name="message" id="inputMessage" rows="6" class="form-control form--control" required>{{ old('message') }}</textarea>
                    </div>

                    <div class="col-md-9">
                        <button type="button" class="btn btn-dark btn--sm addAttachment my-2"> <i class="fas fa-plus"></i> @lang('Add Attachment') </button>
                        <p class="mb-2"><span class="fs-14 text-muted">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                        <div class="row gy-3 fileUploadsContainer">
                        </div>
                    </div>
                    <div class="col-md-3">
                        <button class="btn btn--base btn--sm w-100 my-2" type="submit"><i class="las la-paper-plane"></i> @lang('Submit')
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@push('style-lib')
    <link rel="stylesheet" href="{{ asset('assets/global/css/select2.min.css') }}">
@endpush

@push('script-lib')
    <script src="{{ asset('assets/global/js/select2.min.js') }}"></script>
@endpush

@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .removeFile {
            color: hsl(var(--white));
        }
    </style>
@endpush

@push('script')
    <script>
        (function($) {
            "use strict";
            var fileAdded = 0;
            $('.addAttachment').on('click', function() {
                fileAdded++;
                if (fileAdded == 5) {
                    $(this).attr('disabled', true)
                }
                $(".fileUploadsContainer").append(`
                    <div class="col-lg-6 col-md-12 removeFileInput">
                        <div class="form-group">
                            <div class="input-group">
                                <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                                <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
                            </div>
                        </div>
                    </div>
                `)
            });
            $(document).on('click', '.removeFile', function() {
                $('.addAttachment').removeAttr('disabled', true)
                fileAdded--;
                $(this).closest('.removeFileInput').remove();
            });
        })(jQuery);
    </script>
@endpush
