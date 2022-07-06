#### abstract与interface的区别

> abstract：抽象类 interface：接口
>
>  interface 强调特定功能的实现，而 abstractclass 强调所属关系。

##### 主要区别：

1. 抽象类需要继承，用extends，而接口需要实现，用implements。
2. 一个类可以实现多个接口，但只能继承一个抽象类。
3. 接口中每个方法都只有声明而没有实现，其中的每个方法实现类必须要实现；而抽象类中只需要实现抽象方法，即方法名带关键字"abstract"（例如：abstract function fun1();），其它方法可以选择性的实现。
4. 接口中只能声明public的方法，不能声明private和protected的方法，不能对方法进行实现，也不能声明实例变量；但是抽象类中可以。

##### 理解：

> interface接口类说白了，就是一个类的模板，一个类的规定，如果属于此类，就必须遵循此类的规定，方法一个都不能少。
>
> abstract抽象类就是把多个类相同的部分抽出来，这句看上去很搞笑，其实它说出了抽象类的真理，不过省去了实例化公共类这个步骤，让你像直接调用本类方法一样方 便，而且还可以重载这个方法（抽象类就是一个类的服务提供商，拥有众多服务，你不是必须用，但当需要的时候来用就可以了，如果你觉得提供的服务不满意，你还可以自己在提供的服务上进行定制）。
>
> 1.一个类可以同时继承一个父类和实现任意多个接口， extends 子句应该在 implements 子句之前。
>
> 2.任何实现接口的类都要实现接口中所定义的所有方法，
>
> 3.abstractclass 是 interface 与 class 的中介。 abstract class 在 interface 及 class 中起到了承上启下的作用。一方面， abstractclass 是抽象的，可以声明抽象方法，以规范子类必须实现的功能；另一方面，它又可以定义缺省的方法体，供子类直接使用或覆盖。另外，它还可以定义自己的实例变量，以供子类通过继承来使用。

```
class E implements iA  
{  
    public function iAfunc1(){echo "in iAfunc1";}  
    public function iAfunc2(){echo "in iAfunc2";}  
} 
```

> 否则该类必须声明为 abstract 。

```php
abstract class E implements iA{}  
```



#### 六大原则

> **单一职责：**一个类应该只用于处理一个职责。

```php
class product
{
    public function productInfo()
    {
        echo "查看商品详细信息".PHP_EOL;
    }
}

class seckill
{
    public function seckillInfo()
    {
        echo "查看秒杀商品详细信息".PHP_EOL;
    }
}

class client
{
    public function run()
    {
        $product = new product();
        $seckill = new seckill();
        $product->productInfo();
        $seckill->seckillInfo();
    }
}

$client = new client();
$client->run();
```

> **开闭原则：**一个类模块和函数应该对扩展开放，对修改关闭。

```php
interface SetStr
{
    public function boot();
}

class Num implements SetStr
{
    public function boot()
    {
        return rand(0,10000);
    }
}

class English implements SetStr
{
    public function boot()
    {
        $length = 4;
        $arr = ['a','b','c','d','e','f','g'];
        $indexs = array_rand($arr,$length);
        $str = '';
        foreach ($indexs as $index){
            $str .= $arr[$index];
        }
        return $str;
    }
}

class client
{
    public function run()
    {
        $num = new Num();
        return $num->boot();
    }
}

$client = new client();
echo $client->run();
```

> **里氏替换原则：**主要对于继承，子类可以扩展父类的功能，但不能改变父类原有的功能

