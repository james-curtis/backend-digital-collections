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
                    index_url: 'anchor_live/index' + location.search,
                    add_url: 'anchor_live/add',
                    edit_url: 'anchor_live/edit',
                    del_url: 'anchor_live/del',
                    multi_url: 'anchor_live/multi',
                    import_url: 'anchor_live/import',
                    table: 'anchor_live',
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
                        {field: 'id', title: __('Id'),operate: false},
                                 {field: 'anchor.phone', title: __('Anchor.phone'), operate: 'LIKE'},
                        {field: 'head_image', title: __('Head_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {field: 'nick_name', title: __('Nick_name'),operate: false},
                        {field: 'dou_number', title: __('Dou_number'), operate: 'LIKE'},
                        {field: 'order_number', title: __('Order_number'),operate: false},
                        {field: 'rank_id', title: __('Rank_id'),operate: false},
                        {field: 'fans_number', title: __('Fans_number'),operate: false},
                        {field: 'month_live_number', title: __('Month_live_number'),operate: false},
                        {field: 'month_live_tou', title: __('Month_live_tou'), operate: false},
                        {field: 'total_price', title: __('Total_price'),operate: false},
                        {field: 'total_number', title: __('Total_number'),operate: false},
                        {field: 'total_count', title: __('Total_count'),operate: false},
                        {field: 'total_person_number', title: __('Total_person_number'),operate: false},
                        {field: 'predict_commission_price', title: __('Predict_commission_price'),operate: false},
                        {field: 'reality_commission_price', title: __('Reality_commission_price'),operate: false},
                        {field: 'live_total_price', title: __('Live_total_price'),operate: false},
                        {field: 'live_order_number', title: __('Live_order_number'),operate: false},
                        {field: 'duan_total_price', title: __('Duan_total_price'),operate: false},
                        {field: 'duan_order_number', title: __('Duan_order_number'),operate: false},
                        {field: 'return_order_number', title: __('Return_order_number'),operate: false},
                        {field: 'return_person_number', title: __('Return_person_number'),operate: false},
                        {field: 'return_price', title: __('Return_price'),operate: false},
                        {field: 'send_return_price', title: __('Send_return_price'),operate: false},
                        {field: 'send_return_person_number', title: __('Send_return_person_number'),operate: false},
                        {field: 'send_return_order_number', title: __('Send_return_order_number'),operate: false},
                        {field: 'census_time', title: __('Census_time'),operate: false},
                        {field: 'commission_price', title: __('Commission_price'),operate: false},
                        {field: 'create_time', title: __('Create_time'), operate:'RANGE', addclass:'datetimerange', autocomplete:false},
               
                        {field: 'operate', title: __('Operate'), table: table, events: Table.api.events.operate, formatter: Table.api.formatter.operate}
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