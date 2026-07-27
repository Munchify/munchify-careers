<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Department;
use App\Models\NotificationTemplate;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class SettingsController extends Controller
{
    public function index()
    {
        $team = User::orderBy('full_name')->get();
        $departments = Department::orderBy('name')->get();
        $notifications = NotificationTemplate::all();
        
        $settings = \App\Models\SystemSetting::all()->pluck('value', 'key');

        return view('dashboard.settings.index', compact('team', 'departments', 'notifications', 'settings'));
    }

    public function saveGateways(Request $request)
    {
        // Require admin role for credential changes
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->withErrors(['error' => 'Only Super Admins / Admins can modify gateway credentials.']);
        }

        $validated = $request->validate([
            'mail_host' => 'nullable|string|max:255',
            'mail_port' => 'nullable|numeric',
            'mail_username' => 'nullable|string|max:255',
            'mail_password' => 'nullable|string|max:255',
            'mail_encryption' => 'nullable|string|max:10',
            'mail_from_address' => 'nullable|email|max:255',
            'mail_from_name' => 'nullable|string|max:255',

            'sms_hostpinnacle_url' => 'nullable|url|max:255',
            'sms_hostpinnacle_api_key' => 'nullable|string|max:255',
            'sms_hostpinnacle_partner_id' => 'nullable|string|max:255',
            'sms_hostpinnacle_sender_id' => 'nullable|string|max:50',
        ]);

        foreach ($validated as $key => $val) {
            $isSecret = in_array($key, ['mail_password', 'sms_hostpinnacle_api_key']);
            $group = str_starts_with($key, 'mail_') ? 'smtp' : 'sms';
            
            // If secret is passed as null or blank during update, retain existing value
            if ($isSecret && empty($val)) {
                continue;
            }

            \App\Models\SystemSetting::set($key, $val, $group, $isSecret);
        }

        AuditLog::log(
            actorId: Auth::id(),
            action: 'gateway_settings_updated',
            entityType: \App\Models\SystemSetting::class,
            entityId: 0,
            details: ['updated_by' => Auth::user()->email]
        );

        return redirect()->route('settings.index')->with('success', 'SMTP Mail & SMS Gateway credentials saved to database.');
    }

    public function testEmail(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->withErrors(['error' => 'Only Admins can send test emails.']);
        }

        $validated = $request->validate([
            'test_email' => 'required|email|max:255',
        ]);

        try {
            \Illuminate\Support\Facades\Mail::raw('This is a test email sent from Munchify Careers portal to verify Brevo SMTP configurations.', function ($message) use ($validated) {
                $message->to($validated['test_email'])
                        ->subject('Munchify Careers - SMTP Mail Gateway Test');
            });

            return redirect()->route('settings.index')->with('success', 'Test email dispatched successfully to ' . $validated['test_email']);
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to send test email: ' . $e->getMessage()]);
        }
    }

    public function testSms(Request $request)
    {
        if (!Auth::user()->isAdmin()) {
            return redirect()->back()->withErrors(['error' => 'Only Admins can send test SMS.']);
        }

        $validated = $request->validate([
            'test_phone' => 'required|string|max:20',
        ]);

        try {
            $smsService = app(\App\Services\SmsService::class);
            $success = $smsService->sendSms(
                $validated['test_phone'],
                'Munchify Careers Gateway Test: Hostpinnacle SMS integration is working successfully.'
            );

            if ($success) {
                return redirect()->route('settings.index')->with('success', 'Test SMS dispatched successfully to ' . $validated['test_phone']);
            } else {
                return redirect()->back()->withErrors(['error' => 'Hostpinnacle SMS API returned failure. Check logs or gateway credentials.']);
            }
        } catch (\Exception $e) {
            return redirect()->back()->withErrors(['error' => 'Failed to send test SMS: ' . $e->getMessage()]);
        }
    }

    public function saveTeamUser(Request $request)
    {
        $userId = $request->input('id');

        $validated = $request->validate([
            'full_name' => 'required|string|max:255',
            'email' => [
                'required',
                'string',
                'email',
                'max:255',
                Rule::unique('users')->ignore($userId),
            ],
            'password' => $userId ? 'nullable|string|min:8' : 'required|string|min:8',
            'role' => 'required|in:admin,hr_manager,hiring_manager,interviewer,viewer',
            'department' => 'nullable|string|max:100',
        ]);

        return DB::transaction(function () use ($validated, $userId, $request) {
            $data = [
                'full_name' => $validated['full_name'],
                'email' => $validated['email'],
                'role' => $validated['role'],
                'department' => $validated['department'],
            ];

            if ($request->filled('password')) {
                $data['password'] = Hash::make($validated['password']);
            }

            if ($userId) {
                $user = User::findOrFail($userId);
                $user->update($data);
                $action = 'team_user_updated';
            } else {
                $user = User::create(array_merge($data, ['is_active' => true]));
                $action = 'team_user_created';
            }

            AuditLog::log(
                actorId: Auth::id(),
                action: $action,
                entityType: User::class,
                entityId: $user->id,
                details: ['email' => $user->email]
            );

            return redirect()->route('settings.index')->with('success', 'Team member details saved.');
        });
    }

    public function toggleTeamUser($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->withErrors(['error' => 'You cannot deactivate your own account.']);
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        $statusLabel = $user->is_active ? 'activated' : 'deactivated';

        AuditLog::log(
            actorId: Auth::id(),
            action: 'team_user_toggle_status',
            entityType: User::class,
            entityId: $user->id,
            details: ['status' => $statusLabel]
        );

        return redirect()->route('settings.index')->with('success', "Team member has been {$statusLabel}.");
    }

    public function saveDepartment(Request $request)
    {
        $deptId = $request->input('id');

        $validated = $request->validate([
            'name' => [
                'required',
                'string',
                'max:100',
                Rule::unique('departments')->ignore($deptId),
            ],
            'description' => 'nullable|string|max:500',
        ]);

        $dept = Department::updateOrCreate([
            'id' => $deptId
        ], [
            'name' => $validated['name'],
            'description' => $validated['description'],
        ]);

        $action = $deptId ? 'department_updated' : 'department_created';

        AuditLog::log(
            actorId: Auth::id(),
            action: $action,
            entityType: Department::class,
            entityId: $dept->id,
            details: ['name' => $dept->name]
        );

        return redirect()->route('settings.index')->with('success', 'Department details saved.');
    }

    public function toggleDepartment($id)
    {
        $dept = Department::findOrFail($id);

        $dept->update([
            'is_active' => !$dept->is_active
        ]);

        $statusLabel = $dept->is_active ? 'activated' : 'deactivated';

        AuditLog::log(
            actorId: Auth::id(),
            action: 'department_toggle_status',
            entityType: Department::class,
            entityId: $dept->id,
            details: ['status' => $statusLabel]
        );

        return redirect()->route('settings.index')->with('success', "Department has been {$statusLabel}.");
    }

    public function saveNotificationTemplate(Request $request, $id)
    {
        $template = NotificationTemplate::findOrFail($id);

        $validated = $request->validate([
            'email_subject' => 'required|string|max:255',
            'email_body' => 'required|string|max:5000',
            'sms_body' => 'required|string|max:500',
        ]);

        $template->update([
            'email_subject' => $validated['email_subject'],
            'email_body' => $validated['email_body'],
            'sms_body' => $validated['sms_body'],
        ]);

        AuditLog::log(
            actorId: Auth::id(),
            action: 'notification_template_updated',
            entityType: NotificationTemplate::class,
            entityId: $template->id,
            details: ['event_key' => $template->event_key]
        );

        return redirect()->route('settings.index')->with('success', 'Notification template updated successfully.');
    }
}
