<tr>
<td class="header">
{{-- <a href="{{ $url }}" style="display: inline-block;"> --}}
@if (trim($slot) === '')
<img src="{{URL::asset('renmoney_logo.svg')}}" class="logo" alt="Renmoney" data-auto-embed="attachment">
@else
{{ $slot }}
@endif
{{-- </a> --}}
</td>
</tr>
