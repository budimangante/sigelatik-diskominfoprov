<?php
use App\Models\User;
use Illuminate\Support\Facades\Broadcast;
use App\Models\Tickets;

// Broadcast::channel('App.Models.User.{id}', function ($user, $id) {
//     return (int) $user->id === (int) $id;
// });

Broadcast::channel('auth-tracker', function (User $user) {
    // Hanya Super Admin dan Admin Pemprov yang diberi izin menyadap aktivitas login
    return $user->hasRole(['super_admin', 'admin_pemprov']);
});

Broadcast::channel('tickets.{ticketId}.chat', function (User $user, $ticketId) {
    $ticket = Tickets::with('tasks')->find($ticketId);

    if (! $ticket) {
        return false;
    }

    // 1. Cek jika user adalah Pelapor (pembuat tiket)
    $isReporter = (int) $user->id === (int) $ticket->user_id;

    // 2. Cek jika user adalah Teknisi yang ditugaskan di tiket ini
    $isAssignedTechnician = $ticket->tasks()->where('user_id', $user->id)->exists()
        || (isset($ticket->technician_id) && (int) $user->id === (int) $ticket->technician_id);

    // 3. Cek jika user adalah Manajerial / Admin pengawas
    $isManagement = $user->hasRole(['super_admin', 'admin_pemprov', 'helpdesk_utama', 'helpdesk_bidang']);

    return $isReporter || $isAssignedTechnician || $isManagement;
});
