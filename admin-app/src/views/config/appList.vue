<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <!--<el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>-->
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%;"
    >
      <el-table-column
        label="设备"
        prop="device"
      />
      <el-table-column
        label="版本"
        prop="version"
      />
      <el-table-column
        label="版本类型"
        prop="version_type"
      />
      <el-table-column
        label="类型"
        prop="type"
      />
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
        <el-form-item label="设备" label-width="100px">
          <el-select v-model="formData.device" disabled placeholder="请选择">
            <el-option
              v-for="formData in options"
              :key="formData.value"
              :label="formData.label"
              :value="formData.value"
            />
          </el-select>
        </el-form-item>
        <el-form-item label="上传版本" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            <span style="color: red"> 点击更换</span>
          </el-upload>
        </el-form-item>
        <el-form-item label="下载地址" label-width="100px">
          <el-input v-model="formData.url" />
        </el-form-item>
        <el-form-item label="版本" label-width="100px">
          <el-input v-model="formData.version" />
        </el-form-item>
        <el-form-item label="版本类型" label-width="100px">
          <el-input v-model="formData.version_type" />
        </el-form-item>
        <el-form-item label="类型" label-width="100px">
          <el-input v-model="formData.type" />
        </el-form-item>
        <el-form-item label="强制更新" label-width="100px">
          <el-radio-group v-model="formData.must">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
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
  appList, updateAddAppVersion
} from '@/api/config'
import { qiniuToken
} from '@/api/dateHandle'
const formJson = {
  // id: '',
  // problem_title: '',
  // problem_content: ''
}
export default {
  data() {
    return {
      query: {
        title: '',
        page: 1,
        limit: 20
      },
      qiniu: {},
      options: [{
        value: 'android',
        label: 'android'
      }, {
        value: 'ios',
        label: 'ios'
      }],
      value: '',
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
      formRules: {
        problem_title: [
          { required: true, help: '请输入相关问题', trigger: 'blur' }
        ],
        problem_content: [
          { required: true, help: '请输入相关回答', trigger: 'blur' }
        ]
      },
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
        typeTypeId: '',
        type_name: '',
        status: '',
        page: 1,
        limit: 20
      }
      this.getList()
    },
    handleAvatarSuccess(res, file) {
      this.url = URL.createObjectURL(file.raw)
      this.formData.url = this.qiniu.Domain + res.key
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
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    getList() {
      this.loading = true
      appList(this.query)
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
      this.formRules = this.addRules
      this.isEdit = 0
      if (index !== null) {
        this.isEdit = 1
        this.index = index
        this.formName = 'edit'
      }
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          updateAddAppVersion(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            // 刷新表单
            this.getList()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    }
  }
}
</script>

<style type="text/scss" lang="scss">
  #avatar-uploader{
    width: 10px;
    height: 10px;
  }
</style>
