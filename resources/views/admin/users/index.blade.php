<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-secondary">Users</h2>
    </x-slot>

    <div class="max-w-5xl mx-auto py-8 px-4 space-y-6">
        @if (session('status'))
            <div class="p-3 rounded border border-green-200 bg-green-50 text-green-700">{{ session('status') }}</div>
        @endif

        @if ($errors->any())
            <div class="p-3 rounded border border-red-200 bg-red-50 text-red-700">
                <ul class="list-disc list-inside space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="bg-white shadow rounded p-6">
            <div class="flex items-center justify-between gap-4">
                <h3 class="text-lg font-semibold">Invite a User</h3>
                <button type="button" id="toggle-create-user-button" onclick="toggleCreateUserForm()" aria-expanded="false" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">Create User</button>
            </div>
            <form id="create-user-form" method="POST" action="{{ route('admin.users.store') }}" class="mt-4 grid grid-cols-1 md:grid-cols-2 gap-4 hidden">
                @csrf
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Name</label>
                    <input name="name" value="{{ old('name') }}" class="w-full border rounded px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                    <input type="email" name="email" value="{{ old('email') }}" class="w-full border rounded px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Password</label>
                    <input type="password" name="password" class="w-full border rounded px-3 py-2" required />
                </div>
                <div>
                    <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" required />
                </div>
                <div class="md:col-span-2 flex items-center space-x-2">
                    <input type="checkbox" name="is_admin" id="store-is-admin" value="1" class="accent-secondary text-secondary focus:ring-secondary" />
                    <label for="store-is-admin" class="text-sm text-gray-700">Grant admin access</label>
                </div>
                <div class="md:col-span-2">
                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">Create User</button>
                </div>
            </form>
        </div>

        @php($currentUser = auth()->user())

        <div class="bg-white shadow rounded p-6">
            <h3 class="text-lg font-semibold mb-4">Existing Users</h3>
            <div class="space-y-4">
                @forelse ($users as $user)
                    @php($isSelf = $currentUser && $currentUser->id === $user->id)
                    @php($isPrimaryAdmin = (bool) $user->primary_admin)
                    <details class="border rounded">
                        <summary class="px-4 py-3 flex items-center justify-between cursor-pointer select-none">
                            <div>
                                <p class="font-medium">{{ $user->name }}</p>
                                <p class="text-sm text-gray-500">{{ $user->email }}</p>
                            </div>
                            <span class="text-xs uppercase tracking-wide px-2 py-1 rounded {{ $user->is_admin ? 'bg-primary text-white' : 'bg-gray-100 text-gray-600' }}">
                                {{ $user->is_admin ? 'Admin' : 'User' }}
                            </span>
                        </summary>
                        <div class="px-4 pb-4">
                            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                @csrf
                                @method('PATCH')
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Name</label>
                                    <input name="name" value="{{ old('name', $user->name) }}" class="w-full border rounded px-3 py-2 {{ $isPrimaryAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ $isPrimaryAdmin ? 'readonly' : '' }} required />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Email</label>
                                    <input type="email" name="email" value="{{ old('email', $user->email) }}" class="w-full border rounded px-3 py-2 {{ $isPrimaryAdmin ? 'bg-gray-100 cursor-not-allowed' : '' }}" {{ $isPrimaryAdmin ? 'readonly' : '' }} required />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">New Password <span class="text-xs text-gray-500">(optional)</span></label>
                                    <input type="password" name="password" class="w-full border rounded px-3 py-2" />
                                </div>
                                <div>
                                    <label class="block text-sm text-gray-600 mb-1">Confirm Password</label>
                                    <input type="password" name="password_confirmation" class="w-full border rounded px-3 py-2" />
                                </div>
                                <div class="md:col-span-2 flex items-center space-x-2">
                                    @if ($isPrimaryAdmin)
                                        <input type="hidden" name="is_admin" value="1">
                                    @endif
                                    <input type="checkbox" name="is_admin" id="is-admin-{{ $user->id }}" value="1" class="accent-secondary text-secondary focus:ring-secondary disabled:cursor-not-allowed disabled:opacity-70" {{ $user->is_admin ? 'checked' : '' }} {{ $isPrimaryAdmin ? 'disabled' : '' }}>
                                    <label for="is-admin-{{ $user->id }}" class="text-sm text-gray-700">Admin access</label>
                                </div>
                                <div class="md:col-span-2 flex flex-wrap gap-3">
                                    <button type="submit" class="inline-flex items-center px-4 py-2 bg-primary text-white rounded hover:opacity-90">Save Changes</button>
                                    <button type="button" class="inline-flex items-center px-4 py-2 bg-red text-white rounded hover:opacity-90"
                                            onclick="openModal('{{ $isSelf ? 'cannot-self' : ($isPrimaryAdmin ? 'cannot-main-admin' : 'delete-user-' . $user->id) }}')">Delete</button>
                                </div>
                            </form>

                            @if (!$isSelf && !$isPrimaryAdmin)
                                <div id="delete-user-{{ $user->id }}" class="hidden fixed inset-0 z-50 items-center justify-center">
                                    <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal('delete-user-{{ $user->id }}')"></div>
                                    <div class="relative bg-white rounded shadow-lg max-w-md w-full mx-4 p-6">
                                        <h4 class="text-lg font-semibold mb-2">Delete User?</h4>
                                        <p class="text-sm text-gray-600 mb-4">This will permanently remove <strong>{{ $user->email }}</strong> from the system.</p>
                                        <div class="flex items-center justify-end gap-2">
                                            <button type="button" class="px-4 py-2 rounded border" onclick="closeModal('delete-user-{{ $user->id }}')">Cancel</button>
                                            <form method="POST" action="{{ route('admin.users.destroy', $user) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="px-4 py-2 bg-red text-white rounded hover:opacity-90">Confirm Delete</button>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </details>
                @empty
                    <div class="text-gray-500">No users found.</div>
                @endforelse
            </div>

            <div class="mt-4">
                {{ $users->links() }}
            </div>
        </div>
    </div>

    <div id="cannot-self" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal('cannot-self')"></div>
        <div class="relative bg-white rounded shadow-lg max-w-md w-full mx-4 p-6">
            <h4 class="text-lg font-semibold mb-2">Action not allowed</h4>
            <p class="text-sm text-gray-600 mb-4">You cannot delete your own account. Please ask another administrator to handle account changes if needed.</p>
            <div class="flex items-center justify-end gap-2">
                <button type="button" class="px-4 py-2 rounded border" onclick="closeModal('cannot-self')">Close</button>
            </div>
        </div>
    </div>

    <div id="cannot-main-admin" class="hidden fixed inset-0 z-50 items-center justify-center">
        <div class="absolute inset-0 bg-black bg-opacity-40" onclick="closeModal('cannot-main-admin')"></div>
        <div class="relative bg-white rounded shadow-lg max-w-md w-full mx-4 p-6">
            <h4 class="text-lg font-semibold mb-2">Protected account</h4>
            <p class="text-sm text-gray-600 mb-4">The primary Admin account cannot be deleted. Create another admin if you need to transfer ownership.</p>
            <div class="flex items-center justify-end gap-2">
                <button type="button" class="px-4 py-2 rounded border" onclick="closeModal('cannot-main-admin')">Close</button>
            </div>
        </div>
    </div>

    <script>
        function toggleCreateUserForm() {
            const form = document.getElementById('create-user-form');
            const button = document.getElementById('toggle-create-user-button');
            if (!form || !button) return;

            const isHidden = form.classList.contains('hidden');

            if (isHidden) {
                form.classList.remove('hidden');
                button.setAttribute('aria-expanded', 'true');
                button.textContent = 'Hide form';
            } else {
                form.classList.add('hidden');
                button.setAttribute('aria-expanded', 'false');
                button.textContent = 'Create User';
            }
        }

        function openModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('hidden');
            el.classList.add('flex');
        }

        function closeModal(id) {
            const el = document.getElementById(id);
            if (!el) return;
            el.classList.remove('flex');
            el.classList.add('hidden');
        }
    </script>
</x-app-layout>
