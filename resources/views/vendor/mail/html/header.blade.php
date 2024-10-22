@props(['url'])
<tr>
    <td class="header">
        <a href="{{ $url }}" style="display: inline-block;">
            @if (trim($slot) === 'Laravel')
            <img src="https://laravel.com/img/notification-logo.png" class="logo" alt="Laravel Logo">
            @else
            <img src="{{ asset('logo.png') }}" alt="{{ config('app.name') }}" style="height: 40px; width: 40px">
            @endif
        </a>
    </td>
</tr>