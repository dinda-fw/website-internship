# Dokumentasi API - Sistem Manajemen Inventaris

REST API ini merupakan **fitur bonus** yang dibangun menggunakan **Laravel Sanctum** (autentikasi berbasis Bearer Token). Base URL default (lokal): `http://localhost:8000/api`

## Autentikasi

Semua endpoint (kecuali `/login`) membutuhkan header berikut:

```
Authorization: Bearer {token}
Accept: application/json
```

### 1. Login (mendapatkan token)

`POST /api/login`

**Body:**
```json
{
  "email": "admin@telkomsel.test",
  "password": "password"
}
```

**Response 200:**
```json
{
  "message": "Login berhasil.",
  "user": { "id": 1, "name": "Admin Inventaris", "email": "admin@telkomsel.test", "role": { "name": "admin" } },
  "token": "1|xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx"
}
```

### 2. Logout

`POST /api/logout` — menghapus token yang sedang digunakan.

### 3. Profil User Saat Ini

`GET /api/me`

---

## Barang (Products)

| Method | Endpoint                | Akses          | Keterangan                          |
|--------|--------------------------|----------------|--------------------------------------|
| GET    | `/api/products`          | semua role     | List barang (pagination, `?q=` cari) |
| POST   | `/api/products`          | admin, staff   | Tambah barang baru                   |
| GET    | `/api/products/{id}`     | semua role     | Detail barang                        |
| PUT    | `/api/products/{id}`     | admin, staff   | Update barang                        |
| DELETE | `/api/products/{id}`     | admin          | Hapus barang                         |

**Contoh Request - Tambah Barang**

`POST /api/products`
```json
{
  "code": "BRG-0099",
  "name": "Mouse Wireless Logitech",
  "category_id": 1,
  "stock": 20,
  "location": "Gudang IT Lt. 2",
  "condition": "baik"
}
```

**Contoh Response 201:**
```json
{
  "id": 13,
  "code": "BRG-0099",
  "name": "Mouse Wireless Logitech",
  "category_id": 1,
  "stock": 20,
  "total_stock": 20,
  "condition": "baik"
}
```

---

## Peminjaman (Borrowings)

| Method | Endpoint                              | Akses        | Keterangan                       |
|--------|-----------------------------------------|--------------|-----------------------------------|
| GET    | `/api/borrowings`                       | semua role   | List peminjaman (`?status=`)     |
| POST   | `/api/borrowings`                       | admin, staff | Catat peminjaman baru            |
| GET    | `/api/borrowings/{id}`                  | semua role   | Detail peminjaman                |
| PATCH  | `/api/borrowings/{id}/return`           | admin, staff | Proses pengembalian barang       |

**Contoh Request - Tambah Peminjaman**

`POST /api/borrowings`
```json
{
  "borrower_name": "Budi Santoso",
  "borrow_date": "2026-07-05",
  "due_date": "2026-07-12",
  "items": [
    { "product_id": 1, "quantity": 1 },
    { "product_id": 7, "quantity": 2 }
  ]
}
```

**Contoh Request - Pengembalian**

`PATCH /api/borrowings/5/return`
```json
{
  "return_date": "2026-07-10"
}
```

---

## Dashboard

`GET /api/dashboard/summary`

**Response:**
```json
{
  "total_barang": 63,
  "barang_tersedia": 58,
  "jenis_barang": 12,
  "stok_menipis": 3
}
```

---

## Kode Status HTTP yang Digunakan

| Kode | Arti                                             |
|------|---------------------------------------------------|
| 200  | Sukses                                             |
| 201  | Data berhasil dibuat                               |
| 401  | Belum login / token tidak valid                    |
| 403  | Tidak memiliki akses (role tidak sesuai)           |
| 422  | Validasi gagal / stok tidak mencukupi              |
| 404  | Data tidak ditemukan                               |

---

## Contoh Uji Coba dengan cURL

```bash
# Login
curl -X POST http://localhost:8000/api/login \
  -H "Content-Type: application/json" \
  -H "Accept: application/json" \
  -d '{"email":"admin@telkomsel.test","password":"password"}'

# Ambil daftar barang (gunakan token dari hasil login)
curl http://localhost:8000/api/products \
  -H "Accept: application/json" \
  -H "Authorization: Bearer {token}"
```
