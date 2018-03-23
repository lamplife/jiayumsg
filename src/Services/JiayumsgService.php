<?php
/**
 * Author: 狂奔的螞蟻 <www.firstphp.com>
 * Date: 2018/3/21
 * Time: 下午4:38
 */
namespace Firstphp\Jiayumsg\Services;

use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\DB;

class JiayumsgService {



    /**
     * 添加系统公告
     *
     * @params $mainId      通知类型 0:全体 1:主体ID
     * @params $title       标题
     * @params $content     内容
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function addNotice($params = '') {
        $mainId = isset($params['main_id']) && $params['main_id'] ? intval($params['main_id']) : 0;
        $title = isset($params['title']) && $params['title'] ? htmlspecialchars($params['title']) : '';
        $content = isset($params['content']) && $params['content'] ? htmlspecialchars($params['content']) : '';
        if (empty($title) || empty($content)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }

        DB::beginTransaction();
        try {
            $data = [
                'main_id' => $mainId,
                'title' => $title,
                'content' => $content,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ];
            DB::table('notification_system')->insert($data);
            DB::table("notification_system_mark")->increment('unread');
            DB::commit();
        } catch (\Exception $e){
            DB::rollback();
            return ['code' => 400, 'message' => "SQL错误", 'data' => []];
        }

        return ['code' => 200, 'message'=> '操作成功', 'data' => []];

    }



    /**
     * 编辑系统公告
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function putNotice() {
        $noticeId = isset($params['id']) && $params['id'] ? intval($params['id']) : 0;
        $mainId = isset($params['main_id']) && $params['main_id'] ? intval($params['main_id']) : 0;
        $title = isset($params['title']) && $params['title'] ? htmlspecialchars($params['title']) : '';
        $content = isset($params['content']) && $params['content'] ? htmlspecialchars($params['content']) : '';
        if (empty($noticeId) || empty($title) || empty($content)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }

        DB::beginTransaction();
        try {
            $data = [
                'main_id' => $mainId,
                'title' => $title,
                'content' => $content,
                'updated_at' => Carbon::now()
            ];
            DB::table('notification_system')->where('id', $noticeId)->update($data);
            DB::commit();
        } catch (\Exception $e){
            DB::rollback();
            return ['code' => 400, 'message' => "SQL错误", 'data' => []];
        }

        return ['code' => 200, 'message'=> '操作成功', 'data' => []];

    }



    /**
     * 删除系统公告
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function delNotice($params = []) {
        $noticeId = isset($params['id']) && $params['id'] ? intval($params['id']) : 0;
        if (empty($noticeId)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }

        $checkNotice = DB::table("notification_system")->where(['id' => $noticeId, 'is_delete' => 0])->first();
        if ($checkNotice) {
            $readInfo = DB::table("notification_system_read")->where(['notice_id' => $noticeId])->pluck('user_id');
            $readInfo = $readInfo ? $readInfo->toArray() : '';
            if ($readInfo) {
                $userIds = implode(',', $readInfo);
                DB::table("notification_system_mark")->where('unread', '>', 0)->whereIn('user_id', [$userIds])->decrement('read');
                DB::table("notification_system_mark")->where('unread', '>', 0)->whereNotIn('user_id', [$userIds])->decrement('unread');
            } else {
                DB::table("notification_system_mark")->where('unread', '>', 0)->decrement('unread');
            }

            DB::table("notification_system")->where(['id' => $noticeId, 'is_delete' => 0])->update(['is_delete' => 1]);
        } else {
            return ['code' => 400, 'message' => 'Hacking Attempt', 'data' => []];
        }

        return ['code' => 200, 'message' => 'success', 'data' => []];

    }



    /**
     * 未读消息(用户读取)
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNotices($params = []) {
        $userId = isset($params['user_id']) && $params['user_id'] ? intval($params['user_id']) : 0;
        if (empty($userId)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }

        $unread = 0;
        if ($userId) {
            $markInfo = DB::table("notification_system_mark")->where('user_id', $userId)->first();
            if (empty($markInfo)) {
                $total = DB::table("notification_system")->count();
                $markData = [
                    'user_id' => $userId,
                    'unread' => $total,
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now()
                ];
                DB::table("notification_system_mark")->insert($markData);
                $unread = $total;
            } else {
                $unread = $markInfo->unread;
            }
        }

        return ['code' => 200, 'message'=> '操作成功', 'data' => ['unread' => $unread]];

    }



    /**
     * 系统公告列表
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNoticeList($params = []) {
        $offset = isset($params['offset']) && $params['offset'] ? intval($params['offset']) : 0;
        $limit = isset($params['limit']) && $params['limit'] ? intval($params['limit']) : 10;
        $userId = isset($params['user_id']) && $params['user_id'] ? intval($params['user_id']) : 0;

        if (empty($userId)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }
        $total = DB::table("notification_system")->count();
        $res = DB::table("notification_system")
            ->take($limit)
            ->skip($offset)
            ->orderBy('weight', 'DESC')
            ->orderBy('id', 'DESC')
            ->get();
        $res = $res ? $res->toArray() : '';

        // 是否存在mark记录
        $this->checkMarkRecord($userId, $total);

        $result = [];
        if ($res) {
            foreach ($res as $key => $val) {
                $hasRead = DB::table("notification_system_read")->where(['notice_id' => $val->id, 'user_id' => $userId])->first();
                if ($hasRead) {
                    $result[$key]['is_read'] = 1;
                } else {
                    $result[$key]['is_read'] = 0;
                }

                $result[$key]['id'] = $val->id;
                $result[$key]['title'] = $val->title;
//                $result[$key]['content'] = $val->content;
                $result[$key]['created_at'] = $val->created_at;
            }
        }

        $data = [
            'code' => 200,
            'message' => '操作成功',
            'data' => [
                'total' => $total,
                'page' => $offset,
                'pageSize' => $limit,
                'list' => $result
            ]
        ];

        return $data;

    }



    /**
     * 读取消息 - 用户读取
     *
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    public function getNoticeDetail($params = []) {
        $noticeId = isset($params['id']) && $params['id'] ? intval($params['id']) : 0;
        $userId = isset($params['user_id']) && $params['user_id'] ? intval($params['user_id']) : 0;
        if (empty($noticeId) || empty($userId)) {
            return ['code' => 400, 'message' => '参数错误', 'data' => []];
        }

        $info = DB::table("notification_system")->select('id', 'title', 'content', 'created_at')->where('id', $noticeId)->first();
        if ($info) {
            $checkRead = DB::table("notification_system_read")->where(['notice_id' => $noticeId, 'user_id' => $userId])->first();
            if(empty($checkRead)) {
                DB::table("notification_system_read")->insert(['notice_id' => $noticeId, 'user_id' => $userId, 'created_at' => Carbon::now(), 'updated_at' => Carbon::now()]);
                DB::table("notification_system_mark")->where('user_id', $userId)->increment('read');
                DB::table("notification_system_mark")->where('user_id', $userId)->decrement('unread');
                DB::table("notification_system")->where('id', $noticeId)->increment('views');
            }
        }

        return ['code' => 200, 'message'=> '操作成功', 'data' => $info];

    }



    /**
     * 校验Mark记录
     *
     * @params $userId       用户ID
     * @author 狂奔的螞蟻 <www.firstphp.com>
     */
    protected function checkMarkRecord($userId = 0, $total = 0) {
        if ($userId) {
            if ($total == 0) {
                $total = DB::table("notification_system")->count();
            }
            $redisKey = 'SYSTEM_MARK:'.$userId;
            if (!Redis::get($redisKey)) {
                $markInfo = DB::table("notification_system_mark")->where('user_id', $userId)->first();
                if (empty($markInfo)) {
                    $markData = [
                        'user_id' => $userId,
                        'unread' => $total,
                        'created_at' => Carbon::now(),
                        'updated_at' => Carbon::now()
                    ];
                    DB::table("notification_system_mark")->insert($markData);
                    Redis::setex($redisKey, 86400, 1);
                }
            }
        }
    }







}