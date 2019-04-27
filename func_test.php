<?php
// 下面是对php的基本函数的使用进行测试
namespace Foobar\Aa; // 如果使用这样的写法的命名空间，只能写在文件的第一行，且命名空间的名字不能为纯数字

ini_set('display_errors', 'On');
ini_set('error_reporting', E_ALL); // 低于5.4版 error_reporting(E_ALL | E_STRICT);
error_reporting(E_ALL);

/* function plus($a, $b, &$sum) {
    $sum = $a + $b;
}
function subtraction($minuend, $subtrahend) {
    return $minuend - $subtrahend;
}
$a = 8;
$b = 9;
$c = 0;
// call_user_func('plus', $a, $b, &$c); // 特别注意了，call_user_func中调用的函数的参数是不能按引用进行传递的
plus($a, $b, $c);
$difference = call_user_func('subtraction', $c, $b);
var_dump($difference); */


// 下面演示在不同的命名空间下，调用call_user_func
// namespace Foobar\Aa; // 命名空间只能放在文件的第一行
class Foo 
{
    static public function test($name) {
        print "Hello {$name}!\n";
    }
}


namespace Qoo\Bb; // 这个是可以的，因为文件最顶部已经声明过使用命名空间，后面再申明命名空间就可以写在中间
call_user_func('Foobar\Aa\Foo::test', '欧海雄');
call_user_func(array('Foobar\Aa\Foo', 'test'), '欧阳海雄');
class Fun1
{
    public function getName() {
        return 'OUOUou';
    }
}
// var_dump(__NAMESPACE__); // string(6) "Qoo\Bb"  获取当前的命名空间的名字
$fun1Namespace = __NAMESPACE__ . '\Fun1';
$fun1 = new $fun1Namespace();
$name = call_user_func(array($fun1, 'getName'));
var_dump($name);

namespace Qoo\Cc;
use Foobar\Aa\Foo;
// call_user_func_array('Foo::test', array('欧艳艳'));  // 不能这样写，会报错：class 'Foo' not found 。
call_user_func_array('Foobar\Aa\Foo::test', array('欧艳艳'));
use Qoo\Bb\Fun1 as f1;
$fun2 = new f1();
$name2 = call_user_func_array(array($fun2, 'getName'), array());
var_dump('第二次调用：' . $name2);

// 引用
namespace Qoo\Dd;
class C3
{
    public static function joinString($a, $b, &$str) {
        $str = $a . ' 浦发的 ' . $b;
    }
}
$aString = 'Hello';
$bString = 'World';
$arr = array('str'=>'');
call_user_func_array('Qoo\Dd\C3::joinString', array($aString, $bString, &$arr['str'])); // 这样写还是不对，为什么？它这里面用的命名空间难道一定要传进去的
var_dump($arr);
// 但是 call_user_func_array(); 的参数是可以用引用的方式传递，但参数的前面需要加&符号

