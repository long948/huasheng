<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button v-waves class="filter-item" type="primary" @click.native="handleForm(null,null)">
            <i class="el-icon-plus" />&nbsp;&nbsp;添加banner
          </el-button>
          <!--<el-button type="primary" @click.native="handleForm(null,null)">新增banner+</el-button>-->
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%;"
    >
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="类型" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.name" />
        </template>
      </el-table-column>
      <el-table-column label="图片" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.MainImg">
            <img v-image-preview :src="qiniu.Domain + scope.row.MainImg" width="50px">
          </span>
        </template>
      </el-table-column>
      <!--<el-table-column label="排序" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-html="scope.row.Sort" />-->
      <!--</template>-->
      <!--</el-table-column>-->

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
    <pagination v-show="total>0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="35%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">

        <el-form-item label="类型" label-width="100px">
          <!--<el-input v-model="name" />-->
          <el-select v-model="formData.name" placeholder="类型" style="width: 30%">
            <el-option v-for="list in type" :key="list.Id" :label="list.TypeTitle" :value="list.TypeTitle" />
          </el-select>
        </el-form-item>
        <el-form-item label="图片" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="imageUrl" :src="imageUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>

        <!--<el-form-item label="排序" label-width="70px">-->
        <!--<el-input v-model="item.Sort" />-->
        <!--</el-form-item>-->
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
  bannerList,
  bannerSave,
  bannerDelete
} from '@/api/bannerNotice.js'
import waves from '@/directive/waves'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import {
  qiniuToken
} from '@/api/dateHandle'
const formJson = {
  Id: '',
  TypeTitle: '',
  name: '',
  MainImg: ''
}
export default {
  components: {
    Pagination
    // Upload
  },
  directives: {
    waves,
    permission
  },
  filters: {
    IsDelFilterType(status) {
      const statusMap = {
        0: 'success',
        1: 'danger'
      }
      return statusMap[status]
    },
    IsDelFilterName(status) {
      const statusMap = {
        0: '正常',
        1: '删除'
      }
      return statusMap[status]
    }
  },
  data() {
    return {
      query: {
        title: '',
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
        add: '新增banner',
        edit: '编辑banner'
      },
      keywords: '',
      qiniu: {},
      imageUrl: '',
      type: [],
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
        typeTypeId: '',
        type_name: '',
        status: '',
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
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.formData.MainImg = res.key
    },
    getList() {
      this.loading = true
      bannerList(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
          this.type = response.data.type || []
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
        this.imageUrl = this.qiniu.Domain + row.MainImg
        this.index = index
        this.formName = 'edit'
      } else {
        this.imageUrl = ''
        this.index = ''
      }
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          let data = Object.assign({}, this.formData)
          bannerSave(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            if (this.formName === 'add') {
              // 向头部添加数据
              if (response.data && response.data.Id) {
                data = Object.assign(data, response.data)
                this.list.unshift(data)
              }
            } else {
              data = Object.assign(data, response.data)
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
            bannerDelete(para)
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
