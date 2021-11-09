<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="手机号">
        <el-input v-model.trim="query.Phone" placeholder="用户手机号" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      border
      style="width: 100%;"
    >
      <el-table-column label="开团用户" align="center" width="170px">
        <template slot-scope="scope">
          <span style="float: left">昵称：&nbsp;&nbsp;&nbsp;&nbsp;{{ scope.row.NickName }}</span>
          <br>
          <span style="float: left">手机号：{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column type="index”" label="商品图" align="center" width="150">
        <template slot-scope="scope">
          <img :src="scope.row.original_img" width="50px">
        </template>
      </el-table-column>
      <el-table-column label="商品名称" align="center" :show-overflow-tooltip="true" width="200">
        <template slot-scope="scope">{{ scope.row.goods_name }}</template>
      </el-table-column>
      <el-table-column label="市场价" align="center">
        <template slot-scope="scope">{{ scope.row.market_price }}</template>
      </el-table-column>
      <el-table-column label="拼购价" align="center">
        <template slot-scope="scope">{{ scope.row.team_price }}</template>
      </el-table-column>
      <el-table-column label="成团人数" align="center">
        <template slot-scope="scope">{{ scope.row.needer }}</template>
      </el-table-column>
      <el-table-column label="中奖人数" align="center">
        <template slot-scope="scope">{{ scope.row.stock_limit }}</template>
      </el-table-column>
      <el-table-column label="未中返利" align="center">
        <template slot-scope="scope">{{ scope.row.return_amount }}</template>
      </el-table-column>
      <el-table-column label="支付币种" align="center">
        <template slot-scope="scope">{{ scope.row.payCoin }}</template>
      </el-table-column>
      <el-table-column label="奖励币种" align="center">
        <template slot-scope="scope">{{ scope.row.getCoin }}</template>
      </el-table-column>
      <el-table-column label="中奖时间" width="180" align="center">
        <template slot-scope="scope">{{ scope.row.create_time }}</template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="check(scope.row)">开团信息
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      @pagination="getList"
    />
  </div>

</template>

<script>
import { teamLottery, user_sapling_packageDel } from '@/api/market'
import Pagination from '@/components/Pagination'

const formJson = {
  id: '',
  NickeName: '',
  Phone: '',
  sapling_share_reward_id: '',
  reward: '',
  create_time: ''
}
export default {
  components: { Pagination },
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
    check(row) {
      var found_id = row.found_id
      this.$router.push({ name: 'team_found', params: { found_id }})
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
      teamLottery(this.query)
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
        this.$confirm('确认删除该记录吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            user_sapling_packageDel(para)
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
    }
    // // 禁用
    // Disable(index, row) {
    //   if (row.id) {
    //     this.$confirm('确认禁用该用户拥有的机器人吗?', '提示', {
    //       type: 'warning'
    //     })
    //       .then(() => {
    //         const para = { id: row.id }
    //         this.deleteLoading = true
    //         ConvertProductDel(para)
    //           .then(response => {
    //             this.deleteLoading = false
    //             this.$message.success('操作成功')
    //             // 刷新数据
    //             this.list.splice(index, 1)
    //           })
    //           .catch(() => {
    //             this.deleteLoading = false
    //           })
    //       })
    //       .catch(() => {
    //         this.$message.info('取消删除')
    //       })
    //   }
    // }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
