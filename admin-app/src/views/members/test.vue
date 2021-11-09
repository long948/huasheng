<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户电话">
        <el-input v-model="query.Phone" placeholder="用户电话" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <el-form-item class="query-form-item" />

        </el-button-group>
      </el-form-item>
      <el-form-item class="query-form-item">
        <div>
          <FilenameOption v-model="filename" />
          <AutoWidthOption v-model="autoWidth" />
          <BookTypeOption v-model="bookType" />
          <el-button
            :loading="loading"
            style="margin: 0 0 20px 20px"
            type="primary"
            icon="el-icon-document"
            @click="handleDownload"
          >
            导出记录
          </el-button>
        </div>
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
        label="份额"
        prop="number"
      />
      <el-table-column
        align="center"
        label="总价"
        prop="price"
      />
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
    </el-table>
    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      @pagination="getList"
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
        <el-form-item label="奖励树苗名称" prop="nickname">
          <el-input v-model="formData.nickname" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="奖励数量" prop="nickname">
          <el-input v-model="formData.number" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="是否可领取" prop="is_disable">
          <el-radio-group v-model="formData.is_reward">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="创建时间" prop="nickname">
          <el-input v-model="formData.create_time" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="领取时间" prop="nickname">
          <el-input v-model="formData.reward_time" :disabled="true" auto-complete="off" />
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
import { equityDividend } from '@/api/members'
// import Tinymce from '../../components/Tinymce/index.vue'
import waves from '@/directive/waves'
import Pagination from '@/components/Pagination'
import Upload from '@/components/Upload/SingleImage3'
import FilenameOption from '@/components/FilenameOption'
import AutoWidthOption from '@/components/AutoWidthOption'
import BookTypeOption from '@/components/BookTypeOption'

const formJson = {
  id: '',
  NickeName: '',
  Phone: '',
  sapling_share_reward_id: '',
  reward: '',
  create_time: ''
}
export default {
  components: {
    Pagination,
    FilenameOption,
    AutoWidthOption,
    BookTypeOption,
    Upload
  },
  directives: { waves },
  // components: {
  //   Tinymce
  // },

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
      langList: [],
      autoWidth: true,
      bookType: 'xlsx',
      filename: ''
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
      equityDividend(this.query)
        .then(response => {
          console.log(response.data)
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
    exportExcel() {
      alert(1)
    },
    handleDownload() {
      this.loading = true
      import('@/vendor/Export2Excel').then((excel) => {
        const tHeader = [
          '用户名称',
          '联系电话',
          '份额',
          '总价'
        ]
        const filterVal = [
          'NickName',
          'Phone',
          'number',
          'price'
        ]
        const list = this.list
        const data = this.formatJson(filterVal, list)
        excel.export_json_to_excel({
          header: tHeader,
          data,
          filename: this.filename,
          autoWidth: this.autoWidth,
          bookType: this.bookType
        })
        this.loading = false
        this.$message({
          message: '导出成功',
          type: 'success'
        })
        this.getList()
      })
    },
    // 洗数据
    formatJson(filterVal, jsonData) {
      return jsonData.map(v =>
        filterVal.map(j => {
          return v[j]
        }),
      )
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
