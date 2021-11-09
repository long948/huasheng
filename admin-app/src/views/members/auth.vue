<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item">
        <el-input v-model="query.UserAccount" placeholder="用户手机号" />
      </el-form-item>
      <el-form-item class="query-form-item">
        <label>状态：</label>
        <el-select v-model="query.Status" placeholder="状态">
          <el-option label="全部" value="" />
          <el-option label="未审核" value="0" />
          <el-option label="已驳回" value="1" />
          <el-option label="审核通过" value="2" />
        </el-select>
      </el-form-item>

      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      element-loading-text="拼命加载中(长时间未加载出来请按F5刷新页面)"
      :data="list"
      border
      align="center"
      style="width: 100%;"
    >
      <el-table-column
        label="ID"
        prop="Id"
        align="center"
      />
      <el-table-column
        label="用户手机/邮箱"
        prop="Phone"
        align="center"
      />
      <el-table-column
        label="真实姓名"
        prop="AuthName"
        align="center"
      />
      <el-table-column
        label="身份证号"
        prop="IdCard"
        align="center"
      />

      <el-table-column
        label="创建时间"
        prop="UpdateTime"
        align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.CreateTime }}
        </template>
      </el-table-column>
      <el-table-column
        label="审核时间"
        prop="UpdateTime"
        align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.UpdateTime }}
        </template>
      </el-table-column>
      <el-table-column
        label="认证次数"
        prop="count"
        align="center"
      />
      <el-table-column
        label="状态"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.Status | statusFilterType">{{ scope.row.Status | statusFilterName }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">操作
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
      width="45%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="真实姓名">
          <span>{{ formData.AuthName }}</span>
        </el-form-item>
        <el-form-item label="身份证号">
          <span>{{ formData.IdCard }}</span>
        </el-form-item>
        <el-form-item label="照片">
          <a target="_blank" :href="formData.FrontImage"> <img :src="formData.FrontImage" width="300" alt=""></a>
          <a target="_blank" :href="formData.FrontImage"> <img :src="formData.Video" width="300" alt=""></a>
        </el-form-item>
        <!--<el-form-item label="身份证反面">-->
        <!--<img :src="formData.ReverseImage" width="300" alt="">-->
        <!--</el-form-item>-->
        <!--<el-form-item label="视频" prop="">-->
        <!--<video :src="formData.Video" style="width: 500px" height="400px" />-->
        <!--</el-form-item>-->
        <el-form-item v-if="formData.Status !== 1 && formData.Status !== 2" label="状态" prop="Status">
          <el-radio-group v-model="formData.SaveStatus">
            <el-radio :label="1">驳回</el-radio>
            <el-radio :label="2">审核通过</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-else label="状态">
          <el-tag :type="formData.Status | statusFilterType">{{ formData.Status | statusFilterName }}</el-tag>
        </el-form-item>
        <el-form-item v-if="formData.SaveStatus == 1" label="驳回信息">
          <el-input v-model="formData.Message" auto-complete="off" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer" align="center">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button v-if="formData.Status==0" type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  membersAuth,
  AuthReject
} from '@/api/members'
const formJson = {
  Id: '',
  Type: '',
  AuthName: '',
  IdCard: '',
  ShouFrontImage: '',
  FrontImage: '',
  ReverseImage: '',
  Sex: '',
  Birthday: '',

  Status: '',
  SaveStatus: '',
  Message: '',
  Video: ''
}
export default {
  filters: {
    statusFilterType(status) {
      const statusMap = {
        0: 'danger',
        1: 'warning',
        2: 'success'
      }
      return statusMap[status]
    },
    statusFilterName(status) {
      const statusMap = {
        0: '未审核',
        1: '已驳回',
        2: '审核通过'
      }
      return statusMap[status]
    }
  },

  data() {
    return {
      query: {
        UserAccount: '',
        Status: '0',
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
      membersAuth(this.query)
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
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          AuthReject(data).then(response => {
            this.formLoading = false
            if (response.code !== 20000) {
              this.$message.error(response.message)
              return false
            }
            this.$message.success('操作成功')
            this.formVisible = false
            data.Status = data.SaveStatus
            this.list.splice(this.index, 1, data)
            // 刷新表单
            this.resetForm()
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
</style>
