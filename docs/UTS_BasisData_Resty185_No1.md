# UTS Basis Data – Soal Nomor 1  
**Nama              :** Resty Amandha  
**NIM               :** 2400018185  
**Kelas/ Semester   :** D / 3  
**Tanggal           :** 10 November 2025  
**Proyek            :** Duweet  
**Dosen Pengampu    :** Dr. Ardiansyah  

---

### Pertanyaan  
Seiring bertambahnya volume data dalam basis data hingga mendekati kapasitas maksimumnya, bagaimana dampaknya terhadap kinerja sistem, seperti kecepatan akses dan efisiensi pengelolaan data?

---

### Jawaban:  
Dalam proyek **Duweet**, setiap pengguna bisa memiliki banyak akun keuangan dan transaksi yang terus bertambah seiring waktu. Saat data semakin banyak dan hampir mencapai kapasitas maksimum basis data, sistem akan mulai terasa lebih lambat misalnya ketika mengambil daftar akun atau riwayat transaksi pengguna.  
Hal ini terjadi karena database harus memproses lebih banyak baris data dan melakukan relasi antar tabel yang semakin kompleks.  

Untuk menjaga agar sistem tetap cepat dan efisien, Duweet perlu menambahkan **indeks pada kolom yang sering digunakan untuk pencarian**, membatasi jumlah data yang ditampilkan dengan *pagination*, serta **menyimpan data yang sering diakses menggunakan caching**.  
Dengan langkah-langkah tersebut, Duweet bisa tetap berjalan lancar meskipun jumlah datanya terus bertambah besar.

