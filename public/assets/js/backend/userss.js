define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                fixedColumns: true,
                fixedRightNumber: 1,
                extend: {
                    index_url: 'users/index' + location.search,
                    add_url: 'users/add',
                    edit_url: 'users/edit',
                    del_url: 'users/del',
                    multi_url: 'users/multi',
                    import_url: 'users/import',
                    table: 'users',
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
                        {checkbox: true, operate: false},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'nick_name', title: __('Nick_name'), operate: false},
                        {field: 'head_image', title: __('Head_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {field: 'rank.name', title: __('Rank.name'), operate: false},
                        {field: 'rank_id', title: __('Rank.name'),visible:false, searchList: $.getJSON('rank/rankList')},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1')}, formatter: Table.api.formatter.status},
                        {field: 'uuid', title: __('Uuid'), operate: false},
                        {field: 'total_direct', title: __('Total_direct'), operate: false},
                        {field: 'group_person_count', title: __('Group_person_count'), operate: false},
                        {field: 'parent_member', title: __('Parent_member'), operate: 'LIKE'},

                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {field: 'card', title: __('Card'), operate: 'LIKE'},

                        {field: 'is_bank', title: __('Is_bank'), searchList: {"0":'未绑定',"1":'已绑定'}, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'is_ali', title: __('Is_ali'), searchList: {"0":'未绑定',"1":'已绑定'}, formatter: Table.api.formatter.normal, operate: false},
                        {field: 'is_wx', title: __('Is_wx'),searchList: {"0":'未绑定',"1":'已绑定'}, formatter: Table.api.formatter.normal, operate: false},

                        {field: 'award_recommend', title: __('Award_recommend'), operate: false},
                        {field: 'wholesale_account', title: __('Wholesale_account'), operate: false},

                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'funds',
                                    text: '充值/扣费',
                                    title: '充值/扣费',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: 'users/funds',
                                    visible:function(row){
                                            return true; //或者return false

                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                },
                                {
                                    name: 'send',
                                    text: '赠送商品',
                                    title: '赠送商品',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: 'users/send',
                                    visible:function(row){

                                            return true; //或者return false

                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                },
                            ]}
                    ]
                ]
            });
            var submitForm = function (ids, layero) {
                var options = table.bootstrapTable('getOptions');
                var columns = [];
                $.each(options.columns[0], function (i, j) {
                    if (j.field && !j.checkbox && j.visible && j.field != 'operate') {
                        columns.push(j.field);
                    }
                });
                var search = options.queryParams({});
                $("input[name=search]", layero).val(options.searchText);
                $("input[name=ids]", layero).val(ids);
                $("input[name=filter]", layero).val(search.filter);
                $("input[name=op]", layero).val(search.op);
                $("input[name=columns]", layero).val(columns.join(','));
                $("form", layero).submit();
            };
            $(document).on("click", ".btn-export", function () {
                var ids = Table.api.selectedids(table);
                var page = table.bootstrapTable('getData');
                var all = table.bootstrapTable('getOptions').totalRows;
                console.log(ids, page, all);
                Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl("users/export") + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
                    title: '导出数据',
                    btn: ["选中项(" + ids.length + "条)", "本页(" + page.length + "条)", "全部(" + all + "条)"],
                    success: function (layero, index) {
                        $(".layui-layer-btn a", layero).addClass("layui-layer-btn0");
                    }
                    , yes: function (index, layero) {
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn2: function (index, layero) {
                        var ids = [];
                        $.each(page, function (i, j) {
                            ids.push(j.id);
                        });
                        submitForm(ids.join(","), layero);
                        return false;
                    }
                    ,
                    btn3: function (index, layero) {
                        submitForm("all", layero);
                        return false;
                    }
                })
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
        funds: function () {
            Controller.api.bindevent();
        },
        send: function () {
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