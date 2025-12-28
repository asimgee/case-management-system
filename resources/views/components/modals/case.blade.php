<!-- Case Modal -->
<div id="caseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden items-center justify-center p-4 overflow-y-auto">
    <div class="min-h-screen flex items-center justify-center p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl mx-auto my-8 transform transition-all duration-300 scale-100">
            
            <!-- Sticky Header -->
            <div class="sticky top-0 bg-white dark:bg-gray-800 rounded-t-xl z-10 border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Add New Case</h2>
                    <button 
                        onclick="window.closeCaseModal()" 
                        class="text-gray-500 hover:text-red-600 dark:hover:text-red-400 transition-colors p-2 rounded-lg hover:bg-gray-100 dark:hover:bg-gray-700"
                        aria-label="Close modal">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                </div>
            </div>

            <!-- Scrollable Form Body -->
            <div class="p-6 max-h-[70vh] overflow-y-auto scrollbar-thin scrollbar-thumb-gray-400 dark:scrollbar-thumb-gray-600">
                <form id="caseForm" method="POST" action="{{ route('cases.store') }}" enctype="multipart/form-data" class="space-y-8">
                    @csrf

                    <!-- Case Basic Information -->
                    <section>
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Basic Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Case Title with VS separator -->
                            <div class="sm:col-span-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Title <span class="text-red-500">*</span></label>
                                <div class="flex flex-col sm:flex-row items-center gap-2">
                                    <input type="text" id="first_party_title" name="first_party_title" required 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="First Party">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium my-2 sm:my-0">vs</span>
                                    <input type="text" id="second_party_title" name="second_party_title" required 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="Second Party">
                                </div>
                            </div>
                            
                            <!-- Case Type with Search -->
                            <div>
                                <label for="case_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Type <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="case_type_id" name="case_type_id" required 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Select Case Type</option>
                                        @php
                                            use Illuminate\Support\Facades\Auth;
                                            use App\Models\CaseType;
                                            $caseTypes = CaseType::where('is_custom', false)
                                                ->orWhere(function($query) {
                                                    $query->where('is_custom', true)
                                                          ->where('user_id', Auth::id());
                                                })->get();
                                        @endphp
                                        @foreach($caseTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <input type="text" id="case_type_search" 
                                       class="w-full mt-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white hidden"
                                       placeholder="Search or add new case type...">
                                <button type="button" id="add_case_type_btn" 
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add "<span id="new_case_type_name"></span>"
                                </button>
                            </div>

                            <!-- Case Remedy Type with Search -->
                            <div>
                                <label for="case_remedy_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Remedy Type <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="case_remedy_id" name="case_remedy_id" required 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Select Remedy Type</option>
                                        <!-- Will be populated dynamically -->
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <input type="text" id="case_remedy_search" 
                                       class="w-full mt-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white hidden"
                                       placeholder="Search or add new remedy type...">
                                <button type="button" id="add_case_remedy_btn" 
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add "<span id="new_case_remedy_name"></span>"
                                </button>
                            </div>
                        </div>
                    </section>

                    <!-- Court Information -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Court Information</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <!-- Court Type with Search -->
                            <div>
                                <label for="court_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Court Type <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="court_type_id" name="court_type_id" required 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Select Court Type</option>
                                        @php
                                            use App\Models\CourtType;
                                            $courtTypes = CourtType::where('is_custom', false)
                                                ->orWhere(function($query) {
                                                    $query->where('is_custom', true)
                                                          ->where('user_id', Auth::id());
                                                })->get();
                                        @endphp
                                        @foreach($courtTypes as $type)
                                            <option value="{{ $type->id }}">{{ $type->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <input type="text" id="court_type_search" 
                                       class="w-full mt-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white hidden"
                                       placeholder="Search or add new court type...">
                                <button type="button" id="add_court_type_btn" 
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden flex items-center justify-center">
                                    <i class="fas fa-plus mr-2"></i> Add "<span id="new_court_type_name"></span>"
                                </button>
                            </div>

                            <div>
                                <label for="court_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Court Name <span class="text-red-500">*</span></label>
                                <input type="text" id="court_name" name="court_name" required 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter court name">
                            </div>
                        </div>
                    </section>

                    <!-- Criminal Case Details -->
                    <section id="criminal_details" class="border-t border-gray-200 dark:border-gray-700 pt-6 hidden">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">FIR Details</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="fir_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">FIR No</label>
                                <input type="text" id="fir_no" name="fir_no" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="fir_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">FIR Year</label>
                                <input type="number" id="fir_year" name="fir_year" min="1900" max="{{ date('Y') }}"
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="offence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Offence</label>
                                <input type="text" id="offence" name="offence" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="police_station" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Police Station with City</label>
                                <input type="text" id="police_station" name="police_station" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div class="sm:col-span-2">
                                <label for="other_fir_details" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Other FIR Details</label>
                                <textarea id="other_fir_details" name="other_fir_details" rows="2" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Case Status and Dates -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Case Status & Dates</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="case_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Status <span class="text-red-500">*</span></label>
                                <select id="case_status" name="case_status" required 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white cursor-pointer">
                                    <option value="pending">Pending</option>
                                    <option value="in_hearing">In Hearing</option>
                                    <option value="closed">Closed</option>
                                </select>
                            </div>
                            <div>
                                <label for="filing_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Filing Date <span class="text-red-500">*</span></label>
                                <input type="date" id="filing_date" name="filing_date" required 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="next_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Next Hearing Date</label>
                                <input type="date" id="next_date" name="next_date" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            
                            <!-- Next Order Section -->
                            <div id="next_order_container" class="hidden sm:col-span-2">
                                <label for="next_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Next Order</label>
                                <textarea id="next_order" name="next_order" rows="3" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>

                            <!-- Judgment Date -->
                            <div id="judgment_date_container" class="hidden">
                                <label for="judgment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Judgment Date</label>
                                <input type="date" id="judgment_date" name="judgment_date" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                        </div>
                    </section>

                    <!-- Lawyer Assignment -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Lawyer Assignment</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="assigned_lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Assigned Lawyer</label>
                                <div class="relative">
                                    <select id="client_type" name="client_type" 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none cursor-pointer">
                                        <option value="">Select Lawyer</option>
                                        <option value="1">Plaintiff / Petitioner / Appellant</option>
                                        <option value="2">Defendant / Respondent</option>
                                      
                                    </select>
                                </div>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="offending_lawyer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Offending Lawyer</label>
                                <input type="text" id="offending_lawyer" name="offending_lawyer" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Enter offending lawyer name">
                            </div>
                        </div>
                    </section>

                    <!-- Parties Information -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Parties Information</h3>
                        
                        <!-- First Party -->
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/50">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">First Party - [Plaintiff / Petitioner / Appellant]</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="first_party_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="first_party_name" name="first_party_name" required 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="first_party_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                                    <input type="text" id="first_party_contact" name="first_party_contact" 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="first_party_cnic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CNIC</label>
                                    <input type="text" id="first_party_cnic" name="first_party_cnic" placeholder="xxxxx-xxxxxxx-x"
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="first_party_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                                    <textarea id="first_party_address" name="first_party_address" rows="2" 
                                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Second Party -->
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/50">
                            <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200 mb-3">Second Party - [Defendant / Respondent]</h4>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <label for="second_party_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name <span class="text-red-500">*</span></label>
                                    <input type="text" id="second_party_name" name="second_party_name" required 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="second_party_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                                    <input type="text" id="second_party_contact" name="second_party_contact" 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div>
                                    <label for="second_party_cnic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">CNIC</label>
                                    <input type="text" id="second_party_cnic" name="second_party_cnic" placeholder="xxxxx-xxxxxxx-x"
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="second_party_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                                    <textarea id="second_party_address" name="second_party_address" rows="2" 
                                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Add Other Parties Button -->
                        <div class="flex justify-end">
                            <button type="button" id="add_other_party" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                                <i class="fas fa-plus"></i> Add Other Parties
                            </button>
                        </div>

                        <!-- Other Parties Container -->
                        <div id="other_parties_container" class="mt-4 space-y-4">
                            <!-- Other parties will be added here dynamically -->
                        </div>
                    </section>
                    <!-- Documents Upload -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Upload Documents / Case File</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File Category</label>
                                <select name="document_category" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white cursor-pointer">
                                    <option value="petition">Petition</option>
                                    <option value="affidavit">Affidavit</option>
                                    <option value="evidence">Evidence</option>
                                    <option value="judgment">Judgment</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Files (Multiple)</label>
                                <input type="file" name="documents[]" multiple accept=".pdf,.doc,.docx,.jpg,.jpeg,.png"
                                       class="block w-full text-sm text-gray-600 dark:text-gray-400
                                              file:mr-4 file:py-2.5 file:px-4 file:rounded-lg file:border-0
                                              file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300
                                              cursor-pointer transition-colors">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 10MB per file. Supported: PDF, DOC, DOCX, JPG, PNG.</p>
                            </div>
                            <div class="sm:col-span-2">
                                <label for="document_description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Document Description</label>
                                <textarea id="document_description" name="document_description" rows="2" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>
                        </div>
                    </section>

                    <!-- Remarks and Notes -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Remarks</label>
                                <textarea id="remarks" name="remarks" rows="3" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <!-- Sticky Footer -->
            <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-6 rounded-b-xl">
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="window.closeCaseModal()" 
                            class="px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="button" onclick="window.resetCaseForm()" 
                            class="px-5 py-2.5 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/60 rounded-xl transition-colors font-medium">
                        Reset Form
                    </button>
                    <button type="submit" form="caseForm" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors flex items-center justify-center gap-2">
                        <i class="fas fa-save"></i> Save Case
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Make functions globally accessible
    window.openCaseModal = function() {
        const modal = document.getElementById('caseModal');
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.classList.add('overflow-hidden');
        document.getElementById('first_party_title').focus();
        
        // Set default filing date to today
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('filing_date').value = today;
        
        // Set minimum date for next hearing date to tomorrow
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        document.getElementById('next_date').min = tomorrowStr;
        
        // Set maximum date for FIR year to current year
        const currentYear = new Date().getFullYear();
        document.getElementById('fir_year').max = currentYear;
    };

    window.closeCaseModal = function() {
        const modal = document.getElementById('caseModal');
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.classList.remove('overflow-hidden');
    };

    window.resetCaseForm = function() {
        if (confirm('Are you sure you want to reset the form? All entered data will be lost.')) {
            document.getElementById('caseForm').reset();
            document.getElementById('other_parties_container').innerHTML = '';
            window.partyCounter = 0;
            
            const today = new Date().toISOString().split('T')[0];
            document.getElementById('filing_date').value = today;
            
            // Reset dynamic sections
            hideDynamicSections();
            
            // Reset searchable dropdowns
            resetSearchableDropdowns();
            
            // Clear file inputs
            const fileInputs = document.querySelectorAll('input[type="file"]');
            fileInputs.forEach(input => input.value = '');
        }
    };

    // Hide all dynamic sections
    function hideDynamicSections() {
        document.getElementById('criminal_details').classList.add('hidden');
        document.getElementById('next_order_container').classList.add('hidden');
        document.getElementById('judgment_date_container').classList.add('hidden');
    }

    // Reset searchable dropdowns
    function resetSearchableDropdowns() {
        // Hide all search inputs and add buttons
        document.querySelectorAll('[id$="_search"]').forEach(el => {
            el.classList.add('hidden');
            el.value = '';
        });
        
        document.querySelectorAll('[id$="_btn"]').forEach(el => {
            el.classList.add('hidden');
        });
        
        document.getElementById('add_lawyer_form').classList.add('hidden');
        
        // Reset select fields to first option
        document.getElementById('case_type_id').selectedIndex = 0;
        document.getElementById('case_remedy_id').innerHTML = '<option value="">Select Remedy Type</option>';
        document.getElementById('court_type_id').selectedIndex = 0;
        document.getElementById('assigned_lawyer_id').selectedIndex = 0;
        document.getElementById('additional_lawyer_id').selectedIndex = 0;
        
        // Clear new lawyer form
        document.getElementById('new_lawyer_name').value = '';
        document.getElementById('new_lawyer_email').value = '';
        document.getElementById('new_lawyer_phone').value = '';
        document.getElementById('new_lawyer_license').value = '';
        document.getElementById('new_lawyer_address').value = '';
    }

    // Case Type Change Handler
    document.getElementById('case_type_id').addEventListener('change', function() {
        const caseTypeId = this.value;
        const caseTypeText = this.options[this.selectedIndex].text.toLowerCase();
        const criminalDetails = document.getElementById('criminal_details');
        
        // Show/hide criminal details section
        if (caseTypeText.includes('criminal')) {
            criminalDetails.classList.remove('hidden');
        } else {
            criminalDetails.classList.add('hidden');
        }
        
        // Load remedies based on case type
        if (caseTypeId) {
            loadRemedies(caseTypeId);
        } else {
            // Reset remedies dropdown
            const remedySelect = document.getElementById('case_remedy_id');
            remedySelect.innerHTML = '<option value="">Select Remedy Type</option>';
        }
    });

    // Case Status Change Handler
    document.getElementById('case_status').addEventListener('change', function() {
        const status = this.value;
        const nextOrderContainer = document.getElementById('next_order_container');
        const judgmentDateContainer = document.getElementById('judgment_date_container');
        
        nextOrderContainer.classList.add('hidden');
        judgmentDateContainer.classList.add('hidden');
        
        if (status === 'in_hearing') {
            nextOrderContainer.classList.remove('hidden');
        } else if (status === 'closed') {
            judgmentDateContainer.classList.remove('hidden');
        }
    });

    // Searchable Dropdown Functionality
    function setupSearchableDropdown(selectId, searchInputId, addButtonId, newNameSpanId, apiEndpoint) {
        const select = document.getElementById(selectId);
        const searchInput = document.getElementById(searchInputId);
        const addButton = document.getElementById(addButtonId);
        const newNameSpan = document.getElementById(newNameSpanId);

        if (!select || !searchInput || !addButton || !newNameSpan) return;

        select.addEventListener('focus', function() {
            searchInput.classList.remove('hidden');
            searchInput.value = '';
            searchInput.focus();
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const options = select.options;
            let found = false;

            // Show all options first
            for (let i = 0; i < options.length; i++) {
                options[i].style.display = '';
            }

            if (searchTerm) {
                // Filter options
                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    if (option.text.toLowerCase().includes(searchTerm) && option.value !== '') {
                        option.style.display = '';
                        if (!found) {
                            select.selectedIndex = i;
                            found = true;
                        }
                    } else {
                        option.style.display = 'none';
                    }
                }

                // Show add button if not found
                if (!found && searchTerm.length >= 2) {
                    newNameSpan.textContent = searchTerm;
                    addButton.classList.remove('hidden');
                } else {
                    addButton.classList.add('hidden');
                }
            } else {
                // Reset to first option when search is cleared
                select.selectedIndex = 0;
                addButton.classList.add('hidden');
            }
        });

        addButton.addEventListener('click', function() {
            const newName = searchInput.value.trim();
            if (newName && newName.length >= 2) {
                addNewItem(newName, apiEndpoint, select, searchInput, addButton, newNameSpan);
            } else {
                alert('Please enter a valid name (minimum 2 characters).');
            }
        });

        // Close search on outside click
        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !searchInput.contains(e.target) && !addButton.contains(e.target)) {
                searchInput.classList.add('hidden');
                addButton.classList.add('hidden');
                searchInput.value = '';
                
                // Reset select display
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].style.display = '';
                }
            }
        });
    }

    // Lawyer search functionality
    function setupLawyerSearch() {
        const select = document.getElementById('assigned_lawyer_id');
        const searchInput = document.getElementById('lawyer_search');
        const addForm = document.getElementById('add_lawyer_form');
        const addButton = document.getElementById('add_lawyer_btn');

        if (!select || !searchInput || !addForm || !addButton) return;

        select.addEventListener('focus', function() {
            searchInput.classList.remove('hidden');
            searchInput.value = '';
            searchInput.focus();
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase().trim();
            const options = select.options;
            let found = false;

            // Show all options first
            for (let i = 0; i < options.length; i++) {
                options[i].style.display = '';
            }

            if (searchTerm) {
                // Filter options
                for (let i = 0; i < options.length; i++) {
                    const option = options[i];
                    if (option.text.toLowerCase().includes(searchTerm) && option.value !== '') {
                        option.style.display = '';
                        if (!found) {
                            select.selectedIndex = i;
                            found = true;
                        }
                    } else {
                        option.style.display = 'none';
                    }
                }

                // Show add form if not found
                if (!found && searchTerm.length >= 2) {
                    addForm.classList.remove('hidden');
                    document.getElementById('new_lawyer_name').value = searchTerm;
                } else {
                    addForm.classList.add('hidden');
                }
            } else {
                // Reset to first option when search is cleared
                select.selectedIndex = 0;
                addForm.classList.add('hidden');
            }
        });

        addButton.addEventListener('click', function() {
            const newLawyer = {
                name: document.getElementById('new_lawyer_name').value.trim(),
                email: document.getElementById('new_lawyer_email').value.trim(),
                phone: document.getElementById('new_lawyer_phone').value.trim(),
                license_number: document.getElementById('new_lawyer_license').value.trim(),
                address: document.getElementById('new_lawyer_address').value.trim()
            };

            if (!newLawyer.name) {
                alert('Lawyer name is required');
                return;
            }

            addNewLawyer(newLawyer, select, searchInput, addForm);
        });

        // Close search on outside click
        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !searchInput.contains(e.target) && !addForm.contains(e.target)) {
                searchInput.classList.add('hidden');
                addForm.classList.add('hidden');
                searchInput.value = '';
                
                // Reset select display
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].style.display = '';
                }
            }
        });
    }

    // Load remedies based on case type
    function loadRemedies(caseTypeId) {
        fetch(`/cases/remedies/${caseTypeId}`)
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(remedies => {
                const remedySelect = document.getElementById('case_remedy_id');
                remedySelect.innerHTML = '<option value="">Select Remedy Type</option>';
                
                if (remedies && remedies.length > 0) {
                    remedies.forEach(remedy => {
                        const option = document.createElement('option');
                        option.value = remedy.id;
                        option.textContent = remedy.name;
                        remedySelect.appendChild(option);
                    });
                } else {
                    const option = document.createElement('option');
                    option.value = "";
                    option.textContent = "No remedies found for this case type";
                    remedySelect.appendChild(option);
                }
            })
            .catch(error => {
                console.error('Error loading remedies:', error);
                const remedySelect = document.getElementById('case_remedy_id');
                remedySelect.innerHTML = '<option value="">Error loading remedies</option>';
            });
    }

    // Add new item to database
    function addNewItem(name, endpoint, select, searchInput, addButton, newNameSpan) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Security error. Please refresh the page and try again.');
            return;
        }

        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Failed to add item'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.caseType) {
                const option = document.createElement('option');
                option.value = data.caseType.id;
                option.textContent = data.caseType.name;
                select.appendChild(option);
                
                select.value = data.caseType.id;
                
                searchInput.classList.add('hidden');
                addButton.classList.add('hidden');
                newNameSpan.textContent = '';
                searchInput.value = '';
                
                // Reset select display
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].style.display = '';
                }
            } else {
                throw new Error(data.message || 'Invalid response from server');
            }
        })
        .catch(error => {
            console.error('Error adding item:', error);
            alert(error.message || 'Failed to add item. Please try again.');
        });
    }

    // Add new lawyer
    function addNewLawyer(lawyer, select, searchInput, addForm) {
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
        
        if (!csrfToken) {
            console.error('CSRF token not found');
            alert('Security error. Please refresh the page and try again.');
            return;
        }

        fetch('/cases/lawyers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify(lawyer)
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw new Error(err.message || 'Failed to add lawyer'); });
            }
            return response.json();
        })
        .then(data => {
            if (data.success && data.lawyer) {
                // Add to assigned lawyer select
                const option1 = document.createElement('option');
                option1.value = data.lawyer.id;
                option1.textContent = data.lawyer.name;
                select.appendChild(option1);

                // Add to additional lawyer select
                const additionalSelect = document.getElementById('additional_lawyer_id');
                const option2 = document.createElement('option');
                option2.value = data.lawyer.id;
                option2.textContent = data.lawyer.name;
                additionalSelect.appendChild(option2);
                
                // Select the new lawyer
                select.value = data.lawyer.id;
                
                // Reset form
                searchInput.classList.add('hidden');
                addForm.classList.add('hidden');
                searchInput.value = '';
                document.getElementById('new_lawyer_name').value = '';
                document.getElementById('new_lawyer_email').value = '';
                document.getElementById('new_lawyer_phone').value = '';
                document.getElementById('new_lawyer_license').value = '';
                document.getElementById('new_lawyer_address').value = '';
                
                // Reset select display
                for (let i = 0; i < select.options.length; i++) {
                    select.options[i].style.display = '';
                }
                
                alert('Lawyer added successfully!');
            } else {
                throw new Error(data.message || 'Invalid response from server');
            }
        })
        .catch(error => {
            console.error('Error adding lawyer:', error);
            alert(error.message || 'Failed to add lawyer. Please try again.');
        });
    }

    // Add Other Parties Functionality
    window.partyCounter = 0;
    document.getElementById('add_other_party').addEventListener('click', function() {
        window.partyCounter++;
        const container = document.getElementById('other_parties_container');
        const partyDiv = document.createElement('div');
        partyDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-xl bg-gray-50 dark:bg-gray-900/50';
        partyDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200">Other Party ${window.partyCounter}</h4>
                <button type="button" onclick="this.parentElement.parentElement.remove()" 
                        class="text-red-500 hover:text-red-700 transition-colors p-1">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                    <input type="text" name="other_parties[${window.partyCounter}][name]" required
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                    <input type="text" name="other_parties[${window.partyCounter}][contact]" 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                    <textarea name="other_parties[${window.partyCounter}][address]" rows="2" 
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
                    <select name="other_parties[${window.partyCounter}][role]" 
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white cursor-pointer">
                        <option value="witness">Witness</option>
                        <option value="co-accused">Co-Accused</option>
                        <option value="co-plaintiff">Co-Plaintiff</option>
                        <option value="co-defendant">Co-Defendant</option>
                        <option value="other">Other</option>
                    </select>
                </div>
            </div>
        `;
        container.appendChild(partyDiv);
        
        // Scroll to the new party
        partyDiv.scrollIntoView({ behavior: 'smooth', block: 'nearest' });
    });

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        // Setup searchable dropdowns
        setupSearchableDropdown('case_type_id', 'case_type_search', 'add_case_type_btn', 'new_case_type_name', '/cases/case-types');
        setupSearchableDropdown('case_remedy_id', 'case_remedy_search', 'add_case_remedy_btn', 'new_case_remedy_name', '/cases/case-remedies');
        setupSearchableDropdown('court_type_id', 'court_type_search', 'add_court_type_btn', 'new_court_type_name', '/cases/court-types');
        setupLawyerSearch();
        
        // Set default filing date
        const today = new Date().toISOString().split('T')[0];
        document.getElementById('filing_date').value = today;
        
        // Set minimum date for next hearing date
        const tomorrow = new Date();
        tomorrow.setDate(tomorrow.getDate() + 1);
        const tomorrowStr = tomorrow.toISOString().split('T')[0];
        document.getElementById('next_date').min = tomorrowStr;
        
        // Set maximum date for FIR year
        const currentYear = new Date().getFullYear();
        document.getElementById('fir_year').max = currentYear;
        
        // Hide dynamic sections initially
        hideDynamicSections();
        
        // Close modal on backdrop click
        document.getElementById('caseModal').addEventListener('click', function(e) {
            if (e.target === this) window.closeCaseModal();
        });
        
        // Close modal on Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                window.closeCaseModal();
            }
        });
        
        // Form validation before submit
        document.getElementById('caseForm').addEventListener('submit', function(e) {
            // Basic validation
            const firstPartyTitle = document.getElementById('first_party_title').value.trim();
            const secondPartyTitle = document.getElementById('second_party_title').value.trim();
            const firstPartyName = document.getElementById('first_party_name').value.trim();
            const secondPartyName = document.getElementById('second_party_name').value.trim();
            const caseType = document.getElementById('case_type_id').value;
            const caseRemedy = document.getElementById('case_remedy_id').value;
            const courtType = document.getElementById('court_type_id').value;
            const courtName = document.getElementById('court_name').value.trim();
            const filingDate = document.getElementById('filing_date').value;
            
            let isValid = true;
            let errorMessage = '';
            
            if (!firstPartyTitle) {
                isValid = false;
                errorMessage += 'First party title is required.\n';
            }
            
            if (!secondPartyTitle) {
                isValid = false;
                errorMessage += 'Second party title is required.\n';
            }
            
            if (!firstPartyName) {
                isValid = false;
                errorMessage += 'First party name is required.\n';
            }
            
            if (!secondPartyName) {
                isValid = false;
                errorMessage += 'Second party name is required.\n';
            }
            
            if (!caseType) {
                isValid = false;
                errorMessage += 'Case type is required.\n';
            }
            
            if (!caseRemedy) {
                isValid = false;
                errorMessage += 'Case remedy type is required.\n';
            }
            
            if (!courtType) {
                isValid = false;
                errorMessage += 'Court type is required.\n';
            }
            
            if (!courtName) {
                isValid = false;
                errorMessage += 'Court name is required.\n';
            }
            
            if (!filingDate) {
                isValid = false;
                errorMessage += 'Filing date is required.\n';
            }
            
            if (!isValid) {
                e.preventDefault();
                alert('Please fix the following errors:\n\n' + errorMessage);
            }
        });
        
        // Auto-submit form when filters change
        const filters = ['courtFilter', 'statusFilter', 'dateFilter'];
        filters.forEach(filterId => {
            const element = document.getElementById(filterId);
            if (element) {
                element.addEventListener('change', function() {
                    document.getElementById('filterForm').submit();
                });
            }
        });
    });
</script>

<style>
    /* Custom scrollbar styles */
    .scrollbar-thin::-webkit-scrollbar {
        width: 6px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 3px;
    }
    
    .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    .dark .scrollbar-thin::-webkit-scrollbar-track {
        background: #374151;
    }
    
    .dark .scrollbar-thin::-webkit-scrollbar-thumb {
        background: #6b7280;
    }
    
    .dark .scrollbar-thin::-webkit-scrollbar-thumb:hover {
        background: #9ca3af;
    }
    
    /* Card styles */
    .card {
        background: white;
        border-radius: 0.75rem;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1), 0 1px 2px 0 rgba(0, 0, 0, 0.06);
    }
    
    .dark .card {
        background: #1f2937;
        box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.3), 0 1px 2px 0 rgba(0, 0, 0, 0.2);
    }
    
    /* Fade in animation */
    .fade-in {
        animation: fadeIn 0.5s ease-in-out;
    }
    
    @keyframes fadeIn {
        from {
            opacity: 0;
            transform: translateY(10px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }
    
    /* Mobile responsive */
    @media (max-width: 640px) {
        .mobile-space-y-4 > * + * {
            margin-top: 1rem;
        }
    }
</style>