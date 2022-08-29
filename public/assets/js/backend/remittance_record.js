define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'remittance_record/index' + location.search,
                    add_url: 'remittance_record/add',
                    edit_url: 'remittance_record/edit',
                    del_url: 'remittance_record/del',
                    multi_url: 'remittance_record/multi',
                    import_url: 'remittance_record/import',
                    table: 'remittance_record',
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
                        {field: 'id', title: __('Id') ,operate: false},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'configpay.name', title: __('Configpay.name'), operate: 'LIKE'},
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'account', title: __('Account'),operate: false},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('load-success.bs.table', function (e, data) {
                $("#account").text(data.account);
            });
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