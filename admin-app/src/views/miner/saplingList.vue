<template>
  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
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
        label="花田名称"
        prop="nickname"
      />
      <el-table-column
        align="center"
        label="售价"
        prop="price"
      />
      <el-table-column
        align="center"
        label="收益率"
        prop="rate_of_return"
      />
      <el-table-column
        align="center"
        label="总收益"
        prop="total_profit"
      />
      <el-table-column
        align="center"
        label="总算力"
        prop="computing_power"
      />
      <el-table-column
        align="yield"
        label="日产量"
        prop="total_profit"
      />
      <el-table-column
        align="center"
        label="生产周期（天）"
        prop="cycle"
      />
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="新增时间"
        prop="create_time"
      />
      <el-table-column label="是否删除" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_delete == 0">未删除</span>
          <span v-else-if="scope.row.is_delete == 1">已删除</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
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
      width="65%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="树苗名称：" prop="nickname" label-width="280px">
          <el-input v-model="formData.nickname" auto-complete="off" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>  &nbsp;&nbsp;&nbsp;售价：</label>
          <el-input v-model="formData.price" type="number" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <!--<el-form-item label="售价 " prop="price">-->
        <!--<el-input v-model="formData.price" :disabled="true" auto-complete="off" />-->
        <!--</el-form-item>-->
        <el-form-item label="收益率：" prop="rate_of_return" label-width="280px">
          <el-input v-model="formData.rate_of_return" type="number" auto-complete="off" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>总收益：</label>
          <el-input v-model="formData.total_profit" type="number" :disabled="true" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <!--<el-form-item label="总收益" prop="total_profit">-->
        <!--<el-input v-model="formData.total_profit" :disabled="true" auto-complete="off" />-->
        <!--</el-form-item>-->
        <el-form-item label="总算力：" prop="computing_power" label-width="280px">
          <el-input v-model="formData.computing_power" type="number" :disabled="true" placeholder="1为100%" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>日产量：</label>
          <el-input v-model="formData.yield" type="number" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <el-form-item label="生产周期(天数）：" prop="cycle" label-width="280px">
          <el-input v-model="formData.cycle" type="number" auto-complete="off" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label style="margin-left: -12px">删除时间：</label>
          <el-input v-model="formData.delete_time" :disabled="true" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <el-form-item label="创建时间：" prop="create_time" label-width="280px">
          <el-input v-model="formData.create_time" :disabled="true" auto-complete="off" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label style="margin-left: -12px">更新时间：</label>
          <el-input v-model="formData.update_time" :disabled="true" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <el-form-item label="说明：" prop="explanation" label-width="280px">
          <el-input v-model="formData.explanation" auto-complete="off" style="width: 21%" type="textarea" autosize />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>排序(数字越大级别越大，用于排序)：</label>
          <el-input v-model="formData.sort" :disabled="true" auto-complete="off" style="width: 21%" />
        </el-form-item>
        <el-form-item label="最大持有数量(只限制购买，赠送不限制)：" prop="max_hold" label-width="280px">
          <el-input v-model="formData.max_hold" type="number" auto-complete="off" style="width: 21%" />
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <!--<label>延迟周期（天数）基于开始时间计算：</label>-->
          <!--<el-input v-model="formData.extend_cycle" type="number" auto-complete="off" style="width: 21%" />-->
        </el-form-item>
        <el-form-item label="是否属于体验：" prop="is_experience" label-width="280px">
          <el-radio-group v-model="formData.is_experience">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">是</el-radio>
          </el-radio-group>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>是否有上级奖励：</label>
          <el-radio-group v-model="formData.is_superior_reward">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否禁用：" prop="is_disable" label-width="280px">
          <el-radio-group v-model="formData.is_disable">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">是</el-radio>
          </el-radio-group>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label style="margin-left: 42px">是否删除：</label>
          <el-radio-group v-model="formData.is_delete">
            <el-radio :label="0">否</el-radio>
            <el-radio :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
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
  saplingList, saplingEdit, SettingMinerSapling
} from '@/api/miner'
const formJson = {
  id: '',
  user_id: '',
  child_user_id: '',
  computing_power: '',
  business_id: '',
  type: '',
  is_self: '',
  create_time: ''
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
      setting: {
        basie_event: '',
        delay_period: ''
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
      saplingList(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
          this.setting.basie_event = response.data.basie_event || []
          this.setting.delay_period = response.data.delay_period || []
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
          let data = Object.assign({}, this.formData)
          saplingEdit(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            data = Object.assign(data, response.data)
            this.list.splice(this.index, 1, data)
            // 刷新表单
            this.resetForm()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    },
    handleSetting() {
      this.listLoading = true
      SettingMinerSapling(this.setting).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          setTimeout(() => {
            this.listLoading = false
          })
          this.getList()
        } else {
          this.$message({
            type: 'error',
            message: res.msg
          })
        }
      })
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
