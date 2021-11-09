<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="会员手机号">
        <el-input v-model="query.Phone" placeholder="会员手机号" />
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
      @sort-change="tableSortChange"
    >
      <el-table-column
        align="center"
        label="会员名称"
        prop="NickName"
      />
      <el-table-column
        align="center"
        label="会员手机号"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="EB余额"
        prop="Money"
        sortable="custom"
      />
      <el-table-column
        align="center"
        label="拥有树苗总数"
        prop="sapling_count"
        sortable="custom"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="管理员累计充值"-->
      <!--prop="SumMoney"-->
      <!--/>-->
    </el-table>

    <pagination v-show="total > 0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
  </div>

</template>

<script>
import {
  UserAmount
} from '@/api/members'
import Pagination from '@/components/Pagination'
// import Tinymce from '../../components/Tinymce/index.vue'
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
  // components: {
  //   Tinymce
  // },
  components: {
    Pagination
  },
  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 10
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
        limit: 10
      }
      this.getList()
    },
    // 排序
    tableSortChange(column) {
      if (column.prop === 'Money') {
        if (column.order === 'descending') {
          this.query.sortby = column.prop
          this.query.order = 'desc'
        } else {
          this.query.sortby = column.prop
          this.query.order = 'asc'
        }
      } else if (column.prop === 'sapling_count') {
        if (column.order === 'descending') {
          this.query.sortby = column.prop
          this.query.order = 'desc'
        } else {
          this.query.sortby = column.prop
          this.query.order = 'asc'
        }
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
      UserAmount(this.query)
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
