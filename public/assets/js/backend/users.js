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
                    //plrecharge_url: 'users/plrecharge',
                    plgive_url: 'users/plgive',
                    multi_url: 'users/multi',
                    import_url: 'users/import',
                    table: 'users',
                }
            });

            var table = $("#table");
            $(document).on("click", ".btn-plrecharge", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id']
                }

                Layer.prompt({
                    title: "充值金额",
                    success: function (layero) {
                        $("input", layero).prop("placeholder", "填写充值金额");
                    }
                }, function (value) {
                    Fast.api.ajax({
                        url: "users/plrecharge",
                        data: {
                            ids: ids,
                            account: value
                        },
                    }, function (data, ret) {
                        Layer.closeAll();
                        $(".btn-refresh").trigger("click");
                        //return false;
                    });
                });

            });

            $(document).on("click", ".btn-nftupdate", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id']
                }

                Layer.confirm(
                    '确认选中的' + ids.length + '条修改成上链吗?', {
                        icon: 3,
                        title: __('Warning'),
                        offset: '40%',
                        shadeClose: true
                    },
                    function (index) {
                        Layer.close(index);
                        Backend.api.ajax({
                            //url: "lgwy/attrchg/approve?ids=" + JSON.stringify(ids),
                            //方法一：传参方式，后台需要转换变成数组
                            /*url: "lgwy/attrchg/approve?ids=" + (ids),
                            data: {}*/
                            //方法二：传参方式，直接是数组传递给后台
                            url: "users/nftupdate",
                            data: {
                                ids: ids
                            }
                        }, function (data, ret) { //成功的回调
                            if (ret.code === 1) {

                                table.bootstrapTable('refresh');
                                Layer.close(index);
                            } else {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        }, function (data, ret) { //失败的回调
                            console.log(ret);
                            // Toastr.error(ret.msg);
                            Layer.close(index);
                        });
                    }
                );
            });

            $(document).on("click", ".btn-plgive", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id']
                }
                console.log(ids);
                sessionStorage.setItem('arr', JSON.stringify(ids));
                //   Layer.prompt({
                //                                 title: "充值金额",
                //                                 success: function (layero) {
                //                                     $("input", layero).prop("placeholder", "填写充值金额");
                //                                 }
                //                             }, function (value) {
                //                                 Fast.api.ajax({
                //                                     url: "users/plrecharge",
                //                                     data: {
                //                                       ids: ids,
                //                                       account: value
                //                                           },
                //                                 }, function (data, ret) {
                //                                     Layer.closeAll();
                //                                     $(".btn-refresh").trigger("click");
                //                                     //return false;
                //                                 });
                //                             });

            });
            //改为类型一
            $(document).on("click", ".btn-plstatus", function () {
                var data = table.bootstrapTable('getSelections');
                var ids = [];
                if (data.length === 0) {
                    Toastr.error("请选择操作信息");
                    return;
                }
                for (var i = 0; i < data.length; i++) {
                    ids[i] = data[i]['id']
                }

                Layer.confirm(
                    '确认选中的' + ids.length + '条改为优先购吗?', {
                        icon: 3,
                        title: __('Warning'),
                        offset: '40%',
                        shadeClose: true
                    },
                    function (index) {
                        Layer.close(index);
                        Backend.api.ajax({
                            //url: "lgwy/attrchg/approve?ids=" + JSON.stringify(ids),
                            //方法一：传参方式，后台需要转换变成数组
                            /*url: "lgwy/attrchg/approve?ids=" + (ids),
                            data: {}*/
                            //方法二：传参方式，直接是数组传递给后台
                            url: "users/plstatus",
                            data: {
                                ids: ids
                            }
                        }, function (data, ret) { //成功的回调
                            if (ret.code === 1) {

                                table.bootstrapTable('refresh');
                                Layer.close(index);
                            } else {
                                Layer.close(index);
                                Toastr.error(ret.msg);
                            }
                        }, function (data, ret) { //失败的回调
                            console.log(ret);
                            // Toastr.error(ret.msg);
                            Layer.close(index);
                        });
                    }
                );
            });
            //       // 指定搜索条件
            // $(document).on("click", ".btn-snapshot", function () {
            //     var options = table.bootstrapTable('getOptions');
            //     var queryParams = options.queryParams;
            //     options.pageNumber = 1;
            //     options.queryParams = function (params) {
            //         //这一行必须要存在,否则在点击下一页时会丢失搜索栏数据
            //         params = queryParams(params);

            //         //如果希望追加搜索条件,可使用
            //         var filter = params.filter ? JSON.parse(params.filter) : {};
            //         var op = params.op ? JSON.parse(params.op) : {};
            //         filter.kz = 0;
            //         //op.url = 'like';

            //          params.filter = JSON.stringify(filter);
            //         // params.op = JSON.stringify(op);
            //         //var snapshot='kz';
            //         // params.snapshot;
            //         //如果希望忽略搜索栏搜索条件,可使用
            //         //params.filter = JSON.stringify({url: 'login'});
            //         //params.op = JSON.stringify({url: 'like'});
            //         return params;
            //     };
            //     table.bootstrapTable('refresh', {});
            //     return false;


            //   // $(".btn-refresh").trigger("click");
            // });
            // 初始化表格
            table.bootstrapTable({
                url: $.fn.bootstrapTable.defaults.extend.index_url,

                pk: 'id',
                sortName: 'id',

                columns: [
                    [
                        {checkbox: true},
                        {field: 'id', title: __('Id'), operate: false},
                        {field: 'nick_name', title: __('Nick_name'), operate: 'LIKE'},
                        {
                            field: 'head_image',
                            title: __('Head_image'),
                            operate: false,
                            events: Table.api.events.image,
                            formatter: Table.api.formatter.image
                        },
                        {
                            field: 'goods_id',
                            title: __("藏品"),
                            visible: false,
                            searchList: $.getJSON("goods/goodsList")
                        },
                        {field: 'phone', title: __('Phone'), operate: 'LIKE'},
                        {
                            field: 'Nftstatus',
                            title: __('Nftstatus'),
                            searchList: {"0": __('Nftstatus 0'), "1": __('Nftstatus 1')},
                            formatter: Table.api.formatter.status
                        },
                        {field: 'role.name', title: __('Role.name'), operate: false},
                        {
                            field: 'role_id',
                            title: __('Role.name'),
                            visible: false,
                            searchList: $.getJSON('role/roleList')
                        },
                        {
                            field: 'status',
                            title: __('Status'),
                            searchList: {"0": __('Status 0'), "1": __('Status 1')},
                            formatter: Table.api.formatter.status
                        },
                        {field: 'total_direct', title: __('Total_direct'), operate: false, sortable: true},
                        {field: 'group_person_count', title: __('Group_person_count'), operate: false, sortable: true},
                        {
                            field: 'group_valid_person_count',
                            title: __('Group_valid_person_count'),
                            operate: false,
                            sortable: true
                        },

                        {field: 'cpzs', title: __('藏品数量'), operate: false, sortable: true},
                        {field: 'parent_member', title: __('Parent_member'), operate: 'LIKE'},
                        {
                            field: 'is_auth',
                            title: __('Is_auth'),
                            searchList: {"0": __('Is_auth 0'), "1": __('Is_auth 1')},
                            formatter: Table.api.formatter.status
                        },
                        {field: 'name', title: __('Name'), operate: false},
                        {field: 'card', title: __('Card'), operate: false},
                        // {field: 'account', title: __('Account'), operate:false},
                        {field: 'wallet_address', title: __('Wallet_address'), operate: false},
                        // {field: 'wallet_private_key', title: __('Wallet_private_key'), operate: 'LIKE'},
                        // {field: 'wx_opend_id', title: __('Wx_opend_id'), operate: 'LIKE'},
                        // {field: 'wx_samll_id', title: __('Wx_samll_id'), operate: 'LIKE'},
                        // {field: 'wx_union_id', title: __('Wx_union_id'), operate: 'LIKE'},
                        // {field: 'card_front_image', title: __('Card_front_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        // {field: 'card_back_image', title: __('Card_back_image'), operate: false, events: Table.api.events.image, formatter: Table.api.formatter.image},
                        {
                            field: 'is_bank',
                            title: __('Is_bank'),
                            searchList: {"0": __('Is_bank 0'), "1": __('Is_bank 1')},
                            formatter: Table.api.formatter.normal,
                            operate: false
                        },
                        {
                            field: 'is_ali',
                            title: __('Is_ali'),
                            searchList: {"0": __('Is_ali 0'), "1": __('Is_ali 1')},
                            formatter: Table.api.formatter.normal,
                            operate: false
                        },
                        {
                            field: 'is_wx',
                            title: __('Is_wx'),
                            searchList: {"0": __('Is_wx 0'), "1": __('Is_wx 1')},
                            formatter: Table.api.formatter.normal,
                            operate: false
                        },

                        {
                            field: 'create_time',
                            title: __('Create_time'),
                            operate: 'RANGE',
                            addclass: 'datetimerange',
                            autocomplete: false
                        },
                        {
                            field: 'operate',
                            title: __('Operate'),
                            table: table,
                            events: Table.api.events.operate,
                            formatter: Table.api.formatter.operate,
                            buttons: [
                                {
                                    name: 'funds',
                                    text: '充值/扣费',
                                    title: '充值/扣费',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: 'users/funds',
                                    visible: function (row) {
                                        return true; //或者return false

                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                },
                                {
                                    name: 'send',
                                    text: '赠送藏品',
                                    title: '赠送藏品',
                                    classname: 'btn  btn-success btn-dialog',
                                    url: 'users/send',
                                    visible: function (row) {
                                        return true; //或者return false

                                    },
                                    callback: function (data) {
                                        $(".btn-refresh").trigger("click");
                                    }
                                },
                            ]
                        }
                    ]
                ],
                onLoadSuccess: function (data) {
                    //我要在这里获取所有的数据的总行数
                    var tab1 = $("#table");
                    var nft0 = tab1.find("td a[data-field='Nftstatus'][data-value=0]").find(".text-primary");
                    var nft1 = tab1.find("td a[data-field='Nftstatus'][data-value=1]").length
                    nft0.css("color", "#e74c3c")
                    // console.log(nft0,nft1)


                },
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
                Layer.confirm("请选择导出的选项<form action='" + Fast.api.fixurl($(this).data().exportUrl) + "' method='post' target='_blank'><input type='hidden' name='ids' value='' /><input type='hidden' name='filter' ><input type='hidden' name='op'><input type='hidden' name='search'><input type='hidden' name='columns'></form>", {
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
        // plrecharge: function () {
        //     Controller.api.bindevent();
        // },
        plgive: function () {

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

