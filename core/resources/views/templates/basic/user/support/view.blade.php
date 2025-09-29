@extends($activeTemplate . 'layouts.' . $layout)
@section('content')
    @guest
        <section class="my-120">
            <div class="container">
            @endguest
            <div class="card custom--card">
                <div class="card-header card-header-bg d-flex flex-wrap justify-content-between align-items-center">
                    <h5 class="text--base mb-0">
                        @php echo $myTicket->statusBadge; @endphp
                        [@lang('Ticket')#{{ $myTicket->ticket }}] {{ $myTicket->subject }}
                    </h5>
                    @if ($myTicket->status != Status::TICKET_CLOSE && $myTicket->user)
                        <button class="btn btn--danger btn--xsm close-button btn--sm confirmationBtn" type="button" data-question="@lang('Are you sure to close this ticket?')" data-action="{{ route('ticket.close', $myTicket->id) }}"><i class="fas fa-lg fa-times-circle"></i>
                        </button>
                    @endif
                </div>
                <div class="card-body">
                    <form method="post" class="disableSubmission" action="{{ route('ticket.reply', $myTicket->id) }}" enctype="multipart/form-data">
                        @csrf
                        <div class="row gy-3 justify-content-between">
                            <div class="col-md-12">
                                <textarea name="message" class="form-control form--control" rows="4" required>{{ old('message') }}</textarea>
                            </div>

                            <div class="col-md-9">
                                <button type="button" class="btn btn-dark btn--sm addAttachment my-2"> <i class="fas fa-plus"></i> @lang('Add Attachment') </button>
                                <p class="mb-2"><span class="fs-14 text-muted">@lang('Max 5 files can be uploaded | Maximum upload size is ' . convertToReadableSize(ini_get('upload_max_filesize')) . ' | Allowed File Extensions: .jpg, .jpeg, .png, .pdf, .doc, .docx')</span></p>
                                <div class="row fileUploadsContainer gy-3">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <button class="btn btn--base btn--sm w-100 my-2" type="submit"><i class="la la-fw la-lg la-reply"></i> @lang('Reply')
                                </button>
                            </div>

                        </div>
                    </form>
                </div>
            </div>

            <div class="card custom--card mt-4">
                <div class="card-body">
                    @forelse($messages as $message)
                        @if ($message->admin_id == 0)
                            <div class="row border border-primary my-3 py-3 mx-1 reply-box">
                                <div class="col-md-3 border-end text-end">
                                    <h5 class="my-3">{{ $message->ticket->fullname }}</h5>
                                </div>
                                <div class="col-md-9">
                                    <p class="text-muted fw-bold my-3">
                                        @lang('Posted on') {{ showDateTime($message->created_at, 'l, dS F Y @ h:i a') }}</p>
                                    <p>{{ $message->message }}</p>
                                    @if ($message->attachments->count() > 0)
                                        <div class="mt-2">
                                            @foreach ($message->attachments as $k => $image)
                                                <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3"><i class="fa-regular fa-file"></i> @lang('Attachment') {{ ++$k }} </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @else
                            <div class="row border border-warning my-3 py-3 mx-1 reply-bg reply-box">
                                <div class="col-md-3 border-end text-end">
                                    <h5 class="my-3">{{ $message->admin->name }}</h5>
                                    <p class="lead text-muted">@lang('Staff')</p>
                                </div>
                                <div class="col-md-9">
                                    <p class="text-muted fw-bold my-3">
                                        @lang('Posted on') {{ showDateTime($message->created_at, 'l, dS F Y @ h:i a') }}</p>
                                    <p>{{ $message->message }}</p>
                                    @if ($message->attachments->count() > 0)
                                        <div class="mt-2">
                                            @foreach ($message->attachments as $k => $image)
                                                <a href="{{ route('ticket.download', encrypt($image->id)) }}" class="me-3"><i class="fa-regular fa-file"></i> @lang('Attachment') {{ ++$k }} </a>
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @endif
                    @empty
                        <div class="empty-message text-center">
                            <img src="{{ asset('assets/images/empty_list.png') }}" alt="empty">
                            <h5 class="text-muted">@lang('No replies found here!')</h5>
                        </div>
                    @endforelse
                </div>

            </div>
            @guest
            </div>
        </section>
    @endguest
    <x-confirmation-modal />
@endsection
@push('style')
    <style>
        .input-group-text:focus {
            box-shadow: none !important;
        }

        .reply-bg {
            background-color: #faf8f1;
        }

        .empty-message img {
            width: 120px;
            margin-bottom: 15px;
        }

        .removeFile {
            color: hsl(var(--white));
        }

        .reply-box {
            border-radius: 8px;
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
                        <div class="input-group">
                            <input type="file" name="attachments[]" class="form-control form--control" accept=".jpeg,.jpg,.png,.pdf,.doc,.docx" required>
                            <button type="button" class="input-group-text removeFile bg--danger border--danger"><i class="fas fa-times"></i></button>
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
