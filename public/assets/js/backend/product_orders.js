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
                    index_url: 'product_orders/index' + location.search,
                    add_url: 'product_orders/add',
                    edit_url: 'product_orders/edit',
                    del_url: 'product_orders/del',
                    pass_url: 'pass_orders/del',
                    refuse_url: 'refuse_orders/del',
                    multi_url: 'product_orders/multi',
                    import_url: 'product_orders/import',
                    table: 'product_orders',
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
                        {field: 'order_num', title: __('Order_num'), operate: 'LIKE'},
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'product.order_num', title: __('Product.order_num'), operate: 'LIKE'},
                        {field: 'goodscategory.name', title: __('Goodscategory.name'), operate: 'LIKE'},
                        {field: 'goods_category_id', title: __('Goodscategory.name'), visible:false,searchList: $.getJSON('goods_category/list')},
                        {field: 'users.phone', title: __('Users.phone'), operate: 'LIKE'},
                        {field: 'susers.phone', title: __('Susers.phone'), operate: 'LIKE'},
                        {field: 'price', title: __('Price'), operate:false},
                        {field: 'paid_price', title: __('Paid_price'), operate:false},
                        {field: 'pay_image', title: __('Pay_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'status', title: __('Status'), searchList: {"1":__('Status 1'),"2":__('Status 2'),"3":__('Status 3'),"4":__('Status 4'),"5":__('Status 5')}, formatter: Table.api.formatter.status},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'pay_time', title: __('Pay_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'confirm_time', title: __('Confirm_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
                        {field: 'appeal_remark', title: __('Appeal_remark'), operate: false},
                        {field: 'appeal_images', title: __('Appeal_images'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.images},
                        // {field: 'buy_price', title: __('Buy_price'), operate:'BETWEEN'},
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'pass',
                                    text: '通过申诉',
                                    title: '确认通过申诉吗',
                                    classname: 'btn btn-success btn-ajax',
                                    icon: 'fa fa-check',
                                    url: 'product_orders/pass',
                                    confirm: '确认通过申诉吗?',
                                    visible:function(row){
                                        if(row.status == 4){
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
                                    text: '拒绝申诉',
                                    title: '拒绝申诉',
                                    classname: 'btn  btn-primary btn-ajax',
                                    icon: 'fa fa-close',
                                    url: 'product_orders/refuse',
                                    confirm: '确认拒绝申诉吗?',
                                    visible:function(row){
                                        if(row.status == 4){
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
                            ]

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
        pass: function () {
            Controller.api.bindevent();
        },
        refuse:function () {
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