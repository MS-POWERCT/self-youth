<?php

namespace App\Admin\Forms;

use App\Admin\Metrics\Tools\GlobalTool;
use Dcat\Admin\Widgets\Form;
use App\Services\I18nService;
use Dcat\Admin\Contracts\LazyRenderable;
use Dcat\Admin\Traits\LazyWidget;
use Illuminate\Support\Facades\Log;

class I18nForm extends Form implements LazyRenderable
{
    protected $modelClass;
    use LazyWidget; // 使用异步加载功能


    public function handle(array $input)
    {
        try {
            $model = $input['model'];
            $fields = $input['fields'];
            $fields = array_merge(['id'], $fields);
            $langs = $input['langs'];
            $langs = array_combine($langs, $langs);
            $htmls = I18nService::$htmls[$model] ?? [];

            $items = $model::select($fields)->get();
            foreach ($items as $item) {
                $preData = [];
                foreach ($fields as $field) {
                    $preData[$field] = [
                        'value' => $item->$field,
                    ];
                    if ($htmls && in_array($fields, $htmls)) {
                        $preData[$field]['format_type'] = 'html';
                    }
                }
                I18nService::setI18n($item->id, $model, $preData, $langs);
            }

            return $this->response()->success('国际化操作成功')->refresh();
        } catch (\Exception $e) {
            Log::error('error', ['s' => $e->getMessage(), 'e' => $e->getLine(), 'f' => $e->getFile()]);
            return $this->response()->error('国际化操作失败：' . $e->getMessage())->refresh();
        }
    }
    public function form()
    {
        $this->divider('建议总量控制在 100 条以内, 否则会导致部分内容丢失');
        $this->divider('如何计算总量, 如模型中对应的产品表中有10条数据');
        $this->divider('字段选择2个字段, 加上国际化: 英、日、韩, 那么总量就是 10 * 2 * 3 = 60');
        $this->divider(GlobalTool::setColor('中文不计算', true, 'red', '18px'));

        $this->radio('model', '模型')->options(I18nService::$models)->required()->load('fields', '/getI18nField');
        $this->multipleSelect('fields', '字段')->required();
        $this->checkbox('langs', '国际化')->options(GlobalTool::$LANGS)->required()->canCheckAll();
    }


    // 设置表单的处理逻辑

    public function default() {}
}
