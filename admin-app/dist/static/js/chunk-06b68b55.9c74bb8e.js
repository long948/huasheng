(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-06b68b55"],{"1a2c":function(t,e,n){},"333d":function(t,e,n){"use strict";var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"pagination-container",class:{hidden:t.hidden}},[n("el-pagination",t._b({attrs:{background:t.background,"current-page":t.currentPage,"page-size":t.pageSize,layout:t.layout,"page-sizes":t.pageSizes,total:t.total},on:{"update:currentPage":function(e){t.currentPage=e},"update:current-page":function(e){t.currentPage=e},"update:pageSize":function(e){t.pageSize=e},"update:page-size":function(e){t.pageSize=e},"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}},"el-pagination",t.$attrs,!1))],1)},i=[];n("c5f6");Math.easeInOutQuad=function(t,e,n,o){return t/=o/2,t<1?n/2*t*t+e:(t--,-n/2*(t*(t-2)-1)+e)};var a=function(){return window.requestAnimationFrame||window.webkitRequestAnimationFrame||window.mozRequestAnimationFrame||function(t){window.setTimeout(t,1e3/60)}}();function r(t){document.documentElement.scrollTop=t,document.body.parentNode.scrollTop=t,document.body.scrollTop=t}function l(){return document.documentElement.scrollTop||document.body.parentNode.scrollTop||document.body.scrollTop}function s(t,e,n){var o=l(),i=t-o,s=20,c=0;e="undefined"===typeof e?500:e;var u=function t(){c+=s;var l=Math.easeInOutQuad(c,o,i,e);r(l),c<e?a(t):n&&"function"===typeof n&&n()};u()}var c={name:"Pagination",props:{total:{required:!0,type:Number},page:{type:Number,default:1},limit:{type:Number,default:20},pageSizes:{type:Array,default:function(){return[10,20,30,50]}},layout:{type:String,default:"total, sizes, prev, pager, next, jumper"},background:{type:Boolean,default:!0},autoScroll:{type:Boolean,default:!0},hidden:{type:Boolean,default:!1}},computed:{currentPage:{get:function(){return this.page},set:function(t){this.$emit("update:page",t)}},pageSize:{get:function(){return this.limit},set:function(t){this.$emit("update:limit",t)}}},methods:{handleSizeChange:function(t){this.$emit("pagination",{page:this.currentPage,limit:t}),this.autoScroll&&s(0,800)},handleCurrentChange:function(t){this.$emit("pagination",{page:t,limit:this.pageSize}),this.autoScroll&&s(0,800)}}},u=c,d=(n("e498"),n("2877")),f=Object(d["a"])(u,o,i,!1,null,"6af373ef",null);e["a"]=f.exports},4124:function(t,e,n){"use strict";n.d(e,"n",(function(){return i})),n.d(e,"o",(function(){return a})),n.d(e,"s",(function(){return r})),n.d(e,"k",(function(){return l})),n.d(e,"a",(function(){return s})),n.d(e,"l",(function(){return c})),n.d(e,"b",(function(){return u})),n.d(e,"d",(function(){return d})),n.d(e,"h",(function(){return f})),n.d(e,"j",(function(){return m})),n.d(e,"e",(function(){return g})),n.d(e,"i",(function(){return p})),n.d(e,"f",(function(){return h})),n.d(e,"g",(function(){return _})),n.d(e,"q",(function(){return b})),n.d(e,"p",(function(){return v})),n.d(e,"r",(function(){return y})),n.d(e,"m",(function(){return w})),n.d(e,"t",(function(){return k}));var o=n("b775");function i(t){return Object(o["a"])({url:"/market/goods_list",method:"get",params:t})}function a(t,e){var n="add"===e?"/market/goods_add":"/market/goods_edit";return Object(o["a"])({url:n,method:"post",data:t})}function r(t){return Object(o["a"])({url:"/market/up_shelf",method:"post",data:t})}function l(t){return Object(o["a"])({url:"/market/down_shelf",method:"post",data:t})}function s(t){return Object(o["a"])({url:"/market/pg_goods",method:"get",params:t})}function c(t){return Object(o["a"])({url:"/market/getGoodsList",method:"get",params:t})}function u(t,e){var n="add"===e?"/market/pg_add":"/market/pg_edit";return Object(o["a"])({url:n,method:"post",data:t})}function d(t){return Object(o["a"])({url:"/market/activity",method:"get",params:t})}function f(t){return Object(o["a"])({url:"/market/checkGoods",method:"get",params:t})}function m(t){return Object(o["a"])({url:"/market/checkTime",method:"post",data:t})}function g(t){return Object(o["a"])({url:"/market/activity_add",method:"post",data:t})}function p(t){return Object(o["a"])({url:"/market/checkPgGoods",method:"post",data:t})}function h(t){return Object(o["a"])({url:"/market/activity_goods_add",method:"post",data:t})}function _(t){return Object(o["a"])({url:"/market/activity_goods_del",method:"post",data:t})}function b(t){return Object(o["a"])({url:"/market/team_found",method:"get",params:t})}function v(t){return Object(o["a"])({url:"/market/team_follow",method:"get",params:t})}function y(t){return Object(o["a"])({url:"/market/team_lottery",method:"get",params:t})}function w(t){return Object(o["a"])({url:"/market/order_setting",method:"get",params:t})}function k(t){return Object(o["a"])({url:"/market/order_setting_save",method:"post",data:t})}},7456:function(t,e,n){},"9c37":function(t,e,n){"use strict";n("1a2c")},c942:function(t,e,n){"use strict";n.r(e);var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"app-container"},[n("el-form",{staticClass:"query-form",attrs:{inline:!0,model:t.query,size:"mini"}},[n("el-form-item",{staticClass:"query-form-item",attrs:{label:"活动标题"}},[n("el-input",{attrs:{placeholder:"关键词"},model:{value:t.query.title,callback:function(e){t.$set(t.query,"title",e)},expression:"query.title"}})],1),t._v(" "),n("el-form-item",[n("el-button-group",[n("el-button",{staticStyle:{height:"28px"},attrs:{type:"primary",icon:"el-icon-refresh"},on:{click:t.onReset}}),t._v(" "),n("el-button",{attrs:{type:"primary",icon:"search"},on:{click:t.onSubmit}},[t._v("查询")]),t._v(" "),n("el-button",{attrs:{type:"primary"},on:{click:function(e){return t.add()}}},[t._v("新增")])],1)],1)],1),t._v(" "),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:t.list,border:""}},[n("el-table-column",{attrs:{align:"center",label:"活动标题",prop:"title"}}),t._v(" "),n("el-table-column",{attrs:{align:"center",label:"开始时间",prop:"begin_time"}}),t._v(" "),n("el-table-column",{attrs:{align:"center",label:"结束时间",prop:"end_time"}}),t._v(" "),n("el-table-column",{attrs:{align:"center",label:"状态",prop:"status"}}),t._v(" "),n("el-table-column",{attrs:{label:"状态",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[1==e.row.status?n("el-tag",{attrs:{type:"success"}},[t._v("进行中")]):t._e(),t._v(" "),2==e.row.status?n("el-tag",{attrs:{type:"warning"}},[t._v("未开始")]):t._e(),t._v(" "),3==e.row.status?n("el-tag",{attrs:{type:"info"}},[t._v("已结束")]):t._e()]}}])}),t._v(" "),n("el-table-column",{attrs:{align:"center",label:"操作"},scopedSlots:t._u([{key:"default",fn:function(e){return[n("el-button",{attrs:{type:"primary",size:"small"},nativeOn:{click:function(n){return t.handleForm(e.$index,e.row)}}},[t._v("编辑\n        ")])]}}])})],1),t._v(" "),n("pagination",{directives:[{name:"show",rawName:"v-show",value:t.total>0,expression:"total > 0"}],attrs:{total:t.total,page:t.query.page,limit:t.query.limit},on:{"update:page":function(e){return t.$set(t.query,"page",e)},"update:limit":function(e){return t.$set(t.query,"limit",e)},pagination:t.getList}}),t._v(" "),n("el-dialog",{attrs:{title:t.formMap[t.formName],visible:t.formVisible,"before-close":t.hideForm,width:"90%",top:"5vh"},on:{"update:visible":function(e){t.formVisible=e}}},[n("el-card",{staticClass:"filter-container",attrs:{shadow:"never"}},[n("el-form",{ref:"dataForm",attrs:{model:t.formData,"label-width":"120px"}},[n("el-form-item",{attrs:{label:"活动标题：",prop:"team_price"}},[n("el-input",{staticStyle:{width:"30%"},attrs:{placeholder:"请输入活动标题",type:"textarea",autosize:""},model:{value:t.formData.title,callback:function(e){t.$set(t.formData,"title",e)},expression:"formData.title"}})],1),t._v(" "),n("el-form-item",{attrs:{label:"活动开始时间",prop:"begin_time"}},[n("el-date-picker",{staticStyle:{width:"30%"},attrs:{type:"datetime",placeholder:"请选择活动开始时间","value-format":"yyyy-MM-dd HH:mm:ss","picker-options":t.startTime},on:{blur:function(e){return t.checkTime()}},model:{value:t.formData.begin_time,callback:function(e){t.$set(t.formData,"begin_time",e)},expression:"formData.begin_time"}})],1),t._v(" "),n("el-form-item",{attrs:{label:"活动结束时间",prop:"end_time"}},[n("el-date-picker",{staticStyle:{width:"30%"},attrs:{type:"datetime",placeholder:"请选择活动结束时间","value-format":"yyyy-MM-dd HH:mm:ss","picker-options":t.endTime},on:{blur:function(e){return t.checkTime()}},model:{value:t.formData.end_time,callback:function(e){t.$set(t.formData,"end_time",e)},expression:"formData.end_time"}})],1)],1)],1),t._v(" "),n("el-card",[n("span",[t._v("活动商品列表")]),t._v(" "),n("el-button",{staticStyle:{float:"right"},attrs:{type:"danger"},nativeOn:{click:function(e){return t.delGoods()}}},[t._v("删除")]),t._v(" "),n("el-button",{staticStyle:{float:"right","margin-bottom":"15px"},on:{click:function(e){t.innerVisible=!0}}},[t._v("添加商品")])],1),t._v(" "),n("el-card",{staticClass:"filter-container",attrs:{shadow:"never"}},[n("el-table",{ref:"delFormRef",staticStyle:{width:"100%"},attrs:{"header-cell-style":{background:"#eef1f6",color:"#909399"},data:t.pgGoods,border:"","row-key":t.handleReserve},on:{"selection-change":t.delSelectionChange}},[n("el-table-column",{attrs:{selectable:t.handleDisable,type:"selection","reserve-selection":"reserve-selection",width:"55"},on:{click:function(e){t.drawer=!0}}}),t._v(" "),n("el-table-column",{attrs:{type:"index”",label:"商品图",align:"center",width:"150"},scopedSlots:t._u([{key:"default",fn:function(t){return[n("img",{attrs:{src:t.row.original_img,width:"50px"}})]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品名称",align:"center","show-overflow-tooltip":!0},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.goods_name))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"市场价",width:"160",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.market_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"拼购价",width:"160",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.team_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"成团人数",width:"160",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.needer))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"中奖人数",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.stock_limit))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"未中返利",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.return_amount))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"支付币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.payCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"奖励币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.getCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品状态",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[0==e.row.goods_state?n("el-tag",{attrs:{type:"warning"}},[t._v("待上架")]):t._e(),t._v(" "),1==e.row.goods_state?n("el-tag",{attrs:{type:"success"}},[t._v("已上架")]):t._e(),t._v(" "),2==e.row.goods_state?n("el-tag",{attrs:{type:"danger"}},[t._v("已下架")]):t._e()]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"状态",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.is_found?n("span",{staticStyle:{color:"green"}},[t._v("正在拼团")]):t._e()]}}])})],1)],1),t._v(" "),n("el-dialog",{attrs:{width:"80%",title:"选择商品","before-close":t.hideForm1,visible:t.innerVisible,"append-to-body":""},on:{"update:visible":function(e){t.innerVisible=e}}},[n("el-card",{staticClass:"filter-container",attrs:{shadow:"never"}},[n("el-input",{staticStyle:{width:"250px","margin-bottom":"20px"},attrs:{clearable:"",size:"small",placeholder:"商品名称关键词"},model:{value:t.pgform.keyword,callback:function(e){t.$set(t.pgform,"keyword",e)},expression:"pgform.keyword"}},[n("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:function(e){return t.SearchpgGoods()}},slot:"append"})],1),t._v(" "),n("el-table",{ref:"editFormRef",staticStyle:{width:"100%"},attrs:{"header-cell-style":{background:"#eef1f6",color:"#909399"},data:t.goodsLists,border:"","row-key":t.handleReserve},on:{"selection-change":t.PgGoodsList}},[n("el-table-column",{attrs:{selectable:t.choice,type:"selection","reserve-selection":"reserve-selection",width:"55"},on:{click:function(e){t.drawer=!0}}}),t._v(" "),n("el-table-column",{attrs:{type:"index”",label:"商品图",align:"center",width:"150"},scopedSlots:t._u([{key:"default",fn:function(t){return[n("img",{attrs:{src:t.row.original_img,width:"50px"}})]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品名称",align:"center","show-overflow-tooltip":!0,width:"200"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.goods_name))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"市场价",width:"150",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.market_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"拼购价",width:"150",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.team_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"成团人数",width:"150",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.needer))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"中奖人数",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.stock_limit))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"未中返利",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.return_amount))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"支付币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.payCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"奖励币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.getCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品状态",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[0==e.row.goods_state?n("el-tag",{attrs:{type:"warning"}},[t._v("待上架")]):t._e(),t._v(" "),1==e.row.goods_state?n("el-tag",{attrs:{type:"success"}},[t._v("已上架")]):t._e(),t._v(" "),2==e.row.goods_state?n("el-tag",{attrs:{type:"danger"}},[t._v("已下架")]):t._e()]}}])})],1)],1),t._v(" "),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{nativeOn:{click:function(e){return t.hideForm1()}}},[t._v("取消")]),t._v(" "),n("el-button",{attrs:{type:"primary",loading:t.formLoading},nativeOn:{click:function(e){return t.addGoods()}}},[t._v("提交")])],1)],1)],1),t._v(" "),n("el-dialog",{attrs:{title:"添加拼购商品",visible:t.selectDialogVisible,"before-close":t.hideForm2,width:"70%",top:"5vh"},on:{"update:visible":function(e){t.selectDialogVisible=e}}},[n("el-card",{staticClass:"filter-container",attrs:{shadow:"never"}},[n("el-form",{ref:"dataForm",attrs:{model:t.form,"label-width":"120px"}},[n("el-form-item",{attrs:{label:"活动标题：",prop:"team_price"}},[n("el-input",{staticStyle:{width:"30%"},attrs:{placeholder:"请输入活动标题",type:"textarea",autosize:""},model:{value:t.form.title,callback:function(e){t.$set(t.form,"title",e)},expression:"form.title"}})],1),t._v(" "),n("el-form-item",{attrs:{label:"活动开始时间",prop:"begin_time"}},[n("el-date-picker",{staticStyle:{width:"30%"},attrs:{type:"datetime",placeholder:"请选择活动开始时间","value-format":"yyyy-MM-dd HH:mm:ss","picker-options":t.startTime},on:{blur:function(e){return t.checkTime()}},model:{value:t.form.begin_time,callback:function(e){t.$set(t.form,"begin_time",e)},expression:"form.begin_time"}})],1),t._v(" "),n("el-form-item",{attrs:{label:"活动结束时间",prop:"end_time"}},[n("el-date-picker",{staticStyle:{width:"30%"},attrs:{type:"datetime",placeholder:"请选择活动结束时间","value-format":"yyyy-MM-dd HH:mm:ss","picker-options":t.endTime},on:{blur:function(e){return t.checkTime()}},model:{value:t.form.end_time,callback:function(e){t.$set(t.form,"end_time",e)},expression:"form.end_time"}})],1)],1)],1),t._v(" "),n("br"),n("br"),t._v(" "),n("span",[t._v("选择活动商品")]),t._v(" "),n("el-card",{staticClass:"filter-container",attrs:{shadow:"never"}},[n("el-input",{staticStyle:{width:"250px","margin-bottom":"20px"},attrs:{clearable:"",size:"small",placeholder:"商品名称关键词"},model:{value:t.goodslist.keyword,callback:function(e){t.$set(t.goodslist,"keyword",e)},expression:"goodslist.keyword"}},[n("el-button",{attrs:{slot:"append",icon:"el-icon-search"},on:{click:function(e){return t.handleSelectSearch()}},slot:"append"})],1),t._v(" "),n("el-table",{ref:"multipleTable",staticStyle:{width:"100%"},attrs:{"header-cell-style":{background:"#eef1f6",color:"#909399"},data:t.goods,border:"","row-key":t.handleReserve},on:{"selection-change":t.handleSelectionChange}},[n("el-table-column",{attrs:{selectable:t.choice,type:"selection","reserve-selection":"reserve-selection",width:"55"},on:{click:function(e){t.drawer=!0}}}),t._v(" "),n("el-table-column",{attrs:{type:"index”",label:"商品图",align:"center",width:"150"},scopedSlots:t._u([{key:"default",fn:function(t){return[n("img",{attrs:{src:t.row.original_img,width:"50px"}})]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品名称",align:"center","show-overflow-tooltip":!0},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.goods_name))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"市场价",width:"160",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.market_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"拼购价",width:"150",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.team_price))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"成团人数",width:"150",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.needer))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"中奖人数",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.stock_limit))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"未中返利",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.return_amount))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"支付币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.payCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"奖励币种",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v(t._s(e.row.getCoin))]}}])}),t._v(" "),n("el-table-column",{attrs:{label:"商品状态",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[0==e.row.goods_state?n("el-tag",{attrs:{type:"warning"}},[t._v("待上架")]):t._e(),t._v(" "),1==e.row.goods_state?n("el-tag",{attrs:{type:"success"}},[t._v("已上架")]):t._e(),t._v(" "),2==e.row.goods_state?n("el-tag",{attrs:{type:"danger"}},[t._v("已下架")]):t._e()]}}])})],1),t._v(" "),n("div",{staticClass:"pagination-container"},[n("el-pagination",{attrs:{background:"",layout:"total, sizes,prev, pager, next,jumper","page-size":t.goodslist.limit,"page-sizes":[5,10,15],"current-page":t.goodslist.page,total:t.goodstotal},on:{"update:currentPage":function(e){return t.$set(t.goodslist,"page",e)},"update:current-page":function(e){return t.$set(t.goodslist,"page",e)},"size-change":t.handleDialogSizeChange,"current-change":t.getGoodsList}})],1)],1),t._v(" "),n("div",{attrs:{slot:"footer"},slot:"footer"},[n("el-button",{attrs:{size:"small"},on:{click:function(e){return t.hideForm2()}}},[t._v("取 消")]),t._v(" "),1===this.is_exist?n("el-button",{attrs:{size:"small",type:"primary"},on:{click:function(e){return t.handleSetting()}}},[t._v("确 定")]):t._e()],1)],1)],1)},i=[],a=n("4124"),r=n("333d"),l={activity_id:"",needer:"",coin_id:"",luck_coin_id:"",stock_limit:"",return_amount:"",team_price:""},s={components:{Pagination:r["a"]},data:function(){return{coinList:{},query:{id:"",page:1,limit:20},goodslist:{page:1,limit:5,keyword:""},form:{tableList:[],title:"",begin_time:"",end_time:""},pgform:{keyword:"",addList:"",page:1,limit:20},startTime:{disabledDate:function(t){return t.getTime()<Date.now()-864e5}},endTime:{disabledDate:function(t){return t.getTime()<Date.now()-864e5}},goodsLists:[],pgGoods:[],pgtotal:0,is_exist:"",goodstotal:0,list:[],goods:[],total:0,loading:!0,index:null,isEdit:null,formName:null,formMap:{add:"新增",edit:"编辑"},delForm:{id:"",list:""},formLoading:!1,formVisible:!1,formData:l,deleteLoading:!1,listLoading:!1,selectDialogVisible:!1,innerVisible:!1,langList:[],currentRow:null}},mounted:function(){},created:function(){var t=this.$route.query;this.query=Object.assign(this.query,t),this.query.limit=parseInt(this.query.limit),this.getList()},methods:{handleReserve:function(t){return t.activity_id},handleSelectionChange:function(t){this.selectIds=t.map((function(t){return t})),this.form.tableList=this.selectIds},handleDisable:function(t,e){return"1"!==t.is_found},choice:function(t){return 2!==t.goods_state&&0!==t.goods_state},delSelectionChange:function(t){this.selectIds=t.map((function(t){return t})),this.delForm.list=this.selectIds},handleSelectSearch:function(){this.getGoodsList()},SearchpgGoods:function(){this.getPgGoodsList(this.formData.id)},onReset:function(){this.$router.push({path:""}),this.query={id:"",page:1,limit:20},this.getList()},handleClose:function(){this.$refs.editFormRef.resetFields()},onSubmit:function(){this.query.page=1,this.$router.push({path:"",query:this.query}),this.getList()},handleCurrentChange:function(t){this.query.page=t,this.getList()},getList:function(){var t=this;this.loading=!0,Object(a["d"])(this.query).then((function(e){t.loading=!1,t.list=e.data.list||[],t.total=e.data.total||0})).catch((function(){t.loading=!1,t.list=[],t.total=0,t.roles=[]}))},resetForm:function(){this.$refs["dataForm"]&&(this.$refs["dataForm"].clearValidate(),this.$refs["dataForm"].resetFields())},hideForm:function(){return this.formVisible=!this.formVisible,this.$refs["dataForm"].resetFields(),this.index=null,!0},hideForm1:function(){return this.innerVisible=!this.innerVisible,this.$refs.editFormRef.clearSelection(),this.table=!1,!0},hideForm2:function(){return this.selectDialogVisible=!this.selectDialogVisible,this.toggleSelection(),!0},handleForm:function(t,e){this.formVisible=!0,this.getPgGoodsList(e.id),this.formData=JSON.parse(JSON.stringify(l)),null!==e&&(this.formData=JSON.parse(JSON.stringify(e))),this.formName="add",this.isEdit=0,null!==t&&(this.isEdit=1,this.index=t,this.formName="edit")},toggleSelection:function(t){this.$refs.multipleTable.clearSelection(),this.$refs.multipleTable.clearSelection(),this.table=!1},add:function(){this.selectDialogVisible=!0,this.getGoodsList()},getGoodsList:function(){var t=this;Object(a["h"])(this.goodslist).then((function(e){t.goods=e.data.list,t.goodstotal=e.data.total}))},handleDialogSizeChange:function(t){this.goodslist.page=1,this.goodslist.limit=t,this.getGoodsList()},getPgGoodsList:function(t){var e=this;Object(a["i"])({id:t,keyword:this.pgform.keyword}).then((function(t){e.pgGoods=t.data.list,e.pgtotal=t.data.total,e.goodsLists=t.data.goodsLists}))},formSubmit:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){t.formLoading=!0;var n=Object.assign({},t.formData);Object(a["b"])(n,t.formName).then((function(e){t.formLoading=!1,t.$message.success("操作成功"),t.formVisible=!1,n=Object.assign(n,e.data),t.list.splice(t.index,1,n),t.resetForm(),t.getList()})).catch((function(){t.formLoading=!1}))}}))},handleSetting:function(){var t=this;this.listLoading=!0,this.formName="add",Object(a["e"])(this.form,this.formName).then((function(e){2e4===e.code?(t.$message({type:"success",message:e.msg}),setTimeout((function(){t.goodslist.keyword="",t.form.tableList="",t.form.title="",t.form.begin_time="",t.form.end_time="",t.toggleSelection(),t.listLoading=!1})),t.selectDialogVisible=!1,t.getList()):t.$message({type:"error",message:e.msg})}))},checkTime:function(){var t=this;this.listLoading=!0,Object(a["j"])({starTime:this.form.begin_time,endTime:this.form.end_time}).then((function(e){if(t.is_exist=e.is_exist,2===t.is_exist)return t.$message.error("所选日期内已存在活动")}))},PgGoodsList:function(t){this.selectIds=t.map((function(t){return t})),this.pgform.addList=this.selectIds},addGoods:function(t){var e=this;if(this.pgform.addList.length<1)return this.$message.error("请选择商品");this.$confirm("确定将所选商品添加到该活动中?","提示",{type:"warning"}).then((function(){var t={id:e.formData.id,list:e.pgform.addList};Object(a["f"])(t).then((function(t){e.$message.success("操作成功"),e.innerVisible=!1,e.$refs.editFormRef.clearSelection(),e.table=!1,e.getPgGoodsList(e.formData.id)})).catch((function(){}))})).catch((function(){e.$message.info("取消")}))},delGoods:function(){var t=this;if(this.delForm.list.length<1)return this.$message.error("请选择商品");this.$confirm("确定将所选商品从该活动中删除?","提示",{type:"warning"}).then((function(){var e={id:t.formData.id,list:t.delForm.list};Object(a["g"])(e).then((function(e){t.$message.success("操作成功"),t.innerVisible=!1,t.$refs.delFormRef.clearSelection(),t.table=!1,t.getPgGoodsList(t.formData.id)})).catch((function(){}))})).catch((function(){t.$message.info("取消")}))}}},c=s,u=(n("9c37"),n("2877")),d=Object(u["a"])(c,o,i,!1,null,null,null);e["default"]=d.exports},e498:function(t,e,n){"use strict";n("7456")}}]);