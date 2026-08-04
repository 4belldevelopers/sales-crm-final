{{--
  Step 8b: SVG sprite icon component — replaces <i class="ki ki-duotone ki-{name}"> / ki-outline / ki-bold.
  Usage:  <x-icon name="search" />
          <x-icon name="user" class="w-4 h-4 text-blue-500" />

  Requires public/icons.svg to be present (see deployment note below).
  Falls back silently to nothing when the sprite file has not been deployed yet;
  Keenicons CSS class is preserved so the existing icon font still renders.

  Deployment: generate the sprite from the Keenicons npm package:
    npm install --save-dev @keenthemes/keenicons
    node scripts/build-icon-sprite.js   → public/icons.svg
  See scripts/build-icon-sprite.js for the extraction script.
--}}
@props(['name', 'class' => ''])
@if(file_exists(public_path('icons.svg')))
<svg class="ki-icon{{ $class ? ' '.$class : '' }}" aria-hidden="true" focusable="false">
    <use href="{{ asset('icons.svg') }}#ki-{{ $name }}"></use>
</svg>
@else
{{-- Fallback: render the standard Keenicons class so existing CSS icon font still shows --}}
<i class="ki ki-duotone ki-{{ $name }}{{ $class ? ' '.$class : '' }}" aria-hidden="true"></i>
@endif
