@extends('layouts.dashboard')

@section('title', 'System Settings - Recruiter Dashboard')
@section('header_title', 'Workspace Settings')

@section('content')
<div class="space-y-6" x-data="settingsDashboard()">
    
    <!-- Tab navigation -->
    <div class="flex border-b border-gray-200">
        <button @click="tab = 'team'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition" :class="tab === 'team' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-users mr-1"></i> Team Management
        </button>
        <button @click="tab = 'departments'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition" :class="tab === 'departments' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-building-user mr-1"></i> Departments
        </button>
        <button @click="tab = 'notifications'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition" :class="tab === 'notifications' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-message mr-1"></i> Notification Templates
        </button>
        @if(Auth::user()->isAdmin())
        <button @click="tab = 'gateways'" class="py-2.5 px-6 font-bold text-xs border-b-2 transition" :class="tab === 'gateways' ? 'border-[#FF6B00] text-[#FF6B00]' : 'border-transparent text-gray-500 hover:text-[#111318]'">
            <i class="fa-solid fa-key mr-1"></i> Gateway Credentials (DB)
        </button>
        @endif
    </div>

    <!-- ====================================================
         1. TAB: TEAM MANAGEMENT
         ==================================================== -->
    <div x-show="tab === 'team'" class="space-y-4 animate-fade-in">
        <div class="flex justify-between items-center">
            <p class="text-xs text-gray-500">Manage recruiter workspace users and roles.</p>
            <button @click="openTeamModal()" class="btn btn-primary btn-sm py-2 px-5 font-bold rounded-full text-xs shadow-md shadow-[#FF6B00]/10">
                <i class="fa-solid fa-plus text-[10px]"></i> Add Team Member
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="data-table">
                    <thead>
                        <tr>
                            <th>Recruiter Name</th>
                            <th>Email Address</th>
                            <th>Role</th>
                            <th>Department</th>
                            <th>Status</th>
                            <th class="text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($team as $user)
                            <tr>
                                <td>
                                    <div class="flex items-center gap-3">
                                        <div class="w-8 h-8 rounded-full bg-orange-50 border border-orange-100 text-[#FF6B00] flex items-center justify-center font-bold text-xs uppercase">
                                            {{ $user->initials }}
                                        </div>
                                        <span class="font-extrabold text-xs text-gray-800">{{ $user->full_name }}</span>
                                    </div>
                                </td>
                                <td class="text-xs text-gray-700 font-semibold">{{ $user->email }}</td>
                                <td>
                                    <span class="badge badge-gray border border-gray-200 capitalize">{{ $user->role_label }}</span>
                                </td>
                                <td class="text-xs text-gray-500 font-semibold">{{ $user->department ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge {{ $user->is_active ? 'badge-green' : 'badge-red' }}">
                                        {{ $user->is_active ? 'Active' : 'Deactivated' }}
                                    </span>
                                </td>
                                <td class="text-right">
                                    <div class="inline-flex gap-1">
                                        <!-- Edit -->
                                        <button @click="openTeamModal(@json($user))" class="btn btn-secondary py-1 px-3 text-xs rounded-xl">
                                            Edit
                                        </button>
                                        
                                        <!-- Status Toggle -->
                                        @if($user->id !== Auth::id())
                                            <form action="{{ route('settings.team.toggle', ['id' => $user->id]) }}" method="POST" class="inline">
                                                @csrf
                                                <button type="submit" class="btn btn-secondary py-1 px-2 text-xs rounded-xl {{ $user->is_active ? 'hover:border-red-400 hover:text-red-500' : 'hover:border-emerald-400 hover:text-emerald-500' }}">
                                                    {{ $user->is_active ? 'Deactivate' : 'Reactivate' }}
                                                </button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Add/Edit Team Member Modal -->
        <div x-show="modals.team" class="modal-overlay" style="display: none;" @keydown.escape.window="modals.team = false">
            <div class="modal-content animate-scale-in max-w-sm p-6" @click.outside="modals.team = false">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                    <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                        <i class="fa-solid fa-user-plus text-[#FF6B00]"></i> Save Team Member
                    </h3>
                    <button @click="modals.team = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('settings.team.save') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="id" x-model="forms.team.id">

                    <!-- Name -->
                    <div>
                        <label for="user_name" class="form-label text-xs">Full Name</label>
                        <input type="text" name="full_name" id="user_name" x-model="forms.team.full_name" class="form-input text-xs" placeholder="e.g. Grace Kemunto" required>
                    </div>

                    <!-- Email -->
                    <div>
                        <label for="user_email" class="form-label text-xs">Email Address</label>
                        <input type="email" name="email" id="user_email" x-model="forms.team.email" class="form-input text-xs" placeholder="e.g. grace@munchify.com" required>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <!-- Role -->
                        <div>
                            <label for="user_role" class="form-label text-xs">Role</label>
                            <select name="role" id="user_role" x-model="forms.team.role" class="form-input text-xs" required>
                                <option value="admin">Admin</option>
                                <option value="hr_manager">HR Manager</option>
                                <option value="hiring_manager">Hiring Manager</option>
                                <option value="interviewer">Interviewer</option>
                                <option value="viewer">Viewer</option>
                            </select>
                        </div>

                        <!-- Department -->
                        <div>
                            <label for="user_dept" class="form-label text-xs">Department</label>
                            <input type="text" name="department" id="user_dept" x-model="forms.team.department" class="form-input text-xs" placeholder="e.g. Operations">
                        </div>
                    </div>

                    <!-- Password -->
                    <div>
                        <label for="user_pwd" class="form-label text-xs">Password</label>
                        <input type="password" name="password" id="user_pwd" class="form-input text-xs" placeholder="••••••••" :required="!forms.team.id">
                        <span x-show="forms.team.id" class="form-help text-[10px] mt-1 block" style="display: none;">Leave blank to keep current password.</span>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-gray-150 pt-4 mt-6">
                        <button type="button" @click="modals.team = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs font-bold rounded-xl">Save User</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ====================================================
         2. TAB: DEPARTMENTS CRUD
         ==================================================== -->
    <div x-show="tab === 'departments'" class="space-y-4 animate-fade-in" style="display: none;">
        <div class="flex justify-between items-center">
            <p class="text-xs text-gray-500">Manage job listing category departments.</p>
            <button @click="openDeptModal()" class="btn btn-primary btn-sm py-2 px-5 font-bold rounded-full text-xs shadow-md shadow-[#FF6B00]/10">
                <i class="fa-solid fa-plus text-[10px]"></i> Add Department
            </button>
        </div>

        <div class="bg-white rounded-2xl border border-gray-200 shadow-sm overflow-hidden">
            <table class="data-table">
                <thead>
                    <tr>
                        <th>Department Name</th>
                        <th>Description</th>
                        <th>Status</th>
                        <th class="text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($departments as $dept)
                        <tr>
                            <td class="font-extrabold text-xs text-gray-800">{{ $dept->name }}</td>
                            <td class="text-xs text-gray-500 leading-normal max-w-sm">{{ $dept->description }}</td>
                            <td>
                                <span class="badge {{ $dept->is_active ? 'badge-green' : 'badge-red' }}">
                                    {{ $dept->is_active ? 'Active' : 'Deactivated' }}
                                </span>
                            </td>
                            <td class="text-right">
                                <div class="inline-flex gap-1">
                                    <button @click="openDeptModal(@json($dept))" class="btn btn-secondary py-1 px-3 text-xs rounded-xl">
                                        Edit
                                    </button>
                                    
                                    <form action="{{ route('settings.department.toggle', ['id' => $dept->id]) }}" method="POST" class="inline">
                                        @csrf
                                        <button type="submit" class="btn btn-secondary py-1 px-2 text-xs rounded-xl {{ $dept->is_active ? 'hover:border-red-400 hover:text-red-500' : 'hover:border-emerald-400 hover:text-emerald-500' }}">
                                            {{ $dept->is_active ? 'Deactivate' : 'Reactivate' }}
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <!-- Add/Edit Department Modal -->
        <div x-show="modals.dept" class="modal-overlay" style="display: none;" @keydown.escape.window="modals.dept = false">
            <div class="modal-content animate-scale-in max-w-sm p-6" @click.outside="modals.dept = false">
                <div class="flex justify-between items-center mb-4 border-b border-gray-100 pb-3">
                    <h3 class="font-extrabold text-sm text-[#111318] flex items-center gap-1.5">
                        <i class="fa-solid fa-building text-[#FF6B00]"></i> Save Department
                    </h3>
                    <button @click="modals.dept = false" class="text-gray-400 hover:text-gray-600">
                        <i class="fa-solid fa-xmark text-lg"></i>
                    </button>
                </div>

                <form action="{{ route('settings.department.save') }}" method="POST" class="space-y-4">
                    @csrf
                    <input type="hidden" name="id" x-model="forms.dept.id">

                    <!-- Name -->
                    <div>
                        <label for="dept_name" class="form-label text-xs">Department Name</label>
                        <input type="text" name="name" id="dept_name" x-model="forms.dept.name" class="form-input text-xs" placeholder="e.g. Customer Care" required>
                    </div>

                    <!-- Description -->
                    <div>
                        <label for="dept_desc" class="form-label text-xs">Description</label>
                        <textarea name="description" id="dept_desc" x-model="forms.dept.description" rows="3" class="form-input text-xs" placeholder="Describe department scope..."></textarea>
                    </div>

                    <div class="flex justify-end gap-2 border-t border-gray-150 pt-4 mt-6">
                        <button type="button" @click="modals.dept = false" class="btn btn-secondary py-1.5 px-4 text-xs rounded-xl">Cancel</button>
                        <button type="submit" class="btn btn-primary py-1.5 px-5 text-xs font-bold rounded-xl">Save Department</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- ====================================================
         3. TAB: NOTIFICATION TEMPLATES FORM
         ==================================================== -->
    <div x-show="tab === 'notifications'" class="space-y-6 animate-fade-in" style="display: none;">
        <p class="text-xs text-gray-500">Modify default Brevo Email templates and Hostpinnacle SMS alerts for candidate interactions.</p>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            @foreach($notifications as $tmpl)
                <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4" x-data="{ editing: false }">
                    <div class="flex justify-between items-center border-b border-gray-100 pb-3">
                        <div class="flex flex-col">
                            <h4 class="font-extrabold text-sm text-gray-800">{{ $tmpl->name }}</h4>
                            <span class="text-[9px] text-gray-400 font-mono mt-0.5">{{ $tmpl->event_key }}</span>
                        </div>
                        <button type="button" @click="editing = !editing" class="btn btn-secondary btn-sm py-1 px-3 text-[10px] font-bold rounded-xl">
                            <i class="fa-regular fa-edit mr-1"></i> Edit Template
                        </button>
                    </div>

                    <p class="text-[11px] text-gray-500 leading-normal italic">{{ $tmpl->description }}</p>

                    <!-- Preview mode -->
                    <div x-show="!editing" class="space-y-3.5 text-xs">
                        <div class="space-y-1">
                            <span class="text-[10px] text-blue-600 font-bold block"><i class="fa-solid fa-envelope"></i> Email Subject (Brevo SMTP):</span>
                            <p class="text-gray-800 font-bold bg-blue-50/30 border border-blue-100 p-2.5 rounded-lg text-xs">{{ $tmpl->email_subject }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-blue-600 font-bold block"><i class="fa-solid fa-align-left"></i> Email Body (Brevo SMTP):</span>
                            <p class="text-gray-700 bg-blue-50/10 border border-blue-100 p-3 rounded-lg leading-relaxed whitespace-pre-wrap">{{ $tmpl->email_body }}</p>
                        </div>
                        <div class="space-y-1">
                            <span class="text-[10px] text-orange-600 font-bold block"><i class="fa-solid fa-mobile-screen"></i> SMS Gateway Body (Hostpinnacle):</span>
                            <p class="text-gray-700 bg-orange-50/20 border border-orange-100 p-3 rounded-lg leading-relaxed whitespace-pre-wrap">{{ $tmpl->sms_body }}</p>
                        </div>
                    </div>

                    <!-- Edit mode form -->
                    <div x-show="editing" class="animate-fade-in space-y-4 bg-gray-50 p-4 border border-gray-200 rounded-xl" style="display: none;">
                        <form action="{{ route('settings.notifications.save', ['id' => $tmpl->id]) }}" method="POST" class="space-y-4">
                            @csrf
                            
                            <div>
                                <label class="form-label text-[10px] text-gray-500">Email Subject</label>
                                <input type="text" name="email_subject" value="{{ $tmpl->email_subject }}" class="form-input text-xs" required>
                            </div>

                            <div>
                                <label class="form-label text-[10px] text-gray-500">Email Body</label>
                                <textarea name="email_body" rows="5" class="form-input text-xs" required>{{ $tmpl->email_body }}</textarea>
                            </div>

                            <div>
                                <label class="form-label text-[10px] text-gray-500">SMS Body (Hostpinnacle)</label>
                                <textarea name="sms_body" rows="3" class="form-input text-xs" required>{{ $tmpl->sms_body }}</textarea>
                            </div>

                            <div class="p-3.5 bg-yellow-50 border border-yellow-200 text-yellow-800 text-[10px] rounded-lg leading-normal font-semibold">
                                <h5 class="font-bold mb-1 flex items-center gap-1"><i class="fa-solid fa-triangle-exclamation"></i> Available Placeholders:</h5>
                                <p class="font-mono text-[9px]">{name}, {job_title}, {app_number}, {status_url}, {stage_name}, {scheduled_at}, {type}, {details}</p>
                            </div>

                            <div class="flex justify-end gap-2 border-t border-gray-150 pt-3">
                                <button type="button" @click="editing = false" class="btn btn-secondary py-1 px-3 text-[10px]">Cancel</button>
                                <button type="submit" class="btn btn-primary py-1 px-4 text-[10px] font-bold">Save Template</button>
                            </div>
                        </form>
                    </div>

                </div>
            @endforeach
        </div>
    </div>

    <!-- ====================================================
         4. TAB: GATEWAY CREDENTIALS (SUPER ADMIN ONLY)
         ==================================================== -->
    @if(Auth::user()->isAdmin())
    <div x-show="tab === 'gateways'" class="space-y-6 animate-fade-in" style="display: none;">
        <p class="text-xs text-gray-500">Configure SMTP Mail (Brevo) and Hostpinnacle SMS gateway credentials stored securely in the database.</p>

        <form action="{{ route('settings.gateways.save') }}" method="POST" class="space-y-6">
            @csrf

            <!-- Brevo SMTP Mail Settings -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fa-solid fa-paper-plane text-blue-600 text-base"></i>
                    <h3 class="font-extrabold text-sm text-gray-800">Brevo / SMTP Email Gateway Configuration</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="form-label text-xs">SMTP Host</label>
                        <input type="text" name="mail_host" value="{{ $settings['mail_host'] ?? 'smtp-relay.brevo.com' }}" class="form-input text-xs" placeholder="e.g. smtp-relay.brevo.com">
                    </div>

                    <div>
                        <label class="form-label text-xs">SMTP Port</label>
                        <input type="number" name="mail_port" value="{{ $settings['mail_port'] ?? '587' }}" class="form-input text-xs" placeholder="587">
                    </div>

                    <div>
                        <label class="form-label text-xs">Encryption</label>
                        <select name="mail_encryption" class="form-input text-xs">
                            <option value="tls" {{ ($settings['mail_encryption'] ?? 'tls') == 'tls' ? 'selected' : '' }}>TLS</option>
                            <option value="ssl" {{ ($settings['mail_encryption'] ?? '') == 'ssl' ? 'selected' : '' }}>SSL</option>
                            <option value="null" {{ ($settings['mail_encryption'] ?? '') == 'null' ? 'selected' : '' }}>None</option>
                        </select>
                    </div>

                    <div>
                        <label class="form-label text-xs">SMTP Username / Login Email</label>
                        <input type="text" name="mail_username" value="{{ $settings['mail_username'] ?? '' }}" class="form-input text-xs" placeholder="your-brevo-email@example.com">
                    </div>

                    <div>
                        <label class="form-label text-xs">SMTP Key / Password</label>
                        <input type="password" name="mail_password" class="form-input text-xs" placeholder="Leave blank to keep existing password">
                        <span class="text-[10px] text-gray-400 mt-1 block">Stored securely in database.</span>
                    </div>

                    <div>
                        <label class="form-label text-xs">From Address</label>
                        <input type="email" name="mail_from_address" value="{{ $settings['mail_from_address'] ?? 'careers@munchify.co.ke' }}" class="form-input text-xs" placeholder="careers@munchify.co.ke">
                    </div>

                    <div class="md:col-span-3">
                        <label class="form-label text-xs">From Sender Name</label>
                        <input type="text" name="mail_from_name" value="{{ $settings['mail_from_name'] ?? 'Munchify Careers' }}" class="form-input text-xs" placeholder="Munchify Careers">
                    </div>
                </div>
            </div>

            <!-- Hostpinnacle SMS Settings -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fa-solid fa-comments text-orange-600 text-base"></i>
                    <h3 class="font-extrabold text-sm text-gray-800">Hostpinnacle SMS Gateway Configuration</h3>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div class="md:col-span-2">
                        <label class="form-label text-xs">Hostpinnacle API Endpoint URL</label>
                        <input type="url" name="sms_hostpinnacle_url" value="{{ $settings['sms_hostpinnacle_url'] ?? 'https://smsportal.hostpinnacle.co.ke/SMSApi/send' }}" class="form-input text-xs" placeholder="https://smsportal.hostpinnacle.co.ke/SMSApi/send">
                    </div>

                    <div>
                        <label class="form-label text-xs">Partner ID / User ID / Username</label>
                        <input type="text" name="sms_hostpinnacle_partner_id" value="{{ $settings['sms_hostpinnacle_partner_id'] ?? '' }}" class="form-input text-xs" placeholder="Your Partner ID / User ID">
                    </div>

                    <div>
                        <label class="form-label text-xs">Sender ID</label>
                        <input type="text" name="sms_hostpinnacle_sender_id" value="{{ $settings['sms_hostpinnacle_sender_id'] ?? 'MUNCHIFY' }}" class="form-input text-xs" placeholder="MUNCHIFY">
                    </div>

                    <div class="md:col-span-2">
                        <label class="form-label text-xs">API Key</label>
                        <input type="password" name="sms_hostpinnacle_api_key" class="form-input text-xs" placeholder="Leave blank to keep existing API Key">
                        <span class="text-[10px] text-gray-400 mt-1 block">Stored securely in database.</span>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="btn btn-primary py-2.5 px-6 text-xs font-bold rounded-full shadow-md shadow-[#FF6B00]/10 flex items-center gap-2">
                    <i class="fa-solid fa-floppy-disk"></i> Save Gateway Credentials to Database
                </button>
            </div>
        </form>

        <!-- Test Triggers Section -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pt-4 border-t border-gray-200">
            <!-- Test Email Form -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fa-solid fa-paper-plane text-blue-600 text-base"></i>
                    <h3 class="font-extrabold text-sm text-gray-800">Test Brevo SMTP Email Gateway</h3>
                </div>
                <form action="{{ route('settings.test-email') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label text-xs">Recipient Email Address</label>
                        <input type="email" name="test_email" value="charlesvaltron@gmail.com" class="form-input text-xs" required placeholder="name@example.com">
                    </div>
                    <button type="submit" class="btn btn-secondary py-2 px-5 text-xs font-bold rounded-xl w-full flex items-center justify-center gap-2 border-blue-200 text-blue-700 hover:bg-blue-50">
                        <i class="fa-solid fa-paper-plane"></i> Dispatch Test Email
                    </button>
                </form>
            </div>

            <!-- Test SMS Form -->
            <div class="bg-white rounded-2xl border border-gray-200 shadow-sm p-6 space-y-4">
                <div class="flex items-center gap-2 border-b border-gray-100 pb-3">
                    <i class="fa-solid fa-mobile-screen text-orange-600 text-base"></i>
                    <h3 class="font-extrabold text-sm text-gray-800">Test Hostpinnacle SMS Gateway</h3>
                </div>
                <form action="{{ route('settings.test-sms') }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="form-label text-xs">Recipient Phone Number (Kenya)</label>
                        <input type="text" name="test_phone" value="254" class="form-input text-xs" required placeholder="2547XXXXXXXX">
                    </div>
                    <button type="submit" class="btn btn-secondary py-2 px-5 text-xs font-bold rounded-xl w-full flex items-center justify-center gap-2 border-orange-200 text-orange-700 hover:bg-orange-50">
                        <i class="fa-solid fa-comments"></i> Dispatch Test SMS
                    </button>
                </form>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@section('scripts')
<script>
    function settingsDashboard() {
        return {
            tab: 'team',
            modals: {
                team: false,
                dept: false
            },
            forms: {
                team: { id: '', full_name: '', email: '', role: 'reviewer', department: '' },
                dept: { id: '', name: '', description: '' }
            },

            openTeamModal(user = null) {
                if (user) {
                    this.forms.team = {
                        id: user.id,
                        full_name: user.full_name,
                        email: user.email,
                        role: user.role,
                        department: user.department || ''
                    };
                } else {
                    this.forms.team = { id: '', full_name: '', email: '', role: 'reviewer', department: '' };
                }
                this.modals.team = true;
            },

            openDeptModal(dept = null) {
                if (dept) {
                    this.forms.dept = {
                        id: dept.id,
                        name: dept.name,
                        description: dept.description || ''
                    };
                } else {
                    this.forms.dept = { id: '', name: '', description: '' };
                }
                this.modals.dept = true;
            }
        }
    }
</script>
@endsection
