<?php
// 下面演示字符串的加密和解密
$aString = '我是欧海雄';
$aEncodeString = base64_encode($aString); // 对字符串进行base64编码
var_dump($aEncodeString); // 5oiR5piv5qyn5rW36ZuE
$aDecodeString = base64_decode($aEncodeString); // base64加密过的字符串是可以解密的。相当于序列化，可方便用于传输和跨平台使用
var_dump($aDecodeString); // 我是欧海雄

$bString = '想想都好可怕！我是欧阳海雄，车位加加打算发的撒范德萨大是打发发生大范德萨地方撒发送范德萨测试加密后的字符串长度测试加密后的字符串长度';
$bEncodeString = base64_encode($bString); // base64随着字符串的长度加长的话，它会很长，数据要比原始数据多占用 33% 左右的空间
var_dump($bEncodeString); // 5oOz5oOz6YO95aW95Y+v5oCV77yB5oiR5piv5qyn6Ziz5rW36ZuE77yM6L2m5L2N5Yqg5Yqg5omT566X5Y+R55qE5pKS6IyD5b636JCo5aSn5piv5omT5Y+R5Y+R55Sf5aSn6IyD5b636JCo5Zyw5pa55pKS5Y+R6YCB6IyD5b636JCo5rWL6K+V5Yqg5a+G5ZCO55qE5a2X56ym5Liy6ZW/5bqm5rWL6K+V5Yqg5a+G5ZCO55qE5a2X56ym5Liy6ZW/5bqm
$bDecodeString = base64_decode($bEncodeString);
var_dump($bDecodeString);

// $str = '我是欧阳海雄';
// $key = '123';
// $string = mcrypt_encrypt(MCRYPT_SAFER64, $key, $str, MCRYPT_MODE_ECB); // 特别注意了：mcrypt_encrypt 已经被废弃了，建议不使用
// print_r('++++++++++++++');
// var_dump($string);
// $string = mcrypt_decrypt(MCRYPT_SAFER64, $key, $string, MCRYPT_MODE_ECB); // mcrypt_decrypt 已经被废弃了， 建议不使用
// var_dump($string);
// print_r('++++++++++++++');

print_r('-----------------' . "\n");
$data = '我是欧阳海雄';
$password = 'abc123';
$iv = '3B65571F4EB0F92a'; // 必须为16位的字符串
$options = OPENSSL_RAW_DATA;
$method = 'AES-128-CBC';
$encryptString = openssl_encrypt($data, $method, $password, $options, $iv); // php7中建议用openssl_*系列的函数代替mcrypt_*
var_dump($encryptString); // string(32) "��Y�ϭ�N�n��;�b�`N��������
$decryptString = openssl_decrypt($encryptString, $method, $password, $options, $iv); // string(18) "我是欧阳海雄"
var_dump($decryptString);
print_r('-----------------' . "\n");




// 下面演示不可逆的加密算法
$cString = 'zhong文，sdf123';
$encodeString = sha1($cString);
var_dump($encodeString); // string(40) "ec08ace04cef7b56d33bbb0c2dc50b7584a11bb8"
$encodeString = sha1($cString, true); // 后面的参数如果为true：加密后密文为 20 位字符长度
var_dump($encodeString); // string(20) "���L�{V�;�-�u���" 
$encodeString = sha1($cString, false); // 后面的参数默认为false：加密后密文是 40 位字符长度的十六进制字符串
var_dump($encodeString);
$salt = 'haihaihai';
$str = '123456';
var_dump(md5($str . $salt)); // string(32) "c63b43f5f74dc8df16edd76b48d84491"    后面的参数默认false：返回32位的16进制字符串
var_dump(md5($str . $salt, true)); // string(16) "�;C��M����kH�D�" 返回16为的字符串，有乱码
var_dump(hash('sha256', $str)); // string(64) "8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92" ；   还有sha512返回128位密文

