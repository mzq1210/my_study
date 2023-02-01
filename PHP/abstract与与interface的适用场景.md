## 不同点

1. **语法上的不同**，对接口的使用是通过关键字**implements**，定义是使用关键字**interface**；对抽象类的使用是通过关键字**extends**（当然接口也可以通过关键字extends继承），定义是使用关键字**abstract class**。
2. 接口只有常量和方法，抽象类则包含普通类中的一切机构。
3. 接口中的方法都必须是public类型的，而抽象类则不受限制。
4. 一个类可以同时**实现多个**接口，但一个类只能**继承一个**抽象类。
5. 抽象类中可以定义普通的带有方法体的方法，而接口不行

## 相同点

1. 接口中的方法和抽象类中的抽象方法都**不能有方法体**，并且在其子类中都**必须被实现**
2. 都可以被继承但不能被实例化

## 适用场景

如果要创建一个模型，这个模型将由一些紧密相关的对象采用，就可以使用抽象类。如果要创建将由一些不相关对象采用的功能，就使用接口。
如果必须从多个来源继承行为，就使用接口。
如果知道所有类都会共享一个公共的行为实现，就使用抽象类，并在其中实现该行为。

## 代码示例

**以下代码摘抄自燕十八公益课堂**

```php
/*
春秋战国时期,燕零七 飞行器专家,能工巧匠. 
他写了一份图纸---飞行器制造术 
飞行器秘制图谱 
1: 要有一个有力的发动机,喷气式. 
2: 要有一个平衡舵,掌握平衡 
他的孙子问: 发动机怎么造呢? 
燕零七眼望夕阳: 我是造不出来,但我相信后代有人造出来 
燕零七的构想在当时的科技造不出来,即这个类只能在图纸化,无法实例化.
***/ 

// 此时这个类没有具体的方法去实现,还太抽象. 
// 因此我们把他做成一个抽象类 
abstract class FlyIdea { 
    // 大力引擎,当时也没法做,这个方法也实现不了,因此方法也是抽象的 
    public abstract function engine();
   
    // 平衡舵 
    public abstract function blance(); 
    /* 
        注意:抽象方法 不能有方法体 
        下面这样写是错误的 
        public abstract function blance() { 
        } 
        Fatal error: Abstract function FlyIdea::engine() cannot contain body 
    */ 
} 

/* 
抽象类不能 new 来实例化 
下面这行是错误的 
$kongke = new FlyIdea(); 
Cannot instantiate abstract class FlyIdea 
*/ 

// 到了明朝,万户用火箭解决了发动机的问题 
abstract class Rocket extends FlyIdea { 
    // 万户把engine方法,给实现了,不再抽象了 
    public function engine() { 
        echo '点燃火药,失去平衡,嘭!<br />'; 
    } 
    // 但是万户实现不了平衡舵,因此平衡舵对于Rocket类来说,还是抽象的,类也是抽象的 
    // 此处由于继承父类的也是抽象类,所以可以不必完成抽象类中的所有抽象方法;
} 

/* 
到了现代,燕十八亲自制作飞行器 
这个Fly类中,所以抽象方法,都已经实现了,不再是梦想. 
*/  

//到了这个类就必须要完成所有的抽象方法;
class Fly extends Rocket{ 
    public function engine() { 
        echo '有力一扔<br />'; 
    } 
    public function blance() { 
        echo '两个纸翼保持平衡~~~'; 
    } 
    public function start() { 
        $this->engine(); 
        for($i=0;$i<10;$i++) { 
            $this->blance(); 
            echo '平稳飞行<br />'; 
        } 
    } 
} 
$apache = new Fly(); 
$apache->start();
```



