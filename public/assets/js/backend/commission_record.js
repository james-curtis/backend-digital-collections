define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'commission_record/index' + location.search,
                    add_url: 'commission_record/add',
                    edit_url: 'commission_record/edit',
                    del_url: 'commission_record/del',
                    multi_url: 'commission_record/multi',
                    import_url: 'commission_record/import',
                    table: 'commission_record',
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
                        {field: 'id', title: __('Id'), operate:false},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'anchor.phone', title: __('Anchor.phone'), operate: 'LIKE'},
                        {field: 'account', title: __('Account'), operate:false},
                        {field: 'remark', title: __('Remark'), operate: false},
                        {field: 'is_freeze', title: __('Is_freeze'), searchList: {"0":__('Is_freeze 0'),"1":__('Is_freeze 1')}, formatter: Table.api.formatter.normal},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'finish_time', title: __('Finish_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
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