```php
abstract class SetStr
{
    public abstract function Str();

    public function rand_str()
    {
        $arr = ['!','#','$','%','^','&','*','(',')','_','-','+','='];
        $indexs = array_rand($arr,4);
        $str = $this->Str();
        foreach ($indexs as $index){
            $str .= $arr[$index];
        }
        return $str;
    }
}

class Num extends SetStr
{
    public function Str()
    {
        return rand(0,10000);
    }
}

class English extends SetStr
{
    public function Str()
    {
        $arr = ['a','b','c','d','e','f','g'];
        $indexs = array_rand($arr,4);
        $str = '';
        foreach ($indexs as $index){
            $str .= $arr[$index];
        }
        return $str;
    }
}

class client
{
    public function run()
    {
        $SetStr = new Num();
        $rand_str = new English();
        echo "4位随机数字字符串:",$SetStr->Str().PHP_EOL;
        echo "4为随机英文字符串:",$rand_str->Str(),PHP_EOL;
        echo "随机数字与特殊字符串:".$SetStr->rand_str(),PHP_EOL;
        echo "随机英文与特殊字符串:".$rand_str->rand_str();
    }
}

$client = new client();
$client->run();
```

> **依赖替换原则：**类A与类B之间存在调用关系,如果我们需要将B进行替换为C,将要修改类A的代码才能够完成,很明显贸然的修改代码可能带来未知的风险,同时这里也不符合开闭原则,我们可以将类A修改为调用接口I类,然后B与C类各自继承并实现接口I,这样类A间接通过接口类I与类B与类C发生关系

修改前：

```php
class B
{
    public function str()
    {
        return rand(0, 10000);
    }
}

class A
{
    public function boot(B $B)
    {
        echo "开始生成字符串,预计时间3秒..." . PHP_EOL;
        for ($i = 3; $i >= 0; $i--) {
            sleep(1);
            echo $i . "秒" . PHP_EOL;
        }
        echo "生成字符串结束,你的字符串为:", $B->str();
    }
}
```

修改后：

```php
interface I
{
    public function str();
}

class B implements I
{
    public function str()
    {
        return rand(0,10000);
    }
}

class C implements I
{
    public function str()
    {
        $arr = ['a','b','c','d','e','f','g'];
        $indexs = array_rand($arr,4);
        $str = '';
        foreach ($indexs as $index){
            $str .= $arr[$index];
        }
        return $str;
    }
}

class D implements I
{
    public function str()
    {
        return "1111";
    }
}

class A
{
    public function boot(I $I)
    {
        echo "开始生成字符串,预计时间3秒...".PHP_EOL;
        for($i=3;$i>=0;$i--){
            sleep(1);
            echo $i."秒".PHP_EOL;
        }
        echo "生成字符串结束,你的字符串为:",$I->str().PHP_EOL;
    }
}

class client
{
    public function run()
    {
        $set = new A();
        $set->boot(new B());
        $set->boot(new C());
        $set->boot(new D());
    }
}

$client = new client();
$client->run();
```

> 接口隔离原则：客户端不应该依赖它不需要的接口；一个类对另一个类的依赖应该建立在最小的接口上

> 与单一职责原则的区别:
>
> 1. 单一职责原则原注重的是职责；而接口隔离原则注重对接口依赖的隔离。
>
> 2. 单一职责原则主要是约束类，其次才是接口和方法
>
> 3. 它针对的是程序中的实现和细节；而接口隔离原则主要约束接口接口，主要针对抽象，针对程序整体框架的构建。

```php
interface I
{
    public function action1();
    public function action2();
    public function action3();
    public function action4();
    public function action5();
    public function action6();
}

class C implements I
{
    public function action1()
    {
        echo "C类的action1方法".PHP_EOL;
    }

    public function action2()
    {
        echo "C类的action2方法".PHP_EOL;
    }

    public function action3()
    {
        echo "C类的action3方法".PHP_EOL;
    }

    public function action4(){}

    public function action6(){}

    public function action5(){}
}

class D implements I
{
    public function action1()
    {
        echo "B类的action1方法".PHP_EOL;
    }

    public function action2(){}
    public function action3(){}

    public function action4()
    {
        echo "B类的action4方法".PHP_EOL;
    }

    public function action5()
    {
        echo "B类的action5方法".PHP_EOL;
    }

    public function action6()
    {
        echo "B类的action6方法".PHP_EOL;
    }
}

class A
{
    public function exec1(I $i)
    {
        $i->action1();
    }

    public function exec2(I $i)
    {
        $i->action2();
    }

    public function exec3(I $i)
    {
        $i->action3();
    }
}

class B
{
    public function exec1(I $i)
    {
        $i->action1();
    }

    public function exec4(I $i)
    {
        $i->action4();
    }
    public function exec5(I $i)
    {
        $i->action5();
    }
    public function exec6(I $i)
    {
        $i->action6();
    }
}

class client
{
    public function run()
    {
        $A = new A();
        $A->exec1(new C);
        $A->exec2(new C);
        $A->exec3(new C);
        $B = new B();
        $B->exec1(new D);
        $B->exec4(new D);
        $B->exec5(new D);
        $B->exec6(new D);
    }
}

$client = new client();
$client->run();
```

