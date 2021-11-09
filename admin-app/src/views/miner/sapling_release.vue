<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户电话">
        <el-input v-model="query.Phone" placeholder="电话" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <!--<el-form-item class="query-form-item" label="树苗名称">-->
      <!--<el-input v-model="query.nickname" placeholder="树苗名称" />-->
      <!--</el-form-item>-->
      <!--<el-form-item class="query-form-item" label="用户编号">-->
      <!--<el-input v-model="query.user_sapling_id" placeholder="用户编号" />-->
      <!--</el-form-item>-->
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
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="Id"-->
      <!--prop="id"-->
      <!--width="200px"-->
      <!--/>-->
      <el-table-column
        align="center"
        label="用户名称"
        prop="NickName"
      />
      <el-table-column
        align="center"
        label="用户手机号"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="今日释放总额"
        prop="amount"
      />
      <el-table-column
        align="center"
        label="可偷取总额"
        prop="steal_amount"
      />
      <el-table-column
        align="center"
        label="已被偷总额"
        prop="already_steal_amount"
      />
      <el-table-column
        align="center"
        label="开始偷取时间"
        prop="begin_receive_time"
      />
      <el-table-column label="是否还可以被偷" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_steal == 0 ">否</span>
          <span v-else-if="scope.row.is_steal == 1">是</span>
        </template>
      </el-table-column>
      <el-table-column label="是否已发放" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_issue == 0 ">否</span>
          <span v-else-if="scope.row.is_issue == 1">是</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="发放时间"
        prop="issue_time"
      />
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="更新时间"
        prop="update_time"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="操作"-->
      <!--&gt;-->
      <!--<template slot-scope="scope">-->
      <!--<el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">详情-->
      <!--</el-button>-->
      <!--</template>-->
      <!--</el-table-column>-->
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
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="树苗名称" prop="Name">
          <el-input v-model="formData.nickname" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="售价 " prop="Phone">
          <el-input v-model="formData.price" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="收益率" prop="OutputNumber">
          <el-input v-model="formData.rate_of_return" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="总收益" prop="Ratio">
          <el-input v-model="formData.total_profit" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="总算力" prop="Ratio">
          <el-input v-model="formData.computing_power" :disabled="true" placeholder="1为100%" />
        </el-form-item>
        <el-form-item label="日产量" prop="OutputDay">
          <el-input v-model="formData.yield" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="生产周期(天数" prop="SurplusDay">
          <el-input v-model="formData.cycle" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="最大持有数量(只限制购买，赠送不限制)" prop="Number">
          <el-input v-model="formData.max_hold" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="延迟周期（天数）基于开始时间计算" prop="AddTime">
          <el-input v-model="formData.extend_cycle" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="说明" prop="AddTime">
          <el-input v-model="formData.explanation" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="排序(数字越大级别越大，用于排序)" prop="AddTime">
          <el-input v-model="formData.sort" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="创建时间" prop="AddTime">
          <el-input v-model="formData.create_time" :disabled="true" auto-complete="off" />
        </el-form-item>
        <el-form-item label="是否属于体验" prop="InvestType">
          <el-radio-group v-model="formData.is_experience">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否有上级" prop="InvestType">
          <el-radio-group v-model="formData.is_experience">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否禁用" prop="InvestType">
          <el-radio-group v-model="formData.is_experience">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否删除" prop="InvestType">
          <el-radio-group v-model="formData.is_experience">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  sapling_release
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
      sapling_release(this.query)
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
