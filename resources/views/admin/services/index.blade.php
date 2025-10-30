<x-app-layout>
    <x-slot name="header">
        <h1 class="text-2xl font-semibold text-secondary">Services</h1>
    </x-slot>

    <div class="max-w-6xl mx-auto p-4">
        <div class="flex items-center justify-between mb-6">
            <div class="text-gray-600">Manage gateway credentials per environment.</div>
            <form method="get" action="{{ route('admin.services.index') }}" class="flex items-center space-x-2">
                <label for="env" class="text-sm text-gray-600">Environment</label>
                <select id="env" name="env" class="border rounded pl-2 pr-3 py-1 w-30" onchange="this.form.submit()">
                    <option value="sandbox" {{ ($environment ?? 'sandbox') === 'sandbox' ? 'selected' : '' }}>Sandbox</option>
                    <option value="live" {{ ($environment ?? 'sandbox') === 'live' ? 'selected' : '' }}>Live</option>
                </select>
            </form>
        </div>

        @if (session('status'))
            <div class="mb-4 p-3 rounded bg-green-50 text-green-700 border border-green-100">{{ session('status') }}</div>
        @endif

        @error('general')
            <div class="mb-4 p-3 rounded bg-red-50 text-red-700 border border-red-100">{{ $message }}</div>
        @enderror

        <div x-data="{ tab: 'mpesa' }" class="space-y-6">
            <div class="flex space-x-2 border-b">
                <button type="button" @click="tab='mpesa'" class="px-4 py-2 border-b-2" :class="tab==='mpesa' ? 'border-primary text-secondary' : 'border-transparent text-gray-500'">M-Pesa</button>
                <button type="button" @click="tab='airtel'" class="px-4 py-2 border-b-2" :class="tab==='airtel' ? 'border-primary text-secondary' : 'border-transparent text-gray-500'">Airtel</button>
                <button type="button" @click="tab='visa'" class="px-4 py-2 border-b-2" :class="tab==='visa' ? 'border-primary text-secondary' : 'border-transparent text-gray-500'">Visa (CyberSource)</button>
            </div>

            <div x-show="tab==='mpesa'" class="space-y-4" x-data="{ editing: false }">
                <form method="post" action="{{ route('admin.services.mpesa.update') }}" class="space-y-4" @submit.prevent="if (!editing) { editing = true; return; } $el.submit();">
                    @csrf
                    <input type="hidden" name="environment" value="{{ $environment }}">
                    <input type="hidden" name="is_active" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700">Active</label>
                            <input type="checkbox" name="is_active" value="1" {{ optional($mpesa)->is_active ? 'checked' : '' }} :disabled="!editing" class="accent-secondary text-secondary focus:ring-secondary">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Shortcode</label>
                            <input name="shortcode" class="w-full border rounded px-3 py-2" value="{{ old('shortcode', optional($mpesa)->shortcode) }}" :disabled="!editing">
                            @error('shortcode')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">API Base URL</label>
                            <input name="api_base_url" class="w-full border rounded px-3 py-2" value="{{ old('api_base_url', optional($mpesa)->api_base_url) }}" :disabled="!editing">
                            @error('api_base_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Callback URL</label>
                            <input name="callback_url" class="w-full border rounded px-3 py-2" value="{{ old('callback_url', optional($mpesa)->callback_url) }}" :disabled="!editing">
                            @error('callback_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Timeout URL</label>
                            <input name="timeout_url" class="w-full border rounded px-3 py-2" value="{{ old('timeout_url', optional($mpesa)->timeout_url) }}" :disabled="!editing">
                            @error('timeout_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Result URL</label>
                            <input name="result_url" class="w-full border rounded px-3 py-2" value="{{ old('result_url', optional($mpesa)->result_url) }}" :disabled="!editing">
                            @error('result_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Consumer Key</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="consumer_key" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('consumer_key', optional($mpesa)->consumer_key) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle consumer key visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('consumer_key')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Consumer Secret</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="consumer_secret" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('consumer_secret', optional($mpesa)->consumer_secret) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle consumer secret visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('consumer_secret')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Passkey</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="passkey" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('passkey', optional($mpesa)->passkey) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle passkey visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('passkey')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Initiator Name</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="initiator_name" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('initiator_name', optional($mpesa)->initiator_name) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle initiator name visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('initiator_name')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Initiator Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="initiator_password" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('initiator_password', optional($mpesa)->initiator_password) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle initiator password visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('initiator_password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Security Credential</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="security_credential" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('security_credential', optional($mpesa)->security_credential) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle security credential visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('security_credential')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-primary text-white rounded" type="submit" x-text="editing ? 'Save changes' : 'Edit'"></button>
                    </div>
                </form>
            </div>

            <div x-show="tab==='airtel'" x-cloak class="space-y-4" x-data="{ editing: false }">
                <form method="post" action="{{ route('admin.services.airtel.update') }}" class="space-y-4" @submit.prevent="if (!editing) { editing = true; return; } $el.submit();">
                    @csrf
                    <input type="hidden" name="environment" value="{{ $environment }}">
                    <input type="hidden" name="is_active" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700">Active</label>
                            <input type="checkbox" name="is_active" value="1" {{ optional($airtel)->is_active ? 'checked' : '' }} :disabled="!editing" class="accent-secondary text-secondary focus:ring-secondary">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">X-Reference-Id</label>
                            <input name="x_reference_id" class="w-full border rounded px-3 py-2" value="{{ old('x_reference_id', optional($airtel)->x_reference_id) }}" :disabled="!editing">
                            @error('x_reference_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Country</label>
                            <input name="country" class="w-full border rounded px-3 py-2" value="{{ old('country', optional($airtel)->country) }}" :disabled="!editing">
                            @error('country')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Currency</label>
                            <input name="currency" class="w-full border rounded px-3 py-2" value="{{ old('currency', optional($airtel)->currency) }}" :disabled="!editing">
                            @error('currency')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">API Base URL</label>
                            <input name="api_base_url" class="w-full border rounded px-3 py-2" value="{{ old('api_base_url', optional($airtel)->api_base_url) }}" :disabled="!editing">
                            @error('api_base_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Callback URL</label>
                            <input name="callback_url" class="w-full border rounded px-3 py-2" value="{{ old('callback_url', optional($airtel)->callback_url) }}" :disabled="!editing">
                            @error('callback_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">API Key</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="api_key" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('api_key', optional($airtel)->api_key) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle API key visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('api_key')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">API Secret</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="api_secret" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('api_secret', optional($airtel)->api_secret) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle API secret visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('api_secret')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Public Key</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="public_key" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('public_key', optional($airtel)->public_key) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle public key visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('public_key')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Username</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="username" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('username', optional($airtel)->username) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle username visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0 a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('username')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="password" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('password', optional($airtel)->password) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle Airtel password visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0 a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('password')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-primary text-white rounded" type="submit" x-text="editing ? 'Save changes' : 'Edit'"></button>
                    </div>
                </form>
            </div>

            <div x-show="tab==='visa'" x-cloak class="space-y-4" x-data="{ editing: false }">
                <form method="post" action="{{ route('admin.services.visa.update') }}" class="space-y-4" @submit.prevent="if (!editing) { editing = true; return; } $el.submit();">
                    @csrf
                    <input type="hidden" name="environment" value="{{ $environment }}">
                    <input type="hidden" name="is_active" value="0">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm text-gray-700">Active</label>
                            <input type="checkbox" name="is_active" value="1" {{ optional($visa)->is_active ? 'checked' : '' }} :disabled="!editing" class="accent-secondary text-secondary focus:ring-secondary">
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">API Base URL</label>
                            <input name="api_base_url" class="w-full border rounded px-3 py-2" value="{{ old('api_base_url', optional($visa)->api_base_url) }}" :disabled="!editing">
                            @error('api_base_url')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Webhook Endpoint</label>
                            <input name="webhook_endpoint" class="w-full border rounded px-3 py-2" value="{{ old('webhook_endpoint', optional($visa)->webhook_endpoint) }}" :disabled="!editing">
                            @error('webhook_endpoint')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Merchant ID</label>
                            <input name="merchant_id" class="w-full border rounded px-3 py-2" value="{{ old('merchant_id', optional($visa)->merchant_id) }}" :disabled="!editing">
                            @error('merchant_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">API Key ID</label>
                            <input name="api_key_id" class="w-full border rounded px-3 py-2" value="{{ old('api_key_id', optional($visa)->api_key_id) }}" :disabled="!editing">
                            @error('api_key_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-sm text-gray-700">Org ID</label>
                            <input name="org_id" class="w-full border rounded px-3 py-2" value="{{ old('org_id', optional($visa)->org_id) }}" :disabled="!editing">
                            @error('org_id')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Shared Secret</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="shared_secret" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('shared_secret', optional($visa)->shared_secret) }}" :disabled="!editing">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle shared secret visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0 a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('shared_secret')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm text-gray-700">Webhook Secret</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="webhook_secret" class="w-full border rounded px-3 py-2 pr-10" placeholder="••••••" value="{{ old('webhook_secret', optional($visa)->webhook_secret) }}">
                                <button type="button" @click="show = !show" class="absolute inset-y-0 right-0 px-3 flex items-center text-gray-400 hover:text-primary" aria-label="Toggle webhook secret visibility">
                                    <svg x-show="!show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    </svg>
                                    <svg x-show="show" xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M6.228 6.228A10.451 10.451 0 0112 4.5c4.639 0 8.576 3.01 9.963 7.178a1.01 1.01 0 010 .639 10.46 10.46 0 01-1.133 2.07M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0 a3 3 0 10-4.243-4.243m4.243 4.243L9.88 9.88" />
                                    </svg>
                                </button>
                            </div>
                            @error('webhook_secret')<p class="text-sm text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                    <div class="flex justify-end">
                        <button class="px-4 py-2 bg-primary text-white rounded" type="submit" x-text="editing ? 'Save changes' : 'Edit'"></button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