var_dump(crypt($str, $salt)); // string(13) "hazsR9UJJSGrk" 
// var_dump(crypt($str)); // string(34) "$1$UEHp0n1D$CBzNvtobw9CWJxlxzKfH80"  .  这里会报notice，建议使用上面那种
var_dump(crypt($str,'ha123')); // string(13) "hazsR9UJJSGrk"  这个结果和上面的一样，是因为salt值只取前两位

print_r('++++++++++++' . "\n");
$options = array(
    'cost' => 12, // 算法递归的层数. 省略时，默认值是 10。 这个 cost 是个不错的底线，但也许可以根据自己硬件的情况，加大这个值。
    //     'salt' => 'dididi' // 在散列密码时加的盐（干扰字符串）。 在最新版中此参数已废弃，因为系统自动产生随机盐值
);
$password = 'ABC_ouhaixiong'; // 需要加密的字符串; 最长为72个字符，超过会被截断
$hash = password_hash($password, PASSWORD_DEFAULT, $options); // 返回散列后的密码， 或者在失败时返回 FALSE。 此函数会有一定的性能消耗，运行会比较慢
// 第二个参数还可以取值：PASSWORD_BCRYPT等. 通过password_hash加密后的字符串，salt也会带在加密串中(使用的算法、cost 和盐值作为散列的一部分返回)
var_dump($hash); // string(60) "$2y$12$9.XRAqqcSXDJ2yLkGArEH.TzSGC1TwR1QbAobydtUmKdW6ebFD2jW"
$newPassword = '123456';
$boolean = password_verify($newPassword, $hash); // bool(false)
// 使用的算法、cost 和盐值作为散列的一部分返回,所以验证散列值的所有信息都已经包含在内。 这使 password_verify() 函数验证的时候，不需要额外储存盐值或者算法的信息。
var_dump($boolean);
$boolean = password_verify($password, $hash); // bool(true)
var_dump($boolean);
print_r('++++++++++++' . "\n");



// 下面演示非对称加密
// 在非对称加密中使用的主要算法有：RSA、Elgamal、背包算法、Rabin、D-H、ECC（椭圆曲线加密算法）等。 其中我们最见的算法是RSA算法
/**
 * 使用openssl实现非对称加密
 * @author Bear[258333309@163.com]
 * @version 1.0
 * @created 2019年1月2日 下午3:30:55
 */
class Rsa
{
    const PRIVATE_KEY_FILE_NAME = 'private.key';
    const PUBLIC_KEY_FILE_NAME = 'public.key';
    
    /**
     * 私钥（private key）
     * @var string
     */
    private $_privateKey;
    
    /**
     * 公钥（public key）
     * @var string
     */
    private $_publicKey;
    
    /**
     * 密钥保存路径（the keys saving path）
     * @var string
     */
    private $_keyPath;
    
    /**
     * the constructor
     * @param string $path is the keys saving path
     * @throws Exception
     */
    public function __construct($path) {
        if (empty($path) || !is_dir($path)) {
            throw new Exception('Must set the keys save path');
        }
        $this->_keyPath = $path;
        $this->createKey();
    }
    
    /**
     * 创建一对密钥，保存在设置的路径中
     * create the key pair, save the key to $this->_keyPath
     */
    public function createKey() {
        $privateKeyPath = $this->_keyPath . DIRECTORY_SEPARATOR . self::PRIVATE_KEY_FILE_NAME;
        $publicKeyPath = $this->_keyPath . DIRECTORY_SEPARATOR . self::PUBLIC_KEY_FILE_NAME;
        if (is_file($privateKeyPath) && is_file($publicKeyPath)) {
            return;
        }
        $r = openssl_pkey_new();
        openssl_pkey_export($r, $privateKey);
        file_put_contents($privateKeyPath, $privateKey);
        $this->_privateKey = openssl_pkey_get_public($privateKey);
        
        $rp = openssl_pkey_get_details($r);
        $publicKey = $rp['key'];
        file_put_contents($publicKeyPath, $publicKey);
        $this->_publicKey = openssl_pkey_get_public($publicKey);
    }
    
