(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["pages-login-privacyPolicy"],{"150d":function(t,n,e){var a=e("87e2");"string"===typeof a&&(a=[[t.i,a,""]]),a.locals&&(t.exports=a.locals);var i=e("4f06").default;i("763893a6",a,!0,{sourceMap:!1,shadowMode:!1})},"16aa":function(t,n,e){"use strict";var a;e.d(n,"b",(function(){return i})),e.d(n,"c",(function(){return o})),e.d(n,"a",(function(){return a}));var i=function(){var t=this,n=t.$createElement,e=t._self._c||n;return e("v-uni-view",{staticClass:"content"},[e("v-uni-view",{staticClass:"info",domProps:{innerHTML:t._s(t.list)}})],1)},o=[]},"275b":function(t,n,e){"use strict";e.r(n);var a=e("e4f1"),i=e.n(a);for(var o in a)"default"!==o&&function(t){e.d(n,t,(function(){return a[t]}))}(o);n["default"]=i.a},"43c5":function(t,n,e){"use strict";var a=e("150d"),i=e.n(a);i.a},"87e2":function(t,n,e){var a=e("24fb");n=a(!1),n.push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 颜色变量 */\n/*  常用字体颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */uni-textarea[data-v-0103acfc]{background-color:#f8f8f8;width:%?650?%;height:%?130?%;display:block;position:relative;font-size:%?28?%;line-height:normal;white-space:pre-wrap;word-break:break-all;padding:%?20?%;color:#777;border-radius:%?10?%}.info[data-v-0103acfc]{padding:%?30?%;color:#000}',""]),t.exports=n},d86f:function(t,n,e){"use strict";e.r(n);var a=e("16aa"),i=e("275b");for(var o in i)"default"!==o&&function(t){e.d(n,t,(function(){return i[t]}))}(o);e("43c5");var r,c=e("f0c5"),s=Object(c["a"])(i["default"],a["b"],a["c"],!1,null,"0103acfc",null,!1,a["a"],r);n["default"]=s.exports},e4f1:function(t,n,e){"use strict";Object.defineProperty(n,"__esModule",{value:!0}),n.default=void 0;var a={data:function(){return{list:"",type:""}},onLoad:function(t){this.type=t.type,1==t.type&&uni.setNavigationBarTitle({title:"用户协议"}),2==t.type&&uni.setNavigationBarTitle({title:"隐私条款"}),this.getData(t.type)},methods:{getData:function(t){var n=this;this.$http.post("login/config").then((function(t){1==t.code&&(1==n.type?n.list=t.data.users_content:2==n.type&&(n.list=t.data.conceal_content))}))}}};n.default=a}}]);