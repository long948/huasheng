<template>
  <div class="m-l-50 m-t-30 w-900">
    <el-form ref="form" :model="form" label-width="130px">
      <el-form-item label="用户组名称" prop="Name">
        <el-input v-model.trim="form.Name" class="h-40 w-200"></el-input>
      </el-form-item>
      <el-form-item label="状态">
        <el-select v-model="form.IsClose" placeholder="请选择状态">
            <el-option label="禁用" value="1" />
            <el-option label="启用" value="0" />
        </el-select>
      </el-form-item>
      <el-form-item label="权限分配">
        <div class="bor-gray h-400 ovf-y-auto bor-ra-5 bg-wh">
          <div v-for="item in nodes">
            <div class="bor-b-ccc bg-gra p-l-10 p-r-10">
              <el-checkbox v-model="item.check" @change="selectProjectRule(item)">{{item.else}}</el-checkbox>
            </div>
            <div v-for="childItem in item.Child">
              <div class="p-l-20 bor-b-ccc">
                <el-checkbox v-model="childItem.check" @change="selectModuleRule(childItem, item, childItem.Child)">{{childItem.else}}</el-checkbox>
              </div>
              <div class="p-l-40 bor-b-ccc bg-gra">
                <el-checkbox v-for="(grandChildItem,index) in childItem.Child" v-model="grandChildItem.check" @change="selectActionRule(grandChildItem, childItem, item)" :key="index">{{grandChildItem.else}}</el-checkbox>
              </div>
            </div>
          </div>
        </div>
      </el-form-item>
      <el-form-item>
        <el-button type="primary" @click="edit('form')">提交</el-button>
        <el-button @click="$router.go(-1)">返回</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>
<script>
  import '@/assets/css/global.css'
  import '@/assets/css/base.css'
  import { getRule, getGroup, editGroup } from '@/api/rule'

  export default {
    data() {
      return {
        isLoading: false,
        form: {
          Id: null,
          Name: '',
          IsClose: ''
        },
        nodes: [],
        selectedNodes: []
      }
    },
    methods: {
      edit(form) {
          let params = {
            Id: this.form.Id,
            Name: this.form.Name,
            IsClose: this.form.IsClose,
            Rules: this.selectedNodes
          }
          //var _this = this;
          editGroup(params).then(res => {
            this.$message({
              message: '权限组已修改',
              type: 'success'
            })
            this.closeTag()
          })
      },
      getRules() {
        return new Promise((resolve, reject) => {
          getRule({type: 'tree'}).then((res) => {
              resolve(res.data)
          })
        })
      },
      async getGroupInfo() {
        this.form.Id = this.$route.params.id
        let arr = await this.getRules()
        this.nodes = this.nodes.concat(arr)
        getGroup({id: this.form.Id}).then((res) => {
            let data = res.data
            this.form.Name = data.Name
            this.form.IsClose = data.IsClose
            let array = data.Rules.split(',')
            _(array).forEach((ret) => {
              this.selectedNodes.push(parseInt(ret))
            })
            _(this.nodes).forEach((ret) => {
              if (_.includes(this.selectedNodes, ret.Id)) {
                ret.check = true
              }
              _(ret.Child).forEach((ret1) => {
                if (_(this.selectedNodes).includes(ret1.Id)) {
                  ret1.check = true
                }
                _(ret1.Child).forEach((ret2) => {
                  if (_(this.selectedNodes).includes(ret2.Id)) {
                    ret2.check = true
                  }
                })
              })
            })
        })
      },
      selectProjectRule(item) {
        if (!item.check) {
          _(item.Child).forEach((res) => {
            res.check = false
            let index = _(this.selectedNodes).indexOf(res.Id)
            this.selectedNodes.splice(index, 1)
            _(res.Child).forEach((res1) => {
              res1.check = false
              let index = _(this.selectedNodes).indexOf(res1.Id)
              this.selectedNodes.splice(index, 1)
            })
          })
        }
        this.selectRule(item)
      },
      selectModuleRule(item, faItem, children) {
        if (!faItem.check) {
          faItem.check = true
          this.selectedNodes.push(faItem.Id)
        }
        if (item.check) {
          this.selectedNodes.push(item.Id)
          _(children).forEach((res) => {
            res.check = true
            this.selectedNodes.push(res.Id)
          })
        } else {
          let index = _(this.selectedNodes).indexOf(item.Id)
          this.selectedNodes.splice(index, 1)
          _(children).forEach((res1) => {
            let temp = _(this.selectedNodes).indexOf(res1.Id)
            if (temp >= 0) {
              res1.check = false
              this.selectedNodes.splice(temp, 1)
            }
          })
        }
      },
      selectActionRule(item, faItem, grandFaItem) {
        if (!faItem.check) {
          faItem.check = true
          this.selectedNodes.push(faItem.Id)
        }
        if (!grandFaItem.check) {
          grandFaItem.check = true
          this.selectedNodes.push(grandFaItem.Id)
        }
        this.selectRule(item)
      },
      selectRule(item) {
        if (item.check) {
          this.selectedNodes.push(item.Id)
        } else {
          const index = _(this.selectedNodes).indexOf(item.Id)
          this.selectedNodes.splice(index, 1)
        }
      },
      closeTag(){
        let routeName = this.$route.name
        let visitedViews = this.$store.state.tagsView.visitedViews
        for(const i in visitedViews){
          if(visitedViews[i].name == routeName)
          this.$store.dispatch('tagsView/delView', visitedViews[i]).then(({ visitedViews }) => {
              const latestView = visitedViews.slice(-1)[0]
              if (latestView) {
                this.$router.push(latestView)
              } else {
                if (view.name === 'Dashboard') {
                  this.$router.replace({ path: '/redirect' + view.fullPath })
                } else {
                  this.$router.push('/')
                }
              }
          })
        }
      }
    },
    created() {
      this.getGroupInfo()
    }
  }
</script>