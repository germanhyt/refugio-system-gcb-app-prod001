<x-filament-panels::page>
    <form wire:submit="save" class="space-y-6">
        {{ $this->form }}

        <div class="flex flex-wrap justify-end gap-3">
            <x-filament::button
                color="gray"
                icon="heroicon-o-paper-airplane"
                type="button"
                wire:click="mountAction('sendTestMail')"
            >
                Enviar correo de prueba
            </x-filament::button>
            <x-filament::button type="submit">
                Guardar
            </x-filament::button>
        </div>
    </form>

    @if (filled($mailPreviewHtml))
        <div class="mt-6 overflow-hidden rounded-xl border border-gray-200 bg-white dark:border-gray-700 dark:bg-gray-900">
            <div class="border-b border-gray-200 px-4 py-3 text-sm text-gray-600 dark:border-gray-700 dark:text-gray-300">
                Vista previa del correo simulado para <strong>{{ $mailPreviewTo }}</strong>
            </div>
            <iframe
                title="Vista previa del correo"
                class="h-[28rem] w-full bg-white"
                srcdoc="{{ $mailPreviewHtml }}"
            ></iframe>
        </div>
    @endif
</x-filament-panels::page>
