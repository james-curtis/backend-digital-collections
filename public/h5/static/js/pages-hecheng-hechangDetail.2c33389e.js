(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-hecheng-hechangDetail"],{"04e6":function(t,o,a){"use strict";var n=a("ccce"),e=a.n(n);e.a},"1fac":function(t,o,a){var n=a("3e1f");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var e=a("4f06").default;e("abce35aa",n,!0,{sourceMap:!1,shadowMode:!1})},2681:function(t,o,a){"use strict";a.r(o);var n=a("7c83"),e=a("5fd2");for(var i in e)"default"!==i&&function(t){a.d(o,t,(function(){return e[t]}))}(i);a("04e6"),a("46ed");var s,r=a("f0c5"),c=Object(r["a"])(e["default"],n["b"],n["c"],!1,null,"65a29fa8",null,!1,n["a"],s);o["default"]=c.exports},"3ddc":function(t,o,a){"use strict";Object.defineProperty(o,"__esModule",{value:!0}),o.default=void 0;var n={data:function(){return{goodsId:"",info:{},fullscreen:!0,winInfo:"",isWin:!1,is_mask:!1,number:0}},onLoad:function(t){this.isWin=!1,this.goodsId=t.id},onShow:function(){this.info={},this.isWin=!1,this.is_mask=!1,this.getData()},methods:{openManghe:function(){var t=this;this.is_mask=!0,this.$http.post("index/chipCompose",{id:this.goodsId}).then((function(o){1==o.code?(t.getData(),t.winInfo="合成成功！",t.$refs.popup.open("center")):(t.toast(o.msg),t.is_mask=!1)})).catch((function(o){t.is_mask=!1}))},closeManghe:function(){this.$refs.popup.close(),this.is_mask=!1},gotoMangheDetail:function(){this.$refs.popup.close(),this.go("goodsMangheDetail?goodsId="+this.winInfo.combination_goods_id)},getData:function(){var t=this;this.$http.post("index/synthesis",{id:this.goodsId}).then((function(o){t.info=o.goodslist,t.number=o.hcsycs}))}}};o.default=n},"3e1f":function(t,o,a){var n=a("24fb"),e=a("1de5"),i=a("a3c4"),s=a("796c"),r=a("f357");o=n(!1);var c=e(i),d=e(s),f=e(r);o.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 颜色变量 */\n/*  常用字体颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-textarea[data-v-65a29fa8]{background-color:#f8f8f8;width:%?650?%;height:%?130?%;display:block;position:relative;font-size:%?28?%;line-height:normal;white-space:pre-wrap;word-break:break-all;padding:%?20?%;color:#777;border-radius:%?10?%}@-webkit-keyframes myfirst-data-v-65a29fa8{0%{-webkit-transform:rotatey(0deg);transform:rotatey(0deg)}25%{-webkit-transform:rotatey(90deg);transform:rotatey(90deg)}50%{-webkit-transform:rotatey(180deg);transform:rotatey(180deg)}75%{-webkit-transform:rotatey(270deg);transform:rotatey(270deg)}100%{-webkit-transform:rotatey(1turn);transform:rotatey(1turn)}}@keyframes myfirst-data-v-65a29fa8{0%{-webkit-transform:rotatey(0deg);transform:rotatey(0deg)}25%{-webkit-transform:rotatey(90deg);transform:rotatey(90deg)}50%{-webkit-transform:rotatey(180deg);transform:rotatey(180deg)}75%{-webkit-transform:rotatey(270deg);transform:rotatey(270deg)}100%{-webkit-transform:rotatey(1turn);transform:rotatey(1turn)}}.container[data-v-65a29fa8]{height:100%;width:100%;position:relative}.mask[data-v-65a29fa8]{height:100%;width:100%;background-color:rgba(0,0,0,.4);position:absolute;opacity:1;top:0}.content[data-v-65a29fa8]{padding-bottom:%?200?%}.content .popup-content[data-v-65a29fa8]{width:%?570?%;height:%?570?%;background-image:url('+c+");background-size:100% auto;background-repeat:no-repeat;position:relative;top:%?-100?%}.content .popup-content .close[data-v-65a29fa8]{width:%?50?%;height:%?50?%;background-image:url("+d+");background-size:100% auto;background-repeat:no-repeat;position:absolute;right:%?0?%;top:%?-40?%}.content .popup-content .manghe_content[data-v-65a29fa8]{display:flex;flex-direction:column;justify-content:space-around;align-items:center}.content .popup-content .manghe_content .image[data-v-65a29fa8]{width:60%;height:%?280?%;margin-top:%?100?%}.content .popup-content .manghe_content .no_win[data-v-65a29fa8]{font-size:%?32?%;color:#fff;padding:%?20?% %?0?% %?20?% %?20?%}.content .popup-content .manghe_content .desc[data-v-65a29fa8]{color:#fff;font-size:%?28?%;padding:%?20?% %?0?%}.content .popup-content .manghe_content .view_btn[data-v-65a29fa8]{border-radius:20px;background-color:#00d18b;width:%?320?%;height:%?70?%;line-height:%?70?%;text-align:center;color:#333}.content .Box[data-v-65a29fa8]{width:100%;height:%?680?%;padding-bottom:%?50?%;background:url("+f+") no-repeat 0 0;background-size:100%;display:flex;flex-direction:column;justify-content:flex-end;align-items:center;perspective:1000;-webkit-perspective:1000}.content .Box .videoBox[data-v-65a29fa8]{width:%?630?%;height:%?473?%}.content .Box .videoBox uni-video[data-v-65a29fa8]{width:100%;height:100%}.content .Box .rotateBox[data-v-65a29fa8]{width:%?540?%;height:%?540?%;-webkit-animation:myfirst-data-v-65a29fa8 10s linear .15s 100;animation:myfirst-data-v-65a29fa8 10s linear .15s 100}.content .Box .rotateBox .image[data-v-65a29fa8]{width:%?540?%;height:%?540?%}.content .type1[data-v-65a29fa8]{width:100%;bottom:%?150?%}.content .msgBox[data-v-65a29fa8]{\n  /* background-color: #23272C; */padding:%?30?%}.content .msgBox .goodsName[data-v-65a29fa8]{color:#fff;font-size:%?36?%;font-weight:500;margin-bottom:%?20?%}.content .msgBox .category_name[data-v-65a29fa8]{height:%?40?%;line-height:%?40?%;font-size:%?22?%;padding:0 %?15?%;background:#f1e2bc;border-radius:%?6?%;color:#1e1817;margin-right:%?20?%}.content .msgBox .number[data-v-65a29fa8]{height:%?40?%;line-height:%?40?%;padding:0 %?15?%;border-radius:%?20?%;background-color:#ffdd9d;color:#8a683a;font-size:%?20?%;font-weight:500}.content .msgBox .describe[data-v-65a29fa8]{color:#777;font-size:%?26?%;line-height:%?38?%;font-weight:500;margin-top:%?20?%}.content .msgBox .priceBox[data-v-65a29fa8]{color:#fff;font-size:%?24?%;font-weight:500}.content .msgBox .priceBox uni-text[data-v-65a29fa8]{font-weight:500;font-size:%?36?%;margin-left:%?10?%}.content .msgBox .rule[data-v-65a29fa8]{background:#383b3f;border-radius:%?20?%;padding:%?20?%;color:#777;font-size:%?24?%;line-height:%?34?%;margin-top:%?20?%}.content .msgBox .rule .rule1[data-v-65a29fa8]{color:#8a683a;margin-bottom:%?10?%}.content .descBox[data-v-65a29fa8]{padding:%?20?% %?30?%;\n  /* background-color: #23272C; */color:#aaa}.content .descBox .item[data-v-65a29fa8]{font-size:%?32?%;font-weight:500;color:#fff;margin-bottom:%?10?%}.content .descBox .desinfo[data-v-65a29fa8]{font-size:%?26?%}.content .footerBox[data-v-65a29fa8]{position:fixed;left:0;bottom:0;z-index:2;width:100%;height:%?120?%;background-color:rgba(0,0,0,.7)}.content .footerBox .price[data-v-65a29fa8]{color:#fff;font-size:%?32?%;margin-left:%?30?%}.content .footerBox .price .no_open[data-v-65a29fa8]{color:red;margin-left:%?30?%}.content .footerBox .count[data-v-65a29fa8]{color:#777;font-size:%?28?%;margin-left:%?30?%}.content .footerBox .subBtn[data-v-65a29fa8]{width:%?360?%;height:%?88?%;line-height:%?88?%;text-align:center;font-size:%?30?%;font-family:PingFangSC-Medium,PingFang SC;font-weight:500;color:#333;font-weight:500;background:#00db7d;border-radius:%?44?%;margin-right:%?30?%}.content .footerBox .subnrn1[data-v-65a29fa8]{background:#8c8c8c}.content .type2 .goodsList[data-v-65a29fa8]{background-color:#23272c;padding:0 %?28?%;display:flex;flex-wrap:wrap;justify-content:space-between}.content .type2 .goodsList .goodsItem[data-v-65a29fa8]{width:%?335?%;margin-bottom:%?20?%;background:#383b3f;border-radius:%?12?%;position:relative}.content .type2 .goodsList .goodsItem .goodsImg[data-v-65a29fa8]{width:100%;height:%?300?%;border-radius:%?20?%}.content .type2 .goodsList .goodsItem .mask[data-v-65a29fa8]{width:100%;height:%?300?%;border-radius:%?20?%;background:rgba(0,0,0,.2);position:absolute;left:0;top:0}.content .type2 .goodsList .goodsItem .mask .maskImg[data-v-65a29fa8]{width:%?170?%;height:%?170?%}.content .type2 .goodsList .goodsItem .goodsinfo[data-v-65a29fa8]{padding:%?20?%}.content .type2 .goodsList .goodsItem .goodsinfo .goodsName[data-v-65a29fa8]{font-size:%?32?%;font-weight:500;color:#fff;overflow:hidden;word-break:break-all;text-overflow:ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2;margin-bottom:%?10?%}.content .type2 .goodsList .goodsItem .goodsinfo .coupon[data-v-65a29fa8]{width:%?48?%;height:%?48?%}.content .type2 .goodsList .goodsItem .goodsinfo .price[data-v-65a29fa8]{color:#fff;font-size:%?26?%;margin-right:%?10?%}.content .type2 .goodsList .goodsItem .goodsinfo .price uni-text[data-v-65a29fa8]{font-size:%?34?%}.content .type2 .goodsList .goodsItem .goodsinfo .count[data-v-65a29fa8]{flex:1;color:#777;font-size:%?24?%;text-align:right}",""]),t.exports=o},"46ed":function(t,o,a){"use strict";var n=a("1fac"),e=a.n(n);e.a},"5fd2":function(t,o,a){"use strict";a.r(o);var n=a("3ddc"),e=a.n(n);for(var i in n)"default"!==i&&function(t){a.d(o,t,(function(){return n[t]}))}(i);o["default"]=e.a},6040:function(t,o,a){var n=a("24fb");o=n(!1),o.push([t.i,"uni-page-body[data-v-65a29fa8]{background-color:#23272c}body.?%PAGE?%[data-v-65a29fa8]{background-color:#23272c}",""]),t.exports=o},"796c":function(t,o){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACwAAAAsCAYAAAAehFoBAAADo0lEQVRYR9WZS6iWRRjHf/9NYqGBl50rhZRcWC2yIHVlntQCNUhtExWEtg/y0iKtA+5TAo02WkE3zdJ0pQZdFpULM4NatUuClC66eeQv88r49n7v5ctDzsBZnG9mnuf3zjy3mRH/oUXEncAyYAWwGLgHmAPMSGIvAxeBn4BzwCngjKS/xlWrcSZGxATwDPAEMH2gjL+BI8Dbko4PnMsg4IhYD7wCLBmqaMT4s8Crkj7sK68XcEQsAPYBKxsEXwJOA18CF4BfAZuCm01jHrAQeBhYDsxskHES2CLp5y7wTuCI2Ai8WVMUwGfAAeBTSVe7FLk/Iu4A1gDPAavhph32h78g6d02Wa3AEbEL2FETcAJ4SZK3c+wWETarPcCjNSG7Je0cJXgkcES8AWzNJv6Rtu2dsSkbJkbEpmRud2fdeyW92KSnEbhhZX8AHpf0y62ErWRFxHzgE+DeTH7jSv8LONlsvorfAI9J+n0qYDPoWcAx4MFMz6a6Td8EnKLBt5mDeWWXTTVsDfpMttJ2xAfy6FEHtkNVocs268FTYgajdiuZhxetsumTkm445g3glBQ+yARtlnRLHayvSSVHPJSN31Allxz4+yyDnZC0qklBRLwM+M9O8qykK31BUiyeBrxlJwYmJU2O0PN5FvLOSrrP464Dp9rABn/9X+D+UXE2ImxXVXHj5LG+L3REGNZp2EnD7bKkpsxnJsfp77LkYsc/XgE7uzyVhDhzrW2xsYPA5qy/F3QDrEUckvR0i66jKTN6yHuSNioi7gJ+y6qudZI+bhFSXyUPbYUeAdv5oRGxLu2IdbjKm2tg22pV5nm753bVBkMAhoytL1KqPbyYldlMGPj15EQef1SSnaGz9QHpM6ZLUUTYuSsTnTTw4VSIe+52Sf6AXq0NKAnIHazTdJqURsQ24LXUd8TA54FF6YcnJeWxuBO8Bdpzq2gwFqwnRcQG4P0E8qOBfeaanX54SNLXnZS1ASOg81GdDtbi5EuBr1L/RQP/A9jz3RZLcv0wuLVAjw2bVtgVnA+wbleKBHbY8NHcrQiTKM7pigtrxSWO4lJzvfhxufhRS1z8P4ufOeWVlyk4+3KvnAI+QZdzRErAvpks5xCaoMs55idgX6uWc5GSoH29WsZVVRV7i7oMzKDLuW5tWWl33Z4X2hl025PBft9JdF0LZLL8ZOBz3vNT8mSQKSrnUSYvgop59qpXbsU8LDaAuzR9ZODT7ReS/hx8LE8TrgFssISm/McszQAAAABJRU5ErkJggg=="},"7c83":function(t,o,a){"use strict";a.d(o,"b",(function(){return e})),a.d(o,"c",(function(){return i})),a.d(o,"a",(function(){return n}));var n={uniPopup:a("2745").default},e=function(){var t=this,o=t.$createElement,n=t._self._c||o;return n("v-uni-view",{staticClass:"container"},[n("v-uni-view",{staticClass:"content"},[n("v-uni-view",{staticClass:"Box"},[n("v-uni-view",{staticClass:"rotateBox",style:"background-image: url("+t.info.image+");background-size: 100% 100%"},[n("v-uni-image",{staticClass:"image",attrs:{src:a("66b5")}})],1)],1),n("v-uni-view",{staticClass:"type1"},[n("v-uni-view",{staticClass:"msgBox"},[n("v-uni-view",{staticClass:"goodsName"},[t._v(t._s(t.info.name))]),n("v-uni-view",{staticClass:"flex_bt"},[n("v-uni-view",{staticClass:"category_name"},[t._v("碎片合成")])],1)],1),t.info.content?n("v-uni-view",{staticClass:"descBox"},[n("v-uni-view",{staticClass:"item"},[t._v("合成介绍")]),n("v-uni-view",{staticClass:"desinfo",domProps:{innerHTML:t._s(t.util.checkImg(t.info.content))}})],1):t._e(),n("v-uni-view",{staticClass:"footerBox flex_bt"},[n("v-uni-view",{staticClass:"price"},[t._v("剩余合成次数:"),n("v-uni-text",{staticClass:"no_open"},[t._v(t._s(t.number))])],1),0!=t.number?n("v-uni-view",{staticClass:"subBtn ",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.openManghe()}}},[t._v("立即合成")]):n("v-uni-view",{staticClass:"subBtn subnrn1"},[t._v("已开完")])],1)],1),n("uni-popup",{ref:"popup",staticClass:"win_popup",attrs:{type:"center","mask-click":!1,animation:!1}},[n("v-uni-view",{staticClass:"popup-content"},[n("v-uni-view",{staticClass:"close",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.closeManghe.apply(void 0,arguments)}}}),n("v-uni-view",{staticClass:"manghe_content"},[n("v-uni-image",{staticClass:"image",attrs:{src:t.info.image}}),t.winInfo?n("v-uni-view",{staticClass:"desc"},[t._v(t._s(t.info.name))]):t._e(),n("v-uni-view",{staticClass:"no_win"},[t._v(t._s(t.winInfo)+"!")]),t.winInfo.is_win?n("v-uni-view",{staticClass:"view_btn",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.gotoMangheDetail.apply(void 0,arguments)}}},[t._v("查看详情")]):t._e()],1)],1)],1)],1),t.is_mask?n("v-uni-view",{staticClass:"mask"}):t._e()],1)},i=[]},a3c4:function(t,o,a){t.exports=a.p+"static/img/bg.6e070fe5.png"},ccce:function(t,o,a){var n=a("6040");"string"===typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);var e=a("4f06").default;e("4dc3a61a",n,!0,{sourceMap:!1,shadowMode:!1})}}]);