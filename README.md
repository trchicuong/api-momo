# MoMo API

## Cài đặt

Sử dụng Composer để cài đặt:

```bash
composer require datlechin/momo
```

Hoặc bạn cũng có thể tải/copy mã code trong tệp `src/Momo.php` để sử dụng lại code.

## Sử dụng

Nếu bạn cài đặt bằng Composer, bạn cần phải `require` file `vendor/autoload.php` để sử dụng thư viện.

```php
require 'vendor/autoload.php';
```

Sau đó, bạn có thể sử dụng thư viện như sau:

```php
use DatLeChin\Momo\Momo;

// Khởi tạo đối tượng
$momo = new Momo('phone', 'password');
```

### Gửi mã OTP về điện thoại

Để lấy mã OTP, bạn cần gọi phương thức `sendOTP()`, sau đó Momo sẽ gọi tới số điện thoại của bạn và gửi mã OTP về.

```php
$momo->sendOTP();
```

### Xác nhận mã OTP trên thiết bị mới

Sau khi có mã OTP, bạn cần gọi phương thức `regDevice()` để xác nhận mã OTP trên thiết bị mới.

```php
$momo->regDevice('mã otp');
```

> **Note**
> Bạn có thể mở file `index.php` để xem code mẫu bên trong.

## Đóng góp

Rất vui nếu bạn có thể đóng góp cho dự án này. Bạn có thể tạo một Pull Request hoặc tạo một Issue để báo lỗi.

### Donate

Nếu bạn thấy dự án này hữu ích, bạn có thể donate cho tôi qua các ví hoặc ngân hàng sau:

- Momo: 0372124043 (Ngô Quốc Đạt)
- Vietcombank: 1017595600 (NGO QUOC DAT)

