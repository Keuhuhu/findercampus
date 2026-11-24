@props(['name', 'id' => null, 'options' => [], 'selected' => '', 'placeholder' => 'Pilih opsi...', 'required' => false])

@php($selectId = $id ?? $name . '-select')

<div x-data="{
        open: false,
        value: @js((string) $selected),
        label: @js($placeholder),
        activeIndex: 0,
        init() {
            const choices = this.$el.querySelectorAll('[role=option]');
            const selectedIndex = Array.from(choices).findIndex((choice) => choice.dataset.value === this.value);
            this.activeIndex = selectedIndex >= 0 ? selectedIndex : 0;
            if (selectedIndex >= 0) this.label = choices[selectedIndex].dataset.label;
        },
        openOptions(index = this.activeIndex) {
            this.open = true;
            this.$nextTick(() => this.focusOption(index));
        },
        focusOption(index) {
            const choices = this.$el.querySelectorAll('[role=option]');
            if (!choices.length) return;

            this.activeIndex = (index + choices.length) % choices.length;
            choices[this.activeIndex].focus();
        },
        moveOption(direction) {
            this.focusOption(this.activeIndex + direction);
        },
        selectOption(val, text) {
            this.value = val;
            this.label = text;
            const choices = this.$el.querySelectorAll('[role=option]');
            const selectedIndex = Array.from(choices).findIndex((choice) => choice.dataset.value === val);
            if (selectedIndex >= 0) this.activeIndex = selectedIndex;
            this.open = false;
            this.$refs.trigger.focus();
        },
        closeOptions() {
            this.open = false;
            this.$refs.trigger.focus();
        }
    }"
    @click.outside="open = false"
    class="relative w-full"
>
    <!-- Hidden input for form submission -->
    <input type="hidden" name="{{ $name }}" x-model="value" {{ $required ? 'required' : '' }}>

    <!-- Trigger Button -->
    <button
        id="{{ $selectId }}"
        x-ref="trigger"
        type="button"
        @click="open = !open"
        @keydown.arrow-down.prevent="openOptions()"
        @keydown.arrow-up.prevent="openOptions()"
        @keydown.escape.stop="closeOptions()"
        aria-haspopup="listbox"
        aria-controls="{{ $selectId }}-listbox"
        :aria-expanded="open"
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
        id="{{ $selectId }}-listbox"
        role="listbox"
        aria-labelledby="{{ $selectId }}"
        @keydown.escape.prevent.stop="closeOptions()"
        @keydown.arrow-down.prevent="moveOption(1)"
        @keydown.arrow-up.prevent="moveOption(-1)"
        @keydown.home.prevent="focusOption(0)"
        @keydown.end.prevent="focusOption($el.querySelectorAll('[role=option]').length - 1)"
        class="absolute z-50 mt-2 w-full bg-white border border-gray-100 rounded-xl shadow-xl max-h-60 overflow-auto py-1"
        style="display: none;"
    >
        <!-- Placeholder Option -->
        <div
            role="option"
            tabindex="-1"
            data-value=""
            data-label="{{ $placeholder }}"
            :aria-selected="value === ''"
            @click="selectOption($el.dataset.value, $el.dataset.label)"
            @keydown.enter.prevent="selectOption($el.dataset.value, $el.dataset.label)"
            @keydown.space.prevent="selectOption($el.dataset.value, $el.dataset.label)"
            class="px-4 py-2.5 text-sm cursor-pointer transition-colors duration-150 focus:outline-none focus:bg-indigo-50"
            :class="value === '' ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-500 hover:bg-gray-50 hover:text-gray-900'"
        >
            {{ $placeholder }}
        </div>

        <!-- Dynamic Options -->
        @foreach($options as $val => $text)
            <div
                role="option"
                tabindex="-1"
                data-value="{{ $val }}"
                data-label="{{ $text }}"
                @click="selectOption($el.dataset.value, $el.dataset.label)"
                @keydown.enter.prevent="selectOption($el.dataset.value, $el.dataset.label)"
                @keydown.space.prevent="selectOption($el.dataset.value, $el.dataset.label)"
                class="px-4 py-2.5 text-sm cursor-pointer transition-colors duration-150 focus:outline-none focus:bg-indigo-50"
                :class="value === $el.dataset.value ? 'bg-indigo-50 text-indigo-700 font-bold' : 'text-gray-700 hover:bg-gray-50 hover:text-indigo-600'"
                :aria-selected="value === $el.dataset.value"
            >
                {{ $text }}
            </div>
        @endforeach
    </div>
</div>
