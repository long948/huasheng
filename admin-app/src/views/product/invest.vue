<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.NickName" placeholder="昵称" style="width: 200px;" class="filter-item" />
      <el-input v-model="listQuery.Phone" placeholder="手机号" style="width: 200px;" class="filter-item" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="init">搜索</el-button>
    </div>
    <div>
      <p>购买数量: {{ BuyNumber }}</p>
      <p>购买总额: {{ BuySumNumber }}</p>
      <p>抢购总额: {{ RushNumber }}</p>
      <p>赠送总额: {{ GiveNumber }}</p>
    </div>
    <!-- 展示用户昵称、用户手机号、购买时间、矿机名称、矿机来源 -->
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="NickName" align="center" label="用户昵称" />
      <el-table-column prop="Phone" align="center" label="用户手机号" />
      <el-table-column label="购买时间" align="center">
        <template slot-scope="scope">
          <span>{{ _date(scope.row.AddTime) }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="Name" align="center" label="矿机名称" />
      <el-table-column label="矿机来源" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.Type == 1">购买</span>
          <span v-else-if="scope.row.Type == 2">抢购</span>
          <span v-else-if="scope.row.Type == 3">赠送</span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="init" />
  </div>
</template>

<script>
import {
  list
} from '@/api/invest'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'
import Pagination from '@/components/Pagination'
import {
  dateHandle
} from '@/api/dateHandle'

export default {
  name: 'InvestList',
  directives: {
    waves,
    permission
  },
  components: {
    Pagination
  },
  data() {
    return {
      tableData: [],
      total: 0,
      editProductVisible: false,
      addProductVisible: false,
      listLoading: true,
      BuyNumber: 0,
      RushNumber: 0,
      GiveNumber: 0,
      BuySumNumber: 0,
      listQuery: {
        page: 1,
        limit: 10
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      this.listLoading = true
      list(this.listQuery).then((res) => {
        this.tableData = res.data.list
        this.total = res.data.total
        this.BuyNumber = res.data.number
        this.RushNumber = res.data.RushNumber
        this.GiveNumber = res.data.GiveNumber
        this.BuySumNumber = res.data.BuySumNumber
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    }
  }
}
</script>
