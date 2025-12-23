@props(['title' => null])

<div class="bg-white rounded-lg shadow {{ $attributes->get('class') }}">
    @if($title)
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">{{ $title }}</h3>
        </div>
    @endif
    <div class="px-6 py-4">
        {{ $slot }}
    </div>
</div>
