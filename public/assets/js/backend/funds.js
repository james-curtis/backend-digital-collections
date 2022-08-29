define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'funds/index' + location.search,
                    add_url: 'funds/add',
                    edit_url: 'funds/edit',
                    del_url: 'funds/del',
                    multi_url: 'funds/multi',
                    import_url: 'funds/import',
                    table: 'funds',
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
                        {field: 'users.phone', title: __('Users.member'), operate: 'LIKE'},
                        {field: 'currency.name', title: __('Currency.name'), operate: false},
                        {field: 'currency_id', title: __('Currency.name'),visible:false, searchList: $.getJSON('currency/currencyList')},
                        {field: 'account', title: __('Account'), operate:false},
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