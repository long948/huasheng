<template>
  <div class="app-container">
    <el-form :inline="true" :model="listQuery" class="query-form" size="mini">

      <el-input v-model="listQuery.Phone" placeholder="用户手机号" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button-group>
        <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
        <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="search">
          搜索
        </el-button>
      </el-button-group>
      <div style="float: right">
        <el-form-item class="query-form-item" label="今日求购量：">
          <el-input v-model="setting.buy_amount" class="filter-item" placeholder="今日求购量" />
        </el-form-item>
        <el-button type="primary" size="mini" round @click="handleSetting">点击应用</el-button>
      </div>
    </el-form>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <!--<el-table-column prop="Id" align="center" label="ID" />-->
      <el-table-column prop="NickName" align="center" label="发布人" />
      <el-table-column prop="Phone" align="center" label="发布人手机号" />
      <el-table-column label="发布时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.AddTime) }}
        </template>
      </el-table-column>
      <el-table-column prop="Number" align="center" label="求购数量" />
      <el-table-column prop="Price" align="center" label="当日单价" />
      <!--<el-table-column align="center" label="银行卡">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.IsBank == 0">不支持</span>-->
      <!--<span v-if="scope.row.IsBank == 1">支持</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <!--<el-table-column align="center" label="微信">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.IsWechat == 0">不支持</span>-->
      <!--<span v-if="scope.row.IsWechat == 1">支持</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column align="center" label="支付宝">
        <template slot-scope="scope">
          <span v-if="scope.row.IsAlipay == 0">不支持</span>
          <span v-if="scope.row.IsAlipay == 1">支持</span>
        </template>
      </el-table-column>
      <!--<el-table-column align="center" label="类型">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.Type == 1">出售</span>-->
      <!--<span v-if="scope.row.Type == 2">求购</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column align="center" label="状态">
        <template slot-scope="scope">
          <span v-if="scope.row.State == 0">正常</span>
          <span v-if="scope.row.State == 1">终止</span>
          <span v-if="scope.row.State == 2">完成</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.State == 0" v-permission="['/ctc/order/stop']">
            <el-button size="small" type="primary" @click="stop(scope.row.Id)">撤销</el-button>
          </span>
        </template>
      </el-table-column>
    </el-table>
    <div style="float: right">
      <label>求购量总计：</label>
      <input v-model="app.Number" style="border: none" disabled="true">
    </div>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import {
  ctcOrder,
  ctcOrderStop,
  SettingByamount
} from '@/api/trade'

import waves from '@/directive/waves'
// import {
//   parseTime
// } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
// import datePicker from '@/components/Upload/datePicker'
import {
  dateHandle
} from '@/api/dateHandle'

export default {
  components: {
    Pagination
    // datePicker
  },
  directives: {
    waves,
    permission
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      buy: '',
      sell: '',
      setting: {
        buy_amount: ''
      },
      app: {
        Number: ''
      },
      listQuery: {
        keywords: '',
        page: 1,
        limit: 10
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    },
    search() {
      this.listQuery.page = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      ctcOrder(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total
        this.app.Number = res.data.Number
        this.sell = res.data.sell
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    onReset() {
      this.$router.push({
        path: ''
      })
      this.listQuery = {
        page: 1,
        limit: 10
      }
      this.getList()
    },
    handleFilter() {
      this.listQuery.page = 1
      this.getList()
    },
    stop(Id) {
      this.$confirm('确定终止此订单?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        ctcOrderStop({ Id: Id }).then(res => {
          this.$message({
            message: '交易已终止',
            type: 'success'
          })
          this.getList()
        })
      }).catch(() => {})
    },
    handleSetting() {
      this.$confirm('确定在现在的今日求购量的基础上增加此数据吗？', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        SettingByamount(this.setting).then(res => {
          this.$message({
            message: '操作成功',
            type: 'success'
          })
          this.getList()
        })
      }).catch(() => {})
    }
  }
}
</script>
