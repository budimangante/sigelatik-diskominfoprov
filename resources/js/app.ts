import { createInertiaApp } from '@inertiajs/vue3';
import { initializeTheme } from '@/composables/useAppearance';
import AppLayout from '@/layouts/AppLayout.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import SettingsLayout from '@/layouts/settings/Layout.vue';
import { initializeFlashToast } from '@/lib/flashToast';
import Echo from 'laravel-echo';
import Pusher from 'pusher-js';

// Deklarasi global Window agar TypeScript tidak komplain
declare global {
    interface Window {
        Pusher: any;
        Echo: any;
        showModernToast: (message: string, status?: 'success' | 'error' | 'info') => void;
    }
}

// 1. Inisialisasi Pusher & Reverb ke Global Window
window.Pusher = Pusher;

window.Echo = new Echo({
    broadcaster: 'reverb',
    key: import.meta.env.VITE_REVERB_APP_KEY,
    wsHost: import.meta.env.VITE_REVERB_HOST,
    wsPort: import.meta.env.VITE_REVERB_PORT ?? 80,
    wssPort: import.meta.env.VITE_REVERB_PORT ?? 443,
    forceTLS: (import.meta.env.VITE_REVERB_SCHEME ?? 'https') === 'https',
    enabledTransports: ['ws', 'wss'],
});

// 2. Fungsi Toast Melayang Modern (Mendukung 'success' | 'error' | 'info')
function showModernToast(message: string, status: 'success' | 'error' | 'info' = 'success') {
    let container = document.getElementById('sigelatik-toast-container');
    if (!container) {
        container = document.createElement('div');
        container.id = 'sigelatik-toast-container';
        container.className = 'fixed top-5 right-5 z-[99999] flex flex-col gap-3 pointer-events-none max-w-sm w-full px-4';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');

    let borderColor = 'border-emerald-500/30';
    let iconBg = 'bg-emerald-500/20 text-emerald-400';
    let iconClass = 'fa-check-circle';
    let titleText = 'Autentikasi Berhasil';
    let titleColor = 'text-emerald-400';

    if (status === 'error') {
        borderColor = 'border-rose-500/30';
        iconBg = 'bg-rose-500/20 text-rose-400';
        iconClass = 'fa-triangle-exclamation';
        titleText = 'Aktivitas Gagal';
        titleColor = 'text-rose-400';
    } else if (status === 'info') {
        borderColor = 'border-sky-500/30';
        iconBg = 'bg-sky-500/20 text-sky-400';
        iconClass = 'fa-info-circle';
        titleText = 'Aktivitas Login';
        titleColor = 'text-sky-400';
    }

    toast.className = `pointer-events-auto flex items-start gap-3 p-4 rounded-2xl shadow-2xl border backdrop-blur-md bg-slate-900/90 transition-all duration-300 transform translate-y-[-20px] opacity-0 ${borderColor} text-white`;

    toast.innerHTML = `
        <div class="mt-0.5 flex-shrink-0 w-8 h-8 rounded-xl flex items-center justify-center ${iconBg}">
            <i class="fas ${iconClass} text-lg"></i>
        </div>
        <div class="flex-1 text-sm">
            <h4 class="font-semibold text-xs tracking-wider uppercase ${titleColor}">
                ${titleText}
            </h4>
            <p class="text-slate-300 text-xs mt-0.5 leading-relaxed font-normal">${message}</p>
        </div>
        <button class="text-slate-400 hover:text-white transition text-xs ml-1 close-toast-btn">
            <i class="fas fa-times"></i>
        </button>
    `;

    toast.querySelector('.close-toast-btn')?.addEventListener('click', () => {
        toast.classList.add('opacity-0', 'scale-95');
        setTimeout(() => toast.remove(), 300);
    });

    container.appendChild(toast);

    requestAnimationFrame(() => {
        toast.classList.remove('translate-y-[-20px]', 'opacity-0');
        toast.classList.add('translate-y-0', 'opacity-100');
    });

    setTimeout(() => {
        if (toast.parentElement) {
            toast.classList.add('opacity-0', 'scale-95');
            setTimeout(() => toast.remove(), 300);
        }
    }, 4500);
}

// Ekspos fungsi ke global window
window.showModernToast = showModernToast;

// 3. Listener Channel Private Auth Tracker
window.Echo.private('auth-tracker')
    .listen('.auth.status', (event: { status: string; message: string; userName: string }) => {
        console.log('Aktivitas Login Terdeteksi:', event);
        window.showModernToast(`${event.userName}: ${event.message}`, 'info');
    });

// 4. Inisialisasi Inertia (Hanya aktif jika ada elemen #app)
const appName = import.meta.env.VITE_APP_NAME || 'Laravel';
const appElement = document.getElementById('app');

if (appElement) {
    void createInertiaApp({
        title: (title) => (title ? `${title} - ${appName}` : appName),
        layout: (name) => {
            switch (true) {
                case name === 'Welcome':
                    return null;
                case name.startsWith('auth/'):
                    return AuthLayout;
                case name.startsWith('settings/'):
                case name.startsWith('teams/'):
                    return [AppLayout, SettingsLayout];
                default:
                    return AppLayout;
            }
        },
        withApp: (app) => {
            app.directive('focus', {
                mounted: (el: HTMLElement, shouldFocus: any) => {
                    if (shouldFocus.value !== false) {
                        el.focus();
                    }
                },
            });
        },
        progress: {
            color: '#4B5563',
        },
    });

    initializeTheme();
    initializeFlashToast();
}