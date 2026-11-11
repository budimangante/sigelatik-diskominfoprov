@extends('components.b-master')

@section('title', 'Manajemen Pengguna')
@section('subtitle', 'Daftar dan pemetaan hak akses role pengguna SIGELATIK')

@section('content')
<div class="min-h-screen bg-[#f3f6f8] p-4 font-sans sm:p-6 lg:p-7">

    <!-- Header Section -->
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-end sm:justify-between lg:mb-7">
        <div>
            <div class="mb-2 inline-flex items-center gap-2 rounded-full border border-[#dce6ec] bg-white px-3.5 py-1.5 text-[10px] font-bold uppercase tracking-[0.15em] text-[#397b9d] shadow-sm">
                <span class="h-2 w-2 rounded-full bg-[#397b9d]"></span>
                <span>SUPER ADMIN</span>
                <span class="text-[#b8c7d0]">•</span>
                <span>INTEGRASI SSO BKD</span>
            </div>
            <h1 class="mb-1 text-3xl font-['StretchPro'] tracking-wider text-[#1d405c] sm:text-[25px] uppercase">
                Manajemen Pengguna & Role
            </h1>
            <p class="mt-1 max-w-2xl text-xs leading-relaxed text-[#71808c] sm:text-sm">
                Verifikasi identitas NIP melalui direktori kepegawaian SSO BKD lalu tentukan peran wewenang di SIGELATIK.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex shrink-0 items-center gap-2 rounded-xl bg-[#2f789e] px-4 py-3 text-xs font-bold text-white shadow-sm transition duration-200 hover:-translate-y-0.5 hover:bg-[#1d6388] hover:shadow-md cursor-pointer">
            <i class="fas fa-id-card text-xs"></i>
            <span>Tambah Pengguna</span>
        </button>
    </div>

    <!-- Alert Notifikasi -->
    @if(session('success'))
        <div class="mb-6 p-4 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center justify-between shadow-2xs">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-check-circle text-emerald-600 text-sm"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button onclick="this.parentElement.remove()" class="text-emerald-500 hover:text-emerald-700 cursor-pointer">
                <i class="fas fa-times text-xs"></i>
            </button>
        </div>
    @endif

    @if($errors->any())
        <div class="mb-6 p-4 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold space-y-1 shadow-2xs">
            @foreach($errors->all() as $error)
                <div class="flex items-center gap-2">
                    <i class="fas fa-exclamation-circle text-rose-600"></i>
                    <span>{{ $error }}</span>
                </div>
            @endforeach
        </div>
    @endif

    <!-- Filter & Pencarian -->
    <div class="mb-5 rounded-2xl border border-[#dce4e9] bg-white p-3.5 shadow-[0_3px_12px_rgba(25,55,75,0.045)] sm:p-4">
        <form method="GET" action="{{ route('pengguna') }}" class="grid grid-cols-1 items-center gap-3 lg:grid-cols-[minmax(260px,1fr)_auto]">
            <div class="relative w-full lg:max-w-[430px]">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari nama, NIP, atau email..."
                       class="w-full rounded-xl border border-[#dce4e9] bg-[#f7f9fb] py-3 pl-10 pr-4 text-xs text-slate-800 placeholder:text-slate-400 transition focus:border-[#397b9d] focus:bg-white focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
                <i class="fas fa-search absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <div class="flex w-full items-center gap-2 sm:justify-end">
                <select name="role" 
                        onchange="this.form.submit()" 
                        class="w-full min-w-0 rounded-xl border border-[#dce4e9] bg-white px-3.5 py-3 text-xs text-slate-700 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10 sm:w-[220px]">
                    <option value="">Semua Hak Akses</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ ($roleFilter ?? '') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>

                @if(!empty($search) || !empty($roleFilter))
                    <a href="{{ route('pengguna') }}" class="inline-flex shrink-0 items-center justify-center rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-xs font-medium text-slate-600 transition hover:bg-slate-100" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="overflow-hidden rounded-2xl border border-[#dce4e9] bg-white shadow-[0_3px_12px_rgba(25,55,75,0.045)]">
        <div class="flex items-center justify-between gap-3 border-b border-slate-100 px-5 py-4 sm:px-6">
            <div class="flex items-center gap-3">
                <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]">
                    <i class="fas fa-users text-sm"></i>
                </span>
                <div>
                    <p class="text-xs font-bold text-[#293b49]">Daftar Pengguna</p>
                    <p class="mt-0.5 text-[10px] text-slate-400">{{ $users->count() }} dari {{ $users->total() }} pengguna ditampilkan</p>
                </div>
            </div>
            <span class="hidden rounded-full bg-[#edf4f8] px-3 py-1 text-[10px] font-semibold text-[#397b9d] sm:inline-flex">Integrasi SSO BKD</span>
        </div>
        <div class="overflow-x-auto">
        <table class="w-full min-w-[900px] text-left whitespace-nowrap">
            <thead class="border-b border-slate-100 bg-[#f8fafc] text-[10px] font-bold uppercase tracking-[0.09em] text-slate-500">
                <tr>
                    <th class="px-6 py-4">PEGAWAI / ASN</th>
                    <th class="px-6 py-4">PERANGKAT DAERAH (OPD)</th>
                    <th class="px-6 py-4">ROLE DI SISTEM</th>
                    <th class="px-6 py-4">TERDAFTAR</th>
                    <th class="px-6 py-4 text-center w-36">AKSI</th>
                </tr>
            </thead>

            <tbody class="divide-y divide-slate-100 text-xs">
                @forelse($users as $user)
                    @php
                        $userRole = $user->roles->first();
                        $roleName = $userRole?->name ?? 'Belum ada role';
                        $roleId   = $userRole?->id ?? '';
                        $nameParts = preg_split('/\s+/', trim($user->name ?? ''));
                        $userInitials = strtoupper(substr($nameParts[0] ?? 'U', 0, 1) . (isset($nameParts[1]) ? substr($nameParts[1], 0, 1) : ''));

                        $badgeClasses = match($roleName) {
                            'super_admin'     => 'bg-purple-50 text-purple-700 border-purple-200',
                            'admin_pemprov'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'helpdesk_utama'  => 'bg-blue-50 text-blue-700 border-blue-200',
                            'helpdesk_bidang' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'teknisi'         => 'bg-amber-50 text-amber-700 border-amber-200',
                            default           => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        };
                    @endphp
                    <tr class="transition-colors duration-150 hover:bg-[#f7fafc]">
                        <!-- Profil Pegawai -->
                        <td class="px-5 py-4 align-middle sm:px-6">
                            <div class="flex items-center gap-3">
                                <div class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl border border-[#d8e8f0] bg-[#2f789e] text-xs font-extrabold text-white shadow-sm">
                                    {{ $userInitials }}
                                </div>
                                <div>
                                    <div class="font-bold leading-snug text-[#293b49]">{{ $user->name }}</div>
                                    <div class="mt-1 text-[10px] font-mono text-slate-400">
                                        NIP: {{ $user->nip ?? '-' }} • {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- OPD -->
                        <td class="px-5 py-4 align-middle font-medium text-slate-600 sm:px-6">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-building text-[11px] text-slate-400"></i>
                                <span>{{ $user->opd_name ?? 'Pemerintah Provinsi' }}</span>
                            </span>
                        </td>

                        <!-- Badge Role -->
                        <td class="px-5 py-4 align-middle sm:px-6">
                            <span class="inline-flex items-center rounded-full border px-3 py-1 text-[10px] font-bold {{ $badgeClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                            </span>
                        </td>

                        <!-- Tanggal Terdaftar -->
                        <td class="px-5 py-4 align-middle text-[11px] font-medium text-slate-500 sm:px-6">
                            {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y, H:i') }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-5 py-4 align-middle text-center sm:px-6">
                            <div class="inline-flex items-center gap-2">
                                <button type="button" 
                                        onclick="bukaModalEdit({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->nip ?? '-' }}', '{{ $roleId }}')"
                                        class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-[#dce4e9] bg-white text-[#397b9d] shadow-sm transition hover:border-[#b7d0dd] hover:bg-[#edf4f8] cursor-pointer"
                                        title="Ubah Role Pengguna">
                                    <i class="fas fa-user-shield text-xs"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hak akses pengguna {{ addslashes($user->name) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="inline-flex h-9 w-9 items-center justify-center rounded-xl border border-rose-100 bg-white text-rose-500 shadow-sm transition hover:bg-rose-50 cursor-pointer" 
                                                title="Hapus Pengguna">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="px-6 py-12 text-center text-xs italic text-slate-400">
                            Tidak ditemukan data pengguna yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
        </div>

        @if($users->hasPages())
            <div class="border-t border-slate-100 bg-slate-50/50 px-5 py-4 sm:px-6">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH PENGGUNA VIA NIP SSO BKD ================= -->
<div id="modalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="flex max-h-[92vh] w-full max-w-lg flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-5">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-id-card"></i></span>
                    <span class="text-base font-bold text-[#293b49]">Tambah Pengguna</span>
                </h3>
                <p class="mt-1 text-[11px] text-slate-500">Verifikasi identitas pegawai melalui direktori SSO BKD.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTambah')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('pengguna.store') }}" method="POST" class="space-y-4 overflow-y-auto p-6">
            @csrf
            
            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    Nomor Induk Pegawai (NIP) <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="nip" id="inputNip" required placeholder="Contoh: 198507122010011002"
                           class="min-w-0 flex-1 rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs font-mono text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
                    <button type="button" onclick="verifikasiNipBkd()" id="btnCekNip"
                            class="inline-flex shrink-0 items-center gap-2 rounded-xl border border-[#cfe0e9] bg-[#edf4f8] px-4 py-3 text-xs font-bold text-[#286b8d] transition hover:bg-[#e0edf4] whitespace-nowrap cursor-pointer">
                        <i class="fas fa-search text-xs"></i>
                        <span>Cek Verifikasi User</span>
                    </button>
                </div>
                <p id="nipStatusFeedback" class="text-[11px] mt-1.5 hidden"></p>
            </div>

            <div id="dataBkdWrapper" class="hidden space-y-3 rounded-2xl border border-emerald-100 bg-emerald-50/40 p-4">
                <div class="mb-1 flex items-center gap-1.5 text-[10px] font-bold uppercase tracking-wider text-emerald-700">
                    <i class="fas fa-user-check text-emerald-500"></i>
                    <span>Data Terverifikasi SSO BKD</span>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="bkdName" readonly required
                           class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-bold text-slate-800">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Perangkat Daerah (OPD)</label>
                    <input type="text" name="opd_name" id="bkdOpd" readonly
                           class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs text-slate-700">
                    <input type="hidden" name="perangkat_daerah_id" id="bkdOpdId">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email Dinas / SSO</label>
                    <input type="email" name="email" id="bkdEmail" readonly required
                           class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-xs font-mono text-slate-700">
                </div>
            </div>

            <div>
                <label class="mb-1.5 block text-[11px] font-bold uppercase tracking-wider text-slate-600">
                    Tetapkan Role / Hak Akses <span class="text-rose-500">*</span>
                </label>
                <select name="role_id" id="selectRole" required disabled
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10 disabled:cursor-not-allowed disabled:bg-slate-100">
                    <option value="" disabled selected>Verifikasi NIP terlebih dahulu...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="flex items-center justify-end gap-2.5 border-t border-slate-100 pt-4">
                <button type="button" onclick="tutupModal('modalTambah')" class="rounded-xl border border-slate-200 px-4 py-2.5 text-xs font-bold text-slate-600 transition hover:bg-slate-50 cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSimpanPengguna" disabled
                        class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1d6388] disabled:cursor-not-allowed disabled:opacity-50 cursor-pointer">
                    Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH ROLE PENGGUNA ================= -->
<div id="modalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="flex max-h-[92vh] w-full max-w-md flex-col overflow-hidden rounded-3xl border border-slate-200 bg-white shadow-2xl animate-in fade-in zoom-in-95 duration-150">
        <div class="flex items-center justify-between border-b border-slate-100 bg-white px-6 py-5">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <span class="flex h-9 w-9 items-center justify-center rounded-xl bg-[#edf4f8] text-[#397b9d]"><i class="fas fa-user-shield"></i></span>
                    <span class="text-base font-bold text-[#293b49]">Ubah Hak Akses / Role</span>
                </h3>
                <p class="mt-1 text-[11px] text-slate-500" id="editSubtitle">Sesuaikan peran wewenang pegawai.</p>
            </div>
            <button type="button" onclick="tutupModal('modalEdit')" class="rounded-lg p-2 text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditRole" method="POST" action="" class="space-y-4 overflow-y-auto p-6">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Pegawai
                </label>
                <input type="text" id="displayEditName" readonly
                       class="w-full cursor-not-allowed rounded-xl border border-slate-200 bg-slate-50 px-3.5 py-3 text-xs font-bold text-slate-800">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pilih Role Baru <span class="text-rose-500">*</span>
                </label>
                <select name="role_id" id="editRoleId" required
                        class="w-full rounded-xl border border-slate-200 bg-white px-3.5 py-3 text-xs text-slate-800 transition focus:border-[#397b9d] focus:outline-none focus:ring-4 focus:ring-[#397b9d]/10">
                    <option value="" disabled>Pilih salah satu role...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalEdit')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" class="rounded-xl bg-[#2f789e] px-5 py-2.5 text-xs font-bold text-white shadow-sm transition hover:bg-[#1d6388] cursor-pointer">
                    Perbarui Role
                </button>
            </div>
        </form>
    </div>
</div>

<script>
    function bukaModalTambah() {
        document.getElementById('inputNip').value = '';
        document.getElementById('nipStatusFeedback').classList.add('hidden');
        document.getElementById('dataBkdWrapper').classList.add('hidden');
        document.getElementById('selectRole').setAttribute('disabled', 'true');
        document.getElementById('btnSimpanPengguna').setAttribute('disabled', 'true');
        document.getElementById('modalTambah').classList.remove('hidden');
    }

    function bukaModalEdit(userId, name, nip, roleId) {
        document.getElementById('displayEditName').value = name + ' (NIP: ' + nip + ')';
        document.getElementById('editRoleId').value = roleId;
        document.getElementById('formEditRole').action = '/superadmin/pengguna/' + userId;
        document.getElementById('modalEdit').classList.remove('hidden');
    }

    function tutupModal(modalId) {
        document.getElementById(modalId).classList.add('hidden');
    }

    function verifikasiNipBkd() {
        const nip = document.getElementById('inputNip').value.trim();
        const feedback = document.getElementById('nipStatusFeedback');
        const wrapper = document.getElementById('dataBkdWrapper');
        const roleSelect = document.getElementById('selectRole');
        const btnSubmit = document.getElementById('btnSimpanPengguna');

        if (!nip || nip.length < 8) {
            feedback.className = 'text-[11px] mt-1.5 text-rose-600 font-semibold';
            feedback.innerText = 'Masukkan NIP valid (minimal 8 digit).';
            feedback.classList.remove('hidden');
            return;
        }

        feedback.className = 'text-[11px] mt-1.5 text-blue-600 font-semibold';
        feedback.innerText = 'Menghubungkan ke database SSO BKD...';
        feedback.classList.remove('hidden');

        fetch(`/superadmin/pengguna/cek-nip?nip=${encodeURIComponent(nip)}`)
            .then(response => response.json().then(data => ({ status: response.status, body: data })))
            .then(({ status, body }) => {
                if (status === 200 && body.success && body.data) {
                    feedback.className = 'text-[11px] mt-1.5 text-emerald-600 font-semibold';
                    feedback.innerHTML = '<i class="fas fa-check-circle"></i> ' + body.data.source + ' - Pegawai Ditemukan.';

                    document.getElementById('bkdName').value = body.data.name;
                    document.getElementById('bkdEmail').value = body.data.email;
                    document.getElementById('bkdOpd').value = body.data.opd_name;
                    document.getElementById('bkdOpdId').value = body.data.perangkat_daerah_id;

                    wrapper.classList.remove('hidden');
                    roleSelect.removeAttribute('disabled');
                    btnSubmit.removeAttribute('disabled');
                } else {
                    feedback.className = 'text-[11px] mt-1.5 text-rose-600 font-semibold';
                    feedback.innerText = body.message || 'NIP tidak ditemukan dalam direktori kepegawaian BKD.';
                    wrapper.classList.add('hidden');
                    roleSelect.setAttribute('disabled', 'true');
                    btnSubmit.setAttribute('disabled', 'true');
                }
            })
            .catch(() => {
                feedback.className = 'text-[11px] mt-1.5 text-rose-600 font-semibold';
                feedback.innerText = 'Gagal memeriksa ke server SSO BKD. Periksa jaringan internal atau service API.';
            });
    }
</script>
@endsection