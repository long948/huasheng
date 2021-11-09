<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="类型名称">
        <el-input v-model="query.nickname" placeholder="类型名称" />
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
        label="Id"
        prop="id"
        width="200px"
      />
      <el-table-column
        align="center"
        label="树苗名称"
        prop="nickname"
      />
      <el-table-column label="是否删除" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_delete == 0">未删除</span>
          <span v-else-if="scope.row.is_delete == 1">已删除</span>
        </template>
      </el-table-column>
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_delete == 0">未禁用</span>
          <span v-else-if="scope.row.is_delete == 1">已禁用</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
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

    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="50%"
      top="15vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="树苗名称" prop="Name">
          <el-input v-model="formData.nickname" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="是否禁用" prop="is_disable">
          <el-radio-group v-model="formData.is_disable">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否删除" prop="is_delete">
          <el-radio-group v-model="formData.is_delete">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  saplingType
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
      saplingType(this.query)
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
    // formSubmit() {
    //   this.$refs['dataForm'].validate(valid => {
    //     if (valid) {
    //       this.formLoading = true
    //       const data = Object.assign({}, this.formData)
    //       ConvertProductSave(data, this.formName).then(response => {
    //         this.formLoading = false
    //         this.$message.success('操作成功')
    //         this.formVisible = false
    //         if (this.formName === 'add') {
    //           // 向头部添加数据
    //           if (response.data && response.data.Id) {
    //             const upData = Object.assign(data, response.data)
    //             this.list.unshift(upData)
    //           }
    //         } else {
    //           const upData = Object.assign(data, response.data)
    //           this.list.splice(this.index, 1, upData)
    //         }
    //         // 刷新表单
    //         this.resetForm()
    //       })
    //         .catch(() => {
    //           this.formLoading = false
    //         })
    //     }
    //   })
    // }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
