define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'goods_transfer/index' + location.search,
                    add_url: 'goods_transfer/add',
                    edit_url: 'goods_transfer/edit',
                    del_url: 'goods_transfer/del',
                    multi_url: 'goods_transfer/multi',
                    import_url: 'goods_transfer/import',
                    table: 'goods_transfer',
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
                        {field: 'id', title: __('Id'), operate: false},
                         {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'tusers.phone', title: __('Tusers.phone'), operate: 'LIKE'},
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
   
                       // {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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