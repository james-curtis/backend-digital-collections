define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'goods_config/index' + location.search+'&goods_id='+Config.goods_id,
                    add_url: 'goods_config/add'+ location.search+'&goods_id='+Config.goods_id,
                    edit_url: 'goods_config/edit',
                    del_url: 'goods_config/del',
                    multi_url: 'goods_config/multi',
                    import_url: 'goods_config/import',
                    table: 'goods_config',
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
                        {field: 'goods_name', title: __('Goods.name'), operate: 'LIKE'},
                        // {field: 'stock', title: __('Stock')},
                        {field: 'sales', title: __('Sales')},
                        {field: 'surplus', title: __('Surplus')},
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