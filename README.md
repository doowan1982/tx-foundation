# tx-foundation
### 项目描述：
    简化当前中台与各系统对接的重复代码开发，以包的形式发布代码，封装所接受的数据逻辑为模型以提高功能的内聚性和便捷性。
    出于安全考虑，Signature、Application两个类（实现）是在具体使用的代码逻辑中创建的。
### 目录描述：
    src/:
        Request为对中台的数据请求，如对中台发送请求（签名验证转发以及其他的数据处理逻辑）
        Response为接收中台的数据请求（由中台主动推送的数据）
            Models是对于中台接收各项数据的封装以提供给各系统使用

    tests/:
        .\vendor\bin\phpunit --testdox-html ./tests/index.html --log-junit ./tests/test.xml .\tests\Response\ReachTest