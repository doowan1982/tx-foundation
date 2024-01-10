# tx-foundation
### 项目描述：
    简化当前中台与各系统对接的重复代码开发，以包的形式发布代码，封装所接受的数据逻辑为模型以提高功能的内聚性和便捷性。
    注意，该项目使用的JWT库的版本与已有的通行证JWT的包不匹配，如果需继续使用原有的通行证库，则需升级相关的包来保证composer时不存在兼容问题。
### 目录描述：
    src/:
        Request为对中台的数据请求，如对中台发送请求（签名验证转发以及其他的数据处理逻辑）
            Transports请求处理并将返回接口进行处理
        Response为接收中台的数据请求（由中台主动推送的数据）
            Models是对于中台接收各项数据的封装以提供给各系统使用
    
    tests/:
        .\vendor\bin\phpunit --testdox-html ./tests/data/index.html --log-junit ./tests/data/test.xml --colors=always -v  .\tests\

### 运行前期设置

```php
$config = new LogConfig();
$config->handlers = [new StreamHandler('{日志路径及文件名}', Logger::DEBUG)]; //更多handler处理可参见monolog官方文档
Context::instance()->setLogConfig($config) //如果未进行该设置，将无日志输出
				->testEnv(); //将当前环境设置为单测环境，该值将影响Guzzle::client请求options中的verify参数
				->setApplication(new Application('id', 'key'))
```

### Request使用实例

```php
$data = Context::instance()->getRequest()
    			->setRequestDomain('targe_url', 'http')
    			->getAdmin(244282);
 //目前对于Request仅支持管理员数据查询，后期将按需新增其他的接口操作
```

### Response使用示例：

```php
$body = Context::instance()->getResponse('加密字符串', [
            'content' => '...',
            'type' => 'test'
        ]);
$data = $body->getData(); //将根据参数返回Data模型数据，具体看参见Tesoon\Models\DataFactory::build()
```