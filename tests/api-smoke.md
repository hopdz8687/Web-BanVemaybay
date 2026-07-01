# API Smoke Tests (Manual)

Base URL (example):
- http://localhost/banvemaybay/api/v1

## 1) Auth
Login (admin):

CMD:
```bat
curl -X POST "http://localhost/banvemaybay/api/v1/auth/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"admin@gmail.com\",\"password\":\"123456\"}"
```

PowerShell:
```powershell
curl -X POST "http://localhost/banvemaybay/api/v1/auth/login" `
  -H "Content-Type: application/json" `
  -d "{\"email\":\"admin@gmail.com\",\"password\":\"123456\"}"
```

Copy token from response.

## 2) Flights (public)
```bat
curl "http://localhost/banvemaybay/api/v1/flights?limit=5"
```

## 3) Admin Planes
```bat
curl "http://localhost/banvemaybay/api/v1/admin/planes" ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 4) Admin Flights
```bat
curl "http://localhost/banvemaybay/api/v1/admin/flights" ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 5) Admin Tickets of a flight
```bat
curl "http://localhost/banvemaybay/api/v1/admin/flights/1/tickets" ^
  -H "Authorization: Bearer YOUR_TOKEN"
```

## 6) Customer Booking flow (JWT)
Login customer to get token:
```bat
curl -X POST "http://localhost/banvemaybay/api/v1/auth/login" ^
  -H "Content-Type: application/json" ^
  -d "{\"email\":\"hop@gmail.com\",\"password\":\"123456\"}"
```

Create booking:
```bat
curl -X POST "http://localhost/banvemaybay/api/v1/bookings" ^
  -H "Authorization: Bearer USER_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"chuyen_bay_id\":1,\"ve_id\":2,\"so_luong\":1,\"hanh_khach\":[{\"ten\":\"A\",\"dien_thoai\":\"0901\",\"email\":\"a@mail.com\"}]}"
```

Checkout:
```bat
curl -X POST "http://localhost/banvemaybay/api/v1/bookings/1/checkout" ^
  -H "Authorization: Bearer USER_TOKEN" ^
  -H "Content-Type: application/json" ^
  -d "{\"ten_thanh_toan\":\"A\",\"dien_thoai_thanh_toan\":\"0901\",\"email_thanh_toan\":\"a@mail.com\",\"phuong_thuc_thanh_toan\":\"direct\"}"
```

## 7) My tickets
```bat
curl "http://localhost/banvemaybay/api/v1/my-tickets" ^
  -H "Authorization: Bearer USER_TOKEN"
```

## 8) Revenue (admin)
```bat
curl "http://localhost/banvemaybay/api/v1/admin/revenue?mode=day&day=2026-05-26" ^
  -H "Authorization: Bearer YOUR_TOKEN"
```
