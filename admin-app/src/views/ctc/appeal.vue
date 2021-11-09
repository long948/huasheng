<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.OrderSn" placeholder="订单号" style="width: 200px;" class="filter-item" clearable />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="filter">搜索</el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column prop="Id" align="center" label="ID" />
      <el-table-column prop="OrderSn" align="center" label="订单编号" width="120px;" />
      <el-table-column prop="CoinName" align="center" label="币种" width="65px;" />
      <el-table-column prop="BuyMember" align="center" label="买家昵称" />
      <el-table-column prop="SellMember" align="center" label="卖家昵称" />
      <el-table-column prop="SumPrice" align="center" label="交易金额" />
      <el-table-column prop="Number" align="center" label="交易数量" />
      <!-- <el-table-column prop="RemarkCode" align="center" label="备注码" /> -->
      <el-table-column prop="Price" align="center" label="交易单价" />
      <el-table-column prop="AppealName" align="center" label="申诉人" />
      <!-- <el-table-column prop="Phone" align="center" label="申诉人手机号" /> -->
      <el-table-column prop="Reason" align="center" label="申诉原因" />
      <el-table-column prop="Content" align="center" label="申诉内容" />
      <el-table-column label="图片" align="center">
        <template slot-scope="scope">
          <img v-for="(item,index) in scope.row.ImgsUrl" :key="index" v-image-preview style="width:50px;height:50px;" :src="item">
        </template>
      </el-table-column>
      <el-table-column label="下单时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.AddTime) }}
        </template>
      </el-table-column>
      <el-table-column label="操作" width="200" align="center">
        <template slot-scope="scope">
          <div v-if="scope.row.Result == 0">
            <span v-permission="['/ctc/appeal/handle']">
              <el-button size="small" type="primary" @click="Handle(scope.row.Id, 1)">已付款</el-button>
            </span>
            <span v-permission="['/ctc/appeal/handle']">
              <el-button size="small" type="primary" @click="Handle(scope.row.Id, 2)">未付款</el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import {
  appeal,
  handleAppeal
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
      listQuery: {
        page: 1,
        limit: 10
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    Handle(id, type) {
      if (type === 1) {
        // 已付款
        this.$confirm('确认把此订单设置为完成?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          handleAppeal({
            Id: id,
            Type: 1
          }).then(res => {
            this.$message({
              message: '订单已设置为已付款',
              type: 'success'
            })
            this.getList()
          })
        }).catch(() => {})
      } else {
        // 未付款
        this.$confirm('确认把此订单设置为待付款?', '提示', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          handleAppeal({
            Id: id,
            Type: 2
          }).then(res => {
            this.$message({
              message: '订单已设置为待付款',
              type: 'success'
            })
            this.getList()
          })
        }).catch(() => {

        })
      }
    },
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    },
    getList() {
      this.listLoading = true
      appeal(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    filter() {
      this.listQuery.page = 1
      this.getList()
    }
  }
}
</script>

<style scoped>
.el-table--border:after, .el-table--group:after, .el-table:before{
  z-index: 0;
}
</style>>
