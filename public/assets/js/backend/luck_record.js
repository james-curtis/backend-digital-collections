define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'luck_record/index' + location.search,
                    add_url: 'luck_record/add',
                    edit_url: 'luck_record/edit',
                    del_url: 'luck_record/del',
                    multi_url: 'luck_record/multi',
                    import_url: 'luck_record/import',
                    table: 'luck_record',
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
                       // {field: 'award_type', title: __('Award_type'), searchList: {"1":__('Award_type 1'),"2":__('Award_type 2')}, formatter: Table.api.formatter.normal},
                        {field: 'users.phone', title:'会员手机号', operate: 'LIKE'},
                        {field: 'luck.name', title: __('Luck.name'), operate: false},
                        {field: 'luck_id', title: __('Luck.name'), visible:false,searchList: $.getJSON('luck/list')},
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