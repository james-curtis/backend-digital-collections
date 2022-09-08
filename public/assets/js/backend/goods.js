define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                search: false,
                commonSearch: true,
                searchFormVisible: true,
                extend: {
                    index_url: 'goods/index' + location.search,
                    add_url: 'goods/add',
                    edit_url: 'goods/edit',
                    del_url: 'goods/del',
                    multi_url: 'goods/multi',
                    config_url: 'goods_config/index',
                    import_url: 'goods/import',
                    table: 'goods',
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
                        {field: 'name', title: __('Name'), operate: 'LIKE'},
                        {
                            field: 'is_can_buy',
                            title: '是否参与买卖',
                            searchList: {"0": '不参与', "1": '参与'},
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'is_manghe',
                            title: '类型',
                            searchList: {"0": '藏品', "1": '盲盒', "2": '合成'},
                            formatter: Table.api.formatter.normal
                        },
                        {field: 'goodscategory.name', title: __('Goodscategory.name'), operate: false},
                        {
                            field: 'goods_category_id',
                            title: __('Goodscategory.name'),
                            visible: false,
                            searchList: $.getJSON('goods_category/list')
                        },
                        {
                            field: 'image',
                            title: __('Image'),
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.image
                        },
                        {field: 'price', title: __('Price'), operate: false},
                        {
                            field: 'type',
                            title: __('Type'),
                            searchList: {"1": __('Type 1')},
                            formatter: Table.api.formatter.normal
                        },
                        {field: 'order', title: __('Order'), operate: false},
                        {
                            field: 'start_time',
                            title: __('Start_time'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false
                        },
                        {field: 'stock', title: __('Stock'), operate: false},
                        {field: 'sales', title: __('Sales'), operate: false},
                        {field: 'surplus', title: __('Surplus'), operate: false},
                        {field: 'label', title: __('Label'), operate: 'LIKE'},
                        {
                            field: 'is_show',
                            title: __('Is_show'),
                            searchList: {"0": __('Is_show 0'), "1": __('Is_show 1')},
                            formatter: Table.api.formatter.normal
                        },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: '商品组合设置',
                                    text: '商品组合设置',
                                    title: '商品组合设置',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: 'goods_config/index',
                                    visible: function (row) {
                                        if (row.type == 2) {
                                            return true; //或者return false
                                        }
                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }

                                },
                                {
                                    name: '盲盒参与商品设置',
                                    text: '盲盒参与商品设置',
                                    title: '盲盒参与商品设置',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: function (row) {
                                        return 'goods_manghe_config/index?goods_id=' + row.id;
                                    },
                                    visible: function (row) {
                                        if (row.is_manghe == 1) {
                                            return true; //或者return false
                                        }
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

            //绑定 盲盒类型切换事件
            $('#c-is_manghe').on('change', function (e) {
                let is_manghe = $(this).val();
                if (is_manghe == 1) {
                    $('.is_manghe').hide();
                    $('#c-type').selectpicker('val', "1");
                    $('#c-type').selectpicker('refresh');
                    $("#c-type").attr("disabled", "disabled");
                } else {
                    $('.is_manghe').show();
                    $("#c-type").removeAttr("disabled");
                }
            });
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