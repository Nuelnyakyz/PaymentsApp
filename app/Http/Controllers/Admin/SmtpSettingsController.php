<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Cache;
use App\Models\SmtpSetting;

class SmtpSettingsController extends Controller
{
    public function index(Request $request)
    {
        $settings = SmtpSetting::query()->first();
        $edit = $request->boolean('edit');
        return view('admin.smtp.index', [
            'settings' => $settings,
            'edit' => $edit,
        ]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'username' => ['nullable','string','max:255'],
            'from_address' => ['nullable','email','max:255'],
            'from_name' => ['nullable','string','max:255'],
            'password' => ['nullable','string','max:2048'],
        ]);

        $settings = SmtpSetting::query()->first() ?: new SmtpSetting();

        $settings->username = $data['username'] ?? null;
        $settings->from_address = $data['from_address'] ?? null;
        $settings->from_name = $data['from_name'] ?? null;

        if (!empty($data['password'])) {
            $settings->password_encrypted = Crypt::encryptString($data['password']);
        }

        $settings->save();

        Cache::forget('smtp_settings:first');

        return redirect()->route('admin.smtp.index')->with('status', 'SMTP settings updated');
    }
}
