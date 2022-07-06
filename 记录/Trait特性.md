## Trait特性

[TOC]

#### 优先级

> 当前类的方法覆盖 trait 的方法，trait的方法覆盖基类。

#### 多个 trait一起使用

> 在 use 声明中可以列出多个 trait，通过逗号分隔。

```php
use Hello, World;
```

#### 冲突的解决

>为了解决多个 trait 在同一个类中的命名冲突，需要使用 `insteadof` 操作符来明确指定使用冲突方法中的哪一个。

```php
/*其定义了使用 B 中的 smallTalk 以及 trait A 中的 bigTalk。*/
use A, B {
    B::smallTalk insteadof A;
    A::bigTalk insteadof B;
    /*使用了 as 操作符来定义了 talk 来作为 B 的 bigTalk 的别名。*/
    B::bigTalk as talk;
}
```

#### 修改方法的访问控制

```php
/*修改 sayHello 的访问控制*/
class MyClass1 {
    use HelloWorld { sayHello as protected; }
}

/*给方法一个改变了访问控制的别名*/
/*原版 sayHello 的访问控制则没有发生变化*/
class MyClass2 {
    use HelloWorld { sayHello as private myPrivateHello; }
}
```

#### trait组合

```php
trait Hello {
    public function sayHello() {
        echo 'Hello ';
    }
}

trait World {
    public function sayWorld() {
        echo 'World!';
    }
}

trait HelloWorld {
    use Hello, World;
}
```

#### Trait 的抽象成员

> 一个可继承实体类，可以通过定义同名非抽象方法来满足要求。

```php
trait Hello {
    public function sayHelloWorld() {
        echo 'Hello'.$this->getWorld();
    }
    abstract public function getWorld();
}

class MyHelloWorld {
    private $world;
    use Hello;
    
    /*必须拥有getWorld方法*/
    public function getWorld() {
        return $this->world;
    }
    
    public function setWorld($val) {
        $this->world = $val;
    }
}
```

#### Trait 的静态成员

```php
trait Counter {
    public function inc() {
        static $c = 0;
        $c = $c + 1;
        echo "$c\n";
    }
}

class C1 {
    use Counter;
}

class C2 {
    use Counter;
}

$o = new C1(); $o->inc(); // echo 1
$p = new C2(); $p->inc(); // echo 1
```

####  静态方法

```php
trait StaticExample {
    public static function doSomething() {
        return 'Doing something';
    }
}

class Example {
    use StaticExample;
}

echo Example::doSomething();
```

#### 属性

> Trait 定义了一个属性后，当前类就不能定义同样名称的属性，否则会产生 fatal error。（PHP 7.0 之前会警告但可以兼容）

```php
trait PropertiesTrait {
    public $x = 1;
}

class PropertiesExample {
    use PropertiesTrait;
}

$example = new PropertiesExample;
$example->x;
```

