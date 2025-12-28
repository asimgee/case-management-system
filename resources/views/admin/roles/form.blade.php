<!-- [file name]: form.blade.php -->
@php
    $isEdit = isset($role);
@endphp

<div class="card">
    <div class="card-body">
        <form action="{{ $isEdit ? route('admin.roles.update', $role) : route('admin.roles.store') }}" method="POST">
            @csrf
            @if($isEdit)
                @method('PUT')
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Basic Information -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">
                        {{ $isEdit ? 'Edit Role' : 'Basic Information' }}
                    </h3>

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Role Name *</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $role->name ?? '') }}" required
                               class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">
                        @error('name')
                            <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label for="description" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Description</label>
                        <textarea name="description" id="description" rows="3"
                                  class="w-full px-4 py-2 border border-gray-300 dark:border-gray-600 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-transparent bg-white dark:bg-gray-700 text-gray-900 dark:text-white">{{ old('description', $role->description ?? '') }}</textarea>
                    </div>

                    <div class="flex items-center">
                        <input type="checkbox" name="is_default" id="is_default" value="1" {{ old('is_default', $role->is_default ?? false) ? 'checked' : '' }}
                               class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                        <label for="is_default" class="ml-2 text-sm font-medium text-gray-900 dark:text-gray-300">Set as Default Role</label>
                    </div>
                </div>

                <!-- Permissions -->
                <div class="space-y-6">
                    <h3 class="text-lg font-medium text-gray-900 dark:text-white">Permissions *</h3>

                    <div class="max-h-96 overflow-y-auto border border-gray-200 dark:border-gray-700 rounded-lg p-4">
                        <div class="space-y-4">
                            @foreach($permissions as $key => $label)
                                <div class="flex items-center">
                                    <input type="checkbox" name="permissions[]" id="permission_{{ $key }}" value="{{ $key }}"
                                           {{ in_array($key, old('permissions', $role->permissions ?? [])) ? 'checked' : '' }}
                                           class="w-4 h-4 text-blue-600 bg-gray-100 border-gray-300 rounded focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-800 focus:ring-2 dark:bg-gray-700 dark:border-gray-600">
                                    <label for="permission_{{ $key }}" class="ml-2 text-sm font-medium text-gray-700 dark:text-gray-300">
                                        {{ $label }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                    @error('permissions')
                        <p class="mt-1 text-sm text-red-600 dark:text-red-400">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="mt-8 flex justify-end space-x-4">
                <a href="{{ route('admin.roles.index') }}" class="btn-secondary">Cancel</a>
                <button type="submit" class="btn-primary">{{ $isEdit ? 'Update Role' : 'Create Role' }}</button>
            </div>
        </form>
    </div>
</div>
