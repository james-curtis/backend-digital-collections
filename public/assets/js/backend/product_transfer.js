define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'product_transfer/index' + location.search,
                    add_url: 'product_transfer/add',
                    edit_url: 'product_transfer/edit',
                    del_url: 'product_transfer/del',
                    multi_url: 'product_transfer/multi',
                    import_url: 'product_transfer/import',
                    table: 'product_transfer',
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
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'tusers.phone', title: __('Tusers.phone'), operate: 'LIKE'},
                        {field: 'productorders.order_num', title: __('Productorders.order_num'), operate: 'LIKE'},
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