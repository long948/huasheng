<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.keywords" placeholder="关键字" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="会员Id" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.MemberId }}</span>
        </template>
      </el-table-column>
      <el-table-column label="昵称" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.NickName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="会员手机号" align="center">
        <template slot-scope="{ row }">
          {{ row.Phone }}
        </template>
      </el-table-column>
      <el-table-column label="币种" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.CoinName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="变动金额" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.Money < 1" style="color: red;">{{ scope.row.Money }}</span>
          <span v-else style="color: green;">{{ scope.row.Money }}</span>
        </template>
      </el-table-column>

      <el-table-column label="变动后余额" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Balance }}</span>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Remark }}</span>
        </template>
      </el-table-column>
      <el-table-column label="交易时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.AddTime) }}
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { capitalMovements } from '@/api/members'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import { dateHandle } from '@/api/dateHandle'

export default {
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20
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
    getList() {
      this.listLoading = true
      capitalMovements(this.listQuery).then(res => {
        this.list = res.data.data
        this.total = res.data.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    }
  }
}
</script>
