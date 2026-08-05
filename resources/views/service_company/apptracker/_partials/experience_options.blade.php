{{--
    experience_options.blade.php
    Year options 1-30 for experience <select> dropdowns. Put the "Freshers"
    option (value=0) in the parent template before this @include.
    Pass $selectedValue to pre-select the matching option; omit for no preselection.
--}}
@php $sel = $selectedValue ?? null; @endphp
@for ($i = 1; $i <= 30; $i++)
    <option value="{{ $i }}" {{ $sel !== null && $i == $sel ? 'selected' : '' }}>{{ $i }} Year{{ $i >= 1 ? 's' : '' }}</option>
@endfor
