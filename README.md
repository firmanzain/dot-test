# DOT Test

## Deskripsi
Implementasi Web Service dengan integrasi API Rajaongkir.

## Panduan Instalasi

Sebelum memulai instalasi proyek, pastikan Komputer telah terinstall **PHP** dan **Composer**.

1. Clone Repository<br>
```sh
git clone https://github.com/firmanzain/dot-test.git
```

2. Masuk ke Direktory Proyek<br>
```sh
cd dot-test
```

3. Copy .env.example ke .env<br>
```sh
cp .env.example .env
```

4. Konfigurasi .env<br>
Buka dan atur file .env sesuai konfigurasi database & API Key dai Raja Ongkir.

5. Buat Database<br>
Buat database sesuai konfigurasi di `.env`.

6. Instal Dependencies<br>
```sh
composer install
```

7. Jalankan Migrasi<br>
```sh
php artisan migrate
```

8. Jalankan Fetch Data Raja Ongkir<br>
```sh
php artisan fetch:data-province
php artisan fetch:data-cities
```

9. Jalankan Server Lokal<br>
```sh
php artisan serve
```
Buka proyek di browser: `http://localhost:8000`.

## Enviroment Variabel

#### Konfigurasi Database
DB_CONNECTION=mysql **(Tipe Database)**<br>
DB_HOST=localhost **(Host Database)**<br>
DB_PORT=3306 **(Port Database)**<br>
DB_DATABASE=dot_test **(Nama Database)**<br>
DB_USERNAME=dot_test **(User Database)**<br>
DB_PASSWORD=password **(Password Database)**<br>

#### API Raja Ongkir
RAJAONGKIR_URL=https://api.rajaongkir.com/starter **(URL Raja Ongkir)**<br>
RAJAONGKIR_KEY=0df6d5bf733214af6c6644eb8717c92c **(API Key Raja Ongkir)**<br>