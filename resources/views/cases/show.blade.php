@extends('layouts.app')

@section('title', 'View Case - ' . $case->case_number)

@section('content')
<div class="fade-in">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between mb-6">
        <div class="flex items-center space-x-4">
            <a href="{{ route('cases.index') }}" class="text-gray-600 dark:text-gray-400 hover:text-blue-600 dark:hover:text-blue-400">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 dark:text-white">Case Details</h1>
                <p class="text-gray-600 dark:text-gray-400 mt-1">{{ $case->case_number }} - {{ $case->case_title }}</p>
            </div>
        </div>
        <div class="flex space-x-3 mt-4 lg:mt-0">
            <a href="{{ route('cases.edit', $case->id) }}" class="btn-secondary">
                <i class="fas fa-edit mr-2"></i>Edit
            </a>
            <form action="{{ route('cases.destroy', $case->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this case?')">
                @csrf
                @method('DELETE')
                <button type="submit" class="btn-danger">
                    <i class="fas fa-trash mr-2"></i>Delete
                </button>
            </form>
        </div>
    </div>

    <!-- Case Status Badge -->
    <div class="mb-6">
        <span class="px-4 py-2 inline-flex text-sm leading-5 font-semibold rounded-full {{ App\Http\Controllers\CaseController::getStatusColor($case->case_status) }}">
            {{ ucfirst(str_replace('_', ' ', $case->case_status)) }}
        </span>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column - Basic Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Case Overview Card -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Case Overview</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Case Number</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->case_number }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Case Type</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->caseType->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Remedy Type</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->caseRemedy->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Court Type</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->courtType->name ?? 'N/A' }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Court Name</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->court_name }}</p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Filing Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ \Carbon\Carbon::parse($case->filing_date)->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>

            <!-- Parties Information -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Parties Information</h2>
                </div>
                <div class="space-y-6">
                    <!-- First Party -->
                    <div>
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">First Party - {{ $case->first_party_title }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->first_party_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Contact</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->first_party_contact ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">CNIC</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->first_party_cnic ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Council For</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->first_party_council ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->first_party_address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Second Party -->
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Second Party - {{ $case->second_party_title }}</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Name</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->second_party_name }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Contact</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->second_party_contact ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">CNIC</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->second_party_cnic ?? 'N/A' }}</p>
                            </div>
                            <div>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Council For</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->second_party_council ?? 'N/A' }}</p>
                            </div>
                            <div class="md:col-span-2">
                                <p class="text-sm text-gray-500 dark:text-gray-400">Address</p>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $case->second_party_address ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Other Parties -->
                    @if($case->other_parties && count($case->other_parties) > 0)
                    <div class="pt-6 border-t border-gray-200 dark:border-gray-700">
                        <h3 class="text-lg font-medium text-gray-900 dark:text-white mb-4">Other Parties</h3>
                        <div class="space-y-4">
                            @foreach($case->other_parties as $index => $party)
                            <div class="p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                                <h4 class="font-medium text-gray-900 dark:text-white mb-2">{{ $party['name'] ?? 'N/A' }}</h4>
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-2 text-sm">
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Contact:</span>
                                        <span class="ml-2 text-gray-900 dark:text-white">{{ $party['contact'] ?? 'N/A' }}</span>
                                    </div>
                                    <div>
                                        <span class="text-gray-500 dark:text-gray-400">Role:</span>
                                        <span class="ml-2 text-gray-900 dark:text-white">{{ $party['role'] ?? 'N/A' }}</span>
                                    </div>
                                    <div class="md:col-span-2">
                                        <span class="text-gray-500 dark:text-gray-400">Address:</span>
                                        <span class="ml-2 text-gray-900 dark:text-white">{{ $party['address'] ?? 'N/A' }}</span>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Criminal Details -->
            @if($case->fir_no || $case->offence)
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">FIR Details</h2>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    @if($case->fir_no)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">FIR No</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->fir_no }}</p>
                    </div>
                    @endif
                    @if($case->fir_year)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">FIR Year</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->fir_year }}</p>
                    </div>
                    @endif
                    @if($case->offence)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Offence</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->offence }}</p>
                    </div>
                    @endif
                    @if($case->police_station)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Police Station</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">{{ $case->police_station }}</p>
                    </div>
                    @endif
                    @if($case->other_fir_details)
                    <div class="md:col-span-2">
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-2">Other FIR Details</h3>
                        <p class="text-gray-900 dark:text-white">{{ $case->other_fir_details }}</p>
                    </div>
                    @endif
                </div>
            </div>
            @endif

            <!-- Documents -->
            @if($case->documents && count($case->documents) > 0)
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Documents</h2>
                </div>
                <div class="space-y-3">
                    @foreach($case->documents as $index => $document)
                    <div class="flex items-center justify-between p-3 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center space-x-3">
                            <i class="fas fa-file text-gray-400 text-xl"></i>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">{{ $document['name'] }}</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    {{ $document['category'] ?? 'Other' }} • 
                                    {{ round($document['size'] / 1024, 2) }} KB
                                </p>
                            </div>
                        </div>
                        <div class="flex space-x-2">
                            <a href="{{ route('cases.download-document', ['case' => $case->id, 'index' => $index]) }}" 
                               class="text-blue-600 dark:text-blue-400 hover:text-blue-800 dark:hover:text-blue-300">
                                <i class="fas fa-download"></i>
                            </a>
                            @if(Auth::user()->role === 'admin' || $case->user_id === Auth::id())
                            <form action="{{ route('cases.remove-document', ['case' => $case->id, 'index' => $index]) }}" 
                                  method="POST" 
                                  onsubmit="return confirm('Are you sure you want to remove this document?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-600 dark:text-red-400 hover:text-red-800 dark:hover:text-red-300">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
            @endif
        </div>

        <!-- Right Column - Side Information -->
        <div class="space-y-6">
            <!-- Timeline Card -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Case Timeline</h2>
                </div>
                <div class="space-y-4">
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Next Hearing Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $case->next_date ? \Carbon\Carbon::parse($case->next_date)->format('M d, Y') : 'Not Scheduled' }}
                        </p>
                    </div>
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Judgment Date</h3>
                        <p class="text-lg font-semibold text-gray-900 dark:text-white">
                            {{ $case->judgment_date ? \Carbon\Carbon::parse($case->judgment_date)->format('M d, Y') : 'Not Decided' }}
                        </p>
                    </div>
                    @if($case->next_order)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Next Order</h3>
                        <p class="text-gray-900 dark:text-white">{{ $case->next_order }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Lawyers Card -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Legal Team</h2>
                </div>
                <div class="space-y-4">
                    @if($case->assignedLawyer)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Assigned Lawyer</h3>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $case->assignedLawyer->name }}</p>
                        @if($case->assignedLawyer->phone)
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $case->assignedLawyer->phone }}</p>
                        @endif
                    </div>
                    @endif
                    @if($case->client_type)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Client Type</h3>
                        <p class="font-medium text-gray-900 dark:text-white">
                            @if($case->client_type == 1)
                                Plaintiff / Petitioner / Appellant
                            @elseif($case->client_type == 2)
                                Defendant / Respondent
                            @endif
                        </p>
                    </div>
                @endif

                    @if($case->offending_lawyer)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Offending Lawyer</h3>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $case->offending_lawyer }}</p>
                    </div>
                    @endif

                </div>
            </div>

            <!-- Notes & Remarks -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white"> Remarks</h2>
                </div>
                <div class="space-y-4">
                    @if($case->case_notes)
                    <div>
                        <h3 class="text-sm font-medium text-gray-500 dark:text-gray-400 mb-1">Case Notes</h3>
                        <p class="text-gray-900 dark:text-white">{{ $case->case_notes }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Case Owner -->
            <div class="card">
                <div class="border-b border-gray-200 dark:border-gray-700 pb-4 mb-4">
                    <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Case Owner</h2>
                </div>
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-blue-100 dark:bg-blue-900 rounded-full flex items-center justify-center">
                        <i class="fas fa-user text-blue-600 dark:text-blue-400"></i>
                    </div>
                    <div>
                        <p class="font-medium text-gray-900 dark:text-white">{{ $case->user->name ?? 'Unknown' }}</p>
                        <p class="text-sm text-gray-600 dark:text-gray-400">{{ $case->user->email ?? '' }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection