(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-9834025c"],{"09f4":function(e,t,n){"use strict";n.d(t,"a",(function(){return o})),Math.easeInOutQuad=function(e,t,n,r){return e/=r/2,e<1?n/2*e*e+t:(e--,-n/2*(e*(e-2)-1)+t)};var r=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(e){window.setTimeout(e,1e3/60)}}();function a(e){document.documentElement.scrollTop=e,document.body.parentNode.scrollTop=e,document.body.scrollTop=e}function i(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function o(e,t,n){var o=i(),l=e-o,c=20,u=0;t="undefined"===typeof t?500:t;var s=function e(){u+=c;var i=Math.easeInOutQuad(u,o,l,t);a(i),u<t?r(e):n&&"function"===typeof n&&n()};s()}},2465:function(e,t,n){"use strict";n.d(t,"h",(function(){return a})),n.d(t,"m",(function(){return i})),n.d(t,"l",(function(){return o})),n.d(t,"i",(function(){return l})),n.d(t,"k",(function(){return c})),n.d(t,"j",(function(){return u})),n.d(t,"f",(function(){return s})),n.d(t,"g",(function(){return d})),n.d(t,"d",(function(){return p})),n.d(t,"e",(function(){return m})),n.d(t,"b",(function(){return f})),n.d(t,"a",(function(){return g})),n.d(t,"c",(function(){return v}));var r=n("b775");function a(e){return Object(r["a"])({url:"/ctc/appeal",method:"get",params:e})}function i(e){return Object(r["a"])({url:"/ctc/appeal/handle",method:"post",data:e})}function o(e){return Object(r["a"])({url:"/ctc/trade",method:"get",params:e})}function l(e){return Object(r["a"])({url:"/ctc/order",method:"get",params:e})}function c(e){return Object(r["a"])({url:"/ctc/cancle",method:"post",data:e})}function u(e){return Object(r["a"])({url:"/ctc/order/stop",method:"post",data:e})}function s(e){return Object(r["a"])({url:"/ctc/trade_rule",method:"get",params:{id:e}})}function d(e){return Object(r["a"])({url:"/ctc/trade_rule_edit",method:"get",params:e})}function p(e){return Object(r["a"])({url:"/ctc/trade_guidance",method:"get",params:{id:e}})}function m(e){return Object(r["a"])({url:"/ctc/trade_guidance_edit",method:"get",params:e})}function f(e){return Object(r["a"])({url:"/ctc/forest_rule",method:"get",params:{id:e}})}function g(e){return Object(r["a"])({url:"/ctc/forest_rule/edit",method:"get",params:e})}function v(e){return Object(r["a"])({url:"/ctc/SettingByAmount",method:"post",data:e})}},4381:function(e,t,n){"use strict";n("6762"),n("2fdb");var r=n("4360"),a={inserted:function(e,t,n){var a=t.value,i=r["a"].getters&&r["a"].getters.roles;if(!(a&&a instanceof Array&&a.length>0))throw new Error("need roles! Like v-permission=\"['admin','editor']\"");var o=a;if("*"!=i){var l=i.some((function(e){return o.includes(e)}));l||e.parentNode&&e.parentNode.removeChild(e)}}},i=function(e){e.directive("permission",a)};window.Vue&&(window["permission"]=a,Vue.use(i)),a.install=i;t["a"]=a},5792:function(e,t,n){"use strict";var r=n("fedf"),a=n.n(r);a.a},6724:function(e,t,n){"use strict";n("8d41");var r="@@wavesContext";function a(e,t){function n(n){var r=Object.assign({},t.value),a=Object.assign({ele:e,type:"hit",color:"rgba(0, 0, 0, 0.15)"},r),i=a.ele;if(i){i.style.position="relative",i.style.overflow="hidden";var o=i.getBoundingClientRect(),l=i.querySelector(".waves-ripple");switch(l?l.className="waves-ripple":(l=document.createElement("span"),l.className="waves-ripple",l.style.height=l.style.width=Math.max(o.width,o.height)+"px",i.appendChild(l)),a.type){case"center":l.style.top=o.height/2-l.offsetHeight/2+"px",l.style.left=o.width/2-l.offsetWidth/2+"px";break;default:l.style.top=(n.pageY-o.top-l.offsetHeight/2-document.documentElement.scrollTop||document.body.scrollTop)+"px",l.style.left=(n.pageX-o.left-l.offsetWidth/2-document.documentElement.scrollLeft||document.body.scrollLeft)+"px"}return l.style.backgroundColor=a.color,l.className="waves-ripple z-active",!1}}return e[r]?e[r].removeHandle=n:e[r]={removeHandle:n},n}var i={bind:function(e,t){e.addEventListener("click",a(e,t),!1)},update:function(e,t){e.removeEventListener("click",e[r].removeHandle,!1),e.addEventListener("click",a(e,t),!1)},unbind:function(e){e.removeEventListener("click",e[r].removeHandle,!1),e[r]=null,delete e[r]}},o=function(e){e.directive("waves",i)};window.Vue&&(window.waves=i,Vue.use(o)),i.install=o;t["a"]=i},"8d41":function(e,t,n){},"97d4":function(e,t,n){"use strict";n.r(t);var r=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container"},[n("div",{staticClass:"filter-container"},[n("el-input",{staticClass:"filter-item",staticStyle:{width:"200px"},attrs:{placeholder:"订单号",clearable:""},model:{value:e.listQuery.OrderSn,callback:function(t){e.$set(e.listQuery,"OrderSn",t)},expression:"listQuery.OrderSn"}}),e._v(" "),n("el-button",{directives:[{name:"waves",rawName:"v-waves"}],staticClass:"filter-item",attrs:{type:"primary",icon:"el-icon-search"},on:{click:e.filter}},[e._v("搜索")])],1),e._v(" "),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.listLoading,expression:"listLoading"}],key:e.tableKey,staticStyle:{width:"100%"},attrs:{data:e.list,border:"",fit:"","highlight-current-row":""}},[n("el-table-column",{attrs:{prop:"Id",align:"center",label:"ID"}}),e._v(" "),n("el-table-column",{attrs:{prop:"OrderSn",align:"center",label:"订单编号",width:"120px;"}}),e._v(" "),n("el-table-column",{attrs:{prop:"CoinName",align:"center",label:"币种",width:"65px;"}}),e._v(" "),n("el-table-column",{attrs:{prop:"BuyMember",align:"center",label:"买家昵称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"SellMember",align:"center",label:"卖家昵称"}}),e._v(" "),n("el-table-column",{attrs:{prop:"SumPrice",align:"center",label:"交易金额"}}),e._v(" "),n("el-table-column",{attrs:{prop:"Number",align:"center",label:"交易数量"}}),e._v(" "),n("el-table-column",{attrs:{prop:"Price",align:"center",label:"交易单价"}}),e._v(" "),n("el-table-column",{attrs:{prop:"AppealName",align:"center",label:"申诉人"}}),e._v(" "),n("el-table-column",{attrs:{prop:"Reason",align:"center",label:"申诉原因"}}),e._v(" "),n("el-table-column",{attrs:{prop:"Content",align:"center",label:"申诉内容"}}),e._v(" "),n("el-table-column",{attrs:{label:"图片",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return e._l(t.row.ImgsUrl,(function(e,t){return n("img",{directives:[{name:"image-preview",rawName:"v-image-preview"}],key:t,staticStyle:{width:"50px",height:"50px"},attrs:{src:e}})}))}}])}),e._v(" "),n("el-table-column",{attrs:{label:"下单时间",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){var n=t.row;return[e._v("\n        "+e._s(e._date(n.AddTime))+"\n      ")]}}])}),e._v(" "),n("el-table-column",{attrs:{label:"操作",width:"200",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[0==t.row.Result?n("div",[n("span",{directives:[{name:"permission",rawName:"v-permission",value:["/ctc/appeal/handle"],expression:"['/ctc/appeal/handle']"}]},[n("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(n){return e.Handle(t.row.Id,1)}}},[e._v("已付款")])],1),e._v(" "),n("span",{directives:[{name:"permission",rawName:"v-permission",value:["/ctc/appeal/handle"],expression:"['/ctc/appeal/handle']"}]},[n("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(n){return e.Handle(t.row.Id,2)}}},[e._v("未付款")])],1)]):e._e()]}}])})],1),e._v(" "),n("pagination",{directives:[{name:"show",rawName:"v-show",value:e.total>0,expression:"total > 0"}],attrs:{total:e.total,page:e.listQuery.page,limit:e.listQuery.limit},on:{"update:page":function(t){return e.$set(e.listQuery,"page",t)},"update:limit":function(t){return e.$set(e.listQuery,"limit",t)},pagination:e.getList}})],1)},a=[],i=n("2465"),o=n("6724"),l=n("333d"),c=n("4381"),u=n("de81"),s={components:{Pagination:l["a"]},directives:{waves:o["a"],permission:c["a"]},data:function(){return{tableKey:0,list:null,total:0,listLoading:!0,listQuery:{page:1,limit:10}}},created:function(){this.getList()},methods:{Handle:function(e,t){var n=this;1===t?this.$confirm("确认把此订单设置为完成?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){Object(i["m"])({Id:e,Type:1}).then((function(e){n.$message({message:"订单已设置为已付款",type:"success"}),n.getList()}))})).catch((function(){})):this.$confirm("确认把此订单设置为待付款?","提示",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){Object(i["m"])({Id:e,Type:2}).then((function(e){n.$message({message:"订单已设置为待付款",type:"success"}),n.getList()}))})).catch((function(){}))},_date:function(e){return Object(u["b"])("Y-m-d H:i:s",e)},getList:function(){var e=this;this.listLoading=!0,Object(i["h"])(this.listQuery).then((function(t){e.list=t.data.list,e.total=t.data.total,setTimeout((function(){e.listLoading=!1}),300)}))},filter:function(){this.listQuery.page=1,this.getList()}}},d=s,p=(n("5792"),n("2877")),m=Object(p["a"])(d,r,a,!1,null,"16dc53b6",null);t["default"]=m.exports},de81:function(e,t,n){"use strict";n.d(t,"c",(function(){return a})),n.d(t,"a",(function(){return i})),n.d(t,"b",(function(){return o}));n("3b2b"),n("a481"),n("6b54");var r=n("b775");function a(e){return Object(r["a"])({url:"qiniu-token",method:"get",params:e})}function i(e,t){return Object(r["a"])({url:e,method:"post",data:t})}function o(e,t){if(!t)return"";var n=new Date;n.setTime(1e3*t);var r=n.getMonth()+1,a=n.getDate(),i=n.getHours(),o=n.getMinutes(),l=n.getSeconds(),c={Y:n.getFullYear(),m:1==r.toString().length?"0"+r:r,d:1==a.toString().length?"0"+a:a,H:1==i.toString().length?"0"+i:i,i:1==o.toString().length?"0"+o:o,s:1==l.toString().length?"0"+l:l};for(var u in c)e=e.replace(new RegExp(u),c[u]);return e}},fedf:function(e,t,n){}}]);