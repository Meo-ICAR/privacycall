<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Nuova Violazione Dati') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form method="POST" action="{{ route('data-breaches.store') }}" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="breach_type" class="block text-sm font-medium text-gray-700">Tipo Violazione</label>
                                <select id="breach_type" name="breach_type" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleziona tipo</option>
                                    <option value="unauthorized_access">Accesso non autorizzato</option>
                                    <option value="data_loss">Perdita di dati</option>
                                    <option value="system_breach">Violazione sistema</option>
                                    <option value="human_error">Errore umano</option>
                                    <option value="malware">Malware</option>
                                    <option value="phishing">Phishing</option>
                                    <option value="other">Altro</option>
                                </select>
                            </div>

                            <div>
                                <label for="breach_date" class="block text-sm font-medium text-gray-700">Data Violazione</label>
                                <input type="date" id="breach_date" name="breach_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="discovery_date" class="block text-sm font-medium text-gray-700">Data Scoperta</label>
                                <input type="date" id="discovery_date" name="discovery_date" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="risk_level" class="block text-sm font-medium text-gray-700">Livello Rischio</label>
                                <select id="risk_level" name="risk_level" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">Seleziona livello</option>
                                    <option value="low">Basso</option>
                                    <option value="medium">Medio</option>
                                    <option value="high">Alto</option>
                                    <option value="critical">Critico</option>
                                </select>
                            </div>

                            <div>
                                <label for="number_of_affected_individuals" class="block text-sm font-medium text-gray-700">Numero Persone Coinvolte</label>
                                <input type="number" id="number_of_affected_individuals" name="number_of_affected_individuals" min="1" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>

                            <div>
                                <label for="notification_required" class="block text-sm font-medium text-gray-700">Notifica Richiesta</label>
                                <select id="notification_required" name="notification_required" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="1">Sì</option>
                                    <option value="0">No</option>
                                </select>
                            </div>
                        </div>

                        <div>
                            <label for="breach_description" class="block text-sm font-medium text-gray-700">Descrizione Violazione</label>
                            <textarea id="breach_description" name="breach_description" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div>
                            <label for="root_cause" class="block text-sm font-medium text-gray-700">Causa Principale</label>
                            <textarea id="root_cause" name="root_cause" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div>
                            <label for="immediate_actions_taken" class="block text-sm font-medium text-gray-700">Azioni Immediate Intraprese</label>
                            <textarea id="immediate_actions_taken" name="immediate_actions_taken" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500"></textarea>
                        </div>

                        <div class="flex justify-end space-x-3">
                            <a href="{{ route('data-breaches.index') }}" class="inline-flex items-center px-4 py-2 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 disabled:opacity-25 transition ease-in-out duration-150">
                                Annulla
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:bg-indigo-700 active:bg-indigo-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Salva Violazione
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