> 迪米特原则：一个类对于自己依赖的类知道的越少越好,如果其中一个类需要调用另一个类的某一个方法的话,可以通过第三者转发这个调用。

```php
class close
{
    public function run()
    {
        $this->request();
        $this->start();
        $this->closeApp();
        $this->end();
    }
    private function request()
    {
        fwrite(STDOUT,'还有应用程序在运行,是否仍要关机:');
        $argv = fgets(STDIN);
        if (strncasecmp($argv,'no',2) == 0)
        {
            $this->exitClose();
            exit();
        }
    }

    private function start()
    {
        echo "开始关机",PHP_EOL;
    }

    private function closeApp()
    {
        echo "正在关闭启动的应用程序",$this->str(),PHP_EOL;
    }

    private function end()
    {
        echo "关机完成",PHP_EOL;
    }

    private function str()
    {
        for ($i=0;$i<3;$i++){
            sleep(1);
            echo '.';
        }
    }

    private function exitClose()
    {
        echo "已取消关机";
    }
}

class music
{
    private $music;

    public function run()
    {
        $this->request();
        $this->select();
        $this->start();
    }

    private function request()
    {
        fwrite(STDOUT,'请点歌:');
        $this->music = fgets(STDIN);
    }

    private function select()
    {
        echo "正在搜索歌曲:".$this->music.$this->str().PHP_EOL;
    }

    private function start()
    {
        echo "开始播放歌曲:".$this->music.PHP_EOL;
    }

    private function str()
    {
        for ($i=0;$i<3;$i++){
            sleep(1);
            echo '.';
        }
    }
}

class start
{
    private $UserName = "starsky";

    private $Password = "starsky99";

    public function run()
    {
        $this->begin();
        $this->init();
        $this->loader();
        $this->end();
    }

    private function begin()
    {
        echo "欢迎使用".PHP_EOL;
    }

    private function init()
    {
        echo "正在初始化系统,请稍后".PHP_EOL;
    }

    private function loader()
    {
        echo "正在加载系统应用".PHP_EOL;
    }

    private function end()
    {
        fwrite(STDOUT,"系统加载完毕,请输入用户名:");
        $username = fgets(STDIN);
        fwrite(STDOUT,"请输入密码:");
        $password = fgets(STDIN);
        $this->check($username,$password);
        echo "登录成功".PHP_EOL;
    }

    private function check($username,$password)
    {
        $restful = true;
        while ($restful){
            if (strncasecmp($username,$this->UserName,strlen($username)) == 0 && strncasecmp($password,$this->Password,strlen($password))==0){
                break;
            }else{
                echo "用户名或密码错误,请重试",PHP_EOL;
                fwrite(STDOUT,"请输入用户名:");
                $username = fgets(STDIN);
                fwrite(STDOUT,"请输入密码:");
                $password = fgets(STDIN);
            }
        }
        return true;
    }
}

class computer
{
    public function close()
    {
        $close = new close();
        $close->run();
    }

    public function muisc()
    {
        $music = new music();
        $music->run();
    }

    public function start()
    {
        $start = new start();
        $start->run();
    }
}

class client
{
    public function run()
    {
        $computer = new computer();
        $computer->start();
    }
}

$client = new client();
$client->run();
```

