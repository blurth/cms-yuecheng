<?php

namespace app\api\controller\v1;

use Qiniu\Auth;
use think\facade\Cache;
use think\facade\Config;
use think\response\Json;

class Token
{
    /**
     * 获取七牛token 有效期两小时

     */
    public function getQnToken(): Json
    {

/*        $token = Cache::get('uptoken');

        if ($token){
            return writeJson(201, $token, 'success');
        }*/

        $param = Config::pull('qn');

        // 初始化签权对象
        $auth = new Auth($param['ak'], $param['sk']);

        // 生成上传Token
        $token = $auth->uploadToken($param['bucket'],null,800);

        Cache::set('uptoken',$token);

        return writeJson(201, $token, 'success');

    }
}