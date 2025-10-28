<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-primary leading-tight text-secondary">Client Apps</h2>
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
                            <div class="md:col-span-2 grid grid-cols-1 md:grid-cols-3 gap-3">
                                <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 rounded text-white bg-secondary hover:opacity-90"
                                        onclick="openModal('regen-{{ $app->id }}')">Regenerate Keys</button>
                                <button type="submit" class="w-full inline-flex items-center justify-center px-4 py-2 rounded text-white bg-primary hover:opacity-90">Save Changes</button>
                                <button type="button" class="w-full inline-flex items-center justify-center px-4 py-2 rounded text-white bg-red hover:opacity-90"
                                        onclick="openModal('del-{{ $app->id }}')">Delete</button>
                            </div>
                        </form>

                        <!-- Regenerate Modal -->
                        <div id="regen-{{ $app->id }}" class="hidden fixed inset-0 z-50 items-center justify-center">
                            <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal('regen-{{ $app->id }}')"></div>
                            <div class="relative bg-white rounded shadow-lg max-w-md w-full mx-4 p-6">
                                <h4 class="text-lg font-semibold mb-2">Regenerate API Credentials?</h4>
                                <p class="text-sm text-gray-600 mb-4">This will create a new API Key and Secret for <strong>{{ $app->name }}</strong>. Existing systems using the old keys will stop working until you update them.</p>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="px-4 py-2 rounded border" onclick="closeModal('regen-{{ $app->id }}')">Cancel</button>
                                    <form method="POST" action="{{ route('admin.client-apps.regenerate', $app) }}">
                                        @csrf
                                        <button type="submit" class="px-4 py-2 bg-yellow-600 text-white rounded hover:opacity-90">Yes, Regenerate</button>
                                    </form>
                                </div>
                            </div>
                        </div>

                        <!-- Delete Modal -->
                        <div id="del-{{ $app->id }}" class="hidden fixed inset-0 z-50 items-center justify-center">
                            <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal('del-{{ $app->id }}')"></div>
                            <div class="relative bg-white rounded shadow-lg max-w-md w-full mx-4 p-6">
                                <h4 class="text-lg font-semibold mb-2">Delete Client App?</h4>
                                <p class="text-sm text-gray-600 mb-4">Deleting <strong>{{ $app->name }}</strong> is irreversible. All existing keys for this app will become invalid. Ensure dependent systems are updated.</p>
                                <div class="flex items-center justify-end gap-2">
                                    <button type="button" class="px-4 py-2 rounded border" onclick="closeModal('del-{{ $app->id }}')">Cancel</button>
                                    <form method="POST" action="{{ route('admin.client-apps.destroy', $app) }}">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-4 py-2 bg-red text-white rounded hover:opacity-90">Yes, Delete</button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                </details>
            @empty
                <div class="text-gray-500">No client apps registered yet.</div>
            @endforelse
        </div>
    </div>
    <script>
        function openModal(id){
            var el = document.getElementById(id);
            if(!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
        }
        function closeModal(id){
            var el = document.getElementById(id);
            if(!el) return;
            el.classList.remove('flex');
            el.classList.add('hidden');
        }
    </script>
</x-app-layout>

