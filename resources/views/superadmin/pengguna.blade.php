@extends('components.b-master')

@section('title', 'Manajemen Pengguna')
@section('subtitle', 'Daftar dan pemetaan hak akses role pengguna SIGELATIK')

@section('content')
<div class="p-6 bg-[#f8fafc] min-h-screen font-sans">

    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold text-slate-400 tracking-wider uppercase mb-1.5">
                <span class="text-[#1a3668]">SUPER ADMIN</span>
                <span class="text-slate-300">•</span>
                <span>INTEGRASI SSO BKD</span>
            </div>
            <h1 class="text-3xl font-extrabold text-[#1a3668] tracking-tight">
                Manajemen Pengguna & Role
            </h1>
            <p class="text-xs text-slate-500 mt-1">
                Verifikasi identitas NIP melalui direktori kepegawaian SSO BKD lalu tentukan peran wewenang di SIGELATIK.
            </p>
        </div>

        <button type="button" 
                onclick="bukaModalTambah()"
                class="inline-flex items-center gap-2 bg-[#1a3668] hover:bg-[#122548] text-white px-4 py-2.5 rounded-xl text-xs font-bold transition shadow-2xs cursor-pointer">
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
    <div class="bg-white p-4 rounded-2xl border border-slate-200/90 shadow-2xs mb-6">
        <form method="GET" action="{{ route('pengguna') }}" class="flex flex-col sm:flex-row items-center justify-between gap-3">
            <div class="relative w-full sm:w-80">
                <input type="text" 
                       name="search" 
                       value="{{ $search ?? '' }}" 
                       placeholder="Cari nama, NIP, atau email..."
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl pl-9 pr-3.5 py-2 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                <i class="fas fa-search absolute left-3 top-1/2 -translate-y-1/2 text-slate-400 text-xs"></i>
            </div>

            <div class="flex items-center gap-2 w-full sm:w-auto">
                <select name="role" 
                        onchange="this.form.submit()" 
                        class="w-full sm:w-48 bg-slate-50 border border-slate-200 rounded-xl px-3 py-2 text-xs text-slate-700 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] transition">
                    <option value="">Semua Hak Akses</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}" {{ ($roleFilter ?? '') == $role->id ? 'selected' : '' }}>
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>

                @if(!empty($search) || !empty($roleFilter))
                    <a href="{{ route('pengguna') }}" class="px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-600 text-xs rounded-xl font-medium transition" title="Reset Filter">
                        <i class="fas fa-redo-alt text-xs"></i>
                    </a>
                @endif
            </div>
        </form>
    </div>

    <!-- Tabel Pengguna -->
    <div class="bg-white rounded-2xl border border-slate-200/90 shadow-2xs overflow-hidden overflow-x-auto">
        <table class="w-full text-left whitespace-nowrap">
            <thead class="text-[11px] font-bold text-slate-400 uppercase tracking-wider bg-slate-50/50 border-b border-slate-100">
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

                        $badgeClasses = match($roleName) {
                            'super_admin'     => 'bg-purple-50 text-purple-700 border-purple-200',
                            'admin_pemprov'   => 'bg-indigo-50 text-indigo-700 border-indigo-200',
                            'helpdesk_utama'  => 'bg-blue-50 text-blue-700 border-blue-200',
                            'helpdesk_bidang' => 'bg-sky-50 text-sky-700 border-sky-200',
                            'teknisi'         => 'bg-amber-50 text-amber-700 border-amber-200',
                            default           => 'bg-emerald-50 text-emerald-700 border-emerald-200',
                        };
                    @endphp
                    <tr class="hover:bg-slate-50/60 transition">
                        <!-- Profil Pegawai -->
                        <td class="px-6 py-4 align-middle">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-[#1a3668] text-white flex items-center justify-center font-bold text-xs shrink-0 shadow-2xs">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="font-bold text-slate-800 leading-snug">{{ $user->name }}</div>
                                    <div class="text-[11px] text-slate-400 font-mono mt-0.5">
                                        NIP: {{ $user->nip ?? '-' }} • {{ $user->email }}
                                    </div>
                                </div>
                            </div>
                        </td>

                        <!-- OPD -->
                        <td class="px-6 py-4 align-middle text-slate-600 font-medium">
                            <span class="inline-flex items-center gap-1.5">
                                <i class="fas fa-building text-slate-400 text-xs"></i>
                                <span>{{ $user->opd_name ?? 'Pemerintah Provinsi' }}</span>
                            </span>
                        </td>

                        <!-- Badge Role -->
                        <td class="px-6 py-4 align-middle">
                            <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold border {{ $badgeClasses }}">
                                {{ ucfirst(str_replace('_', ' ', $roleName)) }}
                            </span>
                        </td>

                        <!-- Tanggal Terdaftar -->
                        <td class="px-6 py-4 text-slate-500 font-medium align-middle">
                            {{ \Carbon\Carbon::parse($user->created_at)->translatedFormat('d M Y, H:i') }}
                        </td>

                        <!-- Aksi -->
                        <td class="px-6 py-4 align-middle text-center">
                            <div class="inline-flex items-center gap-1.5">
                                <button type="button" 
                                        onclick="bukaModalEdit({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->nip ?? '-' }}', '{{ $roleId }}')"
                                        class="p-2 border border-slate-200 text-slate-600 hover:text-[#1a3668] hover:border-slate-300 rounded-xl transition cursor-pointer"
                                        title="Ubah Role Pengguna">
                                    <i class="fas fa-user-shield text-xs"></i>
                                </button>

                                @if($user->id !== auth()->id())
                                    <form action="{{ route('pengguna.destroy', $user->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus hak akses pengguna {{ addslashes($user->name) }}?');" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="p-2 border border-rose-100 bg-rose-50/50 text-rose-600 hover:bg-rose-100 rounded-xl transition cursor-pointer" 
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
                        <td colspan="5" class="py-8 text-center text-xs text-slate-400 italic">
                            Tidak ditemukan data pengguna yang cocok.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
            <div class="px-6 py-4 border-t border-slate-100 bg-slate-50/30">
                {{ $users->links() }}
            </div>
        @endif
    </div>

</div>

<!-- ================= MODAL 1: TAMBAH PENGGUNA VIA NIP SSO BKD ================= -->
<div id="modalTambah" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-lg overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-id-card text-blue-200"></i>
                    <span>Tambah Pengguna</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5">Masukkan NIP.</p>
            </div>
            <button type="button" onclick="tutupModal('modalTambah')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form action="{{ route('pengguna.store') }}" method="POST" class="p-6 space-y-4">
            @csrf
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nomor Induk Pegawai (NIP) <span class="text-rose-500">*</span>
                </label>
                <div class="flex gap-2">
                    <input type="text" name="nip" id="inputNip" required placeholder="Contoh: 198507122010011002"
                           class="flex-1 bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
                    <button type="button" onclick="verifikasiNipBkd()" id="btnCekNip"
                            class="bg-blue-50 hover:bg-blue-100 text-[#1a3668] border border-blue-200 px-4 py-2.5 rounded-xl text-xs font-bold transition flex items-center gap-1.5 whitespace-nowrap cursor-pointer">
                        <i class="fas fa-search text-xs"></i>
                        <span>Cek Verifikasi User</span>
                    </button>
                </div>
                <p id="nipStatusFeedback" class="text-[11px] mt-1.5 hidden"></p>
            </div>

            <div id="dataBkdWrapper" class="space-y-3 p-4 bg-slate-50 border border-slate-200 rounded-xl hidden">
                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5 mb-1">
                    <i class="fas fa-user-check text-emerald-500"></i>
                    <span>Data Terverifikasi SSO BKD</span>
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Nama Lengkap</label>
                    <input type="text" name="name" id="bkdName" readonly required
                           class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs text-slate-800 font-bold cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Perangkat Daerah (OPD)</label>
                    <input type="text" name="opd_name" id="bkdOpd" readonly
                           class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs text-slate-700 cursor-not-allowed">
                    <input type="hidden" name="perangkat_daerah_id" id="bkdOpdId">
                </div>
                <div>
                    <label class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-1">Email Dinas / SSO</label>
                    <input type="email" name="email" id="bkdEmail" readonly required
                           class="w-full bg-white border border-slate-200 rounded-lg p-2 text-xs text-slate-700 font-mono cursor-not-allowed">
                </div>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Tetapkan Role / Hak Akses <span class="text-rose-500">*</span>
                </label>
                <select name="role_id" id="selectRole" required disabled
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668] disabled:bg-slate-100 disabled:cursor-not-allowed">
                    <option value="" disabled selected>Verifikasi NIP terlebih dahulu...</option>
                    @foreach($roles as $role)
                        <option value="{{ $role->id }}">
                            {{ ucfirst(str_replace('_', ' ', $role->name)) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="pt-3 border-t border-slate-100 flex items-center justify-end gap-2.5">
                <button type="button" onclick="tutupModal('modalTambah')" class="px-4 py-2 border border-slate-200 text-slate-600 rounded-xl text-xs font-bold hover:bg-slate-100 transition cursor-pointer">
                    Batal
                </button>
                <button type="submit" id="btnSimpanPengguna" disabled
                        class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition disabled:opacity-50 disabled:cursor-not-allowed cursor-pointer">
                    Simpan Hak Akses
                </button>
            </div>
        </form>
    </div>
</div>

<!-- ================= MODAL 2: UBAH ROLE PENGGUNA ================= -->
<div id="modalEdit" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/40 backdrop-blur-sm hidden p-4">
    <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col animate-in fade-in zoom-in-95 duration-150">
        <div class="bg-[#1a3668] text-white p-5 flex items-center justify-between">
            <div>
                <h3 class="text-sm font-bold flex items-center gap-2">
                    <i class="fas fa-user-shield text-blue-200"></i>
                    <span>Ubah Hak Akses / Role</span>
                </h3>
                <p class="text-[11px] text-blue-100 mt-0.5" id="editSubtitle">Sesuaikan peran wewenang pegawai.</p>
            </div>
            <button type="button" onclick="tutupModal('modalEdit')" class="text-white/70 hover:text-white p-1 cursor-pointer">
                <i class="fas fa-times text-sm"></i>
            </button>
        </div>

        <form id="formEditRole" method="POST" action="" class="p-6 space-y-4">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Nama Pegawai
                </label>
                <input type="text" id="displayEditName" readonly
                       class="w-full bg-slate-50 border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 font-bold cursor-not-allowed">
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-700 uppercase tracking-wider mb-1.5">
                    Pilih Role Baru <span class="text-rose-500">*</span>
                </label>
                <select name="role_id" id="editRoleId" required
                        class="w-full bg-white border border-slate-200 rounded-xl px-3.5 py-2.5 text-xs text-slate-800 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-[#1a3668]">
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
                <button type="submit" class="bg-[#1a3668] hover:bg-[#122548] text-white px-5 py-2 rounded-xl text-xs font-bold shadow-sm transition cursor-pointer">
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