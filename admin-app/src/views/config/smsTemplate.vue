<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%;"
    >
      <el-table-column
        label="ID"
        prop="Id"
      />
      <el-table-column
        label="名称"
        prop="Title"
      />
      <el-table-column
        label="索引"
        prop="CallIndex"
      />
      <el-table-column
        label="模板ID"
        prop="TemplateId"
      />
      <el-table-column
        label="模板参数"
        prop="Template"
      />
      <el-table-column
        label="是否验证"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.IsValid | IsValidFilterType">{{ scope.row.IsValid | IsValidFilterName }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        label="手机号验证存在"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.IsPhone | IsPhoneFilterType">{{ scope.row.IsPhone | IsPhoneFilterName }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
          </el-button>
          <el-button type="text" size="small" @click.native="handleDel(scope.$index, scope.row)">删除
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
      width="85%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData" :rules="formRules">
        <el-form-item label="名称" prop="Title">
          <el-input v-model="formData.Title" auto-complete="off" />
        </el-form-item>
        <el-form-item label="调用索引" prop="CallIndex">
          <el-input v-model="formData.CallIndex" auto-complete="off" />
        </el-form-item>
        <el-form-item label="模板ID" prop="TemplateId">
          <el-input v-model="formData.TemplateId" auto-complete="off" />
        </el-form-item>
        <el-form-item label="模板参数" prop="Template">
          <el-input v-model="formData.Template" auto-complete="off" />
        </el-form-item>
        <el-form-item label="是否验证" prop="IsValid">
          <el-radio-group v-model="formData.IsValid">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">需要</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否验证手机" prop="IsPhone">
          <el-radio-group v-model="formData.IsPhone">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">需要</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  smsTemplateList,
  smsTemplateSave,
  smsTemplateDelete
} from '@/api/smsTemplate'
const formJson = {
  Id: '',
  Title: '',
  CallIndex: '',
  TemplateId: '',
  Template: '',
  IsValid: 0,
  IsPhone: 0
}
export default {
  filters: {
    IsValidFilterType(status) {
      const statusMap = {
        0: 'success',
        1: 'danger'
      }
      return statusMap[status]
    },
    IsValidFilterName(status) {
      const statusMap = {
        0: '否',
        1: '需要'
      }
      return statusMap[status]
    },
    IsPhoneFilterType(status) {
      const statusMap = {
        0: 'success',
        1: 'danger'
      }
      return statusMap[status]
    },
    IsPhoneFilterName(status) {
      const statusMap = {
        0: '否',
        1: '需要'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      query: {
        Title: '',
        Type: '',
        Status: '',
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
        edit: '编辑'
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
    onSelectPic(filePath, filePathUrl) {
      this.formData.ImageUrl = filePathUrl
      this.formData.Image = filePath
    },
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
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
      smsTemplateList(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.total = 0
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
      this.formName = 'add'
      this.formRules = this.addRules
      if (index !== null) {
        this.index = index
        this.formName = 'edit'
      }
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          smsTemplateSave(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            if (this.formName === 'add') {
              // 向头部添加数据
              if (response.data && response.data.Id) {
                data.Id = response.data.Id
                data.CoinName = response.data.CoinName
                this.list.unshift(data)
              }
            } else {
              this.list.splice(this.index, 1, data)
            }
            // 刷新表单
            this.resetForm()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    },
    // 删除
    handleDel(index, row) {
      if (row.Id) {
        this.$confirm('确认删除该记录吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { Id: row.Id }
            this.deleteLoading = true
            smsTemplateDelete(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                // 刷新数据
                this.list.splice(index, 1)
              })
              .catch(() => {
                this.deleteLoading = false
              })
          })
          .catch(() => {
            this.$message.info('取消删除')
          })
      }
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
