<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Third Country') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">
                    <form action="{{ route('third-countries.update', $thirdCountry) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="country_name" class="block text-sm font-medium text-gray-700">Country Name</label>
                                <input type="text" name="country_name" id="country_name" value="{{ old('country_name', $thirdCountry->country_name) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div>
                                <label for="country_code" class="block text-sm font-medium text-gray-700">Country Code</label>
                                <input type="text" name="country_code" id="country_code" value="{{ old('country_code', $thirdCountry->country_code) }}" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                            </div>
                            <div class="md:col-span-2">
                                <label for="risk_level" class="block text-sm font-medium text-gray-700">Risk Level</label>
                                <select name="risk_level" id="risk_level" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                                    <option value="low" @selected(old('risk_level', $thirdCountry->risk_level) == 'low')>Low</option>
                                    <option value="medium" @selected(old('risk_level', $thirdCountry->risk_level) == 'medium')>Medium</option>
                                    <option value="high" @selected(old('risk_level', $thirdCountry->risk_level) == 'high')>High</option>
                                    <option value="very_high" @selected(old('risk_level', $thirdCountry->risk_level) == 'very_high')>Very High</option>
                                </select>
                            </div>
                            <div class="flex items-start">
                                <div class="flex items-center h-5">
                                    <input id="adequacy_decision" name="adequacy_decision" type="checkbox" value="1" @checked(old('adequacy_decision', $thirdCountry->adequacy_decision)) class="focus:ring-indigo-500 size-4 text-indigo-600 border-gray-300 rounded">
                                </div>
                                <div class="ml-3 text-sm">
                                    <label for="adequacy_decision" class="font-medium text-gray-700">Adequacy Decision</label>
                                    <p class="text-gray-500">Does this country have an adequacy decision from the EU?</p>
                                </div>
                            </div>
                        </div>
                        <div class="flex justify-end mt-6">
                            <a href="{{ route('third-countries.index') }}" class="mr-4 inline-flex items-center px-4 py-2 bg-white border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest shadow-sm hover:text-gray-500 focus:outline-none focus:border-blue-300 focus:ring focus:ring-blue-200 active:text-gray-800 active:bg-gray-50 disabled:opacity-25 transition">
                                Cancel
                            </a>
                            <button type="submit" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring ring-gray-300 disabled:opacity-25 transition ease-in-out duration-150">
                                Update
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
