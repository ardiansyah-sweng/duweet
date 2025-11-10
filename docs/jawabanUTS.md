Anggita Ramadanis B
2400018191
kelas D


Secara sederhana, ketika data di database sudah hampir penuh (mendekati kapasitas maksimum), kinerjanya akan melambat secara drastis.

Ini bisa diibaratkan seperti gudang yang tadinya rapi, sekarang jadi penuh sesak. Pekerja gudang (database) jadi jauh lebih sulit dan butuh waktu lebih lama untuk mencari barang atau menyimpan barang baru.

Berikut rincian dampaknya:

1. Dampak ke Kecepatan Akses (Mencari Data / SELECT)
Ini adalah efek yang paling dirasakan pengguna, misalnya saat admin membuka halaman laporan.

Pencarian Jadi Sangat Lambat: Saat data masih sedikit, database bisa menemukan data yang kamu minta dengan cepat. Tapi saat data sudah ada puluhan juta, database harus "mencari" di tumpukan yang jauh lebih besar. Waktu pencarian ini membengkak.

"Daftar Isi" (Indeks) Menjadi Terlalu Tebal: Untuk mempercepat pencarian, database menggunakan "Indeks" (bayangkan ini seperti daftar isi di buku).

Saat data sedikit: Daftar isinya tipis dan gampang dibaca.

Saat data penuh: Daftar isinya jadi setebal kamus besar.

Akibatnya, database butuh waktu lebih lama hanya untuk membaca daftar isinya sebelum ia bisa menemukan datanya. Proses pencariannya jadi "bolak-balik" dan makan waktu.

Query JOIN (Menggabungkan Tabel) Jadi Berat: Jika kamu perlu menggabungkan data (misal: tabel Transactions dan Financial_Accounts), database harus mencocokkan data dari dua "buku tebal" sekaligus. Ini menjadi pekerjaan yang sangat berat dan lambat.

2. Dampak ke Efisiensi Pengelolaan Data (Menulis Data / INSERT, UPDATE)
Tidak hanya mencari, proses menyimpan atau mengubah data juga jadi lambat.

Menyimpan Data Baru (INSERT) Jadi Repot: Saat kamu menyimpan 1 transaksi baru, database tidak hanya meletakkan data itu. Ia juga harus memperbarui semua "Daftar Isi" (Indeks) yang terkait dengan tabel itu.

Jika tabel transactions punya 5 indeks (di user_id, tanggal, dll), maka 1 kali INSERT sama dengan 6 kali kerjaan.

Memperbarui "daftar isi" yang sudah tebal itu jauh lebih repot daripada memperbarui daftar isi yang masih tipis.

Mengubah atau Menghapus Data (UPDATE / DELETE) Jadi Kerja Dobel: Ini proses yang paling lambat:

Tahap 1 (Mencari): Database harus mencari dulu data mana yang mau diubah/dihapus (yang mana proses ini sudah lambat).

Tahap 2 (Mengubah): Setelah ketemu, data diubah, dan database harus ngerapiin lagi semua daftar isinya.

3. Dampak Saat "Kapasitas Maksimum" Tercapai
Ini adalah skenario terburuknya:

Penyimpanan Gagal Total: Jika "kapasitas maksimum" berarti ruang penyimpanannya benar-benar 0 (nol), maka semua proses INSERT (menyimpan data baru) akan gagal total. Sistemmu akan berhenti berfungsi karena tidak bisa mencatat data baru.

Perawatan (Maintenance) Jadi Berisiko: Mau melakukan hal simpel seperti menambah satu kolom baru (ALTER TABLE) di tabel yang sudah penuh? Proses ini bisa memakan waktu berjam-jam. Selama proses itu, tabel bisa "dikunci" sehingga aplikasi tidak bisa menggunakannya (terjadi downtime atau error).