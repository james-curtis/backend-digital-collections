define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'product_send/index' + location.search,
                    add_url: 'product_send/add',
                    edit_url: 'product_send/edit',
                    del_url: 'product_send/del',
                    multi_url: 'product_send/multi',
                    import_url: 'product_send/import',
                    table: 'product_send',
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
                        {field: 'id', title: __('Id'),operate: false},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'up_number', title: __('Up_number'),operate: false},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'paid_price', title: __('Paid_price'), operate:false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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