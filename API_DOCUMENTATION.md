# API Documentation - Ban Vé Máy Bay

**Base URL:** `http://localhost/banvemaybay/public/api/v1`

---

## Mục Lục
1. [Health Check](#health-check)
2. [Authentication (Auth)](#authentication-auth)
3. [Bookings - Khách Hàng](#bookings---khách-hàng)
4. [My Tickets - Vé Của Tôi](#my-tickets---vé-của-tôi)
5. [Flights - Danh Sách Chuyến Bay](#flights---danh-sách-chuyến-bay)
6. [Admin - Máy Bay](#admin---máy-bay)
7. [Admin - Chuyến Bay](#admin---chuyến-bay)
8. [Admin - Vé Máy Bay](#admin---vé-máy-bay)
9. [Admin - Khách Hàng](#admin---khách-hàng)
10. [Admin - Đặt Vé](#admin---đặt-vé)
11. [Admin - Doanh Thu](#admin---doanh-thu)

---

## Health Check

### GET /ping
Kiểm tra API có hoạt động không.

**Thunder Client:**
- **Method**: GET
- **URL**: http://localhost/banvemaybay/api/v1/ping
- **Headers**: (không cần)

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "pong": true,
    "time": "2026-06-20T18:30:45+00:00"
  },
  "message": "pong"
}
```

---

## Authentication (Auth)

### POST /auth/login
Đăng nhập để lấy token JWT.

**Thunder Client:**
- **Method**: POST
- **URL**: http://localhost/banvemaybay/api/v1/auth/login
- **Headers**: `Content-Type: application/json`
- **Body** (Raw JSON):
Bearer eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJzdWIiOjUsInJvbGUiOiJhZG1pbiIsImVtYWlsIjoiYWRtaW5AZ21haWwuY29tIiwiaWF0IjoxNzgxOTU5NTQwLCJleHAiOjE3ODE5NjMxNDB9.OrhP7AmF_ZmIGUbQVA4IhKnEoXZhc4cHQjzrsS1iRqY
```json
{
  "email": "admin@gmail.com",
  "password": "123456"
}
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "token": "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...",
    "user": {
      "id": 5,
      "ten": "Admin",
      "email": "admin@gmail.com",
      "vai_tro": "admin"
    }
  },
  "message": "Dang nhap thanh cong"
}
```

**Sau khi login:**
- Copy giá trị `data.token` từ Response
- Set vào environment variable `{{adminToken}}` hoặc `{{token}}`

---

### POST /auth/register
Đăng ký tài khoản khách hàng mới.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/auth/register
- **Headers**: `Content-Type: application/json`
- **Body** (Raw JSON):
```json
{
  "ten": "Nguyen Van A",
  "email": "nguyenvana@gmail.com",
  "password": "123456"
}
```

**Response (201 Created):**
```json
{
  "ok": true,
  "data": {
    "ten": "Nguyen Van A",
    "email": "nguyenvana@gmail.com"
  },
  "message": "Dang ky thanh cong"
}
```

---

### GET /auth/me
Lấy thông tin người dùng hiện tại (cần token).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/auth/me`
- **Headers**: 
  - `Authorization: Bearer {{token}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "id": 5,
    "ten": "Admin",
    "email": "admin@gmail.com",
    "vai_tro": "admin"
  },
  "message": "Thong tin nguoi dung"
}
```

---

### POST /auth/logout
Đăng xuất (xóa session).

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/auth/logout`
- **Headers**: (không cần)

**Response (200 OK):**
```json
{
  "ok": true,
  "message": "Da dang xuat"
}
```

---

## Bookings - Khách Hàng

### GET /bookings
Lấy danh sách đặt vé của khách hàng (cần token).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/bookings`
- **Headers**: `Authorization: Bearer {{token}}`
- **Query Params** (optional):
  - `status`: `cart` | `paid` | `cancelled` (mặc định: `cart`)

**Ví dụ URL với params:**
```
{{baseUrl}}/bookings?status=paid
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [
    {
      "id": 44,
      "chuyen_bay_id": 15,
      "ve_id": 10,
      "trang_thai": "paid",
      "so_ghe_dat": 2,
      "tong_tien": 20000,
      "dat_luc": "2026-01-13 14:16:15",
      "thanh_toan_luc": "2026-01-13 14:16:36",
      "chuyen_bay": {
        "so_hieu": "VN123456",
        "noi_di": "Nam định",
        "noi_den": "Moscow",
        "gio_khoi_hanh": "2026-01-21 21:11:00",
        "gio_ha_canh": "2026-01-29 21:11:00"
      },
      "ve": {
        "ma_ve": "VN123456-THUONG GIA",
        "hang_ve": "Thuong gia"
      }
    }
  ],
  "message": "Danh sach dat ve"
}
```

---

### POST /bookings
Tạo đặt vé mới.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/bookings`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{token}}`
- **Body** (Raw JSON):
```json
{
  "chuyen_bay_id": 15,
  "ve_id": 10,
  "so_luong": 2,
  "hanh_khach": [
    {
      "ten": "Nguyen Van A",
      "dien_thoai": "0123456789",
      "email": "nguyenvana@gmail.com",
      "gioi_tinh": "Nam",
      "tuoi": 30
    },
    {
      "ten": "Tran Thi B",
      "dien_thoai": "0987654321",
      "email": "tranthib@gmail.com",
      "gioi_tinh": "Nu",
      "tuoi": 28
    }
  ]
}
```

**Response (201 Created):**
```json
{
  "ok": true,
  "data": {
    "booking_id": 45,
    "trang_thai": "cart"
  },
  "message": "Da tao dat ve"
}
```

---

### GET /bookings/{id}
Lấy chi tiết một đặt vé (cần token).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/bookings/44`
- **Headers**: `Authorization: Bearer {{token}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "booking": {
      "id": 44,
      "chuyen_bay_id": 15,
      "ve_id": 10,
      "trang_thai": "paid",
      "so_ghe_dat": 2,
      "tong_tien": 20000,
      "dat_luc": "2026-01-13 14:16:15",
      "thanh_toan_luc": "2026-01-13 14:16:36",
      "chuyen_bay": { ... },
      "ve": { ... }
    },
    "hanh_khach": [
      {
        "ten_hanh_khach": "hop",
        "dien_thoai": "9584",
        "email_hanh_khach": "hopthuthom@gmail.com",
        "gioi_tinh": "Nam",
        "tuoi": 18,
        "loai_ve": "Thuong gia",
        "gia_ve": 10000,
        "so_ghe": 1
      }
    ]
  },
  "message": "Chi tiet dat ve"
}
```

---

### PUT /bookings/{id}
Cập nhật số lượng vé trong đặt vé (status phải là `cart`).

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/bookings/44`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{token}}`
- **Body** (Raw JSON):
```json
{
  "so_luong": 3
}
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": null,
  "message": "Da cap nhat dat ve"
}
```

---

### DELETE /bookings/{id}
Xóa đặt vé (status phải là `cart`).

**Thunder Client:**
- **Method**: DELETE
- **URL**: `{{baseUrl}}/bookings/44`
- **Headers**: `Authorization: Bearer {{token}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": null,
  "message": "Da xoa dat ve"
}
```

---

### POST /bookings/{id}/checkout
Thanh toán / hoàn tất đặt vé.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/bookings/44/checkout`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{token}}`
- **Body** (Raw JSON):
```json
{
  "ten_thanh_toan": "Nguyen Van A",
  "dien_thoai_thanh_toan": "0123456789",
  "email_thanh_toan": "payer@gmail.com",
  "dia_chi_thanh_toan": "123 Nguyen Hue, Hanoi",
  "phuong_thuc_thanh_toan": "direct"
}
```

**Phương thức thanh toán:** `direct` | `atm` | `momo`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "booking_id": 44,
    "trang_thai": "paid"
  },
  "message": "Thanh toan thanh cong"
}
```

---

## My Tickets - Vé Của Tôi

### GET /my-tickets
Lấy danh sách vé của khách hàng (cần token).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/my-tickets`
- **Headers**: `Authorization: Bearer {{token}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [
    {
      "ten_hanh_khach": "hop",
      "dien_thoai": "9584",
      "email_hanh_khach": "hopthuthom@gmail.com",
      "gioi_tinh": "Nam",
      "tuoi": 18,
      "loai_ve": "Thuong gia",
      "gia_ve": 10000,
      "so_ghe": 1
    }
  ],
  "message": "Danh sach ve cua khach hang"
}
```

---

### GET /my-tickets/{id}
Lấy chi tiết một vé của khách hàng.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/my-tickets/12`
- **Headers**: `Authorization: Bearer {{token}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": { ... },
  "message": "Chi tiet ve"
}
```

---

### PUT /my-tickets/{id}
Cập nhật thông tin hành khách trên vé.

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/my-tickets/12`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{token}}`
- **Body** (Raw JSON):
```json
{
  "ten_hanh_khach": "Nguyen Van B",
  "dien_thoai": "0111111111",
  "email_hanh_khach": "nguyenvanb@gmail.com",
  "gioi_tinh": "Nam",
  "tuoi": 32,
  
}
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": null,
  "message": "Da cap nhat ve"
}
```

---

## Flights - Danh Sách Chuyến Bay

### GET /flights
Lấy danh sách tất cả chuyến bay (public, không cần token).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/flights`
- **Query Params** (optional):
  - `limit`: Số bản ghi tối đa
  - `offset`: Vị trí bắt đầu

**Ví dụ URL với params:**
```
{{baseUrl}}/flights?limit=10&offset=0
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [
    {
      "id": 15,
      "so_hieu": "VN123456",
      "noi_di": "Nam định",
      "noi_den": "Moscow",
      "gio_khoi_hanh": "2026-01-21 21:11:00",
      "gio_ha_canh": "2026-01-29 21:11:00",
      "gia_thuong": 9000,
      "gia_thuong_gia": 10000,
      "ghe_con": 88,
      "may_bay_id": 4,
      "may_bay": {
        "ma_may_bay": "A123",
        "ten_may_bay": "VietnamElines",
        "hang_may_bay": "Vietnam Airlines"
      }
    }
  ],
  "message": "Danh sach chuyen bay"
}
```

---

### GET /flights/search
Tìm kiếm chuyến bay theo tiêu chí.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/flights/search`
- **Query Params**:
  - `noi_di` (optional): Nơi đi
  - `noi_den` (optional): Nơi đến
  - `ngay` (optional): Ngày khởi hành (YYYY-MM-DD)

**Ví dụ URL:**
```
{{baseUrl}}/flights/search?noi_di=Hà Nội&noi_den=Moscow&ngay=2026-01-21
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [ ... ],
  "message": "Ket qua tim kiem"
}
```

---

### GET /flights/{id}
Lấy chi tiết một chuyến bay.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/flights/8`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "id": 8,
    "so_hieu": "VN010",
    "noi_di": "Hà Nội",
    "noi_den": "Moscow",
    "gio_khoi_hanh": "2025-12-18 02:42:00",
    "gio_ha_canh": "2025-12-25 03:43:00",
    "gia_thuong": 900000,
    "gia_thuong_gia": 90000000,
    "ghe_con": 100,
    "may_bay_id": 1,
    "may_bay": {
      "id": 1,
      "ma_may_bay": "A320",
      "ten_may_bay": "Vietjet",
      "hang_may_bay": "Vietjet Air"
    }
  },
  "message": "Chi tiet chuyen bay"
}
```

---

## Admin - Máy Bay

*Yêu cầu: Token admin (vai_tro = admin)*

### GET /admin/planes
Lấy danh sách tất cả máy bay.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/planes`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [
    {
      "id": 1,
      "ma_may_bay": "A320",
      "ten_may_bay": "Vietjet",
      "hang_may_bay": "Vietjet Air"
    }
  ],
  "message": "Danh sach may bay"
}
```

---

### POST /admin/planes
Tạo máy bay mới.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/admin/planes`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ma_may_bay": "B787",
  "ten_may_bay": "Boeing 787",
  "hang_may_bay": "Boeing Dreamliner"
}
```

**Response (201 Created):**
```json
{
  "ok": true,
  "data": { "id": 9 },
  "message": "Da tao may bay"
}
```

---

### GET /admin/planes/{id}
Lấy chi tiết một máy bay.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/planes/1`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "id": 1,
    "ma_may_bay": "A320",
    "ten_may_bay": "Vietjet",
    "hang_may_bay": "Vietjet Air"
  },
  "message": "Chi tiet may bay"
}
```

---

### PUT /admin/planes/{id}
Cập nhật máy bay.

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/admin/planes/1`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ma_may_bay": "A321",
  "ten_may_bay": "Vietjet A321",
  "hang_may_bay": "Vietjet Air"
}
```

**Response (200 OK):**
```json
{
  "ok": true,
  "data": null,
  "message": "Da cap nhat may bay"
}
```

---

### DELETE /admin/planes/{id}
Xóa máy bay.

**Thunder Client:**
- **Method**: DELETE
- **URL**: `{{baseUrl}}/admin/planes/1`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": null,
  "message": "Da xoa may bay"
}
```

---

## Admin - Chuyến Bay

*Yêu cầu: Token admin*

### GET /admin/flights
Lấy danh sách tất cả chuyến bay (admin).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/flights`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [ ... ],
  "message": "Danh sach chuyen bay"
}
```

---

### POST /admin/flights
Tạo chuyến bay mới.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/admin/flights`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "so_hieu": "VN999",
  "noi_di": "Hà Nội",
  "noi_den": "Da Nang",
  "gio_khoi_hanh": "2026-07-01 08:00:00",
  "gio_ha_canh": "2026-07-01 10:00:00",
  "gia_thuong": 1500000,
  "gia_thuong_gia": 2500000,
  "ghe_con": 150,
  "may_bay_id": 5
}
```

**Response (201 Created):**
```json
{
  "ok": true,
  "data": { "id": 16 },
  "message": "Da tao chuyen bay"
}
```

---

### GET /admin/flights/{id}
Lấy chi tiết một chuyến bay (admin).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/flights/15`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

