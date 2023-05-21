<?php

require_once 'vendor/autoload.php';

/**
 * Bước 1: Khởi tạo Momo
 */

$momo = new Datlechin\Momo\Momo('phone', 'password');

/**
 * Bước 2: Lấy mã OTP được gửi đến số điện thoại
 */
$momo->sendOTP();

/**
 * Bước 3: Xác thực mã OTP trên thiết bị mới
 */
$momo->regDevice('0000');

/**
 * Bước 4: Đăng nhập
 *
 * Chức năng đang thực hiện
 */