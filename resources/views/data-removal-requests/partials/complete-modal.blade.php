<!-- Complete Modal -->
<div id="completeModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
    <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
        <div class="mt-3">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-green-100 rounded-full">
                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="mt-3 text-center">
                <h3 class="text-lg font-medium text-gray-900">Complete Data Removal Request</h3>
                <div class="mt-2 px-7 py-3">
                    <p class="text-sm text-gray-500">
                        Mark this data removal request as completed. Please provide details about the removal method used.
                    </p>
                </div>
            </div>
            <form method="POST" action="{{ route('data-removal-requests.complete', $dataRemovalRequest) }}" class="mt-4">
                @csrf
                <div class="mb-4">
                    <label for="data_removal_method" class="block text-sm font-medium text-gray-700">Removal Method *</label>
                    <select name="data_removal_method" id="data_removal_method" required
                            class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Select Removal Method</option>
                        <option value="permanent_deletion">Permanent Deletion</option>
                        <option value="anonymization">Anonymization</option>
                        <option value="pseudonymization">Pseudonymization</option>
                        <option value="archival_deletion">Archival Deletion</option>
                        <option value="database_cleanup">Database Cleanup</option>
                        <option value="third_party_notification">Third Party Notification</option>
                        <option value="other">Other</option>
                    </select>
                </div>
                <div class="mb-4">
                    <label for="completion_notes" class="block text-sm font-medium text-gray-700">Completion Notes</label>
                    <textarea name="completion_notes" id="completion_notes" rows="3"
                              class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-blue-500 focus:border-blue-500"
                              placeholder="Add any notes about the completion process..."></textarea>
                </div>
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeCompleteModal()"
                            class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded">
                        Cancel
                    </button>
                    <button type="submit"
                            class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded">
                        Complete
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
function openCompleteModal() {
    document.getElementById('completeModal').classList.remove('hidden');
}

function closeCompleteModal() {
    document.getElementById('completeModal').classList.add('hidden');
}
</script>
