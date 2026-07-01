## Plan: Chuyển MVC sang RESTful API

Mục tiêu ngắn gọn: Chuyển project PHP (MVC thuần, XAMPP, MySQL) sang **RESTful API 100%** có versioning (/api/v1), có auth + CORS cơ bản, và để client (web/SPA) gọi API (không phụ thuộc server-side views).

**Nguyên tắc code (cho bài tập / vấn đáp)**
- Ưu tiên dễ hiểu: ít lớp, ít magic, không cần DI/Service phức tạp.
- Controller “mỏng”: nhận input → gọi model → trả JSON.
- Chuẩn RESTful: dùng đúng HTTP method, status code, và format lỗi thống nhất.
- Tên hàm/biến: ưu tiên tiếng Việt *không dấu* (ASCII) cho an toàn; comment/response message có thể viết tiếng Việt *có dấu*.
- Bảo mật: trong phạm vi bài tập **không bắt buộc** hash mật khẩu; nếu nâng cấp sau thì thêm bcrypt.

**Steps**
1. Discovery & Repo Review (Đã hoàn tất) — kiểm tra routing, models, auth, DB config để xác định mức độ refactor.
2. Design API contract (*depends on 1*) — xác định resource, endpoint, payload request/response (DTO), error format, status code, và versioning (/api/v1).
3. Bootstrap routing & middleware (*depends on 2*) — tạo file routes API, refactor entrypoint để forward /api/* vào router, thêm CorsMiddleware, ErrorMiddleware.
4. Authentication (đơn giản, phục vụ học) (parallel with 6) — dùng JWT access token *hoặc* session-based; giữ logic so khớp mật khẩu như hiện tại (không hash) để tập trung vào REST.
5. Tách controller theo resource (*depends on 2, 3*) — tạo cấu trúc API controllers theo Flights, Bookings, Tickets, Planes, Users, Auth; giữ Admin/Customer/Auth hiện tại cho views trong giai đoạn chuyển đổi.
6. Create API controllers & DTOs (*depends on 2, 3, 5*) — tạo controllers API cho Auth, Flight, Booking, Cart, Ticket, Admin; trả JSON theo DTOs.
7. Refactor models / data layer (*parallel with 6*) — đóng gói DB access vào wrapper (singleton hoặc simple DI), refactor models để trả DTOs, tách SQL queries ra method rõ ràng.
8. Validation, Error Handling & Security (*depends on 3, 6, 7*) — input validation, sanitize, central error responses, rate-limiting considerations, CSRF (cho non-API forms), XSS mitigation.
9. Front-end migration & compatibility (*depends on 2, 6*) — sửa front-end (AJAX/fetch) để gọi `/api/v1/*`, hoặc phát triển SPA; giữ compatibility endpoints nếu cần.
10. Docs + Testing (*parallel with 8,9*) — tạo OpenAPI/Swagger spec, viết test cURL/Postman collections, unit tests cho models và integration tests cho endpoints.
11. Rollout & Deploy — chuyển môi trường sang HTTPS (nếu có), giám sát logs, plan rollback.

**Controller structure (gợi ý tách theo 2 tầng)**
- Web (giữ UI cũ):
	- app/Controllers/Web/AuthController.php
	- app/Controllers/Web/CustomerController.php
	- app/Controllers/Web/AdminController.php
- API (tách theo resource):
	- app/Controllers/Api/V1/AuthController.php
	- app/Controllers/Api/V1/FlightController.php
	- app/Controllers/Api/V1/BookingController.php
	- app/Controllers/Api/V1/TicketController.php
	- app/Controllers/Api/V1/PlaneController.php
	- app/Controllers/Api/V1/UserController.php
	- app/Controllers/Api/V1/Admin/FlightController.php
	- app/Controllers/Api/V1/Admin/TicketController.php
	- app/Controllers/Api/V1/Admin/PlaneController.php
	- app/Controllers/Api/V1/Admin/CustomerController.php
	- app/Controllers/Api/V1/Admin/BookingController.php
	- app/Controllers/Api/V1/Admin/RevenueController.php

**Resource -> controller mapping (nhanh)**
- Flights: FlightController (public), Admin/FlightController (admin CRUD)
- Bookings: BookingController (create/list/checkout), Admin/BookingController (list/detail)
- Tickets: TicketController (my tickets), Admin/TicketController (CRUD)
- Planes: PlaneController (admin CRUD)
- Users/Customers: UserController (profile), Admin/CustomerController (CRUD)
- Auth: AuthController (login/register/logout/profile)

**API contract (tối thiểu để làm bài tập)**

**Quy ước chung**
- Base URL: `/api/v1`
- Header:
	- `Content-Type: application/json`
	- Auth (nếu dùng JWT): `Authorization: Bearer <token>`
- Format response (đề xuất, đơn giản):
	- Thành công: `{ "ok": true, "data": ..., "message": "..." }`
	- Thất bại: `{ "ok": false, "message": "...", "errors": { "field": ["..." ] } }`
- Status code dùng trong bài:
	- `200` OK, `201` Created, `204` No Content
	- `400` Bad Request, `401` Unauthorized, `403` Forbidden, `404` Not Found, `422` Validation Error, `500` Server Error

**AUTH**
- `POST /auth/login`
	- Body: `{ "email": "...", "password": "..." }`
	- 200: `{ ok:true, data:{ token:"...", user:{ id, ten, email, vai_tro } } }`
- `POST /auth/register`
	- Body: `{ "ten": "...", "email": "...", "password": "..." }`
	- 201: `{ ok:true, data:{ id, ten, email } }`
- `GET /auth/me` (auth)
	- 200: `{ ok:true, data:{ id, ten, email, vai_tro } }`
- `POST /auth/logout` (auth)
	- 200: `{ ok:true, message:"Đã đăng xuất" }` (JWT có thể chỉ logout phía client)

**FLIGHTS (public)**
- `GET /flights?limit=&offset=`
- `GET /flights/search?noi_di=&noi_den=&ngay=`
- `GET /flights/{id}`
	- 200: `{ ok:true, data:{ id, so_hieu, noi_di, noi_den, gio_khoi_hanh, gio_ha_canh, ghe_con, may_bay:{...}, gia_thuong, gia_thuong_gia } }`

**BOOKINGS / CART (customer, auth)**
- `POST /bookings`
	- Body (gợi ý): `{ "chuyen_bay_id": 1, "ve_id": 2, "so_luong": 3, "hanh_khach": [ {"ten":"A","cccd":"..."} ] }`
	- 201: `{ ok:true, data:{ booking_id, trang_thai:"cart" } }`
- `GET /bookings?status=cart|paid` (auth)
- `GET /bookings/{id}` (auth)
- `PUT /bookings/{id}` (auth) — cập nhật số lượng / hành khách
- `DELETE /bookings/{id}` (auth)
- `POST /bookings/{id}/checkout` (auth)
	- 200: `{ ok:true, data:{ booking_id, trang_thai:"paid", thanh_toan_luc:"..." } }`

**TICKETS (customer, auth)**
- `GET /my-tickets` (auth)
- `GET /my-tickets/{id}` (auth)
- `PUT /my-tickets/{id}` (auth) — sửa thông tin hành khách (nếu chưa thanh toán)

**ADMIN (auth + role=admin)**
- Flights:
	- `GET /admin/flights`
	- `POST /admin/flights`
	- `PUT /admin/flights/{id}`
	- `DELETE /admin/flights/{id}`
- Tickets of a flight:
	- `GET /admin/flights/{id}/tickets`
	- `POST /admin/flights/{id}/tickets`
	- `PUT /admin/tickets/{id}`
	- `DELETE /admin/tickets/{id}`
- Planes:
	- `GET /admin/planes`
	- `POST /admin/planes`
	- `PUT /admin/planes/{id}`
	- `DELETE /admin/planes/{id}`
- Customers:
	- `GET /admin/customers`
	- `POST /admin/customers`
	- `PUT /admin/customers/{id}`
	- `DELETE /admin/customers/{id}`
- Bookings:
	- `GET /admin/bookings`
	- `GET /admin/bookings/{id}`
- Revenue:
	- `GET /admin/revenue?mode=day|month|year|all&date=YYYY-MM-DD`

**Ghi chú đơn giản hóa (phù hợp bài tập)**
- Không bắt buộc refresh token; dùng 1 access token đủ demo.
- Không bắt buộc hash mật khẩu trong phạm vi môn học.
- Ưu tiên response key ASCII (không dấu) để tránh lỗi encoding.

**Relevant files**
- public/index.php — refactor router để forward `/api/*`, add middleware stack
- config/app.php — thêm env support (DB, JWT secret)
- app/Helpers/helpers.php — tạo DB wrapper, password helper, session helpers (tạm giữ)
- app/Controllers/AuthController.php — tích hợp JWT/validate
- app/Controllers/CustomerController.php — map hành vi -> endpoints
- app/Controllers/AdminController.php — admin API endpoints
- app/Models/*.php — chuẩn hóa return DTOs, thống nhất kiểu dữ liệu trả về
- sql/banvemaybay.sql — kiểm tra schema, tạo migration script nếu cần
- Nên thêm: `app/Http/Controllers/Api/V1/` và `app/Http/Responses/` cho DTOs

**Verification**
1. Design review: export OpenAPI spec, review endpoints và sample payloads (agree on shape before coding).
2. Unit tests: model methods (create/find/update/delete) đều có test tăng/giảm seat counts, reservation logic.
3. Integration tests: cURL/Postman collection để test flow login -> get flights -> create booking -> checkout.
4. Security checks: JWT/session hoạt động đúng, CORS headers đúng, CSRF protections cho non-API forms (nếu còn giữ form POST).
5. Manual checks: existing admin UI endpoints preserved hoặc có redirect nếu tạm thời giữ compatibility.

**Decisions (gợi ý, cần bạn xác nhận)**
- Authentication: dùng JWT access token (đủ cho bài tập) hoặc session-based; **không bắt buộc** refresh token và **không bắt buộc** hash mật khẩu trong phạm vi môn học.
- Versioning: bắt đầu với `/api/v1/`.
- Rollout strategy: triển khai theo strangler pattern (deploy API song song với web UI) để giảm rủi ro.

**Further Considerations**
1. Nếu muốn giữ rollback dễ dàng: triển khai API song song với web UI, chuyển từng feature (strangler pattern).
2. Nếu có traffic nhiều: cân nhắc dùng Redis cho session blacklisting / refresh token revocation.
3. Tài nguyên quan trọng: backup DB trước khi chỉnh schema/logic quan trọng; nếu nâng cấp bảo mật sau này thì bổ sung bcrypt + migration.

Khi bạn đồng ý, bước tiếp theo tôi sẽ: hoàn thiện "Design API contract" (bản chi tiết endpoints + request/response schemas). Bạn muốn tôi tiếp tục soạn API contract chi tiết cho resources chính (Flights, Bookings, Tickets, Auth) hay muốn chọn phương án auth trước (JWT vs giữ session)?
