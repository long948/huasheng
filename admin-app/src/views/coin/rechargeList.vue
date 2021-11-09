<template>
  <div class="app-container">
    <el-form :inline="true" :model="listQuery" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="listQuery.keywords" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="转入时间范围">
        <div class="block">
          <el-date-picker
            v-model="listQuery.times"
            style="width: 360px"
            type="datetimerange"
            value-format="yyyy-MM-dd HH:mm:ss"
            :picker-options="pickerOptions"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            align="right"
          />
        </div>
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px;" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
        </el-button-group>
      </el-form-item>
      <el-form-item class="query-form-item">
        <div>
          <FilenameOption v-model="filename" />
          <AutoWidthOption v-model="autoWidth" />
          <BookTypeOption v-model="bookType" />
          <el-button :loading="listLoading" style="margin:0 0 20px 20px;" type="primary" icon="el-icon-document" @click="handleDownload">
            导出记录
          </el-button>
        </div>
      </el-form-item>

      <el-alert
        style="margin-top: -20px"
        title="注：导出记录只能导出当前页的记录，若需导出全部记录（或筛选出的全部记录），请将每页显示条数选到最大。"
        type="error"
      />
    </el-form>
    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="序号" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="协议" align="center">
        <template slot-scope="scope">
          <span v-for="(item,index) in protocolList" :key="index" :v-if="item.id == scope.row.Protocol">{{ item.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="用户名/手机号" align="center">
        <template slot-scope="scope">
          <div>{{ scope.row.NickName }}</div>
          <div>{{ scope.row.Phone }}</div>
        </template>
      </el-table-column>
      <el-table-column label="转入币种" align="center">
        <template slot-scope="scope">
          {{ scope.row.EnName }}
        </template>
      </el-table-column>
      <el-table-column label="转入地址" align="center">
        <template slot-scope="scope">
          {{ scope.row.Address }}
        </template>
      </el-table-column>
      <el-table-column label="转入数量" align="center">
        <template slot-scope="scope">
          {{ scope.row.Money }}
        </template>
      </el-table-column>
      <el-table-column label="Hash" align="center">
        <template slot-scope="scope">
          {{ scope.row.Hash }}
        </template>
      </el-table-column>
      <el-table-column label="转入时间" align="center">
        <template slot-scope="scope">
          {{ scope.row.AddTime }}
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.Status == 0">未到账</span>
          <span v-if="scope.row.Status == 1">到账</span>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

  </div>
</template>

<script>
import { rechargeList, getProtocol } from '@/api/coin.js'
import waves from '@/directive/waves'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
// import FilenameOption from '../coin/components/FilenameOption'
// import AutoWidthOption from '../coin/components/AutoWidthOption'
// import BookTypeOption from '../coin/components/BookTypeOption'
export default {
  // FilenameOption, AutoWidthOption, BookTypeOption, Upload
  components: { Pagination },
  directives: { waves, permission },
  data() {
    return {
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30)
            picker.$emit('pick', [start, end])
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date()
            const start = new Date()
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90)
            picker.$emit('pick', [start, end])
          }
        }]
      },
      autoWidth: true,
      bookType: 'xlsx',
      filename: '',
      tableKey: 0,
      list: null,
      listLoading: true,
      total: 0,
      listQuery: {
        page: 1,
        limit: 10,
        keywords: ''
      },
      protocolList: null

    }
  },
  created() {
    this.getList()
    this.getProtocolList()
  },
  methods: {
    search() {
      this.listQuery.page = 1
      this.getList()
    },
    getList() {
      rechargeList(this.listQuery).then(res => {
        if (res.code === 20000) {
          console.log(res)
          this.list = res.data.data
          this.total = res.data.total
          this.listLoading = false
        }
      })
      this.listLoading = true
    },

    // 获取币种类型
    getProtocolList() {
      getProtocol().then(res => {
        if (res.code === 20000) {
          this.protocolList = res.data
        }
      })
    },
    onReset() {
      this.$router.push({
        path: ''
      })
      this.listQuery = {
        page: 1,
        limit: 20
      }
      this.getList()
    },
    onSubmit() {
      this.listQuery.page = 1
      this.$router.push({
        path: '',
        listQuery: this.listQuery
      })
      this.getList()
    },
    handleDownload() {
      this.listLoading = true
        import('@/vendor/Export2Excel').then(excel => {
          const tHeader = ['序号', '用户名', '转入币种', '转入地址', '转入数量', 'Hash', '转入时间', '状态']
          const filterVal = ['Id', 'NickName', 'EnName', 'Address', 'Money', 'Hash', 'AddTime', 'Status']
          const list = this.list
          const data = this.formatJson(filterVal, list)
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: this.filename,
            autoWidth: this.autoWidth,
            bookType: this.bookType
          })
          this.listLoading = false
          this.$message({
            message: '导出成功',
            type: 'success'
          })
          this.getList()
        })
    },
    // 洗数据
    formatJson(filterVal, jsonData) {
      return jsonData.map(v =>
        filterVal.map(j => {
          if (j === 'Status') {
            if (v[j] === '0') {
              return (v[j] = '未到账')
            } else if (v[j] === '1') {
              return (v[j] = '到账')
            }
          }
          return v[j]
        }),
      )
    }
  }
}
</script>

