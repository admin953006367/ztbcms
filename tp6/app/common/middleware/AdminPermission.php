<?php
declare (strict_types=1);

namespace app\common\middleware;

use app\admin\model\AdminUserModel;
use app\admin\service\AdminUserService;
use app\admin\service\RbacService;
use app\common\model\UserModel;
use think\facade\Config;
use think\facade\View;
use think\Request;
use think\Response;

/**
 * 管理后台用户权限检测
 *
 * 注意：使用该中间件前，请确保session中间件 app\common\middleware\AdminAuth::class 已启用且先于此中间件启用
 *
 * @package app\common\middleware
 */
class AdminPermission
{
    /**
     * 处理请求
     *
     * @param  \think\Request  $request
     * @param  \Closure  $next
     *
     * @return Response
     */
    public function handle($request, \Closure $next)
    {
        var_dump('AdminPermission..');
        // 权限检测
        $noNeedPermission = $request->noNeedPermission ?? [];
        // 该方法是否需要权限检测
        if ($this->_checkActionMatch($request->action(), $noNeedPermission)) {
            // 不需要检测
            return $next($request);
        } else {
            $info = AdminUserService::getInstance()->getInfo();
            $hasPremission = $this->hasAccessPermission($info['id'], $request->baseUrl());
            if (!$hasPremission) {
                return $this->_handleNoPermiassion($request);
            }
        }

        return $next($request);
    }

    /**
     * 检测控制器的方法是否匹配
     *
     * @param $action
     * @param $arr
     *
     * @return bool
     */
    function _checkActionMatch($action,array $arr)
    {
        if (empty($arr)) {
            return false;
        }

        $arr = array_map('strtolower', $arr);
        // 是否存在
        if (in_array(strtolower($action), $arr) || in_array('*', $arr)) {
            return true;
        }

        // 没找到匹配
        return false;
    }

    /**
     * 权限检测
     *
     * @param  int|string  $user_id  用户ID
     * @param  string  $base_url  路由
     *
     * @return bool
     */
    private function hasAccessPermission($user_id, string $base_url = '')
    {
        if (!empty($base_url)) {
            // 格式：/app/controller/action
            //去除参数的校验
            $base_url = explode('&', $base_url)[0];

            $items = explode('/', $base_url);
            $app = $items[1];
            $controller = $items[2];
            $action = $items[3];
        } else {
            $app = strtoupper(app('http')->getName());
            $controller = strtoupper(request()->controller());
            $action = strtoupper(request()->action());
        }
        $rbacService = new RbacService();
        $res = $rbacService->enableUserAccess($user_id, $app, $controller, $action);
        return $res['status'];
    }

    // 无权限情况
    private function _handleNoPermiassion(Request $request)
    {
        if (request()->isAjax()) {
            return json(createReturn(false, null, '无权限', 403));
        } else {
            $file = 'common/403';
            $template_file = app_path(Config::get('view.view_dir_name')).$file.'.'.Config::get('view.view_suffix');
            if (!file_exists($template_file)) {
                // 默认使用admin模块样式
                $template_file = base_path().'admin'.DIRECTORY_SEPARATOR.Config::get('view.view_dir_name').DIRECTORY_SEPARATOR.$file.'.'.Config::get('view.view_suffix');
            }
            return response(View::fetch($template_file));
        }
    }
}