### PUT /admin/flights/{id}
Cập nhật chuyến bay.

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/admin/flights/15`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "so_hieu": "VN999",
  "noi_di": "Hà Nội",
  "noi_den": "Da Nang",
  "gio_khoi_hanh": "2026-07-02 08:00:00",
  "gio_ha_canh": "2026-07-02 10:00:00",
  "gia_thuong": 1600000,
  "gia_thuong_gia": 2600000,
  "ghe_con": 160,
  "may_bay_id": 1
}
```

---

### DELETE /admin/flights/{id}
Xóa chuyến bay.

**Thunder Client:**
- **Method**: DELETE
- **URL**: `{{baseUrl}}/admin/flights/15`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

## Admin - Vé Máy Bay

*Yêu cầu: Token admin*

### GET /admin/flights/{id}/tickets
Lấy danh sách vé của một chuyến bay.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/flights/15/tickets`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

### POST /admin/flights/{id}/tickets
Tạo vé mới cho một chuyến bay.

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/admin/flights/15/tickets`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ma_ve": "VN999-PREMIUM",
  "hang_ve": "Thuong gia",
  "gia": 3000000,
  "so_luong_con": 20
}
```

---

### GET /admin/tickets/{id}
Lấy chi tiết một vé.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/tickets/10`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

