@props(['url'])
<tr>
<td class="header">
<a href="{{ $url }}" style="display: inline-block; text-decoration: none;">
<img src="{{ asset('logo.svg') }}" class="logo" alt="{{ config('app.name') }} Logo" style="display: inline-block; vertical-align: middle;">
<span style="display: inline-block; vertical-align: middle; color: #1565C0; font-size: 22px; font-weight: bold; margin-left: 8px;">{{ config('app.name') }}</span>
</a>
</td>
</tr>
