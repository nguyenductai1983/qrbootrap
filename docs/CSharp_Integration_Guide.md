# Tài Liệu Tích Hợp C# Client (Trạm Cân & Trạm In)

Tài liệu này hướng dẫn chi tiết cách thức giao tiếp giữa các ứng dụng viết bằng C# (máy khách) và hệ thống máy chủ Laravel.

Hệ thống bao gồm hai ứng dụng C# riêng biệt:
1.  **Ứng dụng Trạm Cân (Scale App):** Lấy dữ liệu từ cân điện tử qua cổng COM và đẩy lên hệ thống theo thời gian thực.
2.  **Ứng dụng Trạm In (Print App):** Đóng vai trò print-server cục bộ, nhận lệnh in tem/nhãn từ máy chủ Web và điều khiển máy in qua LAN/USB.

---

## MỤC LỤC

1. [Cơ Chế Xác Thực Chung (Authentication)](#1-cơ-chế-xác-thực-chung-authentication)
2. [Tích Hợp Ứng Dụng Trạm Cân (Scale App)](#2-tích-hợp-ứng-dụng-trạm-cân-scale-app)
3. [Tích Hợp Ứng Dụng Trạm In (Print App)](#3-tích-hợp-ứng-dụng-trạm-in-print-app)
4. [Mã Nguồn Mẫu (Base API Client)](#4-mã-nguồn-mẫu-base-api-client)

---

## 1. Cơ Chế Xác Thực Chung (Authentication)

Tất cả các API gửi từ C# Client đến Laravel đều được bảo mật bằng **Sanctum Token**. 
Hoạt động này yêu cầu ứng dụng C# có màn hình đăng nhập, hoặc đăng nhập ngầm qua tài khoản hệ thống đã cấp.

- **Endpoint:** `POST /api/login`
- **Payload (JSON):**
  ```json
  {
      "email": "ten_dang_nhap_hoac_email",
      "password": "mat_khau"
  }
  ```
- **Response Thành Công (200 OK):**
  ```json
  {
      "success": true,
      "message": "Đăng nhập thành công!",
      "token": "1|abc123xyz...",
      "token_type": "Bearer"
  }
  ```
- **Cách Sử Dụng:** Gắn Token nhận được vào HTTP Header của tất cả các request tiếp theo:
  `Authorization: Bearer 1|abc123xyz...`

---

## 2. Tích Hợp Ứng Dụng Trạm Cân (Scale App)

### 2.1. Phát Sóng Trọng Lượng Thời Gian Thực (Broadcast Weight)
API này được gọi liên tục (khuyến nghị 0.5s - 1s/lần) khi cân đang nhảy số để Website nhận được số nhảy theo thời gian thực (thông qua WebSockets của hệ thống Laravel).

- **Endpoint:** `POST /api/scale/broadcast-weight`
- **Header Bắt Buộc:** `Authorization: Bearer <token>`
- **Payload (JSON):**
  ```json
  {
      "station_token": "TOKEN_TRAM_CAN_CUA_BAN", 
      "weight": 14.5,
      "is_stable": false 
  }
  ```
  *(Chú thích: `is_stable` = `true` nếu cân báo cờ ổn định, ngược lại gửi `false`)*

### 2.2. Ghi Nhận / Chốt Trọng Lượng (Update Weight)
API này được gọi khi nhân viên **quét mã Barcode** của một kiện hàng. C# App sẽ báo cho Server chốt số cân hiện tại gán vào mã Barcode đó.

- **Endpoint:** `POST /api/warehouse/update-weight`
- **Header Bắt Buộc:** `Authorization: Bearer <token>`
- **Payload (JSON):**
  ```json
  {
      "station_token": "TOKEN_TRAM_CAN_CUA_BAN",
      "barcode": "SP001002",
      "weight": 14.5
  }
  ```
- **Response:** Hệ thống sẽ trả về cờ `is_surplus` (có tái nhập rác dư hay không) và thông tin trọng lượng `old_weight`, `new_weight`.

---

## 3. Tích Hợp Ứng Dụng Trạm In (Print App)

Trạm In hỗ trợ 2 cơ chế (Poll API hoặc WebSockets Realtime). Khuyến cáo sử dụng **WebSockets** để ra lệnh in ngay lập tức mà không bị trễ.

### 3.1. Lấy Danh Sách Lệnh In Đang Chờ (Pending Jobs)
Gọi API này khi ứng dụng C# mới bật lên, hoặc C# gọi định kỳ để check lệnh in.

- **Endpoint:** `GET /api/print/pending-jobs/{station_token}`
- **Header Bắt Buộc:** `Authorization: Bearer <token>`
- **Response Template:**
  ```json
  {
      "success": true,
      "jobs": [
          {
              "JobId": 124,
              "Path": "D:/Templates/Zebra_Standard.prn",
              "Data": [
                  {"Name": "MaSP", "Value": "SP001002"},
                  {"Name": "TenSP", "Value": "Thép cuộn A"}
              ]
          }
      ]
  }
  ```

### 3.2. Cập Nhật Trạng Thái Lệnh In (Update Status)
Sau khi đẩy lệnh vào phần cứng máy in hoàn tất, báo lại cho Server để đóng Job.

- **Endpoint:** `POST /api/print/statusupdate`
- **Header Bắt Buộc:** `Authorization: Bearer <token>`
- **Payload (JSON):**
  ```json
  {
      "job_id": 124,
      "status": 2
  }
  ```
  *(Lưu ý: Status = 2 thường là in Thành công, Status = 3 là Lỗi).*

### 3.3. Kết Nối WebSockets (Reverb) Lắng Nghe Lệnh In Tức Thời
Để không phải Poll liên tục, C# ứng dụng nên thiết lập kết nối WebSocket tới máy chủ.
 
1. **Lấy Cấu Hình Socket:** Gọi `GET /api/print/config` *(Cần Bearer Token)*. API trả về các cấu hình bao gồm `app_key`, `ws_host`, `ws_port` để dùng cho PusherClient C#.
2. **Kênh Lắng Nghe (Channel):** Kết nối vào kênh riêng tư (Private Channel):
   `private-printstationapp.{stationKey}`
3. **Sự Kiện Lắng Nghe (Event):** Lắng nghe gói tin mang tên:
   `App\Events\PrintLabelAppEvent`
4. **Hành động phản hồi:** Khi bắt được Data Event, hãy gọi API `Pending Jobs (3.1)` phía trên để kéo Job về và in ấn.

---

## 4. Mã Nguồn Mẫu (Base API Client)

Bạn cần khai báo `HttpClient` để thực thi các Request. Dưới đây là Base class mẫu trên khung .NET.

```csharp
using System;
using System.Net.Http;
using System.Net.Http.Headers;
using System.Text;
using System.Text.Json;
using System.Threading.Tasks;

public class AppApiClient
{
    private readonly HttpClient _client;
    public string Token { get; private set; }

    public AppApiClient(string baseAddress)
    {
        _client = new HttpClient { BaseAddress = new Uri(baseAddress) };
        _client.DefaultRequestHeaders.Accept.Add(new MediaTypeWithQualityHeaderValue("application/json"));
    }

    // 1. Hàm Đăng Nhập
    public async Task<bool> LoginAsync(string email, string password)
    {
        var payload = new { email, password };
        var content = new StringContent(JsonSerializer.Serialize(payload), Encoding.UTF8, "application/json");

        var response = await _client.PostAsync("/api/login", content);
        if (response.IsSuccessStatusCode)
        {
            var jsonRes = await response.Content.ReadAsStringAsync();
            using var doc = JsonDocument.Parse(jsonRes);
            Token = doc.RootElement.GetProperty("token").GetString();
            
            // Tự động gán Token vào tất cả Request tiếp theo
            _client.DefaultRequestHeaders.Authorization = new AuthenticationHeaderValue("Bearer", Token);
            return true;
        }
        return false;
    }

    // 2. Hàm Phát sòng trọng lượng (App Cân)
    public async Task BroadcastWeightAsync(string stationToken, double weight, bool isStable)
    {
        var payload = new { station_token = stationToken, weight, is_stable = isStable };
        var content = new StringContent(JsonSerializer.Serialize(payload), Encoding.UTF8, "application/json");
        await _client.PostAsync("/api/scale/broadcast-weight", content);
    }

    // 3. Hàm Lấy dữ liệu in (App In)
    public async Task<string> FetchPendingJobsAsync(string stationToken)
    {
        var response = await _client.GetAsync($"/api/print/pending-jobs/{stationToken}");
        return response.IsSuccessStatusCode ? await response.Content.ReadAsStringAsync() : null;
    }
}
```
