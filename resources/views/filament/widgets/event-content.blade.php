<div x-data="{ showTooltip: false }" class="relative">
    <div @mouseover="showTooltip = true" @mouseleave="showTooltip = false">
        <span x-text="event.title"></span>
    </div>
    <div x-show="showTooltip" class="absolute z-10 p-2 bg-gray-200 border border-gray-400 rounded shadow-lg" style="display: none;">
        <p x-text="event.extendedProps.description"></p>
        <a :href="event.extendedProps.url" target="_blank" class="text-blue-500 underline">Ver Detalhes</a>
    </div>
</div>
