<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item">
        <el-input v-model="query.Phone" placeholder="会员手机号" />
      </el-form-item>
      <el-form-item class="query-form-item">
        <label>状态：</label>
        <el-select v-model="query.Status" placeholder="状态">
          <el-option label="全部" value="" />
          <el-option label="未审核" value="0" />
          <el-option label="已驳回" value="3" />
          <el-option label="充值成功" value="1" />
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
        label="会员名称/手机号"
        align="center"
      >
        <template slot-scope="scope">
          {{ scope.row.NickName }}
          <br>
          {{ scope.row.Phone }}
        </template>
      </el-table-column>
      <el-table-column label="充值类型" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.type == 1 ">话费</span>
          <span v-if="scope.row.type == 2 ">油卡</span>
        </template>
      </el-table-column>
      <el-table-column label="油卡类型" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.child_type == 1 ">中石化</span>
          <span v-if="scope.row.child_type == 2 ">中石油</span>
        </template>
      </el-table-column>
      <el-table-column
        label="充值用户名称/手机号"
        align="center"
        width="160px"
      >
        <template slot-scope="scope">
          {{ scope.row.nickname }}
          <br>
          {{ scope.row.phone }}
        </template>
      </el-table-column>
      <el-table-column
        label="充值金额"
        prop="amount"
        align="center"
      />
      <el-table-column
        label="扣除EB金额"
        prop="deduction_amount"
        align="center"
      />

      <el-table-column
        label="当日EB单价"
        prop="price"
        align="center"
      />
      <el-table-column
        label="充值时间"
        prop="create_time"
        align="center"
      />
      <el-table-column
        label="状态"
        align="center"
      >
        <template slot-scope="scope">
          <el-tag :type="scope.row.status | statusFilterType">{{ scope.row.status | statusFilterName }}</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        label="操作"
        align="center"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">审核
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="query.page" :limit.sync="query.limit" @pagination="getList" />
    <!--<el-pagination-->
    <!--:page-size="query.limit"-->
    <!--layout="prev, pager, next"-->
    <!--:total="total"-->
    <!--@current-change="handleCurrentChange"-->
    <!--/>-->

    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="50%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="会员名称:" prop="NickName" label-width="110px">
          <el-input v-model="formData.NickName" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 100px">会员手机号：</label>
          <el-input v-model="formData.Phone" auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item label="充值用户名称:" prop="nickname" label-width="110px">
          <el-input v-model="formData.nickname" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 86px">充值用户手机：</label>
          <el-input v-model="formData.phone" auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item label="身份证号:" prop="address" label-width="110px">
          <el-input v-model="formData.card_id" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 144px">卡号：</label>
          <el-input v-model="formData.card_num" auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item label="充值金额:" prop="amount" label-width="110px">
          <el-input id="changecolor" v-model="formData.amount" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 96px">扣除EB金额：</label>
          <el-input v-model="formData.deduction_amount" auto-complete="off" style="width: 30%;" disabled="true" />
        </el-form-item>
        <el-form-item label="当日EB单价:" prop="ArticleAuthor" label-width="110px">
          <el-input v-model="formData.price" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 144px">备注：</label>
          <el-input v-model="formData.remarks" auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item label="充值时间:" prop="create_time" label-width="110px">
          <el-input v-model="formData.create_time" auto-complete="off" style="width: 30%" disabled="true" />
          <label style="margin-left: 114px">审核时间：</label>
          <el-input v-model="formData.update_time" auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item label="地址:" prop="ReadNum" label-width="110px">
          <el-input v-model="formData.address" auto-complete="off" style="width: 70%" disabled="true" />
        </el-form-item>
        <el-form-item label="充值类型:" prop="type" label-width="110px">
          <el-radio-group v-model="formData.type">
            <el-radio disabled="true" :label="1">话费</el-radio>
            <el-radio disabled="true" :label="2">油卡</el-radio>
          </el-radio-group>
          <label style="margin-left: 114px">油卡类型：</label>
          <el-radio-group v-model="formData.child_type">
            <el-radio disabled="true" :label="1">中石化</el-radio>
            <el-radio disabled="true" :label="2">中石油</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-if="formData.status !== 1 && formData.status !== 3" label="状态" prop="status" label-width="110px">
          <el-radio-group v-model="formData.SaveStatus">
            <el-radio :label="3">驳回</el-radio>
            <el-radio :label="1">审核通过</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item v-else label="状态:" label-width="110px">
          <el-tag :type="formData.status | statusFilterType">{{ formData.status | statusFilterName }}</el-tag>
        </el-form-item>
        <el-form-item v-if="formData.SaveStatus == 3" label="驳回信息:" label-width="110px">
          <el-input v-model="formData.remarks" type="textarea" autosize auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item v-if="formData.SaveStatus == 1" label="通过信息:" label-width="110px">
          <el-input v-model="formData.remarks" type="textarea" autosize auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item v-if="formData.status == 1" label="驳回信息:" label-width="110px">
          <el-input v-model="formData.remarks" type="textarea" autosize auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
        <el-form-item v-if="formData.status == 3" label="通过信息:" label-width="110px">
          <el-input v-model="formData.remarks" type="textarea" autosize auto-complete="off" style="width: 30%" disabled="true" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer" align="center">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button v-if="formData.status==0" type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  EcologyOrderList,
  EcologyOrderCheck
} from '@/api/members'
import Pagination from '@/components/Pagination'
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
  components: {
    Pagination
  },
  filters: {
    statusFilterType(status) {
      const statusMap = {
        0: 'danger',
        1: 'success',
        3: 'info'
      }
      return statusMap[status]
    },
    statusFilterName(status) {
      const statusMap = {
        0: '待审核',
        1: '充值成功',
        3: '驳回'
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
      EcologyOrderList(this.query)
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
          EcologyOrderCheck({
            Id: this.formData.id,
            Status: this.formData.SaveStatus,
            remarks: this.formData.remarks,
            deduction_amount: this.formData.deduction_amount,
            user_id: this.formData.user_id
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

<style  type="text/scss" lang="scss">
#changecolor{
  color:red !important;
}
</style>
