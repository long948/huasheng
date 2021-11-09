<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="会员账号">
        <el-input v-model="query.keywords" placeholder="会员账号" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <!--<el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>-->
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      element-loading-text="拼命加载中(长时间未加载出来请按F5刷新页面)"
      :data="list"
      border
      style="width: 100%;"
    >
      <el-table-column
        align="center"
        label="会员手机号/账号"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="币种名称"
        prop="CoinName"
      />
      <el-table-column
        align="center"
        label="变动类型"
        prop="MoldTitle"
      />
      <el-table-column
        align="center"
        label="变动金额"
        prop="Money"
      />
      <el-table-column
        align="center"
        label="变动后的余额"
        prop="Balance"
      />
      <el-table-column
        align="center"
        label="变动时间"
        prop="AddTime"
      />
    </el-table>
    <div style="float: right">
      <label>今日充值总计：</label>
      <input v-model="app.TodaySUM" style="border: none" disabled="true">
      <!--<label v-if="app.TodaySUM==0">今日充值总计：</label>-->
      <!--<input v-if="app.TodaySUM==0" style="border: none" disabled="true">-->
      <!--<input   style="border: none" disabled="true">-->
      <label>总计：</label>
      <input v-model="app.SUM" style="border: none" disabled="true">
    </div>
    <pagination v-show="total > 0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
    <!--<el-form-item label="安卓下载地址" label-width="100px">-->
    <!--<input v-model="app.SUM" />-->
    <!--</el-form-item>-->
  </div>

</template>

<script>
import {
  AdminOperationRecord
} from '@/api/members'
// import Tinymce from '../../components/Tinymce/index.vue'
import Pagination from '@/components/Pagination'
const formJson = {
  id: '',
  user_id: '',
  child_user_id: '',
  computing_power: '',
  business_id: '',
  type: '',
  SUM: '',

  is_self: '',
  create_time: '',
  remarks: '',
  begin_time: '',
  NickName: '',
  ChildUser: ''
}
export default {
  components: {
    Pagination
  },

  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 20
      },
      app: {
        SUM: ''
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
      AdminOperationRecord(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
          this.app.SUM = response.data.SUM || 0
          this.app.TodaySUM = response.data.TodaySUM || 0
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
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
