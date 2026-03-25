{{-- Admin bar is now in admin/layout.blade.php — this partial is kept for legacy compatibility --}}
<div class="flex items-center justify-between gap-4 mb-6 pb-4 border-b border-white/10">
    <div class="flex items-center gap-4">
        <span class="text-slate-400 text-sm font-medium">Admin</span>
        <a href="{{ route('admin.dashboard') }}" class="text-slate-300 hover:text-amber-400 text-sm font-semibold transition">🏠 Dashboard</a>
        <a href="{{ route('admin.weddings.index') }}" class="text-slate-300 hover:text-amber-400 text-sm font-semibold transition">💍 Undangan</a>
        <a href="{{ route('admin.orders.index') }}" class="text-slate-300 hover:text-amber-400 text-sm font-semibold transition">📋 Pesanan</a>
    </div>
    <form action="{{ route('admin.logout') }}" method="post" class="inline">
        @csrf
        <button type="submit" class="text-red-400 border border-red-800/50 hover:bg-red-900/30 px-4 py-1.5 rounded-lg text-sm font-semibold transition">Keluar</button>
    </form>
</div>
