<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户ID">
        <el-input v-model="query.user_id" placeholder="ID" />
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
        label="用户名称"
        prop="NickName"
      />
      <el-table-column
        align="center"
        label="贡献者名称"
        prop="ChildUser"
      />
      <el-table-column
        align="center"
        label="算力金额"
        prop="computing_power"
      />
      <el-table-column
        align="center"
        label="业务编号"
        prop="business_id"
      />
      <el-table-column
        align="center"
        label="新增时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="算力开始时间"
        prop="begin_time"
      />
      <el-table-column
        align="center"
        label="算力结束时间"
        prop="end_time"
      />
      <el-table-column label="算力来源类型" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.type == 1 ">认证赠送</span>
          <span v-else-if="scope.row.type == 2">购买</span>
          <span v-else-if="scope.row.type == 3">达到条件赠送</span>
        </template>
      </el-table-column>
      <el-table-column label="是否是自己的" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_self == 1 ">是</span>
          <span v-else-if="scope.row.is_self == 0">团队贡献</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="备注"
        prop="remarks"
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
    <!--<el-dialog-->
    <!--:title="formMap[formName]"-->
    <!--:visible.sync="formVisible"-->
    <!--:before-close="hideForm"-->
    <!--width="85%"-->
    <!--top="5vh"-->
    <!--&gt;-->
    <!--<el-form ref="dataForm" :model="formData">-->
    <!--<el-form-item label="投资产品" prop="Name">-->
    <!--<el-input  :disabled="true" v-model="formData.Name"  auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="投资人 " prop="NickName">-->
    <!--<el-input  :disabled="true" v-model="formData.NickName" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="投资人电话 " prop="Phone">-->
    <!--<el-input  :disabled="true" v-model="formData.Phone" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="总产数量" prop="OutputNumber">-->
    <!--<el-input  :disabled="true" v-model="formData.OutputNumber" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="产出比例" prop="Ratio">-->
    <!--<el-input  :disabled="true" v-model="formData.Ratio" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="产出比例" prop="Ratio">-->
    <!--<el-input  :disabled="true" placeholder="1为100%" v-model="formData.Ratio">-->
    <!--<template slot="append">%</template>-->
    <!--</el-input>-->
    <!--</el-form-item>-->
    <!--<el-form-item   label="产出天数" prop="OutputDay">-->
    <!--<el-input :disabled="true" v-model="formData.OutputDay" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="剩余天数" prop="SurplusDay">-->
    <!--<el-input  :disabled="true" v-model="formData.SurplusDay" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="投资类型" prop="InvestType">-->
    <!--<el-radio-group v-model="formData.InvestType">-->
    <!--<el-radio disabled :label="1">积分</el-radio>-->
    <!--<el-radio disabled :label="2">币种</el-radio>-->
    <!--</el-radio-group>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="投资币种" prop="CoinEnName" >-->
    <!--<el-select v-model="formData.CoinEnName" placeholder="币种" >-->
    <!--<el-option v-for="item in coinList" :key="item.Id" :label="item.EnName" :value="item.Id" />-->
    <!--</el-select>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="投资数量" prop="Number">-->
    <!--<el-input  :disabled="true" v-model="formData.Number" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="产出类型" prop="RewardType">-->
    <!--<el-radio-group v-model="formData.RewardType">-->
    <!--<el-radio :label="1">积分</el-radio>-->
    <!--<el-radio :label="2">币种</el-radio>-->
    <!--</el-radio-group>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="产出币种" prop="RewardCoin" >-->
    <!--<el-select v-model="formData.RewardCoin" placeholder="币种" >-->
    <!--<el-option v-for="item in coinList" :key="item.Id" :label="item.EnName" :value="item.Id" />-->
    <!--</el-select>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="下单时间" prop="AddTime">-->
    <!--<el-input  :disabled="true" v-model="formData.AddTime" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--<el-form-item label="最后产出时间" prop="LastOuputTime">-->
    <!--<el-input  :disabled="true" v-model="formData.LastOuputTime" auto-complete="off"/>-->
    <!--</el-form-item>-->
    <!--</el-form>-->
    <!--<div slot="footer" class="dialog-footer">-->
    <!--<el-button @click.native="hideForm">取消</el-button>-->
    <!--</div>-->
    <!--</el-dialog>-->
  </div>

</template>

<script>
import {
  UserComputingPower
} from '@/api/members'
// import Tinymce from '../../components/Tinymce/index.vue'
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
  // components: {
  //   Tinymce
  // },

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
      UserComputingPower(this.query)
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
