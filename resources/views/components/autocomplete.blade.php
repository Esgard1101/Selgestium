@php
    $inputId = $uid . '_input';
@endphp

<div
    x-data="{
        query:         @js($valueLabel),
        selectedId:    @js((string) $value),
        selectedLabel: @js($valueLabel),
        results:       [],
        isOpen:        false,
        isLoading:     false,
        focused:       -1,
        _timer:        null,
        minChars:      {{ $minChars }},
        endpoint:      @js($endpoint),

        get hasSelection() { return this.selectedId !== ''; },

        onInput() {
            this.selectedId = '';
            clearTimeout(this._timer);
            if (this.query.length < this.minChars) {
                this.results = [];
                this.isOpen  = false;
                return;
            }
            this._timer = setTimeout(() => this.search(), 280);
        },

        async search() {
            this.isLoading = true;
            try {
                const sep = this.endpoint.includes('?') ? '&' : '?';
                const res = await fetch(this.endpoint + sep + 'q=' + encodeURIComponent(this.query), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!res.ok) throw new Error(res.status);
                this.results = await res.json();
                this.isOpen  = this.results.length > 0;
                this.focused = -1;
            } catch (e) {
                this.results = [];
                this.isOpen  = false;
            } finally {
                this.isLoading = false;
            }
        },

        select(item) {
            this.selectedId    = String(item.id);
            this.selectedLabel = item.label;
            this.query         = '';
            this.isOpen        = false;
            this.results       = [];
            this.focused       = -1;
        },

        clear() {
            this.selectedId    = '';
            this.selectedLabel = '';
            this.query         = '';
            this.focused       = -1;
            this.$nextTick(() => this.$refs.input && this.$refs.input.focus());
        },

        onKeydown(e) {
            if (e.key === 'ArrowDown') {
                e.preventDefault();
                if (!this.isOpen && this.query.length >= this.minChars) this.search();
                this.focused = Math.min(this.focused + 1, this.results.length - 1);
            } else if (e.key === 'ArrowUp') {
                e.preventDefault();
                this.focused = Math.max(this.focused - 1, 0);
            } else if (e.key === 'Enter') {
                e.preventDefault();
                if (this.focused >= 0 && this.focused < this.results.length) {
                    this.select(this.results[this.focused]);
                } else if (this.isOpen && this.results.length === 1) {
                    this.select(this.results[0]);
                }
            } else if (e.key === 'Escape') {
                this.isOpen  = false;
                this.focused = -1;
            }
        },
    }"
    class="x-autocomplete"
    @click.outside="isOpen = false; focused = -1"
>
    {{-- Hidden input — valor real que se envía en el form --}}
    <input type="hidden" name="{{ $name }}" :value="selectedId">

    {{-- Label --}}
    <label class="block text-xs font-semibold mb-1" style="color: var(--color-text-secondary);">
        {{ $label }}
        @if ($required)
            <span style="color: var(--color-danger);">*</span>
        @endif
    </label>

    {{-- Chip de selección confirmada --}}
    <div x-show="hasSelection" x-cloak class="x-autocomplete__chip">
        <span class="x-autocomplete__chip-label" x-text="selectedLabel"></span>
        <button
            type="button"
            class="x-autocomplete__chip-remove"
            @click="clear()"
            tabindex="-1"
            aria-label="Limpiar selección"
        >
            <i class="fas fa-xmark"></i>
        </button>
    </div>

    {{-- Input de búsqueda (visible cuando no hay selección) --}}
    <div x-show="!hasSelection" class="x-autocomplete__field" style="position: relative;">
        <input
            x-ref="input"
            id="{{ $inputId }}"
            type="text"
            x-model="query"
            @input="onInput()"
            @keydown="onKeydown($event)"
            @focus="if (results.length > 0) isOpen = true"
            placeholder="{{ $placeholder ?: 'Buscar...' }}"
            autocomplete="off"
            class="x-autocomplete__input"
            @if($required) required @endif
            aria-autocomplete="list"
            :aria-expanded="isOpen"
        >

        {{-- Spinner de carga --}}
        <span x-show="isLoading" x-cloak class="x-autocomplete__spinner" aria-hidden="true">
            <i class="fas fa-circle-notch fa-spin"></i>
        </span>
    </div>

    {{-- Dropdown de resultados --}}
    <div
        x-show="isOpen"
        x-cloak
        class="x-autocomplete__dropdown"
        role="listbox"
    >
        <template x-for="(item, index) in results" :key="item.id">
            <div
                class="x-autocomplete__option"
                :class="{ 'x-autocomplete__option--focused': focused === index }"
                @click="select(item)"
                @mouseenter="focused = index"
                role="option"
                :aria-selected="focused === index"
            >
                <span class="x-autocomplete__option-label" x-text="item.label"></span>
                <span
                    x-show="item.sublabel"
                    class="x-autocomplete__option-sublabel"
                    x-text="item.sublabel"
                ></span>
            </div>
        </template>
    </div>
</div>
