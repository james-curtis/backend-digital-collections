define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'config_transfer/index' + location.search,
                    add_url: 'config_transfer/add',
                    edit_url: 'config_transfer/edit',
                    del_url: 'config_transfer/del',
                    multi_url: 'config_transfer/multi',
                    import_url: 'config_transfer/import',
                    table: 'config_transfer',
                }
            });

            var table = $("#table");

            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,
                pk: 'id',
                sortName: 'id',
                columns: [
                    [
                        {field: 'id', title: __('Id')},
                        {field: 'currency.name', title: __('Currency.name'), operate: 'LIKE'},
                        {field: 'min', title: __('Min'), operate:'BETWEEN'},
                        {field: 'max', title: __('Max'), operate:'BETWEEN'},
                        {field: 'cardinal_number', title: __('Cardinal_number')},
                        {field: 'rate', title: __('Rate'), operate:'BETWEEN'},
                        {field: 'is_show', title: __('Is_show'), searchList: {"0":__('Is_show 0'),"1":__('Is_show 1')}, formatter: Table.api.formatter.normal},

                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
        },
        add: function () {
            Controller.api.bindevent();
        },
        edit: function () {
            Controller.api.bindevent();
        },
        api: {
            bindevent: function () {
                Form.api.bindevent($("form[role=form]"));
            }
        }
    };
    return Controller;
});