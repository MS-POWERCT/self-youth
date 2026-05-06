<?php

namespace App\Admin\Controllers;

use App\Admin\Metrics\Tools\GlobalTool;
use App\Admin\Repositories\Announce;
use App\Models\Announce as ModelsAnnounce;
use App\Services\I18nService;
use Dcat\Admin\Form;
use Dcat\Admin\Grid;
use Dcat\Admin\Http\Controllers\AdminController;
use Illuminate\Support\Facades\Redis;
use Illuminate\Support\Facades\Log;

class AnnounceController extends AdminController
{
    /**
     * Make a grid builder.
     *
     * @return Grid
     */
    protected function grid()
    {
        return Grid::make(new Announce(), function (Grid $grid) {
            $grid->column('id')->sortable()->editable();
            $grid->column('sort')->editable();
            $grid->column('title');
            // $grid->column('content');
            $grid->column('postion')->using(ModelsAnnounce::$postion);

            $grid->column('is_popup')->switch();
            $grid->column('status')->switch()->sortable();
            $grid->column('created_at');

            $grid->filter(function (Grid\Filter $filter) {
                $filter->equal('id')->width('20%');
                $filter->equal('postion')->select(ModelsAnnounce::$postion)->width('20%');
            });
        });
    }

    /**
     * Make a form builder.
     *
     * @return Form
     */
    protected function form()
    {
        return Form::make(new Announce(), function (Form $form) {
            $form->divider(GlobalTool::setColor('本部分中文内容国际化全部由后台自动处理，如需要修改请联系管理员', true, 'red', '18px'));
            $form->text('id');
            $form->number('sort');
            $form->text('title');
            $form->editor('content');
            $form->select('postion')->options(ModelsAnnounce::$postion)->required()->default('HOME');
            $form->switch('is_popup')->default(0);
            $form->switch('status')
                ->customFormat(function ($v) {
                    return $v == 1 ? 1 : 0;
                })
                ->saving(function ($v) {
                    return $v ? 1 : 0;
                })->help('如是新增公告,默认不启动 !!!');

            $form->radio('jumpType')->options([0 => '本地', 1 => '外链'])->default(0);
            $form->text('link');
            $form->image('img_url')->move(GlobalTool::getImageMove())
                ->maxSize(GlobalTool::getImageMaxsize())
                ->accept(GlobalTool::getImageAccept())
                ->autoUpload();

            $form->display('created_at');
            $form->display('updated_at');

            $form->saved(function (Form $form) {

                // 清理缓存
                ModelsAnnounce::clearCache($form->postion);

                if ($form->title || $form->content) {

                    // 国际化变更
                    I18nService::setI18n($form->getKey(), ModelsAnnounce::class, [
                        'title' => [
                            'value' => $form->title
                        ],
                        'content' => [
                            'value' => $form->content,
                            'format_type' => 'html'
                        ]
                    ]);
                }


                // 缓存清理现在由模型事件自动处理，这里可以移除手动清理逻辑
                // 或者保留作为额外的保险措施，但使用更精确的缓存清理
                try {
                    $announce = $form->model();
                    if ($announce) {
                        // 清理该公告相关的所有缓存
                        // 下面两个缓存key需要修改
                        // 1. announce_list:*
                        // 2. announce_popup:*
                        $listKeys = Redis::keys('announce_list:' . $announce->postion . '*');
                        $popupKeys = Redis::keys('announce_popup:' . $announce->postion . '*');
                        $allKeys = array_merge($listKeys, $popupKeys);
                        if (!empty($allKeys)) {
                            Redis::pipeline(function ($pipe) use ($allKeys) {
                                foreach ($allKeys as $key) {
                                    $pipe->del(substr($key, getRedisPrefixLen()));
                                }
                            });
                        }
                    }
                } catch (\Exception $e) {
                    Log::error('Failed to clear announce cache in admin: ' . $e->getMessage());
                }
            });
        });
    }
}
