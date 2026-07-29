<x-layouts.dashboard title="Novo gradilište">
    <div class="max-w-3xl mx-auto space-y-6">
        <div>
            <a href="{{ route('gradilista.index') }}" class="text-sm text-gray-500 hover:text-gray-800">← Nazad</a>
            <h2 class="font-['Plus_Jakarta_Sans',sans-serif] text-2xl font-bold text-gray-900 mt-2">Novo gradilište</h2>
            <p class="text-sm text-gray-500 mt-1">Broj građevinske dozvole je obavezan — automatski se dodaje na svaki DKO obrazac.</p>
        </div>
        @include('gradilista._form')
    </div>
</x-layouts.dashboard>
