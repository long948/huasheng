<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="手机号">
        <el-input v-model.trim="query.Phone" placeholder="用户手机号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="状态">
        <el-select v-model="query.status" placeholder="开团状态">
          <el-option label="全部" value="" />
          <el-option label="待开团" value="0" />
          <el-option label="已开团" value="1" />
          <el-option label="拼团成功" value="2" />
          <el-option label="拼团失败" value="3" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px;" @click="onReset" />
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
        label="团编号"
        prop="found_id"
      />
      <el-table-column label="开团用户" align="center" width="170px">
        <template slot-scope="scope">
          <span style="float: left">昵称：&nbsp;&nbsp;&nbsp;&nbsp;{{ scope.row.NickName }}</span>
          <br>
          <span style="float: left">手机号：{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="拼购商品"
        prop="goods_name"
        :show-overflow-tooltip="true"
      />
      <el-table-column type="index”" label="商品图" align="center" width="130">
        <template slot-scope="scope">
          <img v-image-preview :src="scope.row.original_img" width="50px">
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="支付币种"
        prop="payCoin"
        width="100px"
      />
      <el-table-column
        align="center"
        label="奖励币种"
        prop="getCoin"
        width="90px"
      />
      <el-table-column
        align="center"
        label="成团人数"
        prop="need"
        width="100px"
      />
      <el-table-column
        align="center"
        label="中奖人数"
        prop="stock_limit"
        width="100px"
      />
      <el-table-column
        align="center"
        label="已参团人数"
        prop="join"
        width="100px"
      />
      <el-table-column
        align="center"
        label="未中返还"
        prop="return_amount"
        width="100px"
      />
      <el-table-column label="拼团状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status == 0" type="warning">待开团</el-tag>
          <el-tag v-if="scope.row.status == 1" type="success">已开团</el-tag>
          <el-tag v-if="scope.row.status == 2" type="success">拼团成功</el-tag>
          <el-tag v-if="scope.row.status == 3" type="info">拼团失败</el-tag>
        </template>
      </el-table-column>
      <el-table-column label="时间" align="center" width="250px">
        <template slot-scope="scope">
          <span style="float: left">开团时间：{{ scope.row.found_time }}</span><br>
          <span style="float: left">截止时间：{{ scope.row.found_end_time }}</span><br>
          <span style="float: left">开奖时间：{{ scope.row.open_found_time }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="check(scope.row)">参团详情
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
import { teamFound, UserDogDel } from '@/api/market'
import Pagination from '@/components/Pagination'

const formJson = {

}
export default {
  components: { Pagination },
  data() {
    return {
      query: {
        page: 1,
        limit: 20,
        status: '',
        found_id: ''
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
    this.query.found_id = this.$route.params.found_id
    // 消息列表
    this.getList()
  },
  methods: {
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
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
    check(row) {
      var found_id = row.found_id
      this.$router.push({ name: 'team_follow', params: { found_id }})
    },
    getList() {
      this.loading = true
      teamFound(this.query)
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
            UserDogDel(para)
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
