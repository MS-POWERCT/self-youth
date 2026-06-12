<?php

use Dcat\Admin\Grid;
use Dcat\Admin\Form;
use Dcat\Admin\Grid\Filter;
use Dcat\Admin\Grid\Displayers\Actions;
use Dcat\Admin\Admin;

/**
 * Dcat-admin - admin builder based on Laravel.
 * @author jqh <https://github.com/jqhph>
 *
 * Bootstraper for Admin.
 *
 * Here you can remove builtin form field:
 *
 * extend custom field:
 * Dcat\Admin\Form::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Column::extend('php', PHPEditor::class);
 * Dcat\Admin\Grid\Filter::extend('php', PHPEditor::class);
 *
 * Or require js and css assets:
 * Admin::css('/packages/prettydocs/css/styles.css');
 * Admin::js('/packages/prettydocs/js/main.js');
 *
 */


Filter::resolving(function (Filter $filter) {
    $filter->expand();
    $filter->panel();
});

// 对表格=内容进行全局定义
Grid::resolving(function (Grid $grid) {
    // $grid->setActionClass(Actions::class);
    $grid->disableViewButton(); // 隐藏查看按钮
    $grid->disableBatchDelete(); // 隐藏批量删除按钮
    $grid->disableRowSelector(); // 隐藏行选中
    // $grid->tableCollapse(false); // 表格不折叠
    // $grid->showQuickEditButton(); // 显示快捷编辑
    // $grid->enableDialogCreate(); // 启用弹窗创建
    $grid->paginate(10); // 默认分页10行
    // $grid->model()->orderBy('id', 'desc'); // id倒叙
    $grid->actions(function (Actions $actions) {
        $actions->disableDelete();
        $actions->disableView();
        // $actions->disableEdit(); // 编辑
    });
});

Form::resolving(function (Form $form) {
    $form->disableEditingCheck();
    $form->disableViewCheck();
    $form->disableCreatingCheck();

    $form->tools(function (Form\Tools $tools) {
        $tools->disableDelete();
        $tools->disableView();
        //  $tools->disableList();
    });
});