### PUT /admin/tickets/{id}
Cập nhật vé.

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/admin/tickets/10`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ma_ve": "VN999-PREMIUM",
  "hang_ve": "Thuong gia",
  "gia": 3200000,
  "so_luong_con": 25
}
```

---

### DELETE /admin/tickets/{id}
Xóa vé.

**Thunder Client:**
- **Method**: DELETE
- **URL**: `{{baseUrl}}/admin/tickets/10`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

## Admin - Khách Hàng

*Yêu cầu: Token admin*

### GET /admin/customers
Lấy danh sách tất cả khách hàng.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/customers`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [
    {
      "id": 4,
      "ten": "hop",
      "email": "hop@gmail.com",
      "vai_tro": "customer"
    }
  ],
  "message": "Danh sach khach hang"
}
```

---

### POST /admin/customers
Tạo khách hàng mới (admin).

**Thunder Client:**
- **Method**: POST
- **URL**: `{{baseUrl}}/admin/customers`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ten": "Tran Thi C",
  "email": "tranthic@gmail.com",
  "mat_khau": "123456"
}
```

---

### GET /admin/customers/{id}
Lấy chi tiết một khách hàng.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/customers/4`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

### PUT /admin/customers/{id}
Cập nhật khách hàng.

**Thunder Client:**
- **Method**: PUT
- **URL**: `{{baseUrl}}/admin/customers/4`
- **Headers**: 
  - `Content-Type: application/json`
  - `Authorization: Bearer {{adminToken}}`
