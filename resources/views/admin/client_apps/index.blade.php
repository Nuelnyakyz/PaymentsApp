<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight">Client Apps</h2>
    </x-slot>

    <div class="py-10 max-w-3xl mx-auto px-4 sm:px-8 lg:px-12">
        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-800">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="mb-4 p-3 rounded bg-red-50 text-red-800">
                <ul class="list-disc list-inside">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded p-6 mb-8 mt-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Register New App</h3>
            </div>
            <form method="POST" action="{{ route('admin.client-apps.store') }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Name</label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Callback URL</label>
                    <input name="callback_url" value="{{ old('callback_url') }}" class="w-full border rounded px-3 py-2" placeholder="https://example.com/return" />
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">
                        <span class="mr-2">+</span> Register App
                    </button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-semibold">Registered Apps</h3>
            </div>

            @forelse ($apps as $app)
                <details class="mb-3 border rounded">
                    <summary class="cursor-pointer select-none px-4 py-3 flex items-center justify-between">
                        <span class="font-medium">{{ $app->name }}</span>
                        <span class="text-sm text-gray-500">API Key: {{ $app->api_key }}</span>
                    </summary>
                    <div class="px-4 pb-4">
                        <form method="POST" action="{{ route('admin.client-apps.update', $app) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @csrf
                            @method('PATCH')
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Name</label>
                                <input name="name" value="{{ old('name', $app->name) }}" class="w-full border rounded px-3 py-2" required />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">Callback URL</label>
                                <input name="callback_url" value="{{ old('callback_url', $app->callback_url) }}" class="w-full border rounded px-3 py-2" />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">API Key</label>
                                <input value="{{ $app->api_key }}" class="w-full border rounded px-3 py-2 bg-gray-50" readonly />
                            </div>
                            <div>
                                <label class="block text-sm text-gray-600 mb-1">API Secret</label>
                                <input value="{{ $app->api_secret }}" class="w-full border rounded px-3 py-2 bg-gray-50" readonly />
                            </div>
                            <div class="md:col-span-2 flex items-center justify-between">
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-secondary text-white rounded hover:opacity-90">Save Changes</button>
                                <span></span>
                            </div>
                        </form>
                        <div class="mt-3 flex justify-end">
                            <form method="POST" action="{{ route('admin.client-apps.destroy', $app) }}" onsubmit="return confirm('Delete this app?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-4 py-2 bg-red-600 text-white rounded hover:opacity-90">Delete</button>
                            </form>
                        </div>
                    </div>
                </details>
            @empty
                <div class="text-gray-500">No client apps registered yet.</div>
            @endforelse
        </div>
    </div>
</x-app-layout>

