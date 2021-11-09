<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户手机号">
        <el-input v-model="query.Phone" placeholder="手机号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="树苗名称">
        <el-input v-model="query.nickname" placeholder="树苗名称" />
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
        label="会员手机号"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="树苗名称"
        prop="nickname"
      />
      <el-table-column
        align="center"
        label="树苗释放总额"
        prop="total_amount"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="日产量"-->
      <!--prop="yield"-->
      <!--/>-->
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="算力"-->
      <!--prop="computing_power"-->
      <!--/>-->
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="岗位售价"-->
      <!--prop="total_price"-->
      <!--/>-->
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="收益率"-->
      <!--prop="rate_of_return"-->
      <!--/>-->
      <el-table-column
        align="center"
        label="已释放金额"
        prop="release_amount"
      />
      <el-table-column
        align="center"
        label="剩余释放金额"
        prop="surplus_amount"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="总的释放天数"-->
      <!--prop="total_freed"-->
      <!--/>-->
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <el-button v-if="scope.row.is_disable == 1 " type="info">已禁用</el-button>
          <el-button v-else-if="scope.row.is_disable == 0" type="primary">未禁用</el-button>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="备注"
        prop="remarks"
      />
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="更新时间"-->
      <!--prop="update_time"-->
      <!--/>-->
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="开始领取时间"-->
      <!--prop="begin_receive_time"-->
      <!--/>-->

      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">详情
          </el-button>
          <el-button v-if="scope.row.is_disable==0" type="text" size="small" @click.native="handleDel(scope.$index, scope.row)">删除
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
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="会员手机号:" prop="Phone" label-width="120px">
          <el-input v-model="formData.Phone" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 130px">创建时间：</label>
          <el-input v-model="formData.create_time" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="树苗名称:" prop="Name" label-width="120px">
          <el-input v-model="formData.nickname" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 100px">树苗释放总额：</label>
          <el-input v-model="formData.total_amount" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="树苗释放总额" prop="total_amount">-->
        <!--<el-input v-model="formData.total_amount" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="日产量:" prop="yield" label-width="120px">
          <el-input v-model="formData.yield" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 155px">算力：</label>
          <el-input v-model="formData.computing_power" :disabled="true" placeholder="1为100%" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="算力" prop="computing_power">-->
        <!--<el-input v-model="formData.computing_power" :disabled="true" placeholder="1为100%" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="岗位售价:" prop="total_price" label-width="120px">
          <el-input v-model="formData.total_price" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 140px">收益率：</label>
          <el-input v-model="formData.rate_of_return" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="收益率" prop="rate_of_return">-->
        <!--<el-input v-model="formData.rate_of_return" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="已释放金额:" prop="release_amount" label-width="120px">
          <el-input v-model="formData.release_amount" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 100px">剩余释放金额：</label>
          <el-input v-model="formData.surplus_amount" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="剩余释放金额" prop="surplus_amount">-->
        <!--<el-input v-model="formData.surplus_amount" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="总的释放天数:" prop="total_freed" label-width="120px">
          <el-input v-model="formData.total_freed" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 130px">剩余天数：</label>
          <el-input v-model="formData.freed" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="剩余释放金额" prop="surplus_amount">-->
        <!--<el-input v-model="formData.surplus_amount" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <!--<el-form-item label="创建时间" prop="create_time">-->
        <!--<el-input v-model="formData.create_time" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="开始领取时间:" prop="begin_receive_time" label-width="120px">
          <el-input v-model="formData.begin_receive_time" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 130px">更新时间：</label>
          <el-input v-model="formData.update_time" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="更新时间" prop="update_time">-->
        <!--<el-input v-model="formData.update_time" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="释放完成时间:" prop="release_complete_time" label-width="120px">
          <el-input v-model="formData.release_complete_time" :disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 160px">备注：</label>
          <el-input v-model="formData.remarks" :disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label="备注" prop="remarks">-->
        <!--<el-input v-model="formData.remarks" :disabled="true" auto-complete="off" style="width: 30%"/>-->
        <!--</el-form-item>-->
        <el-form-item label="是否属于体验:" prop="InvestType" label-width="120px">
          <el-radio-group v-model="formData.is_experience">
            <el-radio disabled :label="0">是</el-radio>
            <el-radio disabled :label="1">否</el-radio>
          </el-radio-group>
          <label style="margin-left: 440px">是否释放完成：</label>
          <el-radio-group v-model="formData.is_release_complete" label-width="120px">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <!--<el-form-item label="是否释放完成:" prop="is_release_complete">-->
        <!--<el-radio-group v-model="formData.is_release_complete">-->
        <!--<el-radio disabled :label="0">否</el-radio>-->
        <!--<el-radio disabled :label="1">是</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
        <el-form-item label="是否禁用:" prop="is_disable" label-width="120px">
          <el-radio-group v-model="formData.is_disable">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
          <label style="margin-left: 470px">是否删除：</label>
          <el-radio-group v-model="formData.is_delete">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <!--<el-form-item label="是否删除:" prop="is_delete">-->
        <!--<el-radio-group v-model="formData.is_delete">-->
        <!--<el-radio disabled :label="0">否</el-radio>-->
        <!--<el-radio disabled :label="1">是</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
        <el-form-item label="是否有上级奖励:" prop="is_superior_reward" label-width="120px">
          <el-radio-group v-model="formData.is_superior_reward">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
          <label style="margin-left: 470px">是否赠送：</label>
          <el-radio-group v-model="formData.is_gave_away">
            <el-radio disabled :label="0">否</el-radio>
            <el-radio disabled :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <!--<el-form-item label="是否赠送:" prop="is_gave_away">-->
        <!--<el-radio-group v-model="formData.is_gave_away">-->
        <!--<el-radio disabled :label="0">否</el-radio>-->
        <!--<el-radio disabled :label="1">是</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  user_sapling, user_saplingEdit
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
        edit: '详情'
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
      user_sapling(this.query)
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
    // 删除
    handleDel(index, row) {
      if (row.id) {
        this.$confirm(' 确定删除（禁用）该用户的此颗树苗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            user_saplingEdit(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                this.getList()
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
