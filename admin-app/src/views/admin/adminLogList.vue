<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input
        v-model="listQuery.keywords"
        placeholder="关键字"
        style="width: 200px;"
        class="filter-item"
        @keyup.enter.native="handleFilter"
      />

      <!--      <el-select v-model="listQuery.cateid" placeholder="文章分类" clearable style="width: 150px" class="filter-item">
        <el-option v-for="(item, index) in catelist" :key="index" :label="item.title" :value="item.id" v-html="item.title_show" />
      </el-select> -->
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
    </div>

    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作管理员ID" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Admin }}</span>
        </template>
      </el-table-column>
      <el-table-column label="管理员昵称" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.AdminName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作内容" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="操作uri" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Uri }}</span>
        </template>
      </el-table-column>
      <el-table-column label="IP地址" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Ip }}</span>
        </template>
      </el-table-column>

      <el-table-column label="添加时间" align="center">
        <template slot-scope="scope">
          <span>{{ _date(scope.row.Time) }}</span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import {
  adminLogList
} from '@/api/admin'
import {
  dateHandle
} from '@/api/dateHandle'
import waves from '@/directive/waves'
import {
  parseTime
} from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'

export default {
  components: {
    Pagination
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
        limit: 10,
        keywords: ''
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
      adminLogList(this.listQuery).then(res => {
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
