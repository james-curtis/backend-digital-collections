(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-my-art"],{"029e":function(t,n,o){"use strict";var e=o("7ed4"),i=o.n(e);i.a},1205:function(t,n,o){var e=o("24fb");n=e(!1),n.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 颜色变量 */\n/*  常用字体颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-textarea[data-v-784933e7]{background-color:#f8f8f8;width:%?650?%;height:%?130?%;display:block;position:relative;font-size:%?28?%;line-height:normal;white-space:pre-wrap;word-break:break-all;padding:%?20?%;color:#777;border-radius:%?10?%}.content[data-v-784933e7]{position:relative}.content .goodsList[data-v-784933e7]{padding:0 %?30?%;display:flex;flex-wrap:wrap;justify-content:space-between}.content .goodsList .goodsItem[data-v-784933e7]{width:%?335?%;border-radius:%?20?%;margin-bottom:%?20?%}.content .goodsList .goodsItem .goodsImg[data-v-784933e7]{width:%?335?%;height:%?335?%;border-radius:%?20?%}.content .goodsList .goodsItem .goodsinfo[data-v-784933e7]{padding:%?20?%}.content .goodsList .goodsItem .goodsinfo .goodsName[data-v-784933e7]{font-size:%?26?%;font-weight:500;color:#000;overflow:hidden;word-break:break-all;text-overflow:ellipsis;display:-webkit-box;-webkit-box-orient:vertical;-webkit-line-clamp:2}',""]),t.exports=n},"15d1":function(t,n,o){"use strict";var e;o.d(n,"b",(function(){return i})),o.d(n,"c",(function(){return s})),o.d(n,"a",(function(){return e}));var i=function(){var t=this,n=t.$createElement,o=t._self._c||n;return o("v-uni-view",{staticClass:"content container"},[o("v-uni-view",{staticClass:"goodsList"},t._l(t.collectionList,(function(n,e){return o("v-uni-view",{key:e,staticClass:"goodsItem",on:{click:function(o){arguments[0]=o=t.$handleEvent(o),t.goDetail(n.id)}}},[o("v-uni-image",{staticClass:"goodsImg",attrs:{src:n.goods_image,mode:"aspectFill"}}),o("v-uni-view",{staticClass:"goodsinfo"},[o("v-uni-view",{staticClass:"goodsName"},[t._v(t._s(n.goods_name))])],1)],1)})),1)],1)},s=[]},"3d97":function(t,n,o){"use strict";o.r(n);var e=o("548b"),i=o.n(e);for(var s in e)"default"!==s&&function(t){o.d(n,t,(function(){return e[t]}))}(s);n["default"]=i.a},"548b":function(t,n,o){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var e={data:function(){return{showType:"1",id:0,collectionList:{}}},onLoad:function(t){this.id=t.id,this.getList()},methods:{getList:function(){var t=this,n={id:this.id,status:this.showType};this.$http.post("index/collectionList",n).then((function(n){t.collectionList=n}))},goDetail:function(t){uni.navigateTo({url:"../user/myArtDetail?goodsId="+t})}}};n.default=e},"7ed4":function(t,n,o){var e=o("1205");"string"===typeof e&&(e=[[t.i,e,""]]),e.locals&&(t.exports=e.locals);var i=o("4f06").default;i("1a8ad052",e,!0,{sourceMap:!1,shadowMode:!1})},c581:function(t,n,o){"use strict";o.r(n);var e=o("15d1"),i=o("3d97");for(var s in i)"default"!==s&&function(t){o.d(n,t,(function(){return i[t]}))}(s);o("029e");var a,d=o("f0c5"),r=Object(d["a"])(i["default"],e["b"],e["c"],!1,null,"784933e7",null,!1,e["a"],a);n["default"]=r.exports}}]);