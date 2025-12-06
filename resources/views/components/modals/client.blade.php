<!-- Client Modal -->
<div id="clientModal" class="modal">
    <div class="bg-white dark:bg-gray-800 rounded-xl shadow-xl w-full max-w-2xl mx-4 fixed-height-450 scrollable">
        <div class="p-6 border-b border-gray-200 dark:border-gray-700">
            <div class="flex items-center justify-between">
                <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Add New Client</h2>
                <button onclick="closeClientModal()" class="text-gray-400 hover:text-gray-600 dark:hover:text-gray-300">
                    <i class="fas fa-times text-xl"></i>
                </button>
            </div>
        </div>

            <!-- Scrollable Form Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600">
                <form id="clientForm" method="POST" class="space-y-6">
                    @csrf
                    <input type="hidden" id="clientId" name="client_id">

                    <!-- Basic Information -->
                    <section>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="clientName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                                <input type="text" id="clientName" name="name" required 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter full name">
                            </div>
                            <div>
                                <label for="clientEmail" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Email Address</label>
                                <input type="email" id="clientEmail" name="email" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter email address">
                            </div>
                        </div>
                    </section>

                    <!-- Contact Information -->
                    <section>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Contact Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="clientContact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                                <input type="text" id="clientContact" name="contact_number" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="e.g., 0300-1234567">
                            </div>
                            <div>
                                <label for="clientCnic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CNIC</label>
                                <input type="text" id="clientCnic" name="cnic" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="xxxxx-xxxxxxx-x">
                            </div>
                        </div>
                    </section>

                    <!-- Client Type & Details -->
                    <section>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Client Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="clientType" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Client Type <span class="text-red-500">*</span></label>
                                <select id="clientType" name="type" required 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Type</option>
                                    <option value="individual">Individual</option>
                                    <option value="corporate">Corporate</option>
                                </select>
                            </div>
                            <div>
                                <label for="clientGender" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Gender</label>
                                <select id="clientGender" name="gender" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                        </div>

                        <!-- Corporate Fields -->
                        <div id="corporateFields" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="companyName" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Company Name</label>
                                <input type="text" id="companyName" name="company_name" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter company name">
                            </div>
                            <div>
                                <label for="designation" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Designation</label>
                                <input type="text" id="designation" name="designation" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter designation">
                            </div>
                        </div>

                        <!-- Individual Fields -->
                        <div id="individualFields" class="hidden mt-4 grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="dateOfBirth" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Date of Birth</label>
                                <input type="date" id="dateOfBirth" name="date_of_birth" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="nationality" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Nationality</label>
                                <input type="text" id="nationality" name="nationality" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="e.g., Pakistani">
                            </div>
                        </div>
                    </section>

                    <!-- Address & Notes -->
                    <section>
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="clientAddress" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                                <textarea id="clientAddress" name="address" rows="3" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                          placeholder="Enter complete address"></textarea>
                            </div>
                            <div>
                                <label for="clientNotes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Notes</label>
                                <textarea id="clientNotes" name="notes" rows="3" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"
                                          placeholder="Any additional notes about the client"></textarea>
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <!-- Sticky Footer -->
            <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-6 rounded-b-xl">
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="closeClientModal()" 
                            class="px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="button" onclick="resetClientForm()" 
                            class="px-5 py-2.5 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/60 rounded-xl transition-colors font-medium">
                        Reset Form
                    </button>
                    <button type="button" onclick="submitClientForm()" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-save"></i> <span id="clientSubmitText">Save Client</span>
                    </button>
                </div>
            </div> 
        </div>
</div>

<script>
// Client Modal Functions
let currentClientId = null;

function openClientModal(clientId = null) {
    currentClientId = clientId;
    const modal = document.getElementById('clientModal');
    const title = document.getElementById('clientModalTitle');
    const submitText = document.getElementById('clientSubmitText');
    
    if (clientId) {
        title.textContent = 'Edit Client';
        submitText.textContent = 'Update Client';
        loadClientData(clientId);
    } else {
        title.textContent = 'Add New Client';
        submitText.textContent = 'Save Client';
        resetClientForm();
    }
    
    modal.classList.remove('hidden');
    document.body.classList.add('overflow-hidden');
}

function closeClientModal() {
    document.getElementById('clientModal').classList.add('hidden');
    document.body.classList.remove('overflow-hidden');
    currentClientId = null;
}

function resetClientForm() {
    document.getElementById('clientForm').reset();
    document.getElementById('clientId').value = '';
    document.getElementById('corporateFields').classList.add('hidden');
    document.getElementById('individualFields').classList.add('hidden');
    currentClientId = null;
}

function loadClientData(clientId) {
    fetch(`/clients/${clientId}/edit`)
        .then(response => response.json())
        .then(client => {
            document.getElementById('clientId').value = client.id;
            document.getElementById('clientName').value = client.name;
            document.getElementById('clientEmail').value = client.email || '';
            document.getElementById('clientContact').value = client.contact_number || '';
            document.getElementById('clientCnic').value = client.cnic || '';
            document.getElementById('clientType').value = client.type;
            document.getElementById('clientGender').value = client.gender || '';
            document.getElementById('companyName').value = client.company_name || '';
            document.getElementById('designation').value = client.designation || '';
            document.getElementById('dateOfBirth').value = client.date_of_birth || '';
            document.getElementById('nationality').value = client.nationality || '';
            document.getElementById('clientAddress').value = client.address || '';
            document.getElementById('clientNotes').value = client.notes || '';
            
            // Show/hide fields based on type
            toggleClientTypeFields(client.type);
        })
        .catch(error => {
            console.error('Error loading client data:', error);
            showNotification('Failed to load client data', 'error');
        });
}

function toggleClientTypeFields(type) {
    const corporateFields = document.getElementById('corporateFields');
    const individualFields = document.getElementById('individualFields');
    
    corporateFields.classList.add('hidden');
    individualFields.classList.add('hidden');
    
    if (type === 'corporate') {
        corporateFields.classList.remove('hidden');
    } else if (type === 'individual') {
        individualFields.classList.remove('hidden');
    }
}

// Event listener for client type change
document.getElementById('clientType').addEventListener('change', function() {
    toggleClientTypeFields(this.value);
});

function submitClientForm() {
    const form = document.getElementById('clientForm');
    const formData = new FormData(form);
    
    const url = currentClientId ? `/clients/${currentClientId}` : '/clients';
    const method = currentClientId ? 'PUT' : 'POST';
    
    fetch(url, {
        method: method,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(Object.fromEntries(formData))
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            showNotification(data.message, 'success');
            closeClientModal();
            loadClients(); // Reload the clients list
        } else {
            showNotification(data.message, 'error');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showNotification('An error occurred while saving the client', 'error');
    });
}

// Delete client function
function deleteClient(clientId, clientName) {
    if (confirm(`Are you sure you want to delete client "${clientName}"?`)) {
        fetch(`/clients/${clientId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                'Accept': 'application/json',
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showNotification(data.message, 'success');
                loadClients(); // Reload the clients list
            } else {
                showNotification(data.message, 'error');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showNotification('An error occurred while deleting the client', 'error');
        });
    }
}

// Load clients function (for AJAX)
function loadClients() {
    // This would reload the clients grid via AJAX
    // You can implement this based on your needs
    window.location.reload(); // Simple reload for now
}

// Optional: Close on backdrop click
document.getElementById('clientModal').addEventListener('click', function(e) {
    if (e.target === this) closeClientModal();
});

// Notification function
function showNotification(message, type = 'success') {
    // Implement your notification system here
    alert(message); // Simple alert for now
}
</script>