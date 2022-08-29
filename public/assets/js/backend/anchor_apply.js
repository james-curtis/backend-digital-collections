define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
              $(document).on('click', '.btn-refuses', function () {
                 var ids = Table.api.selectedids(table);
                Fast.api.open("anchor_apply/refuses/ids/"+ids, "驳回信息", {
                    callback:function(value){
                        Fast.api.close();
                        parent.Toastr.success('操作成功');
                        parent.$(".btn-refresh").trigger("click");
                    }
                });
                return false;
            });
            
            // 初始化表格参数配置
            Table.api.init({
                                search: false,
                commonSearch: true,
                searchFormVisible: true,
                 fixedColumns: true,
                 fixedRightNumber: 1,
                extend: {
                    index_url: 'anchor_apply/index' + location.search,
                    add_url: 'anchor_apply/add',
                    edit_url: 'anchor_apply/edit',
                    del_url: 'anchor_apply/del',
                    multi_url: 'anchor_apply/multi',
                    import_url: 'anchor_apply/import',
                    table: 'anchor_apply',
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
                                                // {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'anchor_nickname', title: __('Anchor_nickname'), operate: 'LIKE'},
                        {field: 'anchor_dou_number', title: __('Anchor_dou_number'), operate: 'LIKE'},
                        {field: 'anchor_order_name', title: __('Anchor_order_name'), operate: 'LIKE'},
                        {field: 'anchor_order_phone', title: __('Anchor_order_phone'), operate: 'LIKE'},
                         {field: 'city', title: __('City'), operate: false},
                        {field: 'anchor_order_address', title: __('Anchor_order_address'), operate: false},
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'goods.code', title: __('Goods.code'), operate: 'LIKE'},
                       // {field: 'goods_name', title: __('Goods_name'), operate: 'LIKE'},
                        {field: 'goods_number', title: __('Goods_number'), operate: false},
                        {field: 'homepage_image', title: __('Homepage_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'chate_image', title: __('Chate_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,buttons: [
                                {
                                    name: 'pass',
                                    text: '通过',
                                    title: '确认通过吗?',
                                    classname: 'btn btn-success btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'anchor_apply/pass',
                                    confirm: '确认通过吗?',
                                    visible:function(row){
                                        if(row.status == 0){
                                            return true; //或者return false
                                        }
                                    },
                                    success: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    error: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    }

                                },
                                {
                                    name: 'refuses',
                                    text: '拒绝',
                                    title: '确认通过吗?',
                                   classname: 'btn btn-success btn-ajax',
                                    icon: 'fa fa-close',
                                    url: 'anchor_apply/refuses',
                                    confirm: '确认拒绝吗?',
                                    visible:function(row){
                                        if(row.status == 0){
                                            return true; //或者return false
                                        }
                                    },
                                    success: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    error: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    }

                                },
                            ]}
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
        pass: function () {
            Controller.api.bindevent();
        },
        refuses: function () {
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