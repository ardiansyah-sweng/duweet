Ketika volume data dalam basis data mendekati kapasitas maksimumnya, kinerja sistem biasanya akan mengalami penurunan dalam beberapa aspek utama:

1. Penurunan Kecepatan Akses Data
- Waktu query menjadi lebih lama.
    Proses pencarian, penyaringan (WHERE, JOIN, ORDER BY, dsb.) memerlukan waktu lebih lama karena jumlah data yang harus dipindai meningkat.
- Indeks menjadi kurang efisien.
Meskipun indeks membantu mempercepat pencarian, ukuran indeks juga akan membesar, sehingga pencarian di dalam indeks itu sendiri bisa melambat.
- Cache Database menjadi cepat penuh.
Data yang sering diakses mungkin tidak bisa seluruhnya disimpan di memori (RAM), menyebabkan sistem sering mengambil data dari disk — yang jauh lebih lambat.

2. Beban pada Memori dan Prosesor
- Sistem harus menggunakan lebih banyak RAM untuk menyimpan struktur data sementara (misalnya saat melakukan JOIN besar).
- CPU akan bekerja lebih berat untuk melakukan pengurutan, agregasi, dan analisis terhadap data yang semakin besar.

3. Efisiensi Pengelolaan Data Menurun
- Operasi insert/update/delete menjadi lebih lambat, karena sistem harus memperbarui lebih banyak indeks dan metadata.
- Fragmentasi data meningkat, menyebabkan data tersebar secara tidak efisien di disk, yang memperlambat akses.
- Proses backup dan restore membutuhkan waktu jauh lebih lama.
- Pemeliharaan database (maintenance) seperti reindexing atau vacuuming menjadi lebih berat dan sering diperlukan.

4. Masalah Skalabilitas
- Ketika ukuran data melebihi kapasitas server tunggal, skala vertikal (menambah RAM/CPU) tidak lagi cukup.
- Diperlukan skala horizontal (sharding atau partitioning) untuk mendistribusikan data ke beberapa server.