<div x-data="{ showTooltip: false }" class="relative">
    <!-- Evento -->
    <div @mouseover="showTooltip = true" @mouseleave="showTooltip = false"
         class="p-2 rounded-lg bg-gray-900 text-white text-sm font-bold shadow-md cursor-pointer">
        <span x-html="event.title"></span>
    </div>

    <!-- Tooltip -->
    <div 
        x-show="showTooltip"
        x-transition
        class="absolute left-1/2 top-full mt-2 p-3 z-50 w-72 bg-black text-white border border-gray-500 rounded-lg shadow-lg text-sm transform -translate-x-1/2"
        style="display: none;">
        <p x-html="event.extendedProps.description"></p>
    </div>
</div>
