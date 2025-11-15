Nama: Diky Alfiansyah NIM: 2400018184 

Soal 1

Seiring bertambahnya volume data dalam basis data hingga mendekati kapasitas maksimumnya, bagaimana dampaknya terhadap kinerja sistem, seperti kecepatan akses dan efisiensi pengelolaan data? 

Jawaban:

Jika data di database kita (seperti proyek Duweet) bertambah banyak sampai hampir penuh, dampaknya pasti akan negatif dan membuat sistem jadi lambat.

Kinerja sistem akan melambat di dua area utama: (1) saat kita mengambil data, dan (2) saat kita mengelola data (input, edit, hapus).

1. Dampak pada Kecepatan Akses Data (Query SELECT)

Saat kita mau mengambil atau melihat data, prosesnya akan jadi jauh lebih lambat:

Query Jadi Lambat: Perintah SELECT untuk melihat data (apalagi yang butuh JOIN antar tabel, misalnya users dan transactions) akan butuh waktu proses yang lama sekali.

Indeks Jadi Tidak Efektif: Indeks, yang seharusnya mempercepat pencarian, akan ikut membengkak menjadi sangat besar. Akibatnya, database butuh waktu lama juga hanya untuk membaca indeksnya.

Sering Terjadi Full Table Scan: Kalau query-nya rumit, kadang sistem "menyerah" pakai indeks dan akhirnya terpaksa membaca seluruh isi tabel dari awal sampai akhir satu per satu. Ini adalah proses yang paling lambat.

RAM Kewalahan (Cache Turun): Data yang bisa disimpan di RAM (yang cepat) jadi sangat sedikit dibanding total data. Akhirnya, sistem jadi terlalu sering bolak-balik membaca data dari hard disk (yang jauh lebih lambat).

2. Dampak pada Efisiensi Pengelolaan Data (INSERT, UPDATE, DELETE)

Saat kita mau mengelola data (tambah, ubah, atau hapus), prosesnya juga akan melambat drastis:

Input Data Baru (INSERT) Lambat: Waktu kita menginput data baru (misalnya, pengguna Duweet mencatat transaksi baru), sistem tidak hanya menulis data ke tabel, tapi juga harus memperbarui semua indeks yang terkait. Memperbarui indeks yang sudah besar ini adalah proses yang berat dan makan waktu.

Edit dan Hapus (UPDATE/DELETE) Lebih Lambat Lagi: Proses ini lebih parah, karena sistem harus mencari dulu datanya (yang sudah lambat), baru bisa diubah atau dihapus. Prosesnya jadi "dobel" lambatnya.

Proses Backup dan Restore Jadi Susah: Melakukan backup database akan butuh waktu sangat lama dan menghasilkan file yang ukurannya raksasa. Jika terjadi masalah dan kita perlu restore (mengembalikan data), sistem bisa down (mati) selama berjam-jam.

Perawatan (Maintenance) Jadi Sulit: Tugas simpel seperti menambah kolom baru (ALTER TABLE) bisa memakan waktu sangat lama dan membuat tabel "dikunci", yang artinya aplikasi tidak bisa mengakses tabel itu sementara waktu.