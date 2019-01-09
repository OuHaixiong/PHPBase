<?php
// http://www.cnblogs.com/JeffreySun/archive/2010/06/24/1627247.html 这文章讲的比较详细，有些原理和方法可以直接在代码中应用
// 下面演示字符串的加密和解密（对称加密）。 对称加密的常用算法有: DES算法(貌似有安全问题)，3DES算法，TDEA算法，Blowfish算法，RC5算法，IDEA算法，AES。
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
$data = '我是欧阳海雄'; // 需加密的数据
$password = 'abc123'; // 加密密钥（key、salt），最长为16位 （AES-128-CBC为16位，AES-256-CBC为32位）
$iv = '3B65571F4EB0F92a'; // 必须为16位的字符串  【初始化向量（IV）】
$options = OPENSSL_RAW_DATA;
$method = 'AES-128-CBC'; // AES-256-ECB：这个模式不推荐，容易遭受字典攻击 。 AES : 高级加密标准Advanced Encryption Standard
// 推荐使用AES-256-CBC
$encryptString = openssl_encrypt($data, $method, $password, $options, $iv); // php7中建议用openssl_*系列的函数代替mcrypt_*
var_dump($encryptString); // string(32) "��Y�ϭ�N�n��;�b�`N��������
$decryptString = openssl_decrypt($encryptString, $method, $password, $options, $iv); // string(18) "我是欧阳海雄"
var_dump($decryptString);
print_r('-----------------' . "\n");


// 下面是discuz的加密和解密函数
/**
 * discuz的加密和解密函数
 * @param string $string 需要加解密的字符串
 * @param string $operation 操作； DECODE：解密， ENCODE：加密
 * @param string $key 密钥
 * @param number $expiry 过期时间，单位为秒。如果是解密可以不传。0为永不过期
 * @return string 同样的字符串，每次加密出来都不一样
 */
function authcode($string, $operation = 'DECODE', $key = '', $expiry = 0) {
    // 动态密匙长度，相同的明文会生成不同密文就是依靠动态密匙
    $ckey_length = 4;
    $key = md5($key); // 密匙
    // 密匙a会参与加解密
    $keya = md5(substr($key, 0, 16));
    // 密匙b会用来做数据完整性验证
    $keyb = md5(substr($key, 16, 16));
    // 密匙c用于变化生成的密文
    $keyc = $ckey_length ? ($operation == 'DECODE' ? substr($string, 0, $ckey_length):
        substr(md5(microtime()), -$ckey_length)) : '';
    // 参与运算的密匙
    $cryptkey = $keya.md5($keya.$keyc);
    $key_length = strlen($cryptkey);
    // 明文，前10位用来保存时间戳，解密时验证数据有效性，10到26位用来保存$keyb(密匙b)，
    //解密时会通过这个密匙验证数据完整性
    // 如果是解码的话，会从第$ckey_length位开始，因为密文前$ckey_length位保存 动态密匙，以保证解密正确
    $string = $operation == 'DECODE' ? base64_decode(substr($string, $ckey_length)) :
    sprintf('%010d', $expiry ? $expiry + time() : 0).substr(md5($string.$keyb), 0, 16).$string;
    $string_length = strlen($string);
    $result = '';
    $box = range(0, 255);
    $rndkey = array();
    // 产生密匙簿
    for($i = 0; $i <= 255; $i++) {
        $rndkey[$i] = ord($cryptkey[$i % $key_length]);
    }
    // 用固定的算法，打乱密匙簿，增加随机性，好像很复杂，实际上对并不会增加密文的强度
    for($j = $i = 0; $i < 256; $i++) {
        $j = ($j + $box[$i] + $rndkey[$i]) % 256;
        $tmp = $box[$i];
        $box[$i] = $box[$j];
        $box[$j] = $tmp;
    }
    // 核心加解密部分
    for($a = $j = $i = 0; $i < $string_length; $i++) {
        $a = ($a + 1) % 256;
        $j = ($j + $box[$a]) % 256;
        $tmp = $box[$a];
        $box[$a] = $box[$j];
        $box[$j] = $tmp;
        // 从密匙簿得出密匙进行异或，再转成字符
        $result .= chr(ord($string[$i]) ^ ($box[($box[$a] + $box[$j]) % 256]));
    }
    if($operation == 'DECODE') {
        // 验证数据有效性，请看未加密明文的格式
        if((substr($result, 0, 10) == 0 || substr($result, 0, 10) - time() > 0) &&
            substr($result, 10, 16) == substr(md5(substr($result, 26).$keyb), 0, 16)) {
                return substr($result, 26);
            } else {
                return '';
            }
    } else {
        // 把动态密匙保存在密文里，这也是为什么同样的明文，生产不同密文后能解密的原因
        // 因为加密后的密文可能是一些特殊字符，复制过程可能会丢失，所以用base64编码
        return $keyc.str_replace('=', '', base64_encode($result));
    }
}
echo('<br />++++++++<br />');
$string = '我是欧阳海雄OK!';
$operation = 'ENCODE';
$key = 'abc123';
// $expiry = 30;
// $encodeString = authcode($string, $operation, $key, $expiry);
// echo 'discuz加密，“' . $string . '”， 加密后的字符串为： ' . $encodeString;
echo '<br />';
$operation = 'DECODE';
// $string = $encodeString;
// $string = '9d25mq6V88/vPHERg0tADmWYTRLl4c4EMnIeJ/jnXPEiakwJrgv81pj+4+YrtmxAFWY';
// $string = '5a82NZBTZZR+seIIPBRXJlqiFi1xweBgDAswBi9CcpDD1RQHEX4gIfKNlYee/umm4pI';
// $string = '0e32YPGHDyumNKi8Vok9uplxVtV0r2I8VyuSFlevtV0I5Bec91QE2ewUDLu5ZRTB7/M';
$string = 'f7d60p+gL6XswirXp2YBZo6p8MSoZmKSh7fDm3apje1ABG4koc6iRv0IJw19ME8undU';
$decodeString = authcode($string, $operation, $key);
echo 'discuz解密后的字符串为： ' . $decodeString;
echo '<br />++++++++<br />';


// 下面演示不可逆的加密算法(摘要算法、散列函数或哈希函数)
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
