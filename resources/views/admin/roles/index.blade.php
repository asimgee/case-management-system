<!-- [file name]: index.blade.php -->
@extends('layouts.app')

@section('title', 'Roles & Permissions')

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-6">
        <h1 class="text-2xl font-bold text-gray-900 dark:text-white">Roles & Permissions</h1>

        @if(auth()->user()->hasPermission('roles.create'))
        <a href="{{ route('admin.roles.create') }}" class="btn-primary flex items-center space-x-2">
            <i class="fas fa-plus"></i>
            <span>Create Role</span>
        </a>
        @endif
    </div>

    <!-- Roles Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @forelse($roles as $role)
            <div class="card card-hover">
                <div class="card-body">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">{{ $role->name }}</h3>
                            @if($role->is_default)
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900/30 dark:text-green-400">
                                    Default Role
                                </span>
                            @endif
                        </div>
                        <div class="flex items-center space-x-2">
                            @if(auth()->user()->hasPermission('roles.edit'))
                            <a href="{{ route('admin.roles.edit', $role) }}" class="text-blue-600 hover:text-blue-900 dark:text-blue-400 dark:hover:text-blue-300" title="Edit Role">
                                <i class="fas fa-edit"></i>
                            </a>
                            @endif

                            @if(auth()->user()->hasPermission('roles.delete'))
                            @if(!$role->is_default && $role->users_count == 0)
                                <form action="{{ route('admin.roles.destroy', $role) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure you want to delete this role?')" class="text-red-600 hover:text-red-900 dark:text-red-400 dark:hover:text-red-300" title="Delete Role">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            @endif
                            @endif
                        </div>
                    </div>

                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">{{ $role->description }}</p>

                    <div class="mb-4">
                        <span class="text-sm font-medium text-gray-700 dark:text-gray-300">Users: {{ $role->users_count }}</span>
                    </div>

                    <div class="border-t border-gray-200 dark:border-gray-700 pt-4">
                        <h4 class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Key Permissions:</h4>
                        <div class="flex flex-wrap gap-1">
                            @foreach(array_slice($role->permissions ?? [], 0, 5) as $permission)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    {{ $permission }}
                                </span>
                            @endforeach
                            @if(count($role->permissions ?? []) > 5)
                                <span class="inline-flex items-center px-2 py-1 rounded text-xs font-medium bg-gray-100 text-gray-800 dark:bg-gray-700 dark:text-gray-300">
                                    +{{ count($role->permissions) - 5 }} more
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <p class="text-center text-gray-500 dark:text-gray-400 col-span-3">No roles found.</p>
        @endforelse
    </div>
</div>
@endsection
