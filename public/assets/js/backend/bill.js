define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'bill/index' + location.search,
                    add_url: 'bill/add',
                    edit_url: 'bill/edit',
                    del_url: 'bill/del',
                    multi_url: 'bill/multi',
                    import_url: 'bill/import',
                    table: 'bill',
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
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'currency.name', title: __('Currency.name'), operate: false},
                        {field: 'currency_id', title: __('Currency.name'),visible:false,searchList: $.getJSON('currency/currencyList')},

                        {field: 'account', title: __('Account'), operate: false},
                        // {field: 'before_account', title: __('Before_account'), operate: false},
                        // {field: 'after_account', title: __('After_account'), operate: false},
                        {field: 'bill_type', title: __('Bill_type'), operate: 'LIKE'},
                        {field: 'remark', title: __('Remark'), operate: false},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2')}, formatter: Table.api.formatter.normal},
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