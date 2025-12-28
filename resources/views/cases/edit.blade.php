@extends('layouts.app')

@section('title', 'Edit Case - ' . $case->case_number)

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('cases.show', $case->id) }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Edit Case</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $case->case_number }} - {{ $case->case_title }}</p>
            </div>
        </div>
    </div>

    @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6">
            <ul class="list-disc list-inside">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('cases.update', $case->id) }}" enctype="multipart/form-data">
        @csrf
        @method('PUT')
        
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Left Column -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Basic Information -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Basic Information</h2>
                    </div>
                    <div class="space-y-4">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_party_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">First Party Title *</label>
                                <input type="text" id="first_party_title" name="first_party_title" 
                                       value="{{ old('first_party_title', $case->first_party_title) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                            </div>
                            <div>
                                <label for="second_party_title" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Second Party Title *</label>
                                <input type="text" id="second_party_title" name="second_party_title" 
                                       value="{{ old('second_party_title', $case->second_party_title) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                            </div>
                        </div>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="case_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Case Type *</label>
                                <select id="case_type_id" name="case_type_id" required
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Case Type</option>
                                    @foreach($caseTypes as $type)
                                        <option value="{{ $type->id }}" {{ old('case_type_id', $case->case_type_id) == $type->id ? 'selected' : '' }}>
                                            {{ $type->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label for="case_remedy_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remedy Type *</label>
                                <select id="case_remedy_id" name="case_remedy_id" required
                                        class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                    <option value="">Select Remedy Type</option>
                                    @foreach($remedies as $remedy)
                                        <option value="{{ $remedy->id }}" {{ old('case_remedy_id', $case->case_remedy_id) == $remedy->id ? 'selected' : '' }}>
                                            {{ $remedy->name }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Court Information -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Court Information</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="court_type_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Court Type *</label>
                            <select id="court_type_id" name="court_type_id" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="">Select Court Type</option>
                                @foreach($courtTypes as $type)
                                    <option value="{{ $type->id }}" {{ old('court_type_id', $case->court_type_id) == $type->id ? 'selected' : '' }}>
                                        {{ $type->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label for="court_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Court Name *</label>
                            <input type="text" id="court_name" name="court_name" 
                                   value="{{ old('court_name', $case->court_name) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   required>
                        </div>
                    </div>
                </div>

                <!-- Parties Information -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Parties Information</h2>
                    </div>
                    
                    <!-- First Party -->
                    <div class="mb-6">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">First Party</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="first_party_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                <input type="text" id="first_party_name" name="first_party_name" 
                                       value="{{ old('first_party_name', $case->first_party_name) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                            </div>
                            <div>
                                <label for="first_party_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact</label>
                                <input type="text" id="first_party_contact" name="first_party_contact" 
                                       value="{{ old('first_party_contact', $case->first_party_contact) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="first_party_cnic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CNIC</label>
                                <input type="text" id="first_party_cnic" name="first_party_cnic" 
                                       value="{{ old('first_party_cnic', $case->first_party_cnic) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="first_party_council" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Council For</label>
                                <input type="text" id="first_party_council" name="first_party_council" 
                                       value="{{ old('first_party_council', $case->first_party_council) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label for="first_party_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                <textarea id="first_party_address" name="first_party_address" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('first_party_address', $case->first_party_address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Second Party -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Second Party</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label for="second_party_name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name *</label>
                                <input type="text" id="second_party_name" name="second_party_name" 
                                       value="{{ old('second_party_name', $case->second_party_name) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                       required>
                            </div>
                            <div>
                                <label for="second_party_contact" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact</label>
                                <input type="text" id="second_party_contact" name="second_party_contact" 
                                       value="{{ old('second_party_contact', $case->second_party_contact) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="second_party_cnic" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">CNIC</label>
                                <input type="text" id="second_party_cnic" name="second_party_cnic" 
                                       value="{{ old('second_party_cnic', $case->second_party_cnic) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div>
                                <label for="second_party_council" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Council For</label>
                                <input type="text" id="second_party_council" name="second_party_council" 
                                       value="{{ old('second_party_council', $case->second_party_council) }}"
                                       class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                            </div>
                            <div class="md:col-span-2">
                                <label for="second_party_address" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                <textarea id="second_party_address" name="second_party_address" rows="3"
                                          class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('second_party_address', $case->second_party_address) }}</textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Other Parties -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg font-medium text-gray-900 dark:text-white">Other Parties</h3>
                            <button type="button" id="add_other_party" 
                                    class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium text-sm">
                                <i class="fas fa-plus mr-1"></i> Add Party
                            </button>
                        </div>
                        
                        <div id="other_parties_container" class="space-y-4">
                            @php
                                $otherParties = $case->other_parties ?? [];
                                $partyIndex = 0;
                            @endphp
                            
                            @if(count($otherParties) > 0)
                                @foreach($otherParties as $party)
                                <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg party-item">
                                    <div class="flex justify-between items-center mb-3">
                                        <h4 class="font-medium text-gray-900 dark:text-white">Other Party {{ $loop->iteration }}</h4>
                                        <button type="button" onclick="removeParty(this)" 
                                                class="text-red-500 hover:text-red-700">
                                            <i class="fas fa-times"></i>
                                        </button>
                                    </div>
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                                            <input type="text" name="other_parties[{{ $partyIndex }}][name]" 
                                                   value="{{ $party['name'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact</label>
                                            <input type="text" name="other_parties[{{ $partyIndex }}][contact]" 
                                                   value="{{ $party['contact'] ?? '' }}"
                                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                                        </div>
                                        <div class="md:col-span-2">
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                                            <textarea name="other_parties[{{ $partyIndex }}][address]" rows="2"
                                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ $party['address'] ?? '' }}</textarea>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                                            <select name="other_parties[{{ $partyIndex }}][role]" 
                                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                                                <option value="witness" {{ ($party['role'] ?? '') == 'witness' ? 'selected' : '' }}>Witness</option>
                                                <option value="co-accused" {{ ($party['role'] ?? '') == 'co-accused' ? 'selected' : '' }}>Co-Accused</option>
                                                <option value="co-plaintiff" {{ ($party['role'] ?? '') == 'co-plaintiff' ? 'selected' : '' }}>Co-Plaintiff</option>
                                                <option value="co-defendant" {{ ($party['role'] ?? '') == 'co-defendant' ? 'selected' : '' }}>Co-Defendant</option>
                                                <option value="other" {{ ($party['role'] ?? '') == 'other' ? 'selected' : '' }}>Other</option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                                @php $partyIndex++; @endphp
                                @endforeach
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Criminal Details -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">FIR Details</h2>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label for="fir_no" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">FIR No</label>
                            <input type="text" id="fir_no" name="fir_no" 
                                   value="{{ old('fir_no', $case->fir_no) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                        <div>
                            <label for="fir_year" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">FIR Year</label>
                            <input type="number" id="fir_year" name="fir_year" 
                                   value="{{ old('fir_year', $case->fir_year) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                        <div>
                            <label for="offence" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Offence</label>
                            <input type="text" id="offence" name="offence" 
                                   value="{{ old('offence', $case->offence) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                        <div>
                            <label for="police_station" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Police Station</label>
                            <input type="text" id="police_station" name="police_station" 
                                   value="{{ old('police_station', $case->police_station) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                        <div class="md:col-span-2">
                            <label for="other_fir_details" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Other FIR Details</label>
                            <textarea id="other_fir_details" name="other_fir_details" rows="3"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ old('other_fir_details', $case->other_fir_details) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column -->
            <div class="space-y-6">
                <!-- Case Status & Dates -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Case Status & Dates</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="case_status" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Case Status *</label>
                            <select id="case_status" name="case_status" required
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                                <option value="pending" {{ old('case_status', $case->case_status) == 'pending' ? 'selected' : '' }}>Pending</option>
                                <option value="in_hearing" {{ old('case_status', $case->case_status) == 'in_hearing' ? 'selected' : '' }}>In Hearing</option>
                                <option value="closed" {{ old('case_status', $case->case_status) == 'closed' ? 'selected' : '' }}>Closed</option>
                            </select>
                        </div>
                        <div>
                            <label for="filing_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Filing Date *</label>
                            <input type="date" id="filing_date" name="filing_date" 
                                   value="{{ old('filing_date', $case->filing_date) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white"
                                   required>
                        </div>
                        <div id="next_date_container">
                            <label for="next_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Next Hearing Date</label>
                            <input type="date" id="next_date" name="next_date" 
                                   value="{{ old('next_date', $case->next_date) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                        <div id="next_order_container" style="{{ $case->case_status == 'in_hearing' ? '' : 'display: none;' }}">
                            <label for="next_order" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Next Order</label>
                            <textarea id="next_order" name="next_order" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('next_order', $case->next_order) }}</textarea>
                        </div>
                        <div id="judgment_date_container" style="{{ $case->case_status == 'closed' ? '' : 'display: none;' }}">
                            <label for="judgment_date" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Judgment Date</label>
                            <input type="date" id="judgment_date" name="judgment_date" 
                                   value="{{ old('judgment_date', $case->judgment_date) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        </div>
                    </div>
                </div>

                <!-- Lawyer Assignment -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Lawyer Assignment</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="assigned_lawyer_id" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Assigned Lawyer</label>
                            <select id="client_type" name="client_type"
                                    class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">

                                <option value="">Select Lawyer</option>
                                <option value="1" {{ $case->client_type == 1 ? 'selected' : '' }}>
                                    Plaintiff / Petitioner / Appellant
                                </option>
                                <option value="2" {{ $case->client_type == 2 ? 'selected' : '' }}>
                                    Defendant / Respondent
                                </option>
                            </select>
                        </div>
                        <div>
                            <label for="offending_lawyer" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Offending Lawyer</label>
                            <input type="text" id="offending_lawyer" name="offending_lawyer" 
                                   value="{{ old('offending_lawyer', $case->offending_lawyer) }}"
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                        </div>
                    </div>
                </div>

                <!-- Documents -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documents</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Add New Documents</label>
                            <input type="file" name="documents[]" multiple 
                                   class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-medium file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Max 10MB per file. Multiple files allowed.</p>
                        </div>
                        
                        @if($case->documents && count($case->documents) > 0)
                        <div>
                            <h3 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Existing Documents</h3>
                            <div class="space-y-2">
                                @foreach($case->documents as $index => $document)
                                <div class="flex items-center justify-between p-2 border border-gray-200 dark:border-gray-700 rounded">
                                    <div class="flex items-center space-x-2">
                                        <i class="fas fa-file text-gray-400"></i>
                                        <span class="text-sm text-gray-900 dark:text-white truncate max-w-xs">{{ $document['name'] }}</span>
                                    </div>
                                    <a href="{{ route('cases.download-document', ['case' => $case->id, 'index' => $index]) }}" 
                                       class="text-blue-600 dark:text-blue-400 hover:text-blue-800" title="Download">
                                        <i class="fas fa-download"></i>
                                    </a>
                                </div>
                                @endforeach
                            </div>
                        </div>
                        @endif
                    </div>
                </div>

                <!-- Notes & Remarks -->
                <div class="card">
                    <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                        <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Notes & Remarks</h2>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="remarks" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Remarks</label>
                            <textarea id="remarks" name="remarks" rows="4"
                                      class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">{{ old('remarks', $case->remarks) }}</textarea>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form Actions -->
        <div class="mt-8 flex justify-end space-x-4">
            <a href="{{ route('cases.show', $case->id) }}" 
               class="px-6 py-3 border border-gray-300 dark:border-gray-600 text-gray-700 dark:text-gray-300 rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700">
                Cancel
            </a>
            <button type="submit" 
                    class="px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium">
                Update Case
            </button>
        </div>
    </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    let partyCounter = {{ $partyIndex }};
    
    // Add other party
    document.getElementById('add_other_party').addEventListener('click', function() {
        const container = document.getElementById('other_parties_container');
        const partyDiv = document.createElement('div');
        partyDiv.className = 'p-4 border border-gray-200 dark:border-gray-700 rounded-lg party-item';
        partyDiv.innerHTML = `
            <div class="flex justify-between items-center mb-3">
                <h4 class="font-medium text-gray-900 dark:text-white">Other Party ${partyCounter + 1}</h4>
                <button type="button" onclick="removeParty(this)" 
                        class="text-red-500 hover:text-red-700">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Name</label>
                    <input type="text" name="other_parties[${partyCounter}][name]" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Contact</label>
                    <input type="text" name="other_parties[${partyCounter}][contact]" 
                           class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Address</label>
                    <textarea name="other_parties[${partyCounter}][address]" rows="2"
                              class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg"></textarea>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role</label>
                    <select name="other_parties[${partyCounter}][role]" 
                            class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg">
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
        partyCounter++;
    });

    // Case status change handler
    document.getElementById('case_status').addEventListener('change', function() {
        const status = this.value;
        const nextOrderContainer = document.getElementById('next_order_container');
        const judgmentDateContainer = document.getElementById('judgment_date_container');
        
        if (status === 'in_hearing') {
            nextOrderContainer.style.display = 'block';
            judgmentDateContainer.style.display = 'none';
        } else if (status === 'closed') {
            nextOrderContainer.style.display = 'none';
            judgmentDateContainer.style.display = 'block';
        } else {
            nextOrderContainer.style.display = 'none';
            judgmentDateContainer.style.display = 'none';
        }
    });

    // Case type change handler
    document.getElementById('case_type_id').addEventListener('change', function() {
        const caseTypeId = this.value;
        const remedySelect = document.getElementById('case_remedy_id');
        
        if (caseTypeId) {
            fetch(`/cases/remedies/${caseTypeId}`)
                .then(response => response.json())
                .then(remedies => {
                    remedySelect.innerHTML = '<option value="">Select Remedy Type</option>';
                    remedies.forEach(remedy => {
                        const option = document.createElement('option');
                        option.value = remedy.id;
                        option.textContent = remedy.name;
                        remedySelect.appendChild(option);
                    });
                });
        }
    });
});

function removeParty(button) {
    button.closest('.party-item').remove();
}
</script>
@endsection