@props(['title' => null, 'breadcrumbs' => []])

<div {{ $attributes->merge(['class' => 'simf-toolbar-card d-flex align-items-center justify-content-between flex-wrap gap-3 mb-4 py-3 px-4 rounded-3 bg-body shadow-sm']) }}>
    <div class="d-flex align-items-center flex-wrap gap-2">
        @if($title)
            <h1 class="d-flex align-items-center text-gray-900 fw-bold my-0" style="font-size: 16px; font-family: 'Poppins', sans-serif;">
                {{ $title }}
            </h1>
        @endif

        @if(!empty($breadcrumbs))
            <span class="h-20px border-gray-300 border-start mx-2"></span>
            <ul class="breadcrumb breadcrumb-separatorless fw-semibold fs-7 my-0 flex-wrap">
                @foreach($breadcrumbs as $label => $link)
                    @if(!$loop->first)
                        <li class="breadcrumb-item text-muted mx-1">/</li>
                    @endif
                    <li class="breadcrumb-item text-muted">
                        @if($link && !$loop->last)
                            <a href="{{ $link }}" class="text-muted text-hover-primary">{{ $label }}</a>
                        @else
                            <span class="text-gray-800">{{ $label }}</span>
                        @endif
                    </li>
                @endforeach
            </ul>
        @endif
    </div>

    @if($slot->isNotEmpty())
        <div class="d-flex align-items-center gap-2">
            {{ $slot }}
        </div>
    @endif
</div>
