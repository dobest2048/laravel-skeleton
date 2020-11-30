<?php
/**
 * This is NOT a freeware, use is subject to license terms
 * @copyright Copyright (c) 2010-2099 Jinan Larva Information Technology Co., Ltd.
 * @link http://www.larva.com.cn/
 * @license http://www.larva.com.cn/license/
 */

namespace App\Admin\Controllers\Dictionary;

use App\Models\MobileCode;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;

/**
 * 短信验证码
 * @author Tongle Xu <xutongle@gmail.com>
 */
class MobileCodeController extends AdminController
{
    /**
     * Get content title.
     *
     * @return string
     */
    protected function title()
    {
        return '短信验证码';
    }

    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new MobileCode(), function (Grid $grid) {
            $grid->quickSearch(['id', 'mobile']);
            $grid->model()->orderBy('id', 'desc');
            $grid->column('id', 'ID')->sortable();
            $grid->column('mobile', '手机号');
            $grid->column('code', '验证码');
            $grid->column('scenario', '验证场景')->using(MobileCode::getScenarios());
            $grid->column('state', '验证状态')->bool();
            $grid->column('ip', 'IP地址');
            $grid->column('expired_at', '过期时间');
            $grid->column('created_at', '创建时间')->sortable();
            $grid->column('updated_at', '更新时间')->sortable();

            $grid->disableRowSelector();
            $grid->disableViewButton();
            $grid->disableCreateButton();
            $grid->paginate(10);
        });
    }
}
