<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <!--<el-form-item class="query-form-item" label="用户名称">-->
      <!--<el-input v-model="query.NickName" placeholder="用户名称" />-->
      <!--</el-form-item>-->
      <!--<el-form-item class="query-form-item" label="用户手机号">-->
      <!--<el-input v-model="query.Phone" placeholder="手机号" />-->
      <!--</el-form-item>-->
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <!--<el-button type="primary" icon="search" @click="onSubmit">查询</el-button>-->
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
      <el-table-column prop="level" align="center" label="等级" />
      <el-table-column prop="level_name" align="center" label="等级名称" />
      <el-table-column prop="rate_of_return" align="center" label="收益率" />
      <!--<el-table-column label="图标" align="center" label-width="100px">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.icon">-->
      <!--<img v-image-preview :src="qiniu.Domain + scope.row.icon" width="50px">-->
      <!--</span>-->
      <!--</template>-->
      <!--</el-table-column>-->

      <el-table-column
        align="center"
        label="操作"
        width="190px"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">详情
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
        <el-form-item label="等级：" label-width="110px">
          <el-input v-model="formData.level" disabled style="width: 40%" />
          <label>等级名称：</label>
          <el-input v-model="formData.level_name" style="width: 40%" />
        </el-form-item>
        <el-form-item :rules="[{ required: true, message: '收益率必填', trigger: 'blur' }]" label="收益率：" prop="rate_of_return" label-width="110px">
          <el-input v-model="formData.rate_of_return" style="width: 40%" />
        </el-form-item>
        <hr color="#E5E5E5">
        <h3>规则：</h3>
        <el-form-item :rules="[{ required: true, message: '直推人数必填', trigger: 'blur' }]" label="直推人数：" prop="direct_push" label-width="110px">
          <el-input v-model="formData.direct_push" style="width: 40%" />
        <!--<label label-width="70px">兑换树：</label>-->
        <!--<el-input-number v-model="formData.tree" controls-position="right"   />-->
        </el-form-item>
        <el-form-item :rules="[{ required: true, message: '花田亩数必填', trigger: 'blur' }]" label="花田亩数：" prop="mu" label-width="110px">
          <el-input v-model="formData.mu" style="width: 40%" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <!--<el-button type="primary" @click.native="formSubmit()">提交</el-button>-->
        <el-button v-if="formData.direct_push!='' && formData.mu!='' && formData.mu!=''" type="primary" @click="handleEdit()">确 定</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  RegularGrade,
  RegularGradeEdit
} from '@/api/members'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'
const formJson = {
  id: '',
  rule: '',
  level: '',
  level_name: '',
  icon: '',
  rate_of_return: '',
  direct_push: '',
  mu: ''

}
export default {
  directives: {
    waves,
    permission
  },
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
        edit: '等级配置'
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
      RegularGrade(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data || []
          // this.total = response.data.total || 0
          // this.SaplingList = response.data.SaplingList || []
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.roles = []
        })
    },
    handleEdit() {
      RegularGradeEdit(this.formData).then(res => {
        if (res.code === 20000) {
          this.$message({
            message: '常规等级已更新',
            type: 'success'
          })
          this.getList()
          this.formVisible = false
        }
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

<style>
  .avatar-uploader .el-upload {
    margin-left: 20px;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 200px;
    height: 250px;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }

  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 198px;
    height: 198px;
    line-height: 178px;
    text-align: center;
  }

  .avatar {
    width: 190px;
    height: 190px;
    display: block;
  }

  .el-table--border:after, .el-table--group:after, .el-table:before{
    z-index: 0;
  }
</style>
