@props(['name', 'options' => [], 'selected' => '', 'placeholder' => 'Pilih opsi...', 'required' => false])

<div x-data="{
        open: false,
        value: '{{ $selected }}',
        label: '{{ $placeholder }}',
        init() {
            // Set initial label based on selected value
            @if((string)$selected !== '')
                @if(isset($options[$selected]))
                    this.label = {{ json_encode($options[$selected]) }};
                @endif
            @endif
        },
        selectOption(val, text) {
            this.value = val;
            this.label = text;
            this.open = false;
        }
    }"
    @click.outside="open = false"
    class="relative w-full"
>
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="value" {{ $required ? 'required' : '' }}>

    <!-- Trigger Button -->
    <button
        type="button"
        @click="open = !open"
        class="w-full text-left pl-4 pr-10 py-3 bg-gray-50 border border-gray-200 rounded-xl text-sm focus:bg-white focus:outline-none focus:ring-4 focus:ring-indigo-500/20 focus:border-indigo-500 transition-all duration-300 shadow-sm cursor-pointer hover:border-indigo-300 flex items-center justify-between"
        :class="open ? 'ring-4 ring-indigo-500/20 border-indigo-500 bg-white' : ''"
    >
        <span x-text="label" class="block truncate" :class="value !== '' ? 'text-gray-900 font-medium' : 'text-gray-500'"></span>
        <span class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-4 text-gray-500">
            <svg class="w-4 h-4 transition-transform duration-300" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
        </span>
    </button>

    <!-- Dropdown Menu -->
    <div
        x-show="open"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95 -translate-y-2"
        x-transition:enter-end="opacity-100 scale-100 translate-y-0"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100 scale-100 translate-y-0"
        x-transition:leave-end="opacity-0 scale-95 -translate-y-2"
        class="absolute z-50 mt-2 w-full bg-white border border-gray-100 rounded-xl shadow-xl max-h-60 overflow-auto py-1"
        style="display: none;"
    >
        <!-- Placeholder Option -->
        <div
            @click="selectOption('', {{ json_encode($placeholder) }})"
            class="px-4 py-2.5 text-sm cursor-pointer transition-colors duration-150"
            :class="value === '' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
        >
            {{ $placeholder }}
        </div>

        <!-- Dynamic Options -->
        @foreach($options as $val => $text)
            <div
                @click="selectOption('{{ $val }}', {{ json_encode($text) }})"
                class="px-4 py-2.5 text-sm cursor-pointer transition-colors duration-150"
                :class="value === '{{ $val }}' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'"
            >
                {{ $text }}
            </div>
        @endforeach
    </div>
</div>
