<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="发起者手机号">
        <el-input v-model="query.FPhone" placeholder="手机号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="收款用户手机号">
        <el-input v-model="query.TPhone" placeholder="手机号" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%;"
      border
    >
      <el-table-column
        align="center"
        label="ID"
        prop="id"
      />
      <el-table-column
        align="center"
        label="发起用户"
        prop="FPhone"
      />
      <el-table-column
        align="center"
        label="收款用户"
        prop="TPhone"
      />
      <el-table-column
        align="center"
        label="币种名称"
        prop="CoinName"
      />
      <el-table-column
        align="center"
        label="金额"
        prop="amount"
      />
      <el-table-column
        align="center"
        label="手续费"
        prop="fee"
      />
      <el-table-column
        align="center"
        label="提交时间"
        prop="created_at"
      >
        <template slot-scope="scope">
          {{ scope.row.created_at }}
        </template>
      </el-table-column>
    </el-table>
    <el-pagination
      :page-size="query.limit"
      layout="prev, pager, next"
      :total="total"
      @current-change="handleCurrentChange"
    />

    <!--表单-->

  </div>

</template>

<script>
import {
  TransferRecord
} from '@/api/coin'
const formJson = {
}
export default {
  data() {
    return {
      query: {
        UserAccount: '',
        page: 1,
        limit: 20
      },
      list: [],
      total: 0,
      loading: true,
      index: null,
      formName: null,
      formMap: {
        add: '新增',
        edit: '审核'
      },
      formLoading: false,
      formVisible: false,
      formData: formJson,
      formRules: {},
      deleteLoading: false
    }
  },
  mounted() {},
  created() {
    // 将参数拷贝进查询对象
    const query = this.$route.query
    this.query = Object.assign(this.query, query)
    this.query.limit = parseInt(this.query.limit)
    // 加载表格数据
    this.getList()
  },
  methods: {
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
        UserAccount: '',
        Status: '',
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
      TransferRecord(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
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
      this.formData = JSON.parse(JSON.stringify(formJson))
      this.formData.Content = ''
      // 清空表单
      this.$refs['dataForm'].resetFields()
      return true
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      if (row !== null) {
        this.formData = Object.assign({}, row)
      }
      if (index !== null) {
        this.index = index
        this.formName = 'edit'
      }
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
