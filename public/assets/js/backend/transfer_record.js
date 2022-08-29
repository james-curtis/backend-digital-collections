define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'transfer_record/index' + location.search,
                    add_url: 'transfer_record/add',
                    edit_url: 'transfer_record/edit',
                    del_url: 'transfer_record/del',
                    multi_url: 'transfer_record/multi',
                    import_url: 'transfer_record/import',
                    table: 'transfer_record',
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
                        {field: 'phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'currency_name', title: __('Currency_id'), operate:false},
                        {field: 'currency_id', title: __('Currency_id'), visible:false,searchList:$.getJSON('currency/currencyList')},
                        {field: 'account', title: __('Account'), operate:false},
                        {field: 'target_phone', title: __('Target_uid'), operate: 'LIKE'},
                        {field: 'reality_account', title: __('Reality_account'), operate:false},
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