```php
/*
类: 是某一类事物的抽象,是某类对象的蓝图. 
比如: 女娲造人时,脑子中关于人的形象  就是人类 class Human 
如果,女娲决定造人时, 同时,形象又没最终定稿时, 
她脑子有哪些支离破碎的形象呢? 
她可能会这么思考: 
动物: 吃饭 
猴子: 奔跑 
猴子: 哭 
自己: 思考 
小鸟: 飞 
我造一种生物,命名为人,应该有如下功能 
eat() 
run(); 
cry(); 
think(); 
类如果是一种事物/动物的抽象 
那么 接口,则是事物/动物的功能的抽象, 
即,再把他们的功能各拆成小块 
自由组合成新的特种 
*/ ;   
interface animal { 
  const NAME = 'zxg'; //不能定义属性,但可以定义常量;
  public function eat(); 
} 
interface monkey { 
  public function run(); 
  public function cry(); 
} 
interface wisdom { 
  public function think(); 
} 
interface bird { 
  public function fly();
} 

  /*
  如上,我们把每个类中的这种实现的功能拆出来 
  分析: 如果有一种新生物,实现了eat() + run() +cry() + think() ,这种智慧生物,可以叫做人. 
  class Human implements animal,monkey,wisdom { 

  } 

  Human类必须要包含animal,monkey,wisdom接口里面的方法,缺一不可,否则就会报错
  Class Human contains 4 abstract methods 
  */ 

  class Human implements animal, monkey, wisdom, bird { //这里的接口数量可以随意增加;增                        加了以后本类里面的方法必须要有新增加的接口里面的方法
  public function eat() {
  echo "吃东西方法";
  }
  public function run() {
  echo self::NAME; //可以通过self来访问任意一个接口所定义的常量;
  echo '行走的方法';
  }
  public function cry() {
  echo '哭的方法';
  }
  public function think() {
  echo animal::NAME; //也可以通过 接口名
  echo '思考的方法';
  }
  public function smile() {
  echo "这是新增加的微笑方法";
  }
  public function fly() {
  echo "这是新增加的接口bird里面的fly方法";
  }
  }

  $obj = new Human();
  $obj -> think();
/***  
====笔记部分====  
面向对象的一个观点:  
做的越多,越容易犯错  
抽象类{就定义类模板}--具体子类实现{china,japan,english}  
接口:  
***/   
// 抽象的数据库类   
/*  
创业做网站  
到底用什么数据库?  mysql, oracle,sqlserver,postgresql?  
这样:先开发网站,运行再说.  
先弄个mysql开发着,正式上线了再换数据库也不迟  
引来问题:  
换数据库,会不会以前的代码又得重写?  
答:不必,用抽象类  
开发者,开发时,就以db抽象类来开发.  
*/   
abstract class db {   
    public abstract function connect($h,$u,$p);   
    public abstract function query($sql);   
    public abstract function close();   
}   
/*   
// 下面这个代码有误  
// 因为子类实现时, connect和抽象类的connect参数不一致  
class mysql extends db {  
    public function connect($h,$h) {  
        return true;  
    }  
    public function query($sql,$conn) {  
    }  
    public function close() {  
    }  
}  
*/   
/*  
下面这个mysql类,严格实现了db抽象类  
试想: 不管上线时,真正用什么数据库  
我只需要再写一份如下类  
class oracle extends db {  
}  
class mssql extends db {  
}  
class postsql extends db {  
}  
业务逻辑层不用改?  
为什么不用改?  
因为都实现的db抽象类.  
我开发时,调用方法不清楚的地方,我就可以参考db抽象类.  
反正子类都是严格实现的抽象类.  
*/   
class mysql extends db {   
    public function connect($h,$h,$u) {   
        return true;   
    }   
    public function query($sql) {   
    }   
    public function close() {   
    }   
}   


/*  
接口 就更加抽象了  
比如一个社交网站,  
关于用户的处理是核心应用.  
登陆  
退出  
写信  
看信  
招呼  
更换心情  
吃饭  
骂人  
捣乱  
示爱  
撩骚  
这么多的方法,都是用户的方法,  
自然可以写一个user类,全包装起来  
但是,分析用户一次性使不了这么方法  
用户信息类:{登陆,写信,看信,招呼,更换心情,退出}  
用户娱乐类:{登陆,骂人,捣乱,示爱,撩骚,退出}  
开发网站前,分析出来这么多方法,  
但是,不能都装在一个类里,  
分成了2个类,甚至更多.  
作用应用逻辑的开发,这么多的类,这么多的方法,都晕了.  
*/   
interface UserBase {   
    public function login($u,$p);   
    public function logout();   
}   
interface UserMsg {   
    public function wirteMsg($to,$title,$content);   
    public function readMsg($from,$title);   
}   
interface UserFun {   
    public function spit($to);   
    public function showLove($to);   
}   
/*  
作为调用者, 我不需要了解你的用户信息类,用户娱乐类,  
我就可以知道如何调用这两个类  
因为: 这两个类 都要实现 上述接口.  
通过这个接口,就可以规范开发.  
*/   
/*  
下面这个类,和接口声明的参数不一样,就报错,  
这样,接口强制统一了类的功能  
不管你有几个类,一个类中有几个方法  
我只知道,方法都是实现的接口的方法.  
*/   
class User implements UserBase {   
    public function login($u) {   
    }   
}   

```