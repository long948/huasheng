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
      <el-table-column prop="id" label="ID" align="center" width="100px" />
      <el-table-column prop="level" align="center" label="等级" />
      <el-table-column prop="nickname" align="center" label="等级名称" />
      <el-table-column label="图标" align="center" label-width="100px">
        <template slot-scope="scope">
          <span v-if="scope.row.icon">
            <img v-image-preview :src="qiniu.Domain + scope.row.icon" width="50px">
          </span>
        </template>
      </el-table-column>

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
        <el-form-item label="等级名称：" label-width="110px">
          <el-input v-model="formData.nickname" style="width: 50%" />
        </el-form-item>
        <el-form-item label="等级：" label-width="110px">
          <el-input v-model="formData.level" disabled="true" style="width: 50%" />
        </el-form-item>
        <el-form-item label="图标" label-width="100px">
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
          </el-upload>&nbsp;&nbsp;&nbsp;&nbsp;
          <a target="_blank" :href="imageUrl"> <span style="color: red">点击查看图标</span></a>
        </el-form-item>
        <hr color="#E5E5E5">
        <h3>升级规则：</h3>
        <el-form-item label="直推人数：" prop="direct_push" label-width="110px">
          <el-input-number v-model="formData.direct_push" controls-position="right" :min="0" />
          <label style="margin-left: 60px">代数：</label>
          <el-input-number v-model="formData.algebra" controls-position="right" :min="0" />
        </el-form-item>
        <el-form-item>
          <label style="margin-left: 63px">等级：</label>
          <el-input-number v-model="formData.level_id" controls-position="right" :min="0" />
          <label style="margin-left: 60px">亩数：</label>
          <el-input-number v-model="formData.computing_power" controls-position="right" :min="0" />
        </el-form-item>
        <el-alert
          title="例如：百户侯升级规则：直推10人（直推人数输入框），6代（代数输入框）内亩数达到100亩（亩数），直推中有2个升级到金主（等级处输入对应等级如金主等级位2就输入2）"
          type="error"
        />
        <hr color="#E5E5E5">
        <h3 v-if="formData.level!=1 && formData.level!=2">奖励规则:</h3>
        <el-form-item v-if="formData.level!=1 && formData.level!=2" label="奖励树苗：" label-width="110px">
          <el-select v-model="formData.sapling_id" placeholder="树苗名称" style="width: 24%">
            <el-option v-for="item in SaplingList" :key="item.id" :label="item.nickname" :value="item.nickname" />
          </el-select>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <!--<label>分红比例：</label>-->
          <!--<el-input v-model="formData.dividend_ratio" style="width: 30%" placeholder="请输入分红比例" :precision="4" :step="0.01" :max="1" type="number">-->
          <!--<template slot="append">%</template>-->
          <!--</el-input>-->
        </el-form-item>
        <!--<el-form-item label="是否需要审核：" prop="InvestType">-->
        <!--<el-radio-group v-model="formData.is_audit">-->
        <!--<el-radio disabled :label="0">否</el-radio>-->
        <!--<el-radio disabled :label="1">是</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
        <el-form-item label-width="110px" label="是否需要审核：" prop="is_audit">
          <el-radio-group v-model="formData.is_audit">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">是</el-radio>
          </el-radio-group>
        &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <!--<label>是否需要主动给用户弹出提示</label>-->
          <!--<el-radio-group v-model="formData.is_pop_up">-->
          <!--<el-radio :label="0">否</el-radio>-->
          <!--<el-radio :label="1">是</el-radio>-->
          <!--</el-radio-group>-->
        </el-form-item>
        <hr color="#E5E5E5">
        <h3>详情：</h3>
        <el-form-item label="创建时间:" prop="create_time">
          <el-input v-model="formData.create_time" disabled="true" auto-complete="off" style="width: 30%;" />
          <label>更新时间：</label>
          <el-input v-model="formData.update_time" disabled="true" auto-complete="off" style="width: 30%;" />
        </el-form-item>
        <!--<el-form-item label="删除时间:" prop="create_time">-->
        <!--<el-input v-model="formData.create_time" disabled="true" auto-complete="off" style="width: 30%;" />-->
        <!--</el-form-item>-->
        <!--<el-form-item label="是否删除：" prop="InvestType">-->
        <!--<el-radio-group v-model="formData.is_delete">-->
        <!--<el-radio disabled="true" :label="0">未删除</el-radio>-->
        <!--<el-radio disabled="true" :label="1">已删除</el-radio>-->
        <!--</el-radio-group>-->
        <!--&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;-->
        <!--<label>是否禁用：</label>-->
        <!--<el-radio-group v-model="formData.is_disable">-->
        <!--<el-radio disabled="true" :label="0">未禁用</el-radio>-->
        <!--<el-radio disabled="true" :label="1">已禁用</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <!--<el-button type="primary" @click.native="formSubmit()">提交</el-button>-->
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  miner_level,
  edit
} from '@/api/miner'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'
import {
  qiniuToken
} from '@/api/dateHandle'
const formJson = {
  id: '',
  level: '',
  nickname: '',
  icon: '',
  rule: '',
  dividend_ratio: '',
  reward: '',
  is_pop_up: '',
  is_audit: '',
  is_delete: '',
  create_time: '',
  update_time: '',
  imageUrl: '',
  sapling_id: '',
  delete_time: ''
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
      qiniu: {},
      imageUrl: '',
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false,
      langList: [],
      SaplingList: []
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
        id: '',
        page: 1,
        limit: 20
      }
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
      this.formData.icon = res.key
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
      miner_level(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
          this.SaplingList = response.data.SaplingList || []
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.total = 0
          this.roles = []
        })
    },
    handleEdit() {
      edit(this.formData).then(res => {
        if (res.code === 20000) {
          this.$message({
            message: '环保等级已更新',
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
      this.imageUrl = this.qiniu.Domain + row.icon
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