- **Body** (Raw JSON):
```json
{
  "ten": "hop updated",
  "email": "hop.updated@gmail.com",
  "mat_khau": "123456"
}
```

---

### DELETE /admin/customers/{id}
Xóa khách hàng.

**Thunder Client:**
- **Method**: DELETE
- **URL**: `{{baseUrl}}/admin/customers/4`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

## Admin - Đặt Vé

*Yêu cầu: Token admin*

### GET /admin/bookings
Lấy danh sách tất cả đặt vé.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/bookings`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": [ ... ],
  "message": "Danh sach dat ve"
}
```

---

### GET /admin/bookings/{id}
Lấy chi tiết một đặt vé (admin).

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/bookings/44`
- **Headers**: `Authorization: Bearer {{adminToken}}`

---

## Admin - Doanh Thu

*Yêu cầu: Token admin*

### GET /admin/revenue
Lấy thống kê doanh thu.

**Thunder Client:**
- **Method**: GET
- **URL**: `{{baseUrl}}/admin/revenue`
- **Headers**: `Authorization: Bearer {{adminToken}}`

**Response (200 OK):**
```json
{
  "ok": true,
  "data": {
    "total_revenue": 50000000,
    "total_bookings": 25,
    "total_paid_bookings": 20,
    "monthly_revenue": { ... },
    "flight_revenue": [ ... ]
  },
  "message": "Thong ke doanh thu"
}
```

---

## Ghi Chú Quan Trọng

1. **Authentication**: Các endpoint có yêu cầu token phải thêm header:
   ```
   Authorization: Bearer <YOUR_JWT_TOKEN>
   ```

2. **Admin only**: Các endpoint admin yêu cầu token của tài khoản có `vai_tro = "admin"`.

3. **CORS**: API hỗ trợ CORS, có thể gọi từ browser.

4. **Base URL**: Tất cả URL đều bắt đầu bằng `http://localhost/banvemaybay/public/api/v1`

5. **Error Response** (ví dụ 422):
   ```json
   {
     "ok": false,
     "message": "Du lieu khong hop le",
     "errors": {
       "email": ["Email khong hop le"]
     }
   }
   ```

6. **Test Credentials**:
   - Admin: `admin@gmail.com` / `123456`
   - Customer: `hop@gmail.com` / `123456`

---

## Quick Start Flow

1. Đăng nhập: POST `/auth/login`
2. Lấy token từ response
3. Tìm chuyến bay: GET `/flights/search`
4. Tạo đặt vé: POST `/bookings`
5. Cập nhật số lượng: PUT `/bookings/{id}`
6. Thanh toán: POST `/bookings/{id}/checkout`
7. Xem vé: GET `/my-tickets`

---

**Tài liệu được tạo lúc:** 2026-06-20
