define(['jquery', 'bootstrap', 'backend', 'table', 'form'], function ($, undefined, Backend, Table, Form) {

    var Controller = {
        index: function () {
            // 初始化表格参数配置
            Table.api.init({
                commonSearch:false,
                search:false,
                showToggle: false,
                showColumns: false,
                showExport: false,
                extend: {
                    index_url: 'goods_manghe_config/index' + location.search+'&goods_id='+Config.goods_id,
                    add_url: 'goods_manghe_config/add'+ location.search+'&goods_id='+Config.goods_id,
                    edit_url: 'goods_manghe_config/edit',
                    del_url: 'goods_manghe_config/del',
                    //multi_url: 'goods_manghe_config/multi',
                    //import_url: 'goods_manghe_config/import',
                    table: 'goods_manghe_config',
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
                       /* {checkbox: true},*/
                        {field: 'id', title: __('Id')},
                        {field: 'goods.name', title: __('Goods.name'), operate: 'LIKE'},
                        {field: 'is_win', title:'是否是中奖项', formatter:function (val,row) {
                                if(val == 1)
                                {
                                    return '<span class="text-success">是</span>';
                                }
                                return '<span class="">否</span>';
                         }},
                        {field: 'combination_goods_id', title: __('Combination_goods_id')},
                        {field: 'win_rate', title: __('Win_rate')},
                        {field: 'goods.price', title: __('Goods.price'), operate:'BETWEEN'},
                        {field: 'goods.is_show', title: __('Goods.is_show'),formatter: function(val,row){
                            if(val == 1)
                            {
                                return '<span class="text-success">展示</span>';
                            }
                            return '不展示';
                        }},
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