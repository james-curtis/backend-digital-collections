define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                extend: {
                    index_url: 'award/index' + location.search,
                    add_url: 'award/add',
                    edit_url: 'award/edit',
                    del_url: 'award/del',
                    multi_url: 'award/multi',
                    import_url: 'award/import',
                    table: 'award',
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
                        {field: 'id', title: __('Id')},
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"0": __('Status 0'), "1": __('Status 1')},
                            formatter: Table.api.formatter.status
                        },
                        {
                            field: 'is_repeat',
                            title: __('重复获取'),
                            searchList: {"0": __('否'), "1": __('是')},
                            formatter: Table.api.formatter.status
                        },
                        {
                            field: 'type', title: __('Type'), formatter: function (value) {
                                if (value == 0) {
                                    return __('Type 0')
                                } else {
                                    return __('Type 1')
                                }
                            }
                        },
                        {
                            field: 'total_number', title: __('Total_number'), formatter: function (value, row, index) {
                                if (row.id == 1) {
                                    return "--";
                                }
                                return value;
                            }
                        },
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: function (value, row, index) {
                                var that = $.extend({}, this);
                                if (row.id == 1 || row.id == 2) {
                                    var table = $(that.table).clone(true);
                                    $(table).data("operate-del", null);
                                    that.table = table;
                                }
                                return Table.api.formatter.operate.call(that, value, row, index);
                            }
                        }
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