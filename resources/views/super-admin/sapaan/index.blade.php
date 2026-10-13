<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manajemen Sapaan Provinsial</title>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
    <style>
        body{background:#EEF2F6;font-family:'Plus Jakarta Sans','Inter',sans-serif;margin:0;display:flex;justify-content:center}.container{width:100%;max-width:480px;background:#F6F8FC;min-height:100vh}.header{background:linear-gradient(135deg,#003E97,#1455C0);color:#fff;padding:22px 20px;border-bottom-left-radius:20px;border-bottom-right-radius:20px}.title{font-size:20px;font-weight:800;margin:0}.body{padding:20px 16px}.form{display:flex;flex-direction:column;gap:12px}.field label{font-size:12px;font-weight:700;color:#667085;text-transform:uppercase}.field input,.field textarea{width:100%;box-sizing:border-box;padding:12px 14px;border-radius:12px;border:1px solid #E4E7EC;font-size:14px;background:#fff}.btn{border:none;border-radius:12px;padding:14px 18px;font-weight:800;background:#1455C0;color:#fff}.back{display:block;text-align:center;margin-top:12px;color:#1455C0;text-decoration:none;font-weight:700}
    </style>
</head>
<body>
<div class="container">
    <div class="header"><h1 class="title">Sapaan &amp; Pengumuman</h1></div>
    <div class="body">
        @if(session('success'))
            <div style="background:#ECFDF3;color:#027A48;padding:12px;border-radius:12px;font-size:13px;font-weight:700;margin-bottom:12px;">
                {{ session('success') }}
            </div>
        @endif
        <form method="POST" action="{{ route('superadmin.sapaan.store') }}" enctype="multipart/form-data" class="form">
            @csrf
            <div class="field"><label>Judul Sapaan</label><input name="title" required></div>
            <div class="field"><label>Nama Penulis / Pimpinan</label><input name="author_name" value="Br. Agustinus Giwal Santoso, FIC"></div>
            <div class="field"><label>Foto (Opsional)</label><input type="file" name="image" accept="image/*"></div>
            <div class="field"><label>Pesan Rohani / Arahan</label><textarea name="content" rows="5" required></textarea></div>
            <button type="submit" class="btn">PUBLIKASI SAPAAN</button>
        </form>
        <a href="{{ route('superadmin.fase2-check') }}" class="back">← Kembali ke Pengecekan Fase 2</a>
    </div>
</div>
</body>
</html>