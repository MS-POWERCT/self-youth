<?php


namespace App\Admin\Renderable;

use App\Models\AppLog;
use Dcat\Admin\Support\LazyRenderable;
use Dcat\Admin\Widgets\Table;

class LogList extends LazyRenderable
{
    public function render()
    {
        if (!empty($this->key)) {
            return 'id is no';
        };

        $data = AppLog::select('admin_user_id', 'log', 'created_at')->where('morph_model', $this->morph_model)->where('morph_id', $this->key)->get()->toArray();

        return Table::make(
            ['操作员', '操作日志', '操作时间'],
            $data
        );
    }
}
