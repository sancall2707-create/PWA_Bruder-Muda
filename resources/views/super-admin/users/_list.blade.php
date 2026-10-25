<div id="usersContainer" class="list">
    @forelse($users as $u)
        @php
            $words = preg_split('/\s+/', trim($u->name)) ?: [];
            $initials = strtoupper(substr($words[0] ?? 'B', 0, 1).substr($words[1] ?? '', 0, 1));
            $monthDone = ($u->bimbingan_month_count ?? 0) + ($u->wawanhati_month_count ?? 0);
            $isStaffOrAdmin = in_array($u->role, ['admin', 'admin_ketua_tppbm', 'super_admin'], true);
        @endphp
        <article class="card">
            @if ($u->profile_photo && \Illuminate\Support\Facades\Storage::disk('public')->exists($u->profile_photo))
                <img src="{{ route('superadmin.users.photo', [$u, 'v' => substr(md5($u->profile_photo), 0, 8)]) }}" alt="Foto {{ $u->name }}" class="avatar" style="object-fit:cover; padding:0; width:44px; height:44px; border-radius:50%;">
            @else
                <div class="avatar" aria-hidden="true">{{ $initials }}</div>
            @endif
            <div style="flex:1;">
                <div class="name">{{ $u->name }}</div>
                <div class="loc">{{ '@'.$u->username }} · {{ $u->community_location ?? 'Komunitas belum diset' }}</div>
                <div class="meta">
                    <span class="pill" style="color: {{ $u->is_active ? '#027A48' : '#B42318' }}; background: {{ $u->is_active ? '#ECFDF3' : '#FEF3F2' }};">{{ $u->is_active ? 'Aktif' : 'Nonaktif' }}</span>

                    @if($u->role === 'super_admin')
                        <span class="pill" style="color:#B42318; background:#FEF3F2; font-weight:700;">Super Admin</span>
                    @elseif($u->role === 'admin_ketua_tppbm')
                        <span class="pill" style="color:#6941C6; background:#F9F5FF; font-weight:700;">Admin Ketua TPPBM</span>
                    @elseif($u->role === 'admin')
                        <span class="pill" style="color:#026AA2; background:#F0F9FF; font-weight:700;">Admin TPPBM</span>
                    @else
                        <span class="pill" style="color:#1455C0; background:#EFF4FF;">{{ $monthDone }}/2 bulan ini</span>
                    @endif
                </div>
                <div class="actions">
                    @if(! $isStaffOrAdmin)
                        <a href="{{ route('superadmin.users.track-record', $u) }}" class="btn">Track Record</a>
                        <a href="{{ route('superadmin.users.notes.create', $u) }}" class="btn btn-success">Tambahkan Catatan</a>
                        <a href="{{ route('superadmin.users.messages.create', $u) }}" class="btn btn-msg">Kirim Pesan</a>
                    @endif

                    @if(auth()->user()->role === 'super_admin')
                        <a href="{{ route('superadmin.users.account.edit', $u) }}" class="btn btn-account">Kelola Akun</a>
                        @if(auth()->id() !== $u->id)
                            <form method="POST" action="{{ route('superadmin.users.status', $u) }}">
                                @csrf
                                <button type="submit" class="btn btn-muted">{{ $u->is_active ? 'Nonaktifkan' : 'Aktifkan' }}</button>
                            </form>
                            <form method="POST" action="{{ route('superadmin.users.destroy', $u) }}" onsubmit="return confirm('Hapus akun {{ $u->name }} dari daftar? Riwayat laporannya tetap tersimpan.');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger">Hapus</button>
                            </form>
                        @endif
                    @endif
                </div>
            </div>
        </article>
    @empty
        <div class="card" style="text-align:center; color:#667085;">Belum ada data bruder muda atau admin.</div>
    @endforelse
</div>

<div id="usersPagination">
    {{ $users->links() }}
</div>
