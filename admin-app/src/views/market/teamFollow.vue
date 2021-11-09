<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="手机号">
        <el-input v-model.trim="query.Phone" placeholder="用户手机号" />
      </el-form-item>
      <el-form-item class="query-form-item" label="是否中奖">
        <el-select v-model="query.is_lock_draw" placeholder="是否中奖">
          <el-option label="全部" value="" />
          <el-option label="已中奖" value="1" />
          <el-option label="未中奖" value="0" />
        </el-select>
      </el-form-item>
      <el-form-item class="query-form-item" label="状态">
        <el-select v-model="query.status" placeholder="开团状态">
          <el-option label="全部" value="" />
          <el-option label="待支付" value="1" />
          <el-option label="待成团" value="2" />
          <el-option label="已完成" value="3" />
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
      <el-table-column label="参团用户" align="center" width="170px">
        <template slot-scope="scope">
          <span style="float: left">昵称：&nbsp;&nbsp;&nbsp;&nbsp;{{ scope.row.NickName }}</span>
          <br>
          <span style="float: left">手机号：{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="团编号"
        prop="found_id"
      />
      <el-table-column
        align="center"
        label="参团时间"
        prop="follow_time"
      />
      <el-table-column type="index”" label="商品图" align="center" width="120">
        <template slot-scope="scope">
          <img :src="scope.row.original_img" width="50px">
        </template>
      </el-table-column>
      <el-table-column label="商品名称" align="center" :show-overflow-tooltip="true" width="110">
        <template slot-scope="scope">{{ scope.row.goods_name }}</template>
      </el-table-column>
      <el-table-column label="市场价" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.market_price }}</template>
      </el-table-column>
      <el-table-column label="拼购价" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.team_price }}</template>
      </el-table-column>
      <el-table-column label="成团人数" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.needer }}</template>
      </el-table-column>
      <el-table-column label="中奖人数" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.stock_limit }}</template>
      </el-table-column>
      <el-table-column label="未中返利" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.return_amount }}</template>
      </el-table-column>
      <el-table-column label="支付币种" width="90" align="center">
        <template slot-scope="scope">{{ scope.row.payCoin }}</template>
      </el-table-column>
      <el-table-column label="奖励币种" align="center" width="90">
        <template slot-scope="scope">{{ scope.row.getCoin }}</template>
      </el-table-column>
      <el-table-column label="状态" align="center" width="90">
        <template slot-scope="scope">
          <span v-if="scope.row.status == 1">待付款</span>
          <span v-if="scope.row.status == 2">待成团</span>
          <span v-if="scope.row.status == 3">已完成</span>
        </template>
      </el-table-column>
      <el-table-column label="是否中奖" align="center" width="90">
        <template slot-scope="scope">
          <span v-if="scope.row.is_lock_draw == 1" style="color: green">已中奖</span>
          <span v-if="scope.row.is_lock_draw == 0" style="color: grey">未中奖</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="check(scope.row)">查看团信息
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
import { teamFollow } from '@/api/market'
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
    getList() {
      this.loading = true
      teamFollow(this.query)
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
    check(row) {
      var found_id = row.found_id
      this.$router.push({ name: 'team_found', params: { found_id }})
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
