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

    /* ---- Web Push subscription -------------------------------------------- */
    var URL_SUBSCRIBE = tag.dataset.pushSubscribe;
    var URL_UNSUBSCRIBE = tag.dataset.pushUnsubscribe;
    var URL_VAPID = tag.dataset.pushVapid;
    var CSRF = (document.querySelector('meta[name="csrf-token"]') || {}).content;

    function pushDidukung() {
        return 'serviceWorker' in navigator && 'PushManager' in window && 'Notification' in window;
    }

    function urlBase64ToUint8Array(base64) {
        var p = base64.replace(/-/g, '+').replace(/_/g, '/');
        var raw = atob(p);
        var arr = new Uint8Array(raw.length);
        for (var i = 0; i < raw.length; i++) { arr[i] = raw.charCodeAt(i); }
        return arr;
    }

    function updateTombolPush(aktif) {
        var btn = document.getElementById('pushToggle');
        if (!btn) { return; }
        btn.textContent = aktif ? 'Matikan Notifikasi' : 'Aktifkan Notifikasi';
        btn.dataset.aktif = aktif ? '1' : '0';
    }

    function kirimKeServer(url, body) {
        return fetch(url, {
            method: 'POST',
            credentials: 'same-origin',
            headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': CSRF, 'X-Requested-With': 'XMLHttpRequest' },
            body: JSON.stringify(body)
        });
    }

    function subscribe() {
        if (!pushDidukung()) { return; }
        Notification.requestPermission().then(function (izin) {
            if (izin !== 'granted') { return; }
            buatLangganan().catch(function () { /* diam */ });
        });
    }

    function unsubscribe() {
        navigator.serviceWorker.ready.then(function (reg) {
            reg.pushManager.getSubscription().then(function (sub) {
                if (!sub) { updateTombolPush(false); return; }
                kirimKeServer(URL_UNSUBSCRIBE, { endpoint: sub.endpoint }).then(function () {
                    sub.unsubscribe();
                    updateTombolPush(false);
                });
            });
        });
    }

    // Bersihkan langganan push browser saat formulir logout dikirim
    document.querySelectorAll('form[action*="/logout"]').forEach(function (form) {
        form.addEventListener('submit', function () {
            if (navigator.serviceWorker && navigator.serviceWorker.controller) {
                navigator.serviceWorker.ready.then(function (reg) {
                    reg.pushManager.getSubscription().then(function (sub) {
                        if (sub) {
                            sub.unsubscribe().catch(function () {});
                        }
                    });
                });
            }
        });
    });

    function togglePush() {
        var btn = document.getElementById('pushToggle');
        if (!btn) { return; }
        if (btn.dataset.aktif === '1') { unsubscribe(); } else { subscribe(); }
    }

    /**
     * Buat langganan push baru di perangkat ini, lalu kirim ke server.
     * Tidak memunculkan dialog izin: hanya dipakai saat izin sudah 'granted'.
     */
    function buatLangganan() {
        return fetch(URL_VAPID, { credentials: 'same-origin', headers: { 'Accept': 'application/json' } })
            .then(function (r) { return r.json(); })
            .then(function (d) {
                if (!d || !d.key) { throw new Error('VAPID key tidak tersedia'); }
                return navigator.serviceWorker.ready.then(function (reg) {
                    return reg.pushManager.subscribe({
                        userVisibleOnly: true,
                        applicationServerKey: urlBase64ToUint8Array(d.key)
                    });
                });
            })
            .then(function (sub) {
                var k = sub.toJSON();
                return kirimKeServer(URL_SUBSCRIBE, {
                    endpoint: k.endpoint,
                    keys: { p256dh: k.keys.p256dh, auth: k.keys.auth }
                });
            })
            .then(function () { updateTombolPush(true); });
    }

    /**
     * Selaraskan langganan perangkat ini dengan akun yang sedang login.
     * - Sudah punya langganan        : kirim ulang agar kepemilikan pindah ke akun ini.
     * - Belum punya, izin 'granted'  : buat langganan baru lalu kirim (tanpa dialog izin).
     * - Belum punya, izin lain       : diam, tunggu pengguna menekan tombol.
     */
    function syncSubscription() {
        if (!pushDidukung()) { return; }
        navigator.serviceWorker.ready.then(function (reg) {
            return reg.pushManager.getSubscription().then(function (sub) {
                if (sub) {
                    var k = sub.toJSON();
                    return kirimKeServer(URL_SUBSCRIBE, {
                        endpoint: k.endpoint,
                        keys: { p256dh: k.keys.p256dh, auth: k.keys.auth }
                    }).then(function () { updateTombolPush(true); });
                }

                if (Notification.permission === 'granted') {
                    return buatLangganan();
                }

                updateTombolPush(false);
            });
        }).catch(function () { /* diam: penanda tidak boleh mengganggu halaman */ });
    }

    function initPush() {
        if (!pushDidukung()) { return; }
        var btn = document.getElementById('pushToggle');
        if (btn) {
            btn.hidden = false;
            btn.addEventListener('click', togglePush);
        }
        syncSubscription();
    }

    initPush();
})();
