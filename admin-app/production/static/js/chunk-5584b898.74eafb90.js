(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-5584b898"],{6103:function(e,t,n){"use strict";n.d(t,"e",(function(){return r})),n.d(t,"a",(function(){return a})),n.d(t,"b",(function(){return l})),n.d(t,"h",(function(){return o})),n.d(t,"g",(function(){return s})),n.d(t,"l",(function(){return u})),n.d(t,"i",(function(){return d})),n.d(t,"j",(function(){return m})),n.d(t,"k",(function(){return c})),n.d(t,"o",(function(){return f})),n.d(t,"f",(function(){return p})),n.d(t,"n",(function(){return v})),n.d(t,"m",(function(){return _})),n.d(t,"p",(function(){return h})),n.d(t,"q",(function(){return g})),n.d(t,"r",(function(){return b})),n.d(t,"c",(function(){return y})),n.d(t,"d",(function(){return O}));var i=n("b775");function r(e){return Object(i["a"])({url:"/miner/miner_level",method:"get",params:e})}function a(e){return Object(i["a"])({url:"/miner/SettingMinerSapling",method:"post",data:e})}function l(e){return Object(i["a"])({url:"/miner/miner_levelEdit",method:"post",data:e})}function o(e){return Object(i["a"])({url:"/miner/miner_saplingList",method:"get",params:e})}function s(e){return Object(i["a"])({url:"/miner/MinerSaplingEdit",method:"post",data:e})}function u(e){return Object(i["a"])({url:"/miner/miner_saplingType",method:"get",params:e})}function d(e){return Object(i["a"])({url:"/miner/sapling_package",method:"get",params:e})}function m(e){return Object(i["a"])({url:"/miner/sapling_packageDel",method:"post",data:e})}function c(e,t){var n="edit"===t?"/miner/sapling_packageEdit":"/miner/sapling_packageAdd";return Object(i["a"])({url:n,method:"post",data:e})}function f(e){return Object(i["a"])({url:"/miner/sapling_share_reward",method:"get",params:e})}function p(e,t){var n="edit"===t?"/miner/sapling_share_rewardEdit":"/miner/sapling_share_rewardAdd";return Object(i["a"])({url:n,method:"post",data:e})}function v(e){return Object(i["a"])({url:"/miner/sapling_share_rewardDel",method:"post",data:e})}function _(e){return Object(i["a"])({url:"/members/user_sapling_release",method:"get",params:e})}function h(e){return Object(i["a"])({url:"/members/user_sapling",method:"get",params:e})}function g(e){return Object(i["a"])({url:"/members/user_saplingEdit",method:"post",data:e})}function b(e){return Object(i["a"])({url:"/miner/user_sapling_receive",method:"get",params:e})}function y(e){return Object(i["a"])({url:"/miner/miner_dividend",method:"get",params:e})}function O(e,t){var n="edit"===t?"/miner/miner_dividendEdit":"/miner/miner_dividendAdd";return Object(i["a"])({url:n,method:"post",data:e})}},"62b9":function(e,t,n){"use strict";n.r(t);var i=function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"app-container"},[n("el-form",{staticClass:"query-form",attrs:{inline:!0,model:e.query,size:"mini"}},[n("el-form-item",[n("el-button-group",[n("el-button",{attrs:{type:"primary",icon:"el-icon-refresh"},on:{click:e.onReset}}),e._v(" "),n("el-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.handleForm(null,null)}}},[e._v("新增")])],1)],1)],1),e._v(" "),n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticStyle:{width:"100%"},attrs:{data:e.list,border:""}},[n("el-table-column",{attrs:{align:"center",label:"Id",prop:"id",width:"200px"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"分红总额",prop:"amount"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"等级一",prop:"level1"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"等级二",prop:"level2"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"等级三",prop:"level3"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"等级四",prop:"level4"}}),e._v(" "),n("el-table-column",{attrs:{label:"是否分红",align:"center"},scopedSlots:e._u([{key:"default",fn:function(t){return[0==t.row.is_dividend?n("span",{staticStyle:{color:"green"}},[e._v("否")]):1==t.row.is_dividend?n("span",{staticStyle:{color:"red"}},[e._v("是")]):e._e()]}}])}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"创建时间",prop:"create_time"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"开始分红时间",prop:"begin_dividend_time"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"更新时间",prop:"update_time"}}),e._v(" "),n("el-table-column",{attrs:{align:"center",label:"操作"},scopedSlots:e._u([{key:"default",fn:function(t){return[n("el-button",{attrs:{type:"text",size:"small"},nativeOn:{click:function(n){return e.handleForm(t.$index,t.row)}}},[e._v("编辑\n        ")])]}}])})],1),e._v(" "),n("el-pagination",{attrs:{"page-size":e.query.limit,layout:"prev, pager, next",total:e.total},on:{"current-change":e.handleCurrentChange}}),e._v(" "),n("el-dialog",{attrs:{title:e.formMap[e.formName],visible:e.formVisible,"before-close":e.hideForm,width:"50%",top:"15vh"},on:{"update:visible":function(t){e.formVisible=t}}},[n("el-form",{ref:"dataForm",attrs:{model:e.formData}},[n("el-form-item",{attrs:{label:"等级一：",prop:"level1"}},[n("el-input-number",{attrs:{"controls-position":"right",min:0,precision:4,step:.1,max:100},on:{change:e.handleChange},model:{value:e.formData.level1,callback:function(t){e.$set(e.formData,"level1",t)},expression:"formData.level1"}}),e._v("\n               \n        "),n("label",[e._v("等级二：")]),e._v(" "),n("el-input-number",{attrs:{"controls-position":"right",min:0,precision:4,step:.1,max:100},on:{change:e.handleChange},model:{value:e.formData.level2,callback:function(t){e.$set(e.formData,"level2",t)},expression:"formData.level2"}})],1),e._v(" "),n("el-form-item",{attrs:{label:"等级三：",prop:"level1"}},[n("el-input-number",{attrs:{"controls-position":"right",min:0,precision:4,step:.1,max:100},on:{change:e.handleChange},model:{value:e.formData.level3,callback:function(t){e.$set(e.formData,"level3",t)},expression:"formData.level3"}}),e._v("\n               \n        "),n("label",[e._v("等级四：")]),e._v(" "),n("el-input-number",{attrs:{"controls-position":"right",min:0,precision:4,step:.1,max:100},on:{change:e.handleChange},model:{value:e.formData.level4,callback:function(t){e.$set(e.formData,"level4",t)},expression:"formData.level4"}})],1),e._v(" "),n("el-form-item",{attrs:{label:"分红总额:",prop:"amount"}},[n("el-input",{staticStyle:{width:"61.5%"},attrs:{type:"number","auto-complete":"off"},model:{value:e.formData.amount,callback:function(t){e.$set(e.formData,"amount",t)},expression:"formData.amount"}})],1),e._v(" "),n("el-form-item",{attrs:{label:"分红开始时间",prop:"begin_dividend_time"}},[n("div",{staticClass:"block"},[n("el-date-picker",{attrs:{prop:"begin_dividend_time","value-format":"yyyy-MM-dd",type:"date",placeholder:"选择日期时间"},model:{value:e.formData.begin_dividend_time,callback:function(t){e.$set(e.formData,"begin_dividend_time",t)},expression:"formData.begin_dividend_time"}})],1)]),e._v(" "),n("el-form-item",{attrs:{prop:"task_image"}},e._l(e.formData.level_dividend,(function(t,i){return n("div",{key:i,staticStyle:{display:"inline-flex","margin-left":"2px",position:"relative"}},[n("b",{staticStyle:{color:"#FF0000"}},[e._v("等级"+e._s(i+1)+"：")]),e._v("\n           \n          "),e._v(" "),n("div",[n("span",[e._v("总额："+e._s(t.miner_level_amount))])]),e._v(" "),n("div",[n("span",[e._v("总人数："+e._s(t.miner_level_count))])]),e._v(" "),n("div",[n("span",[e._v("单个金额："+e._s(t.miner_level_user_amount))])]),e._v("\n           \n        ")])})),0),e._v(" "),n("el-alert",{attrs:{title:"注：分红开始时间为每周一次",type:"error"}})],1),e._v(" "),n("div",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{nativeOn:{click:function(t){return e.hideForm(t)}}},[e._v("取消")]),e._v(" "),1!=e.formData.is_dividend?n("el-button",{attrs:{type:"primary"},nativeOn:{click:function(t){return e.formSubmit()}}},[e._v("提交")]):e._e()],1)],1)],1)},r=[],a=n("6103"),l={id:"",user_id:"",child_user_id:"",computing_power:"",business_id:"",type:"",is_self:"",create_time:"",remarks:"",begin_time:"",NickName:"",ChildUser:""},o={data:function(){return{query:{id:"",page:1,limit:20},list:[],total:0,loading:!0,index:null,isEdit:null,formName:null,formMap:{add:"添加分红配置",edit:"修改分红配置"},level1:"",level2:"",level3:"",level4:"",formLoading:!1,formVisible:!1,formData:l,deleteLoading:!1,langList:[]}},mounted:function(){},created:function(){var e=this.$route.query;this.query=Object.assign(this.query,e),this.query.limit=parseInt(this.query.limit),this.getList()},methods:{onReset:function(){this.$router.push({path:""}),this.query={id:"",page:1,limit:20},this.getList()},onSubmit:function(){this.query.page=1,this.$router.push({path:"",query:this.query}),this.getList()},handleCurrentChange:function(e){this.query.page=e,this.getList()},getList:function(){var e=this;this.loading=!0,Object(a["c"])(this.query).then((function(t){e.loading=!1,e.list=t.data.list||[],e.total=t.data.total||0})).catch((function(){e.loading=!1,e.list=[],e.total=0,e.roles=[]}))},resetForm:function(){this.$refs["dataForm"]&&(this.$refs["dataForm"].clearValidate(),this.$refs["dataForm"].resetFields())},hideForm:function(){return this.formVisible=!this.formVisible,this.$refs["dataForm"].resetFields(),this.index=null,!0},handleForm:function(e,t){this.formVisible=!0,this.formData=JSON.parse(JSON.stringify(l)),null!==t&&(this.formData=JSON.parse(JSON.stringify(t))),this.formName="add",this.isEdit=0,null!==e&&(this.isEdit=1,this.index=e,this.formName="edit")},formSubmit:function(){var e=this;this.$refs["dataForm"].validate((function(t){if(t){e.formLoading=!0;var n=Object.assign({},e.formData);Object(a["d"])(n,e.formName).then((function(t){if(e.formLoading=!1,e.$message.success("操作成功"),e.formVisible=!1,"add"===e.formName){if(t.data&&t.data.id){var i=Object.assign(n,t.data);e.list.unshift(i)}}else{var r=Object.assign(n,t.data);e.list.splice(e.index,1,r)}e.resetForm(),e.getList()})).catch((function(){e.formLoading=!1}))}}))}}},s=o,u=n("2877"),d=Object(u["a"])(s,i,r,!1,null,null,null);t["default"]=d.exports}}]);