## 1. Seiring bertambahnya volume data dalam basis data hingga mendekati kapasitas maksimumnya, bagaimana dampaknya terhadap kinerja sistem, seperti kecepatan akses dan efisiensi pengelolaan data?

Jawab : Dampaknya sebagai berikut
# Penurunan Kecepatan Akses Data
menurunnya kecepatan akses pada data jika datanya sangat banyak, waktu pencarian data akan makin lambat 
karena banyak baris(rows) yang harus di scan atau di pindai.
dan Query seperti SELECT * FROM ... tanpa kondisi (WHERE) akan semakin lama diproses.

# Efisiensi Pengelolaan Data Menurun
menurunnya efisiensi pengelolaan data, Operasi seperti backup, dan restore menjadi jauh lebih lambat.
Fragmentasi data pada disk jadi meningkat, karena itu akses ke disk jadi kuarng efisien.

# Beban processor dan mememori jadi lebih berat
query yang agak kompleks seperti query JOIN, ORDER BY dan lain lain akan memerlukan lebih banyak RAM dan CPU time
