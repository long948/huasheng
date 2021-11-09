<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>
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
        label="分红总额"
        prop="amount"
      />
      <el-table-column
        align="center"
        label="等级一"
        prop="level1"
      />
      <el-table-column
        align="center"
        label="等级二"
        prop="level2"
      />
      <el-table-column
        align="center"
        label="等级三"
        prop="level3"
      />
      <el-table-column
        align="center"
        label="等级四"
        prop="level4"
      />
      <el-table-column
        align="center"
        label="等级五"
        prop="level5"
      />
      <el-table-column
        align="center"
        label="等级六"
        prop="level6"
      />
      <el-table-column label="是否分红" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_dividend == 0" style="color: green">否</span>
          <span v-else-if="scope.row.is_dividend == 1" style="color: red">是</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="开始分红时间"
        prop="begin_dividend_time"
      />
      <el-table-column
        align="center"
        label="更新时间"
        prop="update_time"
      />
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
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
        <el-form-item :label="formData.LevelName1" prop="level1" label-width="80px">
          <el-input v-model="formData.level1" controls-position="right" style="width: 30%" />

          <label style="margin-left: 30px">{{ formData.LevelName2 }}</label>&nbsp;&nbsp;
          <el-input v-model="formData.level2" controls-position="right" style="width: 30%" />
        </el-form-item>
        <el-form-item :label="formData.LevelName3" prop="level1" label-width="80px">
          <el-input v-model="formData.level3" controls-position="right" style="width: 30%" />
          <label style="margin-left: 16px">{{ formData.LevelName4 }}</label>&nbsp;&nbsp;
          <el-input v-model="formData.level4" controls-position="right" style="width: 30%" />
        </el-form-item>
        <el-form-item :label="formData.LevelName5" prop="level1" label-width="80px">
          <el-input v-model="formData.level5" controls-position="right" style="width: 30%" />
          <label style="margin-left: 30px">{{ formData.LevelName6 }}</label>&nbsp;&nbsp;
          <el-input v-model="formData.level6" controls-position="right" style="width: 30%" />
        </el-form-item>
        <el-form-item label="分红总额:" prop="amount" label-width="80px">
          <el-input v-model="formData.amount" auto-complete="off" style="width: 61.5%" />
        </el-form-item>
        <el-form-item label="分红开始时间" prop="begin_dividend_time">

          <el-date-picker
            v-model="formData.begin_dividend_time"
            prop="begin_dividend_time"
            value-format="yyyy-MM-dd"
            type="date"
            placeholder="选择日期时间"
          />

        </el-form-item>
        <el-form-item prop="task_image">
          <div v-for="(item,index) in formData.level_dividend" :key="index" style="display: inline-flex;margin-left: 2px;position: relative;">
            <b style="color: #FF0000">等级{{ index+1 }}：</b>
            &nbsp;
            <!--<div> <span>等级：{{item.miner_level_1}}</span></div>-->
            <div> <span>总额：{{ item.miner_level_amount }}</span></div>
            <div> <span>总人数：{{ item.miner_level_count }}</span></div>
            <div> <span>单个金额：{{ item.miner_level_user_amount }}</span></div>
            &nbsp;
          </div>
        </el-form-item>
        <el-alert
          title="注：分红开始时间为每周一次"
          type="error"
        />
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button v-if="formData.is_dividend!=1" type="primary" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  miner_dividend,
  miner_dividendSave
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
        add: '添加分红配置',
        edit: '修改分红配置'
      },
      level1: '',
      level2: '',
      level3: '',
      level4: '',
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
      miner_dividend(this.query)
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
      this.formData.LevelName1 = '户主'
      this.formData.LevelName2 = '金主'
      this.formData.LevelName3 = '百户侯'
      this.formData.LevelName4 = '千户侯'
      this.formData.LevelName5 = '万户侯'
      this.formData.LevelName6 = '酋长'
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
          miner_dividendSave(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            if (this.formName === 'add') {
              // 向头部添加数据
              if (response.data && response.data.id) {
                const upData = Object.assign(data, response.data)
                this.list.unshift(upData)
              }
            } else {
              const upData = Object.assign(data, response.data)
              this.list.splice(this.index, 1, upData)
            }
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
