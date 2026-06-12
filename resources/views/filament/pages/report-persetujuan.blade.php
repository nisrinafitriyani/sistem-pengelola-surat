<x-filament-panels::page>
    <div class="space-y-6">
        {{-- Filter Section --}}
        <div class="fi-section rounded-xl bg-white shadow-sm ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 p-6">
            <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-4 flex items-center gap-2">
                <x-heroicon-o-funnel class="w-5 h-5 text-primary-500" />
                Filter Laporan
            </h3>
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="date_from" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Dari</label>
                    <input type="date" id="date_from" wire:model.live.debounce.300ms="date_from"
                        class="block w-full rounded-lg border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label for="date_to" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Tanggal Sampai</label>
                    <input type="date" id="date_to" wire:model.live.debounce.300ms="date_to"
                        class="block w-full rounded-lg border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white sm:text-sm">
                </div>
                <div>
                    <label for="type_filter" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Filter Tipe</label>
                    <select id="type_filter" wire:model.live="type_filter"
                        class="block w-full rounded-lg border-gray-300 shadow-sm transition duration-150 ease-in-out focus:border-primary-500 focus:ring-1 focus:ring-primary-500 dark:bg-gray-800 dark:border-gray-600 dark:text-white sm:text-sm">
                        <option value="">Semua Tipe</option>
                        <option value="po">PO — Purchase Order</option>
                        <option value="wo">WO — Work Order</option>
                    </select>
                </div>
            </div>
        </div>

        {{-- Table --}}
        {{ $this->table }}
    </div>
</x-filament-panels::page>
