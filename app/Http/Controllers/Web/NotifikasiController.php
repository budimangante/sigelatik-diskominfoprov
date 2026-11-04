<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use App\Models\Roles;


class NotifikasiController extends Controller
{
    public function notifikasiIndex(Request $request): View
    {
        $filter = $request->query('filter', 'all'); // 'all', 'unread', atau 'global'
        $user = auth()->user();

        // Eager load notifikasi user aktif
        $user->loadMissing(['notifications', 'unreadNotifications']);

        if ($filter === 'global') {
            // Eager loading model penerima (User) bebas masalah N+1
            $notifications = DatabaseNotification::with('notifiable')
                ->latest()
                ->paginate(10)
                ->withQueryString();
        } elseif ($filter === 'unread') {
            $notifications = $user->unreadNotifications()->latest()->paginate(10)->withQueryString();
        } else {
            $notifications = $user->notifications()->latest()->paginate(10)->withQueryString();
        }

        $unreadCount = $user->unreadNotifications->count();
        $totalCount  = $user->notifications->count();
        $globalCount = DatabaseNotification::count();

        return view('superadmin.notifikasi', compact(
            'notifications', 
            'filter', 
            'unreadCount', 
            'totalCount',
            'globalCount'
        ));
    }

    /**
     * Tandai Satu Notifikasi Telah Dibaca
     */
    public function markNotificationAsRead(string $id): RedirectResponse
    {
        $notification = auth()->user()->notifications()->where('id', $id)->firstOrFail();
        $notification->markAsRead();

        // Jika notifikasi memiliki tautan tujuan tiket, redirect langsung ke tiket terkait
        $targetUrl = $notification->data['url'] ?? null;
        if ($targetUrl) {
            return redirect($targetUrl);
        }

        return back()->with('success', 'Notifikasi telah ditandai sebagai dibaca.');
    }

    /**
     * Tandai Semua Notifikasi Menjadi Sudah Dibaca
     */
    public function markAllNotificationsAsRead(): RedirectResponse
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('success', 'Semua notifikasi berhasil ditandai sebagai telah dibaca.');
    }

    /**
     * Hapus Riwayat Notifikasi Tertentu
     */
    public function destroyNotification(string $id): RedirectResponse
    {
        auth()->user()->notifications()->where('id', $id)->delete();

        return back()->with('success', 'Riwayat notifikasi berhasil dihapus.');
    }

    /**
     * Fitur Khusus Super Admin: Broadcast Pengumuman Sistem ke Pengguna
     */
    public function broadcastNotification(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'title'       => 'required|string|max:255',
            'message'     => 'required|string',
            'target_role' => 'nullable|string', // 'all', 'teknisi', 'asn'
        ], [
            'title.required'   => 'Judul pengumuman wajib diisi.',
            'message.required' => 'Isi pesan pengumuman wajib diisi.',
        ]);

        $targetRole = $validated['target_role'] ?? 'all';

        // 1. Ambil target penerima: Role tujuan + Super Admin selalu diikutsertakan
        $usersQuery = User::query();

        if (!empty($targetRole) && $targetRole !== 'all') {
            $usersQuery->where(function ($query) use ($targetRole) {
                $query->whereHas('roles', function ($q) use ($targetRole) {
                    $q->where('name', $targetRole);
                })
                // Selalu sertakan Super Admin agar notifikasi masuk ke inbox pribadi Super Admin
                ->orWhereHas('roles', function ($q) {
                    $q->where('name', 'super_admin');
                })
                ->orWhere('id', auth()->id());
            });
        }

        $targetUsers = $usersQuery->get();

        $targetLabel = match($targetRole) {
            'teknisi' => 'Khusus Teknisi',
            'asn'     => 'Khusus Pegawai / OPD',
            default   => 'Semua Pengguna'
        };

        // 2. Format payload notifikasi
        $timestamp = now();
        $notificationData = [
            'title'        => $validated['title'],
            'message'      => $validated['message'],
            'category'     => 'broadcast',
            'target_label' => $targetLabel,
            'sender'       => auth()->user()->name,
            'url'          => route('notifikasi'),
        ];

        // 3. Simpan ke database via Eloquent
        foreach ($targetUsers as $targetUser) {
            $targetUser->notifications()->create([
                'id'         => (string) Str::uuid(),
                'type'       => 'App\Notifications\SystemBroadcastNotification',
                'data'       => $notificationData,
                'created_at' => $timestamp,
                'updated_at' => $timestamp,
            ]);
        }

        return redirect()->route('notifikasi')->with('success', "Pengumuman berhasil dikirim ke {$targetLabel}.");
    }
}
