<?php

namespace App\Presentation\Http\Controllers;

use App\Models\AuditLog;
use App\Models\SchoolSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Inertia\Inertia;
use Inertia\Response;

class SchoolSettingController extends Controller
{
    public function edit(): Response
    {
        $this->authorize('school.manage');

        return Inertia::render('Admin/SchoolProfile', [
            'school' => SchoolSetting::getSettings(),
        ]);
    }

    public function update(Request $request): RedirectResponse
    {
        $this->authorize('school.manage');

        $validated = $request->validate([
            'school_name' => ['required', 'string', 'max:255'],
            'portal_name' => ['required', 'string', 'max:255'],
            'tagline' => ['nullable', 'string', 'max:255'],
            'npsn' => ['nullable', 'string', 'max:50'],
            'address' => ['nullable', 'string', 'max:500'],
            'phone' => ['nullable', 'string', 'max:50'],
            'whatsapp' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'website' => ['nullable', 'string', 'max:255'],
            'principal_name' => ['nullable', 'string', 'max:255'],
            'welcome_message' => ['nullable', 'string', 'max:1000'],
            'logo' => ['nullable', 'image', 'mimes:jpeg,png,jpg,webp,svg', 'max:2048'],
            'remove_logo' => ['nullable', 'boolean'],
        ], [
            'school_name.required' => 'Nama sekolah wajib diisi.',
            'portal_name.required' => 'Nama portal aplikasi wajib diisi.',
            'logo.image' => 'Berkas logo harus berupa gambar valid.',
            'logo.max' => 'Ukuran berkas logo maksimal 2 MB.',
            'email.email' => 'Format email resmi tidak valid.',
        ]);

        $setting = SchoolSetting::first() ?? new SchoolSetting();
        $oldValues = $setting->toArray();

        // Handle remove logo
        if (! empty($validated['remove_logo'])) {
            if ($setting->logo_url && str_starts_with($setting->logo_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $setting->logo_url);
                Storage::disk('public')->delete($oldPath);
            }
            $setting->logo_url = null;
        }

        // Handle new logo upload
        if ($request->hasFile('logo')) {
            if ($setting->logo_url && str_starts_with($setting->logo_url, '/storage/')) {
                $oldPath = str_replace('/storage/', '', $setting->logo_url);
                Storage::disk('public')->delete($oldPath);
            }

            $path = $request->file('logo')->store('school', 'public');
            $setting->logo_url = '/storage/'.$path;
        }

        foreach ([
            'school_name',
            'portal_name',
            'tagline',
            'npsn',
            'address',
            'phone',
            'whatsapp',
            'email',
            'website',
            'principal_name',
            'welcome_message',
        ] as $field) {
            if (array_key_exists($field, $validated)) {
                $setting->{$field} = $validated[$field];
            }
        }

        $setting->save();
        SchoolSetting::clearCache();

        // Record Audit Log
        AuditLog::create([
            'user_id' => $request->user()->id,
            'user_name' => $request->user()->name,
            'action' => 'update',
            'module' => 'school_setting',
            'resource_id' => (string) $setting->id,
            'description' => "Memperbarui profil dan identitas sekolah: {$setting->school_name}",
            'old_values' => $oldValues,
            'new_values' => $setting->toArray(),
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
            'created_at' => now(),
        ]);

        return redirect()->back()->with('success', 'Profil dan identitas sekolah berhasil disimpan.');
    }
}
