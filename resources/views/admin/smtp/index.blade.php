<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            SMTP Settings
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    @if (session('status'))
                        <div class="mb-4 text-green-700 bg-green-50 border border-green-200 rounded px-3 py-2">{{ session('status') }}</div>
                    @endif

                    @if (!($edit ?? false))
                        <div class="space-y-4">
                            <div>
                                <div class="text-sm text-gray-500">Mail Username</div>
                                <div class="text-gray-900 font-medium">{{ $settings->username ?? '—' }}</div>
                            </div>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div>
                                    <div class="text-sm text-gray-500">From Address</div>
                                    <div class="text-gray-900 font-medium">{{ $settings->from_address ?? '—' }}</div>
                                </div>
                                <div>
                                    <div class="text-sm text-gray-500">From Name</div>
                                    <div class="text-gray-900 font-medium">{{ $settings->from_name ?? '—' }}</div>
                                </div>
                            </div>
                            <div class="flex items-center justify-between">
                                <div class="text-sm text-gray-500">Password</div>
                                <div class="text-gray-900">••••••••</div>
                            </div>
                            <div class="pt-2">
                                <a href="{{ route('admin.smtp.index', ['edit' => 1]) }}" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded-md">Edit</a>
                            </div>
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.smtp.update') }}" class="space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Mail Username</label>
                                <input type="text" name="username" value="{{ old('username', $settings->username ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                @error('username')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Address</label>
                                <input type="email" name="from_address" value="{{ old('from_address', $settings->from_address ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                @error('from_address')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">From Name</label>
                                <input type="text" name="from_name" value="{{ old('from_name', $settings->from_name ?? '') }}" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                @error('from_name')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700">SMTP Password</label>
                                <input type="password" name="password" placeholder="Leave blank to keep current" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm" />
                                @error('password')<div class="text-red-600 text-sm">{{ $message }}</div>@enderror
                                <p class="text-xs text-gray-500 mt-1">Leave blank to keep existing password. Password is stored encrypted.</p>
                            </div>

                            <div class="pt-2 flex items-center gap-2">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded-md">Save Settings</button>
                                <a href="{{ route('admin.smtp.index') }}" class="inline-flex items-center px-4 py-2 border rounded-md">Cancel</a>
                            </div>
                        </form>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
