@props(['title' => null, 'subtitle' => null, 'headerActions' => null])

<div {{ $attributes->merge(['class' => 'section-card card shadow-sm rounded-3 border-0 bg-body mb-5']) }}>
    @if($title || $headerActions)
        <div class="card-header border-0 pt-5 pb-3 d-flex align-items-center justify-content-between">
            <div>
                @if($title)
                    <h3 class="card-title fw-bold text-gray-900 m-0" style="font-size: 16px; font-family: 'Poppins', sans-serif;">
                        {{ $title }}
                    </h3>
                @endif
                @if($subtitle)
                    <span class="text-muted fs-7 fw-normal d-block mt-1">{{ $subtitle }}</span>
                @endif
            </div>
            @if($headerActions)
                <div class="card-toolbar m-0">
                    {{ $headerActions }}
                </div>
            @endif
        </div>
    @endif
    <div class="card-body pt-0">
        {{ $slot }}
    </div>
</div>
