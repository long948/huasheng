<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户手机号">
        <el-input v-model="query.Phone" placeholder="手机号" />
      </el-form-item>
      <el-form-item>
        <el-select v-model="query.miner_level_id" placeholder="会员等级">
          <el-option v-for="item in leveList" :key="item.id" :label="item.nickname" :value="item.id" />
        </el-select>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <!--<el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>-->
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      border
      style="width: 100%;"
    >
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="Id"-->
      <!--prop="id"-->
      <!--/>-->
      <el-table-column
        align="center"
        label="用户名称"
        prop="NickName"
      />
      <el-table-column
        align="center"
        label="等级名称"
        prop="nickname"
      />
      <el-table-column label="奖励是否发放完成" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_issue == 1 ">是</span>
          <span v-else-if="scope.row.is_issue == 0">否</span>
        </template>
      </el-table-column>
      <el-table-column label="是否通过审核" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_audit == 0 ">是</span>
          <span v-else-if="scope.row.is_audit == 1">否</span>
        </template>
      </el-table-column>
      <el-table-column label="是否分红" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_dividend == 1 ">是</span>
          <span v-else-if="scope.row.is_dividend == 0">否</span>
        </template>
      </el-table-column>
      <el-table-column label="是否奖励" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_reward == 1 ">是</span>
          <span v-else-if="scope.row.is_reward == 0">否</span>
        </template>
      </el-table-column>
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_disable == 1 ">是</span>
          <span v-else-if="scope.row.is_disable == 0">否</span>
        </template>
      </el-table-column>
      <el-table-column label="是否删除" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_delete == 1 ">是</span>
          <span v-else-if="scope.row.is_delete == 0">否</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="分红时间"
        prop="dividend_time"
      />
      <el-table-column
        align="center"
        label="奖励时间"
        prop="reward_time"
      />

      <el-table-column
        align="center"
        label="操作"
        width="230px"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">详情
          </el-button>
          <el-button v-if="scope.row.is_audit==1" type="danger" size="small" @click.native="handleDel(scope.$index, scope.row)">审核
          </el-button>
          <el-button v-if="scope.row.is_audit==0" type="success" size="small">审核通过
          </el-button>
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      :page-size="query.limit"
      layout="prev, pager, next"
      :total="total"
      @current-change="handleCurrentChange"
    />

    <!--<&#45;&#45;表单&ndash;&gt;-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="50%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="会员名称:" prop="NickName">
          <el-input v-model="formData.NickName" :disabled="true" auto-complete="off" style="width: 30%;" />
          <label>会员等级：</label>
          <el-select v-model="formData.miner_level_id" placeholder="会员等级">
            <el-option v-for="item in leveList" :key="item.id" disabled :label="item.nickname" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="分红时间:" prop="create_time">
          <el-input v-model="formData.dividend_time" disabled="true" auto-complete="off" style="width: 30%;" />
          <label>奖励时间：</label>
          <el-input v-model="formData.reward_time" disabled="true" auto-complete="off" style="width: 30%;" />
        </el-form-item>
        <el-form-item label="创建时间:" prop="create_time">
          <el-input v-model="formData.create_time" disabled="true" auto-complete="off" style="width: 30%;" />
          <label>更新时间：</label>
          <el-input v-model="formData.update_time" disabled="true" auto-complete="off" style="width: 30%;" />
        </el-form-item>
        <el-form-item label="删除时间:" prop="delete_time">
          <el-input v-model="formData.delete_time" disabled="true" auto-complete="off" style="width: 68.5%;" />
        </el-form-item>
        <el-form-item label="奖励是否发放完成:" prop="is_issue">
          <el-radio-group v-model="formData.is_issue">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>是否分红：</label>
          <el-radio-group v-model="formData.is_dividend">
            <el-radio disabled :label="1">否</el-radio>
            <el-radio disabled :label="0">是</el-radio>
          </el-radio-group>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>是否奖励：</label>
          <el-radio-group v-model="formData.is_reward">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否审核通过" prop="InvestType">
          <el-radio-group v-model="formData.is_audit">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <!--<el-button type="primary" @click.native="formSubmit()">提交</el-button>-->
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  user_level,
  levelEdit
} from '@/api/members'
import {
  qiniuToken
} from '@/api/dateHandle'
const formJson = {
  id: '',
  user_id: '',
  miner_level_id: '',
  is_issue: '',
  is_dividend: '',
  is_reward: '',
  is_disable: '',
  is_delete: '',
  dividend_time: '',
  reward_time: '',
  icon: ''
}
export default {
  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 20
      },
      list: [],
      total: 0,
      loading: true,
      index: null,
      isEdit: null,
      formName: null,
      formMap: {
        add: '新增',
        edit: '编辑'
      },
      leveList: [],
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false,
      langList: []
    }
  },
  mounted() {
  },
  created() {
    // 将参数拷贝进查询对象
    const query = this.$route.query
    this.query = Object.assign(this.query, query)
    this.query.limit = parseInt(this.query.limit)
    // 消息列表
    this.getList()
    this.qiniuGet()
  },
  methods: {
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
        id: '',
        page: 1,
        limit: 20
      }
      this.getList()
    },
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    onSubmit() {
      this.query.page = 1
      this.$router.push({
        path: '',
        query: this.query
      })
      this.getList()
    },
    handleCurrentChange(val) {
      this.query.page = val
      this.getList()
    },
    getList() {
      this.loading = true
      user_level(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
          this.leveList = response.data.leveList || []
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.total = 0
          this.roles = []
        })
    },
    // 刷新表单
    resetForm() {
      if (this.$refs['dataForm']) {
        // 清空验证信息表单
        this.$refs['dataForm'].clearValidate()
        // 刷新表单
        this.$refs['dataForm'].resetFields()
      }
    },
    // 隐藏表单
    hideForm() {
      // 更改值
      this.formVisible = !this.formVisible
      // 清空表单
      this.$refs['dataForm'].resetFields()
      this.index = null
      return true
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      this.formData = JSON.parse(JSON.stringify(formJson))
      if (row !== null) {
        this.formData = JSON.parse(JSON.stringify(row))
      }
      this.formName = 'add'
      this.isEdit = 0
      if (index !== null) {
        this.isEdit = 1
        this.index = index
        this.formName = 'edit'
      }
    },
    // formSubmit() {
    //   this.$refs['dataForm'].validate(valid => {
    //     if (valid) {
    //       this.formLoading = true
    //       const data = Object.assign({}, this.formData)
    //       user_levelEdit(data, this.formName).then(response => {
    //         this.formLoading = false
    //         this.$message.success('操作成功')
    //         this.formVisible = false
    //         if (this.formName === 'add') {
    //           // 向头部添加数据
    //           if (response.data && response.data.id) {
    //             const upData = Object.assign(data, response.data)
    //             this.list.unshift(upData)
    //           }
    //         } else {
    //           const upData = Object.assign(data, response.data)
    //           this.list.splice(this.index, 1, upData)
    //         }
    //         // 刷新表单
    //         this.resetForm()
    //         this.getList()
    //       })
    //         .catch(() => {
    //           this.formLoading = false
    //         })
    //     }
    //   })
    // },
    handleDel(index, row) {
      if (row.id) {
        this.$confirm('确认审核通过吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            levelEdit(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                this.getList()
              })
              .catch(() => {
                this.deleteLoading = false
              })
          })
          .catch(() => {
            this.$message.info('取消审核')
          })
      }
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
