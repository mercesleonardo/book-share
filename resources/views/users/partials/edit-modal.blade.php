<form action="{{ route('users.update', $user) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
    @csrf
    @method('PUT')
    <div>
        <label for="name" class="block">{{ __('Name') }}</label>
        <input type="text" name="name" id="name" class="w-full border rounded px-3 py-2" value="{{ old('name', $user->name) }}" required>
        @error('name') <div class="text-red-600">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="email" class="block">{{ __('Email') }}</label>
        <input type="email" name="email" id="email" class="w-full border rounded px-3 py-2" value="{{ old('email', $user->email) }}" required>
        @error('email') <div class="text-red-600">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="profile_photo" class="block">{{ __('Profile Photo') }}</label>
        <input type="file" name="profile_photo" id="profile_photo" class="w-full border rounded px-3 py-2">
        @error('profile_photo') <div class="text-red-600">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="description" class="block">{{ __('Description') }}</label>
        <textarea name="description" id="description" class="w-full border rounded px-3 py-2">{{ old('description', $user->description) }}</textarea>
        @error('description') <div class="text-red-600">{{ $message }}</div> @enderror
    </div>
    <div>
        <label for="role" class="block">{{ __('Role') }}</label>
        <select name="role" id="role" class="w-full border rounded px-3 py-2" required>
            <option value="user" {{ old('role', $user->role->value) == 'user' ? 'selected' : '' }}>{{ __('User') }}</option>
            <option value="admin" {{ old('role', $user->role->value) == 'admin' ? 'selected' : '' }}>{{ __('Admin') }}</option>
        </select>
        @error('role') <div class="text-red-600">{{ $message }}</div> @enderror
    </div>
    <div class="flex justify-end gap-2">
        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded">{{ __('Update') }}</button>
        <button type="button" class="px-4 py-2 bg-gray-300 dark:bg-gray-700 text-gray-800 dark:text-gray-200 rounded" @click="$dispatch('close')">{{ __('Cancel') }}</button>
    </div>
</form>
