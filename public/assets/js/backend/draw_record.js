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
                    index_url: 'draw_record/index' + location.search,
                    add_url: 'draw_record/add',
                    edit_url: 'draw_record/edit',
                    del_url: 'draw_record/del',
                    multi_url: 'draw_record/multi',
                    import_url: 'draw_record/import',
                    table: 'draw_record',
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
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'account', title: __('Account'), operate:false},
                        {field: 'reality_account', title: __('Reality_account'), operate:false},
                        {field: 'type', title: __('Type'), searchList: {"1":__('Type 1'),"2":__('Type 2'),"3":__('Type 3')}, formatter: Table.api.formatter.normal},
                        {field: 'status', title: __('Status'), searchList: {"0":__('Status 0'),"1":__('Status 1'),"2":__('Status 2')}, formatter: Table.api.formatter.status},
                        {field: 'refuse', title: __('Refuse'), operate: false},

                        {field: 'bank_name', title: __('Bank_name'), operate: false},
                        {field: 'bank_number', title: __('Bank_number'), operate: false},
                        {field: 'bank_owner', title: __('Bank_owner'), operate: false},
                        {field: 'bank_branch', title: __('Bank_branch'), operate: false},
                        {field: 'ali_name', title: __('Ali_name'), operate: false},
                        // {field: 'ali_number', title: __('Ali_number'), operate: false},
                        {field: 'ali_image', title: __('Ali_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'wx_name', title: __('Wx_name'), operate: false},
                        // {field: 'wx_number', title: __('Wx_number'), operate: false},
                        {field: 'wx_image', title: __('Wx_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'pass',
                                    text: '通过',
                                    title: '确认通过提现吗',
                                    classname: 'btn btn-success btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'draw_record/pass',
                                    confirm: '确认通过提现吗?',
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
                                    name: 'refuse',
                                    text: '拒绝',
                                    title: '拒绝提现',
                                    classname: 'btn  btn-primary btn-dialog',
                                    icon: 'fa fa-close',
                                    url: 'draw_record/refuse',
                                    //  confirm: '确认拒绝实名认证吗?',
                                    visible:function(row){
                                        if(row.status == 0 || row.status == 3){
                                            return true; //或者return false
                                        }
                                    },
                                    success: function (data, ret) {
                                        $(".btn-refresh").trigger("click");
                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }

                                },
                            ]}
                    ]
                ]
            });

            // 为表格绑定事件
            Table.api.bindevent(table);
            table.on('load-success.bs.table', function (e, data) {
                $("#account").text(data.account);
                $("#fee_account").text(data.fee_account);
                $("#reality_account").text(data.reality_account);
            });
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
        refuse: function () {
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