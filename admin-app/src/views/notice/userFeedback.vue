<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户电话">
        <el-input v-model="query.Phone" placeholder="用户电话" />
      </el-form-item>
      <el-form-item class="query-form-item" label="反馈标题">
        <el-input v-model="query.title" placeholder="反馈标题" />
      </el-form-item>
      <el-form-item class="query-form-item" label="是否处理">
        <el-select v-model="query.is_hand" placeholder="是否处理">
          <el-option label="全部" value="" />
          <el-option label="已处理" value="1" />
          <el-option label="未处理" value="0" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
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
        label="会员名称/手机号"
        align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.NickName }}
          <br>
          {{ scope.row.Phone }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="反馈标题"
        prop="title"
      />
      <el-table-column label="是否回复" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_hand == 0 " style="color: red">未回复</span>
          <span v-if="scope.row.is_hand == 1 " style="color: green">已回复</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="更新"
        prop="update_time"
      />
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button v-if="scope.row.is_hand == 0" type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">回复
          </el-button>
          <el-button v-else type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">详情
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
        <el-form-item label="会员名称" prop="NickName">
          <el-input v-model="formData.NickName" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="会员电话" prop="Phone">
          <el-input v-model="formData.Phone" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="反馈标题" prop="title">
          <el-input v-model="formData.title" type="textarea" autosize :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="反馈内容" prop="details">
          <el-input v-model="formData.details" type="textarea" autosize :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="创建时间" prop="nickname">
          <el-input v-model="formData.create_time" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="更新时间" prop="nickname">
          <el-input v-model="formData.update_time" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="是否回复" prop="is_hand">
          <el-radio-group v-model="formData.is_hand">
            <el-radio disabled :label="0">未回复</el-radio>
            <el-radio disabled :label="1">已回复</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="后台回复" prop="reply">
          <el-input v-model="formData.reply" type="textarea" autosize placeholder="请输入回复内容" auto-complete="off" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button v-if="formData.is_hand==0" type="primary" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import { UserFeedback, UserFeedbackAnswer
} from '@/api/bannerNotice'
// import Tinymce from '../../components/Tinymce/index.vue'
const formJson = {
  id: '',
  NickeName: '',
  Phone: '',
  sapling_share_reward_id: '',
  reward: '',
  create_time: ''
}
export default {
  // components: {
  //   Tinymce
  // },

  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 10,
        is_hand: '0'
      },
      list: [],
      total: 0,
      loading: true,
      index: null,
      isEdit: null,
      formName: null,
      formMap: {
        add: '新增',
        edit: '回复'
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
        limit: 10,
        is_hand: ''
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
      UserFeedback(this.query)
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
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          UserFeedbackAnswer({
            Id: this.formData.id,
            reply: this.formData.reply
          }).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            const upData = Object.assign(data, response.data)
            this.list.splice(this.index, 1, upData)
            // 刷新表单
            this.resetForm()
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
</style>
