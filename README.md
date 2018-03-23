# jiayumsg
消息通知服务


安装扩展

	composer require firstphp/jiayumsg:"1.5"

注册服务：

    Firstphp\Jiayumsg\Providers\JiayumsgServiceProvider::class

发布配置：

    php artisan vendor:publish --provider="Firstphp\Jiayumsg\Providers\JiayumsgServiceProvider"


数据表迁移：

    php artisan migrate


示例代码

    use Firstphp\Jiayumsg\Facades\JiayumsgFactory;
    ......
