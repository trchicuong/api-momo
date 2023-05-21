<?php

namespace Datlechin\Momo;

class Momo
{
    /**
     * @var string $phone
     */
    private string $phone;

    /**
     * @var string $password
     */
    private string $password;

    /**
     * @var string $imei
     */
    private string $imei;

    /**
     * @var array $data
     */
    private array $data;

    /**
     * @var string $time
     */
    private string $time;

    /**
     * @var ?string $msgType
     */
    private ?string $msgType = null;

    /**
     * @var array|string[] $msgTypes
     */
    private array $msgTypes = [
        "QUERY_TRAN_HIS_MSG" => "https://api.momo.vn/sync/transhis/browse",
        "USER_LOGIN_MSG" => "https://owa.momo.vn/public/login",
        "CHECK_USER_BE_MSG" => "https://api.momo.vn/backend/auth-app/public/CHECK_USER_BE_MSG",
        "SEND_OTP_MSG" => "https://api.momo.vn/backend/otp-app/public/SEND_OTP_MSG",
        "REG_DEVICE_MSG" => "https://api.momo.vn/backend/otp-app/public/REG_DEVICE_MSG",
        "DETAIL_TRANS" => "https://api.momo.vn/sync/transhis/details",
    ];

    /**
     * @param string $phone
     * @param string $password
     */
    public function __construct(string $phone, string $password)
    {
        $this->phone = $phone;
        $this->password = $password;
        $this->imei = $this->generateUUID();
        $this->time = $this->microtime();
        $this->data = [
            'user' => $this->phone,
            'cmdId' => $this->time . '000000',
            'lang' => 'vi',
            'channel' => 'APP',
            'time' => $this->time,
            'appVer' => 40020,
            'appCode' => '4.0.2',
            'deviceOS' => 'IOS',
        ];
    }

    /**
     * Tạo request gửi OTP đến số điện thoại
     *
     * @return bool|string
     */
    public function sendOTP(): bool|string
    {
        $this->msgType = 'SEND_OTP_MSG';
        $this->setData([
            'msgType' => $this->msgType,
            'extra' => [
                'action' => 'SEND',
                'rkey' => '12345678901234567890',
                'isVoice' => false,
            ],
            'momoMsg' => [
                '_class' => 'mservice.backend.entity.msg.RegDeviceMsg',
            ]
        ]);

        return $this->makeRequest();
    }

    /**
     * Xác thực OTP trên thiết bị mới
     *
     * @param string $otp
     * @return bool|string
     */
    public function regDevice(string $otp): bool|string
    {
        $this->msgType = 'REG_DEVICE_MSG';
        $oHash = hash('sha256', $this->phone . '12345678901234567890' . $otp);
        $this->setData([
            'msgType' => $this->msgType,
            'extra' => [
                'ohash' => $oHash
            ],
            'momoMsg' => [
                '_class' => 'mservice.backend.entity.msg.RegDeviceMsg',
                'number' => $this->phone,
                'imei' => $this->imei,
                'cname' => 'Vietnam',
                'ccode' => '084',
                'device' => 'chimchichchoe',
                'firmware' => '19',
                'hardware' => 'vbox86',
                'manufacture' => 'samsung',
                'device_os' => 'Ios',
                'secure_id' => $this->secureId(),
            ]
        ]);

        return $this->makeRequest();
    }

    /**
     * Đăng nhập vào MoMo
     *
     * @param string $pHash
     * @return bool|string
     */
    public function userLogin(string $pHash): bool|string
    {
        $this->msgType = 'USER_LOGIN_MSG';
        $checksum = $this->generateChecksum();

        $this->setData([
            'msgType' => $this->msgType,
            'extra' => [
                'checkSum' => $checksum,
                'pHash' => $pHash,
            ],
            'pass' => $this->password,
            'momoMsg' => [
                '_class' => 'mservice.backend.entity.msg.LoginMsg',
                'isSetup' => true,
            ]
        ]);

        return $this->makeRequest();
    }

    /**
     * Tạo microtime
     *
     * @return string
     */
    private function microtime(): string
    {
        $arr = explode(' ', microtime());

        return bcadd(($arr[0] * 1000), bcmul($arr[1], 1000));
    }

    /**
     * Tạo mã secure id
     *
     * @return string
     */
    private function secureId(): string
    {
        return $this->randomString(17);
    }

    /**
     * Tạo chuỗi ngẫu nhiên
     *
     * @param int $length
     * @return string
     */
    private function randomString(int $length = 10): string
    {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    /**
     * Tạo UUID cho imei
     *
     * @return string
     */
    private function generateUUID(): string
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Tạo request
     *
     * @return bool|string
     */
    private function makeRequest(): bool|string
    {
        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $this->getURLMsgType(),
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_POSTFIELDS => json_encode($this->data),
            CURLOPT_HTTPHEADER => array(
                "Content-Type: application/json"
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);

        return $response;
    }

    /**
     * Lấy URL theo msgType
     * @return string
     */
    private function getURLMsgType(): string
    {
        return $this->msgTypes[$this->msgType];
    }

    /**
     * Set dữ liệu cho request
     *
     * @param array $data
     * @return void
     */
    private function setData(array $data): void
    {
        $this->data = array_merge($this->data, $data);
    }

    /**
     * Tạo mã checksum
     *
     * @return false|string
     */
    private function generateChecksum(): string|false
    {
        $l = $this->time . '000000';
        $data = $this->phone . $l . $this->msgType . ($this->time / 1e12) . 'E12';

        return openssl_encrypt($data, 'AES-256-CBC', substr('bef490fc-885a-44bd-89b9-66dd79bc', 0, 32), 0, '');
    }
}