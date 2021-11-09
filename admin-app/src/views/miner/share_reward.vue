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
        label="Id"
        prop="id"
        width="200px"
      />
      <el-table-column
        align="center"
        label="直推人数"
        prop="direct_push"
      />
      <el-table-column
        align="center"
        label="奖励树苗"
        prop="nicknames"
      />
      <el-table-column
        align="center"
        label="奖励数量"
        prop="number"
      />
      <el-table-column
        align="center"
        label="所需算力"
        prop="computing_power"
      />
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <el-button v-if="scope.row.is_disable == 0" type="primary">未禁用</el-button>
          <el-button v-if="scope.row.is_disable == 1" type="danger">已禁用</el-button>
        </template>
      </el-table-column>
      <el-table-column label="是否删除" align="center">
        <template slot-scope="scope">
          <el-button v-if="scope.row.is_delete == 0" type="primary">未删除</el-button>
          <el-button v-if="scope.row.is_delete == 1" type="danger">已删除</el-button>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="text" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
          </el-button>
          <el-button v-if="scope.row.is_delete==0" type="text" size="small" @click.native="handleDel(scope.$index, scope.row)">删除
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
        <el-form-item label="直推兑换人数" prop="direct_push">
          <el-input v-model="formData.direct_push" auto-complete="off" style="width: 60%" />
        </el-form-item>
        <el-form-item label="所需算力" prop="computing_power">
          <el-input v-model="formData.computing_power" auto-complete="off" style="width: 60%" />
        </el-form-item>
        <el-form-item label="奖励树苗">
          <el-select v-model="formData.nicknames" placeholder="树苗名称" style="width: 20.5%">
            <el-option v-for="item in SaplingList" :key="item.id" :label="item.nickname" :value="item.nickname" />
          </el-select>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
          <label>奖励数量</label>
          <el-input v-model="formData.number" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="其他规则" prop="other_rule">
          <el-input v-model="formData.other_rule" auto-complete="off" style="width: 60%" />
        </el-form-item>
        <el-form-item label="是否禁用" prop="is_disable">
          <el-radio-group v-model="formData.is_disable">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否删除" prop="is_delete">
          <el-radio-group v-model="formData.is_delete">
            <el-radio disabled :label="1">是</el-radio>
            <el-radio disabled :label="0">否</el-radio>
          </el-radio-group>
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
import {
  share_reward,
  miner_levelSave,
  sapling_share_rewardDel
} from '@/api/miner'
const formJson = {
  id: '',
  direct_push: '',
  computing_power: '',
  other_rule: '',
  is_simultaneously: '',
  is_disable: '',
  is_delete: '',
  update_time: '',
  delete_time: '',
  number: '',
  nicknames: '',
  sapling_id: ''
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
      SaplingList: [],
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
      share_reward(this.query)
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
        this.$confirm('确认删除该记录吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            sapling_share_rewardDel(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                // 刷新数据
                this.list.splice(index, 1)
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
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          miner_levelSave(data, this.formName).then(response => {
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
