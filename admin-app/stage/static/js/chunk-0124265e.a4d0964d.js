(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-0124265e"],{"7b79":function(t,e,a){"use strict";a.r(e);var r=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"app-container"},[a("el-form",{staticClass:"query-form",attrs:{inline:!0,model:t.query,size:"mini"}},[a("el-form-item",{staticClass:"query-form-item",attrs:{label:"用户名称"}},[a("el-input",{attrs:{placeholder:"用户名称"},model:{value:t.query.NickName,callback:function(e){t.$set(t.query,"NickName",e)},expression:"query.NickName"}})],1),t._v(" "),a("el-form-item",{staticClass:"query-form-item",attrs:{label:"用户电话"}},[a("el-input",{attrs:{placeholder:"用户电话"},model:{value:t.query.Phone,callback:function(e){t.$set(t.query,"Phone",e)},expression:"query.Phone"}})],1),t._v(" "),a("el-form-item",{staticClass:"query-form-item",attrs:{label:"反馈标题"}},[a("el-input",{attrs:{placeholder:"反馈标题"},model:{value:t.query.title,callback:function(e){t.$set(t.query,"title",e)},expression:"query.title"}})],1),t._v(" "),a("el-form-item",{staticClass:"query-form-item",attrs:{label:"是否处理"}},[a("el-select",{attrs:{placeholder:"是否处理"},model:{value:t.query.is_hand,callback:function(e){t.$set(t.query,"is_hand",e)},expression:"query.is_hand"}},[a("el-option",{attrs:{label:"全部",value:""}}),t._v(" "),a("el-option",{attrs:{label:"已处理",value:"1"}}),t._v(" "),a("el-option",{attrs:{label:"未处理",value:"0"}})],1)],1),t._v(" "),a("el-form-item",[a("el-button-group",[a("el-button",{staticStyle:{height:"28px"},attrs:{type:"primary",icon:"el-icon-refresh"},on:{click:t.onReset}}),t._v(" "),a("el-button",{attrs:{type:"primary",icon:"search"},on:{click:t.onSubmit}},[t._v("查询")])],1)],1)],1),t._v(" "),a("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:t.list,border:""}},[a("el-table-column",{attrs:{label:"会员名称/手机号",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n        "+t._s(e.row.NickName)+"\n        "),a("br"),t._v("\n        "+t._s(e.row.Phone)+"\n      ")]}}])}),t._v(" "),a("el-table-column",{attrs:{align:"center",label:"反馈标题",prop:"title"}}),t._v(" "),a("el-table-column",{attrs:{label:"是否回复",align:"center"},scopedSlots:t._u([{key:"default",fn:function(e){return[0==e.row.is_hand?a("span",{staticStyle:{color:"red"}},[t._v("未回复")]):t._e(),t._v(" "),1==e.row.is_hand?a("span",{staticStyle:{color:"green"}},[t._v("已回复")]):t._e()]}}])}),t._v(" "),a("el-table-column",{attrs:{align:"center",label:"创建时间",prop:"create_time"}}),t._v(" "),a("el-table-column",{attrs:{align:"center",label:"更新",prop:"update_time"}}),t._v(" "),a("el-table-column",{attrs:{align:"center",label:"操作"},scopedSlots:t._u([{key:"default",fn:function(e){return[0==e.row.is_hand?a("el-button",{attrs:{type:"primary",size:"small"},nativeOn:{click:function(a){return t.handleForm(e.$index,e.row)}}},[t._v("回复\n        ")]):a("el-button",{attrs:{type:"primary",size:"small"},nativeOn:{click:function(a){return t.handleForm(e.$index,e.row)}}},[t._v("详情\n        ")])]}}])})],1),t._v(" "),a("el-pagination",{attrs:{"page-size":t.query.limit,layout:"prev, pager, next",total:t.total},on:{"current-change":t.handleCurrentChange}}),t._v(" "),a("el-dialog",{attrs:{title:t.formMap[t.formName],visible:t.formVisible,"before-close":t.hideForm,width:"50%",top:"15vh"},on:{"update:visible":function(e){t.formVisible=e}}},[a("el-form",{ref:"dataForm",attrs:{model:t.formData}},[a("el-form-item",{attrs:{label:"会员名称",prop:"NickName"}},[a("el-input",{attrs:{disabled:!0,"auto-complete":"off"},model:{value:t.formData.NickName,callback:function(e){t.$set(t.formData,"NickName",e)},expression:"formData.NickName"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"会员电话",prop:"Phone"}},[a("el-input",{attrs:{disabled:!0,"auto-complete":"off"},model:{value:t.formData.Phone,callback:function(e){t.$set(t.formData,"Phone",e)},expression:"formData.Phone"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"反馈标题",prop:"title"}},[a("el-input",{attrs:{type:"textarea",autosize:"",disabled:!0,"auto-complete":"off"},model:{value:t.formData.title,callback:function(e){t.$set(t.formData,"title",e)},expression:"formData.title"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"反馈内容",prop:"details"}},[a("el-input",{attrs:{type:"textarea",autosize:"",disabled:!0,"auto-complete":"off"},model:{value:t.formData.details,callback:function(e){t.$set(t.formData,"details",e)},expression:"formData.details"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"创建时间",prop:"nickname"}},[a("el-input",{attrs:{disabled:!0,"auto-complete":"off"},model:{value:t.formData.create_time,callback:function(e){t.$set(t.formData,"create_time",e)},expression:"formData.create_time"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"更新时间",prop:"nickname"}},[a("el-input",{attrs:{disabled:!0,"auto-complete":"off"},model:{value:t.formData.update_time,callback:function(e){t.$set(t.formData,"update_time",e)},expression:"formData.update_time"}})],1),t._v(" "),a("el-form-item",{attrs:{label:"是否回复",prop:"is_hand"}},[a("el-radio-group",{model:{value:t.formData.is_hand,callback:function(e){t.$set(t.formData,"is_hand",e)},expression:"formData.is_hand"}},[a("el-radio",{attrs:{disabled:"",label:0}},[t._v("未回复")]),t._v(" "),a("el-radio",{attrs:{disabled:"",label:1}},[t._v("已回复")])],1)],1),t._v(" "),a("el-form-item",{attrs:{label:"后台回复",prop:"reply"}},[a("el-input",{attrs:{type:"textarea",autosize:"",placeholder:"请输入回复内容","auto-complete":"off"},model:{value:t.formData.reply,callback:function(e){t.$set(t.formData,"reply",e)},expression:"formData.reply"}})],1)],1),t._v(" "),a("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[a("el-button",{nativeOn:{click:function(e){return t.hideForm(e)}}},[t._v("取消")]),t._v(" "),0==t.formData.is_hand?a("el-button",{attrs:{type:"primary"},nativeOn:{click:function(e){return t.formSubmit()}}},[t._v("提交")]):t._e()],1)],1)],1)},n=[],o=a("da49"),i={id:"",NickeName:"",Phone:"",sapling_share_reward_id:"",reward:"",create_time:""},l={data:function(){return{query:{id:"",page:1,limit:10,is_hand:"0"},list:[],total:0,loading:!0,index:null,isEdit:null,formName:null,formMap:{add:"新增",edit:"回复"},formLoading:!1,formVisible:!1,formData:i,deleteLoading:!1,langList:[]}},mounted:function(){},created:function(){var t=this.$route.query;this.query=Object.assign(this.query,t),this.query.limit=parseInt(this.query.limit),this.getList()},methods:{onReset:function(){this.$router.push({path:""}),this.query={id:"",page:1,limit:10,is_hand:""},this.getList()},onSubmit:function(){this.query.page=1,this.$router.push({path:"",query:this.query}),this.getList()},handleCurrentChange:function(t){this.query.page=t,this.getList()},getList:function(){var t=this;this.loading=!0,Object(o["g"])(this.query).then((function(e){t.loading=!1,t.list=e.data.list||[],t.total=e.data.total||0})).catch((function(){t.loading=!1,t.list=[],t.total=0,t.roles=[]}))},resetForm:function(){this.$refs["dataForm"]&&(this.$refs["dataForm"].clearValidate(),this.$refs["dataForm"].resetFields())},hideForm:function(){return this.formVisible=!this.formVisible,this.$refs["dataForm"].resetFields(),this.index=null,!0},handleForm:function(t,e){this.formVisible=!0,this.formData=JSON.parse(JSON.stringify(i)),null!==e&&(this.formData=JSON.parse(JSON.stringify(e))),this.formName="add",this.isEdit=0,null!==t&&(this.isEdit=1,this.index=t,this.formName="edit")},formSubmit:function(){var t=this;this.$refs["dataForm"].validate((function(e){if(e){t.formLoading=!0;var a=Object.assign({},t.formData);Object(o["h"])({Id:t.formData.id,reply:t.formData.reply}).then((function(e){t.formLoading=!1,t.$message.success("操作成功"),t.formVisible=!1;var r=Object.assign(a,e.data);t.list.splice(t.index,1,r),t.resetForm(),t.getList()})).catch((function(){t.formLoading=!1}))}}))}}},u=l,s=a("2877"),c=Object(s["a"])(u,r,n,!1,null,null,null);e["default"]=c.exports},da49:function(t,e,a){"use strict";a.d(e,"j",(function(){return n})),a.d(e,"k",(function(){return o})),a.d(e,"i",(function(){return i})),a.d(e,"q",(function(){return l})),a.d(e,"w",(function(){return u})),a.d(e,"r",(function(){return s})),a.d(e,"x",(function(){return c})),a.d(e,"p",(function(){return d})),a.d(e,"v",(function(){return m})),a.d(e,"o",(function(){return f})),a.d(e,"u",(function(){return p})),a.d(e,"l",(function(){return b})),a.d(e,"n",(function(){return h})),a.d(e,"a",(function(){return v})),a.d(e,"b",(function(){return _})),a.d(e,"c",(function(){return g})),a.d(e,"d",(function(){return y})),a.d(e,"m",(function(){return N})),a.d(e,"e",(function(){return O})),a.d(e,"f",(function(){return D})),a.d(e,"z",(function(){return k})),a.d(e,"A",(function(){return q})),a.d(e,"y",(function(){return j})),a.d(e,"g",(function(){return x})),a.d(e,"h",(function(){return $})),a.d(e,"s",(function(){return w})),a.d(e,"t",(function(){return F}));var r=a("b775");function n(t){return Object(r["a"])({url:"/bannerNotice/bannerList",method:"get",params:t})}function o(t,e){var a="add"===e?"/bannerNotice/bannerAdd":"/bannerNotice/bannerUpdate";return Object(r["a"])({url:a,method:"post",data:t})}function i(t){return Object(r["a"])({url:"/bannerNotice/bannerDelete",method:"post",data:t})}function l(t){return Object(r["a"])({url:"/bannerNotice/noticeList",method:"get",params:t})}function u(t){return Object(r["a"])({url:"/bannerNotice/qa",method:"get",params:t})}function s(t){return Object(r["a"])({url:"/bannerNotice/noticeUpdate",method:"post",data:t})}function c(t){return Object(r["a"])({url:"/bannerNotice/qaUpdate",method:"post",data:t})}function d(t){return Object(r["a"])({url:"/bannerNotice/noticeDelete",method:"get",params:{id:t}})}function m(t){return Object(r["a"])({url:"/bannerNotice/qaDelete",method:"get",params:{id:t}})}function f(t){return Object(r["a"])({url:"/bannerNotice/noticeAdd",method:"post",data:t})}function p(t){return Object(r["a"])({url:"/bannerNotice/qaAdd",method:"post",data:t})}function b(t){return Object(r["a"])({url:"/bannerNotice/getNotice",method:"get",params:{id:t}})}function h(t){return Object(r["a"])({url:"/bannerNotice/getQa",method:"get",params:{id:t}})}function v(t){return Object(r["a"])({url:"/bannerNotice/MemberDoc",method:"get",params:{id:t}})}function _(t){return Object(r["a"])({url:"/bannerNotice/MemberDocEdit",method:"post",data:t})}function g(t){return Object(r["a"])({url:"/bannerNotice/notice",method:"get",params:t})}function y(t){return Object(r["a"])({url:"/bannerNotice/NoticeEdit",method:"post",data:t})}function N(t){return Object(r["a"])({url:"/bannerNotice/getNotices",method:"get",params:{id:t}})}function O(t){return Object(r["a"])({url:"/bannerNotice/NoticesAdd",method:"post",data:t})}function D(t){return Object(r["a"])({url:"/bannerNotice/NoticesDel",method:"get",params:{id:t}})}function k(t){return Object(r["a"])({url:"/bannerNotice/schoolList",method:"get",params:t})}function q(t,e){var a="add"===e?"/bannerNotice/schoolAdd":"/bannerNotice/schoolUpdate";return Object(r["a"])({url:a,method:"post",data:t})}function j(t){return Object(r["a"])({url:"/bannerNotice/schoolDelete",method:"post",data:t})}function x(t){return Object(r["a"])({url:"/bannerNotice/user_feedback",method:"get",params:t})}function $(t){return Object(r["a"])({url:"/bannerNotice/user_feedback_answer",method:"post",data:t})}function w(t){return Object(r["a"])({url:"/bannerNotice/pg_rule",method:"get",params:t})}function F(t){return Object(r["a"])({url:"/bannerNotice/pgRuleEdit",method:"post",data:t})}}}]);