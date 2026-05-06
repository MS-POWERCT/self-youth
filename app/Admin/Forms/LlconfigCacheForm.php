<?php

namespace App\Admin\Forms;

use App\Models\Llconfig;
use App\Services\ToolsService;
use Dcat\Admin\Traits\LazyWidget;
use Dcat\Admin\Widgets\Form;
use Dcat\Admin\Contracts\LazyRenderable;

class LlconfigCacheForm extends Form implements LazyRenderable
{
    use LazyWidget;


    public function handle(array $input)
    {

        // 获取全部llconfig模型的内容刷新到缓存
        $llconfigs = Llconfig::all();
        foreach ($llconfigs as $llconfig) {
            ToolsService::delCache($llconfig->name);
        }

        return $this->response()->success('操作成功')->refresh();
    }
    public function default()
    {
        return [];
    }

    public function form()
    {
        $this->divider('确定刷新吗');
    }
}
