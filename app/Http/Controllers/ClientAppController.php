<?php

namespace App\Http\Controllers;

use App\Models\ClientApp;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ClientAppController extends Controller
{
    public function index()
    {
        $apps = ClientApp::orderBy('name')->get();
        return view('admin.client_apps.index', compact('apps'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'callback_url' => 'nullable|url|max:255',
        ]);

        // Generate cryptographically strong API credentials
        do {
            $apiKey = 'cli_' . Str::upper(Str::random(24));
        } while (ClientApp::where('api_key', $apiKey)->exists());

        $apiSecret = bin2hex(random_bytes(32));

        $data['api_key'] = $apiKey;
        $data['api_secret'] = $apiSecret;

        ClientApp::create($data);

        return redirect()->route('admin.client-apps.index')->with('status', 'Client app created');
    }

    public function update(Request $request, ClientApp $clientApp)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'callback_url' => 'nullable|url|max:255',
        ]);

        $clientApp->update($data);

        return redirect()->route('admin.client-apps.index')->with('status', 'Client app updated');
    }

    public function destroy(ClientApp $clientApp)
    {
        $clientApp->delete();
        return redirect()->route('admin.client-apps.index')->with('status', 'Client app deleted');
    }
}

