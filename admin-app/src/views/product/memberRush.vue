<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.Phone" placeholder="手机号" style="width: 200px;" class="filter-item" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="search">搜索</el-button>
    </div>
    <div>
      <p>抢购总额: {{ BuyNumber }}</p>
    </div>
    <!-- 展示用户昵称、用户手机号，抢购时间，矿机名称，价格，状态(抢购/放行/中签/抢购失败) -->
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="NickName" align="center" label="用户昵称" />
      <el-table-column prop="Phone" align="center" label="用户手机号" />
      <el-table-column label="抢购时间" align="center">
        <template slot-scope="scope">
          <span>{{ _date(scope.row.AddTime) }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="Name" align="center" label="矿机名称" />
      <el-table-column prop="Price" align="center" label="价格" />
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsSuccess == 1 && scope.row.IsBuy != 1">放行</span>
          <span v-if="scope.row.IsReturn == 1 ">失败</span>
          <span v-if="scope.row.IsBuy == 1 ">中签</span>
          <span v-else-if="scope.row.IsSuccess == 0 && scope.row.IsReturn == 0 && scope.row.IsBuy == 0">抢购</span>
        </template>
      </el-table-column>

      <el-table-column v-permission="['/rush/success']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-if="scope.row.IsSuccess == 0 && scope.row.IsReturn == 0 && scope.row.IsBuy == 0" v-permission="['/rush/success']">
              <el-button size="small" type="primary" @click="rushSuccess(scope.row.Id)">
                放行
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="init" />
  </div>
</template>

<script>
import {
  memberRush,
  RushSuccess
} from '@/api/rush'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'
import Pagination from '@/components/Pagination'
import {
  dateHandle
} from '@/api/dateHandle'

export default {
  name: 'ProductList',
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
      products: [],
      editProductVisible: false,
      addProductVisible: false,
      listLoading: true,
      BuyNumber: 0,
      total: 0,
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
      memberRush(this.listQuery).then((res) => {
        this.total = res.data.total
        this.tableData = res.data.list
        this.BuyNumber = res.data.number
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    rushSuccess(id) {
      RushSuccess({ Id: id }).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '已设为中签'
          })
          this.init()
        }
      })
    },
    search() {
      this.listQuery.page = 1
      this.init()
    },
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    }
  }
}
</script>
