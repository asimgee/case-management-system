@extends('layouts.app')

@section('title', 'Create Role')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Create New Role</h1>
        <a href="{{ route('admin.roles.index') }}" class="btn-secondary">
            <i class="fas fa-arrow-left mr-2"></i> Back to Roles
        </a>
    </div>

    @include('admin.roles.form')
</div>
@endsection
