/* Penanda notifikasi + lonceng. Hanya membaca; status "sudah dibuka" untuk agenda
 * dan sapaan disimpan di perangkat (localStorage), bukan di database. */
(function () {
    'use strict';

    var tag = document.currentScript;
    if (!tag || !tag.dataset.url || !tag.dataset.uid) { return; }

    var URL_RINGKASAN = tag.dataset.url;
    var KUNCI = 'fic_dibuka_' + tag.dataset.uid;
    var TIGA_HARI = 3 * 24 * 3600;
    var path = location.pathname.replace(/\/+$/, '') || '/';

    function muat() {
        try { return JSON.parse(localStorage.getItem(KUNCI)) || null; } catch (e) { return null; }
    }
    function simpan(s) {
        try { localStorage.setItem(KUNCI, JSON.stringify(s)); } catch (e) { /* penyimpanan penuh/dimatikan */ }
    }
    function ids(arr) { return arr.map(function (x) { return x.id; }); }

    function proses(d) {
        var s = muat();

        if (!s) {
            // Pemakaian pertama: yang lebih tua dari 3 hari dianggap sudah dilihat.
            var batas = d.sekarang - TIGA_HARI;
            s = {
                agenda: ids(d.agenda.filter(function (x) { return x.c < batas; })),
                sapaan: ids(d.sapaan.filter(function (x) { return x.c < batas; }))
            };
        }

        // Membuka halaman terkait = menandai semuanya sudah dibuka.
        if (path === '/agenda') { s.agenda = ids(d.agenda); }
        if (path === '/sapaan-provinsial') { s.sapaan = ids(d.sapaan); }
        simpan(s);

        var baruAgenda = d.agenda.filter(function (x) { return s.agenda.indexOf(x.id) === -1; });
        var baruSapaan = d.sapaan.filter(function (x) { return s.sapaan.indexOf(x.id) === -1; });
        var baruPesan = d.pesan || 0;

        tampil('pesan', baruPesan);
        tampil('agenda', baruAgenda.length);
        tampil('sapaan', baruSapaan.length);

        var total = baruPesan + baruAgenda.length + baruSapaan.length;
        tampil('total', total);
        isiPanel(d.pesan_daftar || [], baruPesan, baruAgenda, baruSapaan);

        if (navigator.setAppBadge) {
            (total > 0 ? navigator.setAppBadge(total) : navigator.clearAppBadge()).catch(function () {});
        }
    }

    function tampil(jenis, jumlah) {
        var daftar = document.querySelectorAll('[data-notif="' + jenis + '"]');
        for (var i = 0; i < daftar.length; i++) {
            daftar[i].textContent = jumlah > 99 ? '99+' : String(jumlah);
            daftar[i].hidden = jumlah <= 0;
        }
    }

    /* ---- Panel lonceng ---------------------------------------------------- */
    var panel = document.getElementById('notifPanel');
    var tombol = document.getElementById('notifBell');

    function el(nama, kelas, teks) {
        var e = document.createElement(nama);
        if (kelas) { e.className = kelas; }
        if (teks !== undefined) { e.textContent = teks; }
        return e;
    }

    function baris(jenis, judul, sub, url) {
        var a = el('a', 'notif-item');
        a.href = url;
        a.appendChild(el('span', 'notif-item-jenis', jenis));
        a.appendChild(el('span', 'notif-item-judul', judul || '(tanpa judul)'));
        if (sub) { a.appendChild(el('span', 'notif-item-sub', sub)); }
        return a;
    }

    function isiPanel(pesanDaftar, jumlahPesan, agenda, sapaan) {
        var isi = document.getElementById('notifList');
        if (!isi) { return; }
        while (isi.firstChild) { isi.removeChild(isi.firstChild); }

        var ada = false;
        pesanDaftar.forEach(function (p) { isi.appendChild(baris('Pesan Khusus', p.t, 'Belum dibaca', p.u)); ada = true; });
        if (jumlahPesan > pesanDaftar.length) {
            isi.appendChild(baris('Pesan Khusus', 'Dan ' + (jumlahPesan - pesanDaftar.length) + ' pesan lainnya', '', '/pesan-khusus'));
            ada = true;
        }
        sapaan.forEach(function (x) { isi.appendChild(baris(x.k || 'Sapaan', x.t, 'Baru', x.u)); ada = true; });
        agenda.slice(0, 8).forEach(function (x) { isi.appendChild(baris('Agenda', x.t, x.w, x.u)); ada = true; });
        if (agenda.length > 8) {
            isi.appendChild(baris('Agenda', 'Dan ' + (agenda.length - 8) + ' agenda baru lainnya', '', '/agenda'));
        }

        var kosong = document.getElementById('notifKosong');
        if (kosong) { kosong.hidden = ada; }
    }

    function bukaTutup(buka) {
        if (!panel || !tombol) { return; }
        var tampilkan = typeof buka === 'boolean' ? buka : panel.hidden;
        panel.hidden = !tampilkan;
        tombol.setAttribute('aria-expanded', tampilkan ? 'true' : 'false');
        if (tampilkan) { ambil(true); }
    }

    if (tombol && panel) {
        tombol.addEventListener('click', function (e) { e.stopPropagation(); bukaTutup(); });
        panel.addEventListener('click', function (e) { e.stopPropagation(); });
        document.addEventListener('click', function () { bukaTutup(false); });
        document.addEventListener('keydown', function (e) { if (e.key === 'Escape') { bukaTutup(false); } });
    }

    /* ---- Pengambilan data ------------------------------------------------- */
    function ambil(paksa) {
        if (!paksa && document.visibilityState === 'hidden') { return; }
        fetch(URL_RINGKASAN, {
            credentials: 'same-origin',
            cache: 'no-store',
            headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
        }).then(function (r) {
            if (!r.ok) { throw new Error('status ' + r.status); }
            return r.json();
        }).then(proses).catch(function () { /* diam: penanda tidak boleh mengganggu halaman */ });
    }

    ambil();
    document.addEventListener('visibilitychange', function () { if (document.visibilityState === 'visible') { ambil(); } });
    window.addEventListener('pageshow', function (e) { if (e.persisted) { ambil(); } });
    setInterval(ambil, 60000);
})();
