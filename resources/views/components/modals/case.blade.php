<!-- Case Modal -->
@php
use Illuminate\Support\Facades\Auth;
use App\Models\CaseType;
use App\Models\CaseRemedy;
use App\Models\CourtType;
use App\Models\Lawyer;
 $caseTypes = CaseType::where('is_custom', false)
    ->orWhere(function($query) {
        $query->where('is_custom', true)
              ->where('user_id', Auth::id());
    })->get();
 $courtTypes = CourtType::where('is_custom', false)
        ->orWhere(function($query) {
            $query->where('is_custom', true)
                  ->where('user_id', Auth::id());
        })->get();

    $lawyers = Lawyer::where('is_custom', false)
        ->orWhere(function($query) {
            $query->where('is_custom', true)
                  ->where('user_id', Auth::id());
        })->get();
@endphp
<div id="caseModal" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center p-4">
    <div class="flex items-center justify-center min-h-screen p-4">
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow-2xl w-full max-w-5xl mx-auto my-8 transform transition-all duration-300 scale-100">
            
            <!-- Sticky Header -->
            <div class="sticky top-0 bg-white dark:bg-gray-800 rounded-t-xl z-10 border-b border-gray-200 dark:border-gray-700 p-6">
                <div class="flex items-center justify-between">
                    <h2 id="modalTitle" class="text-2xl font-bold text-gray-900 dark:text-white">Add New Case</h2>
                    <button 
                        onclick="closeCaseModal()" 
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
                                <div class="flex items-center gap-2">
                                    <input type="text" id="first_party_title" name="first_party_title" required 
                                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white transition-colors"
                                           placeholder="First Party">
                                    <span class="text-gray-700 dark:text-gray-300 font-medium">Vers</span>
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
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none">
                                        <option value="">Select Case Type</option>
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
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden">
                                    <i class="fas fa-plus mr-2"></i> Add "<span id="new_case_type_name"></span>"
                                </button>
                            </div>

                            <!-- Case Remedy Type with Search -->
                            <div>
                                <label for="case_remedy_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Remedy Type <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="case_remedy_id" name="case_remedy_id" required 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none">
                                        <option value="">Select Remedy Type</option>
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <input type="text" id="case_remedy_search" 
                                       class="w-full mt-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white hidden"
                                       placeholder="Search or add new remedy type...">
                                <button type="button" id="add_case_remedy_btn" 
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden">
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
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none">
                                        <option value="">Select Court Type</option>
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
                                        class="w-full mt-2 px-4 py-2.5 bg-green-600 hover:bg-green-700 text-white rounded-xl font-medium transition-colors hidden">
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
                                <input type="number" id="fir_year" name="fir_year" 
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
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
                                    <select id="assigned_lawyer_id" name="assigned_lawyer_id" 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white appearance-none">
                                        <option value="">Select Lawyer</option>
                                        @foreach($lawyers as $lawyer)
                                            <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                                        @endforeach
                                    </select>
                                    <div class="absolute inset-y-0 right-0 flex items-center px-2 pointer-events-none">
                                        <i class="fas fa-chevron-down text-gray-400"></i>
                                    </div>
                                </div>
                                <input type="text" id="lawyer_search" 
                                       class="w-full mt-2 px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white hidden"
                                       placeholder="Search or add new lawyer...">
                                <div id="add_lawyer_form" class="hidden mt-2 p-4 border border-gray-300 dark:border-gray-600 rounded-xl">
                                    <input type="text" id="new_lawyer_name" placeholder="Name" class="w-full mb-2 px-3 py-2 border rounded">
                                    <input type="email" id="new_lawyer_email" placeholder="Email" class="w-full mb-2 px-3 py-2 border rounded">
                                    <input type="text" id="new_lawyer_phone" placeholder="Phone" class="w-full mb-2 px-3 py-2 border rounded">
                                    <input type="text" id="new_lawyer_license" placeholder="License Number" class="w-full mb-2 px-3 py-2 border rounded">
                                    <textarea id="new_lawyer_address" placeholder="Address" class="w-full mb-2 px-3 py-2 border rounded" rows="2"></textarea>
                                    <button type="button" id="add_lawyer_btn" class="w-full px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded font-medium">
                                        Add Lawyer
                                    </button>
                                </div>
                            </div>
                            <div>
                                <label for="additional_lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Additional Lawyer (Optional)</label>
                                <select id="additional_lawyer_id" name="additional_lawyer_id" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Additional Lawyer</option>
                                    @foreach($lawyers as $lawyer)
                                        <option value="{{ $lawyer->id }}">{{ $lawyer->name }}</option>
                                    @endforeach
                                </select>
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
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
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
                                <div>
                                    <label for="first_party_council" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Council For</label>
                                    <select id="first_party_council" name="first_party_council" 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Type</option>
                                        <option value="plaintiff">Plaintiff</option>
                                        <option value="defendant">Defendant</option>
                                        <option value="applicant">Applicant</option>
                                        <option value="respondent">Respondent</option>
                                        <option value="complainant">Complainant</option>
                                        <option value="appellant">Appellant</option>
                                        <option value="petitioner">Petitioner</option>
                                    </select>
                                </div>
                                <div class="sm:col-span-2">
                                    <label for="first_party_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                                    <textarea id="first_party_address" name="first_party_address" rows="2" 
                                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Second Party -->
                        <div class="mb-6 p-4 border border-gray-200 dark:border-gray-700 rounded-xl">
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
                                <div>
                                    <label for="second_party_council" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Council For</label>
                                    <select id="second_party_council" name="second_party_council" 
                                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                        <option value="">Select Type</option>
                                        <option value="plaintiff">Plaintiff</option>
                                        <option value="defendant">Defendant</option>
                                        <option value="applicant">Applicant</option>
                                        <option value="respondent">Respondent</option>
                                        <option value="complainant">Complainant</option>
                                        <option value="appellant">Appellant</option>
                                        <option value="petitioner">Petitioner</option>
                                    </select>
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

                    <!-- Legal Representatives -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Legal Representatives</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="opponent_council" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Opponent Council</label>
                                <input type="text" id="opponent_council" name="opponent_council" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Opponent lawyer name">
                            </div>
                            <div>
                                <label for="power_of_attorney" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Power of Attorney Submitted By</label>
                                <input type="text" id="power_of_attorney" name="power_of_attorney" 
                                       class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       placeholder="Name of attorney">
                            </div>
                        </div>
                    </section>

                    <!-- Documents Upload -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h3 class="text-lg font-semibold text-gray-800 dark:text-gray-200 mb-4">Upload Documents / Case File</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">File Category</label>
                                <select name="document_category" 
                                        class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="petition">Petition</option>
                                    <option value="affidavit">Affidavit</option>
                                    <option value="evidence">Evidence</option>
                                    <option value="judgment">Judgment</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Select Files (Multiple)</label>
                                <input type="file" name="documents[]" multiple 
                                       class="block w-full text-sm text-gray-600 dark:text-gray-400
                                              file:mr-4 file:py-2.5 file:px-4 file:rounded-full file:border-0
                                              file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700
                                              hover:file:bg-blue-100 dark:file:bg-blue-900 dark:file:text-blue-300
                                              cursor-pointer">
                                <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 10MB per file. Supported: PDF, DOC, JPG, PNG.</p>
                            </div>
                        </div>
                    </section>

                    <!-- Remarks and Notes -->
                    <section class="border-t border-gray-200 dark:border-gray-700 pt-6">
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Remarks</label>
                                <textarea id="remarks" name="remarks" rows="4" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>
                            <div>
                                <label for="case_notes" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Case Notes</label>
                                <textarea id="case_notes" name="case_notes" rows="4" 
                                          class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                            </div>
                        </div>
                    </section>
                </form>
            </div>

            <!-- Sticky Footer -->
            <div class="sticky bottom-0 bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 p-6 rounded-b-xl">
                <div class="flex flex-col sm:flex-row justify-end gap-3">
                    <button type="button" onclick="closeCaseModal()" 
                            class="px-5 py-2.5 text-gray-700 dark:text-gray-300 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 rounded-xl transition-colors font-medium">
                        Cancel
                    </button>
                    <button type="button" onclick="resetCaseForm()" 
                            class="px-5 py-2.5 bg-yellow-100 dark:bg-yellow-900/40 text-yellow-700 dark:text-yellow-400 hover:bg-yellow-200 dark:hover:bg-yellow-900/60 rounded-xl transition-colors font-medium">
                        Reset Form
                    </button>
                    <button type="submit" form="caseForm" 
                            class="px-6 py-2.5 bg-blue-600 hover:bg-blue-700 text-white rounded-xl font-medium transition-colors flex items-center gap-2">
                        <i class="fas fa-save"></i> Save Case
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Open/Close Modal
    function closeCaseModal() {
        document.getElementById('caseModal').classList.add('hidden');
        document.body.classList.remove('overflow-hidden');
    }

    function openCaseModal() {
        document.getElementById('caseModal').classList.remove('hidden');
        document.body.classList.add('overflow-hidden');
        document.getElementById('first_party_title').focus();
    }

    // Reset Form
    function resetCaseForm() {
        document.getElementById('caseForm').reset();
        document.getElementById('other_parties_container').innerHTML = '';
        document.getElementById('filing_date').valueAsDate = new Date();
        hideDynamicSections();
        resetSearchableDropdowns();
    }

    // Hide all dynamic sections
    function hideDynamicSections() {
        document.getElementById('criminal_details').classList.add('hidden');
        document.getElementById('next_order_container').classList.add('hidden');
        document.getElementById('judgment_date_container').classList.add('hidden');
    }

    // Reset searchable dropdowns
    function resetSearchableDropdowns() {
        // Hide all search inputs and add buttons
        document.querySelectorAll('[id$="_search"]').forEach(el => el.classList.add('hidden'));
        document.querySelectorAll('[id$="_btn"]').forEach(el => el.classList.add('hidden'));
        document.getElementById('add_lawyer_form').classList.add('hidden');
        
        // Reset select elements to first option
        document.getElementById('case_type_id').selectedIndex = 0;
        document.getElementById('case_remedy_id').innerHTML = '<option value="">Select Remedy Type</option>';
        document.getElementById('court_type_id').selectedIndex = 0;
        document.getElementById('assigned_lawyer_id').selectedIndex = 0;
        document.getElementById('additional_lawyer_id').selectedIndex = 0;
    }

    // Case Type Change Handler
    document.getElementById('case_type_id').addEventListener('change', function() {
        const caseTypeId = this.value;
        const caseTypeText = this.options[this.selectedIndex].text;
        
        hideDynamicSections();
        
        // Show criminal details if criminal case type
        if (caseTypeText.toLowerCase().includes('criminal')) {
            document.getElementById('criminal_details').classList.remove('hidden');
        }
        
        // Load remedies based on case type
        if (caseTypeId) {
            loadRemedies(caseTypeId);
        }
    });

    // Case Status Change Handler
    document.getElementById('case_status').addEventListener('change', function() {
        const status = this.value;
        
        document.getElementById('next_order_container').classList.add('hidden');
        document.getElementById('judgment_date_container').classList.add('hidden');
        
        if (status === 'in_hearing') {
            document.getElementById('next_order_container').classList.remove('hidden');
        } else if (status === 'closed') {
            document.getElementById('judgment_date_container').classList.remove('hidden');
        }
    });

    // Searchable Dropdown Functionality
    function setupSearchableDropdown(selectId, searchInputId, addButtonId, newNameSpanId, apiEndpoint) {
        const select = document.getElementById(selectId);
        const searchInput = document.getElementById(searchInputId);
        const addButton = document.getElementById(addButtonId);
        const newNameSpan = document.getElementById(newNameSpanId);

        // Show search input when select is focused
        select.addEventListener('focus', function() {
            searchInput.classList.remove('hidden');
            searchInput.value = '';
            searchInput.focus();
        });

        // Filter options based on search input
        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = select.options;
            let found = false;

            // Show/hide options based on search
            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.text.toLowerCase().includes(searchTerm)) {
                    option.style.display = '';
                    if (!found && searchTerm) {
                        select.selectedIndex = i;
                        found = true;
                    }
                } else {
                    option.style.display = 'none';
                }
            }

            // Show add button if no exact match found
            if (searchTerm && !found) {
                newNameSpan.textContent = searchTerm;
                addButton.classList.remove('hidden');
            } else {
                addButton.classList.add('hidden');
            }
        });

        // Add new item
        addButton.addEventListener('click', function() {
            const newName = searchInput.value.trim();
            if (newName) {
                addNewItem(newName, apiEndpoint, select, searchInput, addButton);
            }
        });

        // Hide search when clicking outside
        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !searchInput.contains(e.target) && !addButton.contains(e.target)) {
                searchInput.classList.add('hidden');
                addButton.classList.add('hidden');
            }
        });
    }

    // Lawyer search functionality
    function setupLawyerSearch() {
        const select = document.getElementById('assigned_lawyer_id');
        const searchInput = document.getElementById('lawyer_search');
        const addForm = document.getElementById('add_lawyer_form');
        const addButton = document.getElementById('add_lawyer_btn');

        select.addEventListener('focus', function() {
            searchInput.classList.remove('hidden');
            searchInput.value = '';
            searchInput.focus();
        });

        searchInput.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const options = select.options;
            let found = false;

            for (let i = 0; i < options.length; i++) {
                const option = options[i];
                if (option.text.toLowerCase().includes(searchTerm)) {
                    option.style.display = '';
                    if (!found && searchTerm) {
                        select.selectedIndex = i;
                        found = true;
                    }
                } else {
                    option.style.display = 'none';
                }
            }

            if (searchTerm && !found) {
                addForm.classList.remove('hidden');
                document.getElementById('new_lawyer_name').value = searchTerm;
            } else {
                addForm.classList.add('hidden');
            }
        });

        addButton.addEventListener('click', function() {
            const newLawyer = {
                name: document.getElementById('new_lawyer_name').value,
                email: document.getElementById('new_lawyer_email').value,
                phone: document.getElementById('new_lawyer_phone').value,
                license_number: document.getElementById('new_lawyer_license').value,
                address: document.getElementById('new_lawyer_address').value
            };

            addNewLawyer(newLawyer, select, searchInput, addForm);
        });

        document.addEventListener('click', function(e) {
            if (!select.contains(e.target) && !searchInput.contains(e.target) && !addForm.contains(e.target)) {
                searchInput.classList.add('hidden');
                addForm.classList.add('hidden');
            }
        });
    }

    // Load remedies based on case type
    function loadRemedies(caseTypeId) {
        fetch(`/cases/remedies/${caseTypeId}`)
            .then(response => response.json())
            .then(remedies => {
                const remedySelect = document.getElementById('case_remedy_id');
                remedySelect.innerHTML = '<option value="">Select Remedy Type</option>';
                
                remedies.forEach(remedy => {
                    const option = document.createElement('option');
                    option.value = remedy.id;
                    option.textContent = remedy.name;
                    remedySelect.appendChild(option);
                });
            })
            .catch(error => console.error('Error loading remedies:', error));
    }

    // Add new item to database
    function addNewItem(name, endpoint, select, searchInput, addButton) {
        fetch(endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify({ name: name })
        })
        .then(response => response.json())
        .then(data => {
            // Add new option to select
            const option = document.createElement('option');
            option.value = data.id;
            option.textContent = data.name;
            select.appendChild(option);
            
            // Select the new option
            select.value = data.id;
            
            // Hide search and add button
            searchInput.classList.add('hidden');
            addButton.classList.add('hidden');
            searchInput.value = '';
        })
        .catch(error => console.error('Error adding item:', error));
    }

    // Add new lawyer
    function addNewLawyer(lawyer, select, searchInput, addForm) {
        fetch('/cases/lawyers', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            },
            body: JSON.stringify(lawyer)
        })
        .then(response => response.json())
        .then(data => {
            // Add to both lawyer selects
            const option1 = document.createElement('option');
            option1.value = data.id;
            option1.textContent = data.name;
            select.appendChild(option1);

            const additionalSelect = document.getElementById('additional_lawyer_id');
            const option2 = document.createElement('option');
            option2.value = data.id;
            option2.textContent = data.name;
            additionalSelect.appendChild(option2);
            
            // Select the new lawyer
            select.value = data.id;
            
            // Hide form and search
            searchInput.classList.add('hidden');
            addForm.classList.add('hidden');
            searchInput.value = '';
        })
        .catch(error => console.error('Error adding lawyer:', error));
    }

    // Add Other Parties Functionality
    let partyCounter = 0;
    document.getElementById('add_other_party').addEventListener('click', function() {
        partyCounter++;
        const container = document.getElementById('other_parties_container');
        const partyDiv = document.createElement('div');
        partyDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-xl';
        partyDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="text-md font-semibold text-gray-800 dark:text-gray-200">Other Party ${partyCounter}</h4>
                <button type="button" onclick="this.parentElement.parentElement.remove()" 
                        class="text-red-500 hover:text-red-700 transition-colors">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Name</label>
                    <input type="text" name="other_parties[${partyCounter}][name]" 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Contact Number</label>
                    <input type="text" name="other_parties[${partyCounter}][contact]" 
                           class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Address</label>
                    <textarea name="other_parties[${partyCounter}][address]" rows="2" 
                              class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white resize-none"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1.5">Role</label>
                    <select name="other_parties[${partyCounter}][role]" 
                            class="w-full px-4 py-2.5 border border-gray-300 dark:border-gray-600 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
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
    });

    // Initialize searchable dropdowns
    document.addEventListener('DOMContentLoaded', function() {
        setupSearchableDropdown('case_type_id', 'case_type_search', 'add_case_type_btn', 'new_case_type_name', '/cases/case-types');
        setupSearchableDropdown('case_remedy_id', 'case_remedy_search', 'add_case_remedy_btn', 'new_case_remedy_name', '/cases/case-remedies');
        setupSearchableDropdown('court_type_id', 'court_type_search', 'add_court_type_btn', 'new_court_type_name', '/cases/court-types');
        setupLawyerSearch();
        
        // Set default filing date
        document.getElementById('filing_date').valueAsDate = new Date();
        hideDynamicSections();
    });

    // Optional: Close on backdrop click
    document.getElementById('caseModal').addEventListener('click', function(e) {
        if (e.target === this) closeCaseModal();
    });
</script>