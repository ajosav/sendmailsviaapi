<tr>
<td class="header">
{{-- <a href="{{ $url }}" style="display: inline-block;"> --}}
@if (trim($slot) === 'Renmoney')
<img src="{{config('app.url')}}/renmoney_logo.svg" class="logo" alt="Renmoney">
@else
{{ $slot }}
@endif
{{-- </a> --}}
</td>
</tr>
