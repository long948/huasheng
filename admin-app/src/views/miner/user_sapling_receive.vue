<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户手机号">
        <el-input v-model="query.Phone" placeholder="手机号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="因子汇总记录编号">
        <el-input v-model="query.user_sapling_id" placeholder="因子汇总记录编号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="因子详细记录编号">
        <el-input v-model="query.user_day_sapling_id" placeholder="因子详细记录编号" />
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
      :data="list"
      border
      style="width: 100%;"
    >
      <el-table-column
        align="center"
        label="当日用户因子记录编号"
        prop="id"
        width="200px"
      />
      <el-table-column
        align="center"
        label="用户名称"
        prop="NickName"
      />
      <el-table-column
        align="center"
        label="用户电话"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="因子汇总记录编号"
        prop="user_sapling_id"
      />
      <el-table-column
        align="center"
        label="因子详细记录编号"
        prop="user_day_sapling_id"
      />
      <el-table-column
        align="center"
        label="美丽因子金额"
        prop="amount"
      />
      <el-table-column label="是否领取" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_receive == 0" style="color: green;">未领取</span>
          <span v-else style="color: red;">已领取</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="领取设备"
        prop="device"
      />
      <el-table-column
        align="center"
        label="领取设备号"
        prop="device_number"
      />
      <el-table-column
        align="center"
        label="领取IP地址"
        prop="ip"
      />
      <el-table-column
        align="center"
        label="领取时间"
        prop="receive_time"
      />
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
    </el-table>
    <el-pagination
      :page-size="query.limit"
      layout="prev, pager, next"
      :total="total"
      @current-change="handleCurrentChange"
    />
  </div>

</template>

<script>
import {
  user_sapling_receive
} from '@/api/miner'
const formJson = {
  id: '',
  user_id: '',
  child_user_id: '',
  computing_power: '',
  business_id: '',
  type: '',
  is_self: '',
  create_time: '',
  remarks: '',
  begin_time: '',
  NickName: '',
  ChildUser: ''
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
      user_sapling_receive(this.query)
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
