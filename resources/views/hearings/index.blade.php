@extends('layouts.app')

@section('content')
<div class="container-fluid">
    <div class="row mb-4">
        <div class="col-12">
            <div class="d-flex justify-content-between align-items-center">
                <h1 class="h3 mb-0">Hearings</h1>
                <a href="{{ route('hearings.create') }}" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Add New Hearing
                </a>
            </div>
        </div>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body">
            <form method="GET" action="{{ route('hearings.index') }}">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select name="status" id="status" class="form-select">
                            <option value="">All Statuses</option>
                            @foreach($statusOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('status') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="type" class="form-label">Type</label>
                        <select name="type" id="type" class="form-select">
                            <option value="">All Types</option>
                            @foreach($typeOptions as $value => $label)
                                <option value="{{ $value }}" {{ request('type') == $value ? 'selected' : '' }}>
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_from" class="form-label">Date From</label>
                        <input type="date" name="date_from" id="date_from" class="form-control" 
                               value="{{ request('date_from') }}">
                    </div>
                    <div class="col-md-3 mb-3">
                        <label for="date_to" class="form-label">Date To</label>
                        <input type="date" name="date_to" id="date_to" class="form-control" 
                               value="{{ request('date_to') }}">
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="search" class="form-label">Search</label>
                        <input type="text" name="search" id="search" class="form-control" 
                               value="{{ request('search') }}" placeholder="Search by purpose, location, case number...">
                    </div>
                    @if(auth()->user()->role === 'admin')
                    <div class="col-md-3 mb-3">
                        <label for="user_id" class="form-label">User</label>
                        <select name="user_id" id="user_id" class="form-select">
                            <option value="">All Users</option>
                            @foreach(\App\Models\User::all() as $user)
                                <option value="{{ $user->id }}" {{ request('user_id') == $user->id ? 'selected' : '' }}>
                                    {{ $user->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="col-md-3 mb-3 d-flex align-items-end">
                        <button type="submit" class="btn btn-primary me-2">
                            <i class="fas fa-filter"></i> Filter
                        </button>
                        <a href="{{ route('hearings.index') }}" class="btn btn-secondary">
                            <i class="fas fa-redo"></i> Reset
                        </a>
                    </div>
                </div>
            </form>
        </div>
    </div>

    <!-- Hearings List -->
    <div class="card">
        <div class="card-body">
            @if($hearings->isEmpty())
                <div class="text-center py-5">
                    <i class="fas fa-gavel fa-3x text-muted mb-3"></i>
                    <p class="text-muted">No hearings found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover">
                        <thead>
                            <tr>
                                <th>Case Number</th>
                                <th>Date & Time</th>
                                <th>Type</th>
                                <th>Location</th>
                                <th>Judge</th>
                                <th>Purpose</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($hearings as $hearing)
                                <tr>
                                    <td>
                                        <a href="{{ route('cases.show', $hearing->case_id) }}">
                                            {{ $hearing->case->case_number ?? 'N/A' }}
                                        </a>
                                    </td>
                                    <td>
                                        <div>{{ $hearing->formatted_hearing_date }}</div>
                                        <small class="text-muted">{{ $hearing->formatted_hearing_time }}</small>
                                    </td>
                                    <td>
                                        <span class="badge bg-info">{{ $hearing->hearing_type_label }}</span>
                                    </td>
                                    <td>{{ Str::limit($hearing->location, 30) }}</td>
                                    <td>{{ Str::limit($hearing->judge, 20) ?: 'N/A' }}</td>
                                    <td>{{ Str::limit($hearing->purpose, 50) }}</td>
                                    <td>
                                        <span class="badge {{ $hearing->status_color }}">
                                            {{ $hearing->hearing_status }}
                                        </span>
                                    </td>
                                    <td>
                                        <div class="btn-group" role="group">
                                            <a href="{{ route('hearings.show', $hearing->id) }}" 
                                               class="btn btn-sm btn-info" title="View">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                            <a href="{{ route('hearings.edit', $hearing->id) }}" 
                                               class="btn btn-sm btn-primary" title="Edit">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                            @if($hearing->is_upcoming)
                                                <a href="{{ route('hearings.complete', $hearing->id) }}" 
                                                   class="btn btn-sm btn-success" title="Mark as Completed"
                                                   onclick="return confirm('Mark this hearing as completed?')">
                                                    <i class="fas fa-check"></i>
                                                </a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div class="d-flex justify-content-center mt-4">
                    {{ $hearings->links() }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection