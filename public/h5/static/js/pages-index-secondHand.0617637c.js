(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-index-secondHand"],{"2d52":function(t,e,o){"use strict";o.d(e,"b",(function(){return a})),o.d(e,"c",(function(){return s})),o.d(e,"a",(function(){return i}));var i={uniLoadMore:o("3dcc").default,uniFooter:o("0e25").default},a=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("v-uni-view",{staticClass:"content"},[i("v-uni-view",{staticClass:"status_bar flexBox",class:[t.scrollTopt>0?"fixedbotto":"",""]},[i("v-uni-view",{staticClass:"search"},[i("v-uni-input",{attrs:{type:"text",placeholder:"请输入搜索内容"},model:{value:t.search,callback:function(e){t.search=e},expression:"search"}}),i("v-uni-view",{staticClass:"search-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.searchClick.apply(void 0,arguments)}}},[t._v("搜索")])],1)],1),i("v-uni-swiper",{staticClass:"banner",attrs:{"indicator-dots":"true",circular:"true",autoplay:"true",interval:"2000",duration:"500","indicator-color":"#FFFFFF","indicator-active-color":"#00D18B"}},t._l(t.banner,(function(t,e){return i("v-uni-swiper-item",{key:e},[i("v-uni-image",{attrs:{src:t.image,mode:""}})],1)})),1),i("v-uni-view",{staticClass:"tabBox flex_bt"},[i("v-uni-scroll-view",{staticClass:"scroll-view_H",attrs:{"scroll-x":"true","scroll-left":"120"}},t._l(t.CategoryList,(function(e,o){return i("v-uni-view",{key:o,class:t.gid==e.id?"tab scroll-view-item_H act":"tab scroll-view-item_H",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.reload(e.id)}}},[t._v(t._s(e.name))])})),1),t.block?i("v-uni-image",{staticClass:"right",attrs:{src:o("80a6"),mode:""},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.block=!t.block}}}):t._e(),t.block?t._e():i("v-uni-image",{staticClass:"right",attrs:{src:o("7953"),mode:""},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.block=!t.block}}})],1),t.block?i("v-uni-view",{staticClass:"goodsList"},t._l(t.GoodsList,(function(e,o){return i("v-uni-view",{key:o,staticClass:"goodsItem",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.go("secondGoodsDetail?goodsId="+e.id)}}},[i("v-uni-image",{staticClass:"goodsImg",attrs:{src:e.image,mode:""}}),i("v-uni-view",{staticClass:"goodsinfo"},[i("v-uni-view",{staticClass:"flex_bt"},[i("v-uni-view",{staticClass:"goodsName"},[t._v(t._s(e.name))])],1),i("v-uni-view",{staticClass:"flexBox flex_bt",staticStyle:{"margin-top":"20rpx"}},[i("v-uni-view",{staticClass:"label"},[t._v(t._s(e.goods_category_name))]),i("v-uni-view",{staticClass:"goodsPrice"},[t._v(t._s(e.price))])],1)],1)],1)})),1):t._e(),t.block?t._e():i("v-uni-view",{staticClass:"goodsList1"},t._l(t.GoodsList,(function(e,o){return i("v-uni-view",{key:o,staticClass:"goodsItem flex",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.go("secondGoodsDetail?goodsId="+e.id)}}},[i("v-uni-image",{staticClass:"goodsImg",attrs:{src:e.image,mode:""}}),i("v-uni-view",{staticClass:"goodsinfo"},[i("v-uni-view",{staticClass:"goodsName"},[t._v(t._s(e.name))]),i("v-uni-view",{staticClass:"label"},[t._v(t._s(e.goods_category_name))]),i("v-uni-view",{staticClass:"creator"},[t._v(t._s(e.creator))]),i("v-uni-view",{staticClass:"goodsPrice"},[t._v("当前 ¥"),i("v-uni-text",{staticStyle:{"font-size":"36rpx"}},[t._v(t._s(e.price))])],1)],1)],1)})),1),t.GoodsList.length?i("uni-load-more",{attrs:{status:t.status}}):t._e(),i("uni-footer",{attrs:{currentTab:"1"}})],1)},s=[]},"2db7":function(t,e,o){"use strict";o.r(e);var i=o("e347"),a=o.n(i);for(var s in i)"default"!==s&&function(t){o.d(e,t,(function(){return i[t]}))}(s);e["default"]=a.a},7953:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAAC5klEQVRYhe2YW4hNURjHfzNz0DlDDi8oxEi5zQONpqRMI2U8yOsgL3iRvPDiMg9ySbk8elJ44kGU28MQpVxeJGOkEHJ5QXLJZdxG3/FfWe219+x9skcezv/l7H3Wt77zW99Z6/u+vesYfDUCq4A5wEfgJHAl6VcNqB5oAybrOos+A9eARym2BrMVmBT5/jhwOrAW0GpgYTCSrh9AF/AkwdJgtmih/QKYCszQ+DHgTHRSAZin68fA+8BtvJqAEUBrAlApAnMU6AaGAJuAZqBTY2ejQEVd2397M3Adr81yWowZTYIxfQP2eVDLZXPOTc66Z7KqJNgm/dARD8bJQfXofgWwZDCAioKZ4sFcCKx+y6D2e1ArgY68gdZ7MIcHgIlC3fagWvMCGg/M1rXtmYuBRbwM6gDQqxPfmRfQcH1adO4GowPLoB44P3kBWcp4q1XaCRsXWCSrHVim0Vt5AfUBB4GvwChl57GBVah2JWZbyFM7CHlu6l7tB4MaDWwDxgRWf9TmwTwDdlmtyzsP9ejkOKiuBKgFwFrBPBfMB5Sp+zUwQUUzixpl8zPG9o6gNnqR2gG8TIDZ6Zcs+3IPMDFwm02HgEsJls2CGgq8VhSmCcb+mReCeedPatAJmeWtOous0t8ATijCcbKIPFQBtkK8CJirIMTCuAg5larI3H3KH1k0E9ggKBSAvUoTgf5Fx4hgDOyTTmPc3vs/VaemqUP9S9aI2Wm8qtX6slO7WO1H1r//C3DdFVkDWAfMD8zSZWHf7tUh0xpl32plB2O31cGCdr7pHvAmoyPbD2WgJQLkfN0HXgWz4jVd+arFAQ2T2fkqW9iyN9fJ3XfrqSSLXAKtzM27dPy1akBpqgGlqQaUphpQmgqqSfV6VdKQYu80Up/RNsLd2xPs92BWvMr+3IIaJnOwNNZ8YNlcX3Zvtcm6h8qzehWqvNaxiFhxNCBbdTXtx+WYF07my1oPezbL6svaD3vFdwro/wUFq5u6suGjBAAAAABJRU5ErkJggg=="},"80a6":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAkCAYAAADhAJiYAAABoklEQVRYhe2YTSsFURjHf/e6XpOS4gNYUEREyobsFBv5AEIWfAGfx9qCYs/CgrzndUFZeVmQDaHQk+fWrTNzzzxz7+gu5ldT05n/mfk/5zzPOc3JAE3ALNAD1BGNH+AJWAV2IvaJRBWwBAwCOUM/CaQRGACOgRdHEZOcjoywDxwYDM1pQN3AraMowVCN3t8A244inBk1VBuikPcOG9PgwjJNViaAaWOf1yQNXWtuWUboKElD51owJrIJGopFashHashHxRmSsn8H6oHeglXbR7Zg73vzaM2GdoFRoFMvC1/AYTkNZXRUpnSEoq6qwiOwBlw5T0o0lCRtmg5RkK3jPklDY8C801qcu4qrsv+YMstu/1CRSb2gZR8HKftlScZyGZJ1aEjvJdJLRxGM5N4kUA30A5uBqpiG8mV5Aqw7inDGdbobQhUxSDdXH6khH6khH0n+l3UBi8atYy9JQx1As9NanL4kDW0Az3EOGz51xW0HRhxZMBk9+RA+AhV/791yWj2IoVM9eMpfFr6BM+tHiyFRyqFAK9BiSPL8kd6KBlQegF9Szz/HQGAOhwAAAABJRU5ErkJggg=="},"8db0":function(t,e,o){var i=o("24fb");e=i(!1),e.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 颜色变量 */\n/*  常用字体颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-textarea[data-v-12f0bed0]{background-color:#f8f8f8;width:%?650?%;height:%?130?%;display:block;position:relative;font-size:%?28?%;line-height:normal;white-space:pre-wrap;word-break:break-all;padding:%?20?%;color:#777;border-radius:%?10?%}[data-v-12f0bed0] uni-swiper .uni-swiper-wrapper{overflow:inherit}[data-v-12f0bed0] uni-swiper .uni-swiper-dots-horizontal{bottom:%?-20?%!important}[data-v-12f0bed0] uni-swiper .uni-swiper-dot-active{width:%?30?%;height:%?12?%;background:#00d18b;border-radius:%?6?%}.status_bar[data-v-12f0bed0]{width:calc(100% - %?40?%);padding:0 %?20?%;height:%?80?%;padding-top:0}.status_bar .search[data-v-12f0bed0]{width:calc(100%);height:%?60?%;border-radius:%?10?%;box-shadow:%?0?% %?0?% %?15?% %?6?% rgba(52,52,52,.1);display:flex}.status_bar .search uni-input[data-v-12f0bed0]{width:calc(100% - %?140?%);height:%?60?%;font-size:%?26?%;padding:0 %?20?%;border-radius:%?5?%}.status_bar .search .search-btn[data-v-12f0bed0]{width:%?99?%;height:%?60?%;border-left:%?1?% solid rgba(52,52,52,.1);font-size:%?28?%;color:#000;text-align:center;line-height:%?60?%}.scroll-view_H[data-v-12f0bed0]{white-space:nowrap;width:90%}.scroll-view-item_H[data-v-12f0bed0]{padding:0 %?10?%;margin-right:%?20?%;display:inline-block;font-size:%?30?%;color:#000;height:%?88?%;line-height:%?88?%;text-align:center}.content[data-v-12f0bed0]{padding-bottom:%?100?%}.content .banner[data-v-12f0bed0]{padding:0 0 %?20?% 0;width:calc(100%);height:%?320?%}.content .banner .uni-swiper-wrapper[data-v-12f0bed0]{z-index:10}.content .banner uni-image[data-v-12f0bed0]{width:100%;height:%?320?%}.content .tabBox[data-v-12f0bed0]{padding:0 %?28?%;height:%?88?%}.content .tabBox .tab[data-v-12f0bed0]{font-size:%?30?%;color:#000;height:%?88?%;text-align:center}.content .tabBox .tab.act[data-v-12f0bed0]{font-weight:600;color:#000}.content .tabBox .right[data-v-12f0bed0]{width:%?36?%;height:%?36?%;padding:%?10?%}.content .goodsList[data-v-12f0bed0]{padding:0 %?28?%;display:flex;flex-wrap:wrap;justify-content:space-between}.content .goodsList .goodsItem[data-v-12f0bed0]{width:%?335?%;margin-bottom:%?25?%;background:#fff;border-radius:%?20?%}.content .goodsList .goodsItem .goodsImg[data-v-12f0bed0]{width:%?330?%;height:%?300?%;border-radius:%?20?%}.content .goodsList .goodsItem .goodsinfo[data-v-12f0bed0]{padding:%?20?% %?20?%}.content .goodsList .goodsItem .goodsinfo .label[data-v-12f0bed0]{height:%?40?%;line-height:%?40?%;text-align:center;font-size:%?28?%;padding:0 %?10?%;background:#ffe9b5;border-radius:%?6?%;color:#1e1817}.content .goodsList .goodsItem .goodsinfo .goodsName[data-v-12f0bed0]{flex:1;font-size:%?28?%;font-weight:500;color:#000;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.content .goodsList .goodsItem .goodsinfo .creator[data-v-12f0bed0]{width:40%;color:#fff;font-size:%?20?%;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}.content .goodsList .goodsItem .goodsinfo .goodsPrice[data-v-12f0bed0]{color:#000;font-size:%?28?%;font-weight:500}.content .goodsList1[data-v-12f0bed0]{padding:0 %?30?%}.content .goodsList1 .goodsItem[data-v-12f0bed0]{margin-bottom:%?20?%}.content .goodsList1 .goodsItem .goodsImg[data-v-12f0bed0]{width:%?300?%;height:%?300?%;border-radius:%?20?%;margin-right:%?25?%}.content .goodsList1 .goodsItem .goodsinfo[data-v-12f0bed0]{padding:%?10?% 0;flex:1}.content .goodsList1 .goodsItem .goodsinfo .label[data-v-12f0bed0]{width:%?100?%;height:%?40?%;line-height:%?40?%;text-align:center;font-size:%?22?%;padding:0 %?15?%;background:#f1e2bc;border-radius:%?6?%;color:#1e1817;margin:%?20?% 0}.content .goodsList1 .goodsItem .goodsinfo .goodsName[data-v-12f0bed0]{font-size:%?32?%;font-weight:500;color:#000;line-height:%?40?%;height:%?80?%;overflow:hidden;word-break:break-all;text-overflow:ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2}.content .goodsList1 .goodsItem .goodsinfo .creator[data-v-12f0bed0]{color:#fff;font-size:%?20?%;margin-bottom:%?15?%}.content .goodsList1 .goodsItem .goodsinfo .goodsPrice[data-v-12f0bed0]{color:#000;font-size:%?24?%;font-weight:500}',""]),t.exports=e},9275:function(t,e,o){"use strict";var i=o("9d10"),a=o.n(i);a.a},"9d10":function(t,e,o){var i=o("8db0");"string"===typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);var a=o("4f06").default;a("f79e4ed8",i,!0,{sourceMap:!1,shadowMode:!1})},e347:function(t,e,o){"use strict";o("99af"),o("ac1f"),o("841c"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var i={data:function(){return{gid:"",search:"",banner:[],GoodsList:[],CategoryList:[{id:0,name:"全部"}],status:"more",pagesize:15,page:1,flag:!1,block:!0,scrollTopt:0}},onLoad:function(t){this.Reset()},onPageScroll:function(t){this.scrollTopt=t.scrollTop,this.scrollTopt},onPullDownRefresh:function(){this.Reset()},onReachBottom:function(){this.flag||(this.status="loading",this.page++,this.getGoodsList())},methods:{searchClick:function(){this.gid="",this.flag=!1,this.page=1,this.GoodsList=[],this.getGoodsList()},Reset:function(){this.flag=!1,this.page=1,this.GoodsList=[],this.CategoryList=[{id:0,name:"全部"}],this.getBanner(),this.getCategoryList()},reload:function(t){this.gid!=t&&(this.gid=t,this.flag=!1,this.page=1,this.GoodsList=[],this.getGoodsList())},getCategoryList:function(){var t=this;this.$http.get("goods/goodsCategoryList").then((function(e){1==e.code&&(t.CategoryList=t.CategoryList.concat(e.data),t.gid=0,t.getGoodsList())}))},getGoodsList:function(){var t=this,e={goods_category_id:this.gid,page:this.page,pagesize:this.pagesize};this.search&&(e.search=this.search),this.$http.get("order/memberGoodsList",e).then((function(e){uni.stopPullDownRefresh(),1==e.code&&0!=e.data.data.length?(t.GoodsList=t.GoodsList.concat(e.data.data),e.data.data.length<t.pagesize&&(t.flag=!0,t.status="noMore")):(t.flag=!0,t.status="noMore")}))},getBanner:function(){var t=this;this.$http.get("index/bannerList",{type:"2"}).then((function(e){uni.stopPullDownRefresh(),1==e.code&&(t.banner=e.data)}))}}};e.default=i},fe19:function(t,e,o){"use strict";o.r(e);var i=o("2d52"),a=o("2db7");for(var s in a)"default"!==s&&function(t){o.d(e,t,(function(){return a[t]}))}(s);o("9275");var n,d=o("f0c5"),r=Object(d["a"])(a["default"],i["b"],i["c"],!1,null,"12f0bed0",null,!1,i["a"],n);e["default"]=r.exports}}]);