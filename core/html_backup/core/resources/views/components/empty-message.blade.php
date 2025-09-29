@props(['message' => $emptyMessage])
<div class="empty-message">
    <p class="empty-message-icon">
        <img src="{{ asset($activeTemplateTrue . 'images/no-data.gif') }}" alt="">
    </p>
    <p class="empty-message-text">
        {{ __($message) }}
    </p>
</div>
