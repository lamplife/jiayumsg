<?php
/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2018/3/21
 * Time: 下午5:00
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use Firstphp\Jiayumsg\Facades\JiayumsgFactory;



class NoticeController extends Controller
{


    protected $params;

    function __construct(Request $request)
    {
        $this->params = $request->all();
    }


    /**
     * 添加系统公告
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function addNotice() {

        return Response::json(JiayumsgFactory::addNotice($this->params));

    }


    /**
     * 编辑系统公告
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function putNotice() {

        return Response::json(JiayumsgFactory::putNotice($this->params));

    }


    /**
     * 删除系统公告
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function delNotice() {
        return Response::json(JiayumsgFactory::delNotice($this->params));
    }


    /**
     * 未读消息(用户读取)
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNotices() {

        return Response::json(JiayumsgFactory::getNotices($this->params, authInfo()['id']));

    }


    /**
     * 系统公告列表(用户读取)
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNoticeList() {

        return Response::json(JiayumsgFactory::getNoticeList($this->params, authInfo()['id']));

    }


    /**
     * 读取消息 - 用户读取
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNoticeDetail() {

        return Response::json(JiayumsgFactory::getNoticeDetail($this->params, authInfo()['id']));

    }



}