    /**
     * 安装（初始化）私钥
     * setup the private key
     * @return boolean
     */
    public function setupPrivateKey() {
        if (is_resource($this->_privateKey)) {
            return true;
        }
        $filePath = $this->_keyPath . DIRECTORY_SEPARATOR . self::PRIVATE_KEY_FILE_NAME;
        $privateKey = file_get_contents($filePath);
        $this->_privateKey = openssl_pkey_get_private($privateKey);
        return true;
    }
    
    /**
     * 安装（初始化）公钥
     * setup the public key
     * @return boolean
     */
    public function setupPublicKey() {
        if (is_resource($this->_publicKey)) {
            return true;
        }
        $filePath = $this->_keyPath . DIRECTORY_SEPARATOR . self::PUBLIC_KEY_FILE_NAME;
        $publicKey = file_get_contents($filePath);
        $this->_publicKey = openssl_pkey_get_public($publicKey);
        return true;
    }
    
    /**
     * 使用私钥进行加密
     * encrypt with the private key
     * @param string $data
     * @return NULL|string
     */
    public function privateEncrypt($data) {
        if (!is_string($data)) {
            return null;
        }
        $this->setupPrivateKey();
        $r = openssl_private_encrypt($data, $encrypted, $this->_privateKey);
        if ($r) {
            return base64_encode($encrypted);
        } else {
            return null;
        }  
    }
    
    /**
     * 使用公钥解密
     * decrypt with the public key
     * @param string $crypted 加密过的字符串
     * @return NULL | string 解密后的字符串
     */
    public function publicDecrypt($crypted) {
        if (!is_string($crypted)) {
            return null;
        }
        $this->setupPublicKey();
        $crypted = base64_decode($crypted);
        $r = openssl_public_decrypt($crypted, $decrypted, $this->_publicKey);
        if ($r) {
            return $decrypted;
        } else {
            return null;
        }
    }
    
    /**
     * 使用公钥加密
     * encrypt with public key
     * @param string $data 需加密的字符串
     * @return null | string 返回加密后的字符串
     */
    public function publicEncrypt($data) {
        if (!is_string($data)) {
            return null;
        }
        $this->setupPublicKey();
        $r = openssl_public_encrypt($data, $encrypted, $this->_publicKey);
        if ($r) {
            return base64_encode($encrypted);
        } else {
            return null;
        }
    }

    /**
     * 使用私钥解密
     * decrypt with the private key
     * @param string $crypted 加密后的字符串
     * @return NULL | string
     */
    public function privateDecrypt($crypted) {
        if (!is_string($crypted)) {
            return null;
        }
        $this->setupPrivateKey();
        $crypted = base64_decode($crypted);
        $r = openssl_private_decrypt($crypted, $decrypted, $this->_privateKey);
        if ($r) {
            return $decrypted;
        } else {
            return null;
        }
    }

    
    public function __destruct() {
        @fclose($this->_privateKey);
        @fclose($this->_publicKey);
    }

}

$path = realpath(dirname(__FILE__) . '/data/ssl_key'); // 如果文件夹不存在会返回false，存在会返回完整路径，如：/data/www/PHPBase/data/ssl_key
$rsa = new Rsa($path);
// 私钥加密，公钥解密
$data = '我是欧阳海雄';
var_dump($data);
$encryptString = $rsa->privateEncrypt($data);
var_dump('加密后的字符串：', $encryptString);
$decryptString = $rsa->publicDecrypt($encryptString);
var_dump('解密后的字符串：', $decryptString);
// 公钥加密， 私钥解密
$data = 'I am OuHaixiong!';
var_dump($data);
$encryptString = $rsa->publicEncrypt($data);
var_dump('公钥加密后的字符串：', $encryptString);
$decryptString = $rsa->privateDecrypt($encryptString);
var_dump('私钥解密后的字符串：', $decryptString);
$decryptString = $rsa->publicDecrypt($encryptString);
var_dump('公钥解密后的字符串：', $decryptString);
