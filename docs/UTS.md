# UTS
- Nama        : Nabilah Elsa Rahmadani
- NIM         : 2400018235
- Kelas       : D
- Mata Kuliah : Basis Data

# Soal
Seiring bertambahnya volume data dalam basis data hingga mendekati kapasitas maksimumnya, bagaimana dampaknya terhadap kinerja sistem, seperti kecepatan akses dan efisiensi pengelolaan data?

# Jawaban
Seiring bertambahnya volume data dalam basis data hingga mendekati kapasitas maksimumnya, kinerja sistem dapat mengalami penurunan secara signifikan. Salah satu dampak utama adalah berkurangnya kecepatan akses data. Hal ini terjadi karena ukuran tabel dan indeks semakin besar, sehingga proses pencarian, pembacaan, maupun penulisan data memerlukan waktu lebih lama. Selain itu, ketika data tidak lagi muat di memori atau cache database, sistem harus sering melakukan pembacaan langsung dari disk, yang memiliki kecepatan jauh lebih lambat dibandingkan akses ke memori. Akibatnya, waktu respon query meningkat dan beban kerja server ikut naik.
Selain itu, efisiensi pengelolaan data juga menurun. Operasi seperti insert, update, dan delete membutuhkan lebih banyak proses untuk mengelola struktur data yang sudah membesar, termasuk risiko fragmentasi dan peningkatan biaya perawatan indeks. Database juga memerlukan waktu lebih lama untuk menjalankan proses pemeliharaan seperti reindex, vacuum, atau backup. Ketika penyimpanan mendekati penuh, beberapa sistem bahkan dapat mengalami penundaan transaksi, error penulisan, hingga downtime apabila ruang tidak lagi tersedia untuk log dan operasi internal. Oleh sebab itu, penting untuk melakukan manajemen data seperti pengarsipan, pembersihan data yang tidak diperlukan, optimasi query, serta penambahan kapasitas penyimpanan agar kinerja sistem tetap stabil.