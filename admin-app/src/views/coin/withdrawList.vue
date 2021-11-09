<template>
  <div class="app-container">

    <div class="filter-container">
      <el-form :inline="true" class="query-form" size="mini">
        <el-form-item class="query-form-item">
          <el-input v-model="listQuery.keywords" placeholder="用户名" />
        </el-form-item>
        <el-form-item class="query-form-item" label="状态">
          <el-select v-model="listQuery.Status" placeholder="状态">
            <el-option label="全部" value="" />
            <el-option label="驳回" value="-1" />
            <el-option label="待处理" value="0" />
            <el-option label="处理中" value="1" />
            <el-option label="已处理" value="2" />
            <el-option label="失败" value="3" />
          </el-select>
        </el-form-item>
        <el-form-item class="query-form-item" label="转出时间范围">
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
            <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
            <el-button type="primary" icon="search" @click="getList">查询</el-button>
          </el-button-group>
        </el-form-item>
        <el-form-item class="query-form-item">
          <div>
            <FilenameOption v-model="filename" />
            <AutoWidthOption v-model="autoWidth" />
            <BookTypeOption v-model="bookType" />
            <el-button :loading="loading" style="margin:0 0 20px 20px;" type="primary" icon="el-icon-document" @click="handleDownload">
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
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="序号" align="center" width="50px">
        <template slot-scope="scope">
          {{ scope.row.Id }}
        </template>
      </el-table-column>
      <el-table-column label="用户名" align="center">
        <template slot-scope="scope">
          <div>{{ scope.row.NickName }}</div>
        </template>
      </el-table-column>
      <el-table-column label="转出币种" align="center" width="80px">
        <template slot-scope="scope">
          {{ scope.row.EnName }}
        </template>
      </el-table-column>
      <el-table-column label="收款地址" align="center" width="200px">
        <template slot-scope="scope">
          {{ scope.row.Address }}
        </template>
      </el-table-column>
      <el-table-column label="转出数量" align="center" width="100px">
        <template slot-scope="scope">
          {{ parseFloat(scope.row.Money) }}
        </template>
      </el-table-column>
      <el-table-column label="真实到账" align="center" width="100px">
        <template slot-scope="scope">
          {{ parseFloat(scope.row.Real) }}
        </template>
      </el-table-column>
      <el-table-column label="Hash" align="center" width="230px">
        <template slot-scope="scope">
          {{ scope.row.Hash }}
        </template>
      </el-table-column>
      <el-table-column label="转出时间" align="center">
        <template slot-scope="scope">
          {{ scope.row.created_at }}
        </template>
      </el-table-column>
      <!--<el-table-column label="审核时间" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.ProcessTime != 0">{{ scope.row.ProcessTime }}</span>-->
      <!--<span v-else>未审核</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column label="处理状态" align="center" width="110px">
        <template slot-scope="scope">
          <span v-if="scope.row.Status == '-1'">驳回</span>
          <span v-if="scope.row.Status == '0'">待处理</span>
          <span v-if="scope.row.Status == '1'">处理中</span>
          <span v-if="scope.row.Status == '2'">已处理</span>
          <span v-if="scope.row.Status == '3'">失败</span>
          <span v-if="scope.row.Status == '4'">直接处理成功</span>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          {{ scope.row.Remark }}
        </template>
      </el-table-column>

      <el-table-column v-permission="['/coin/waitProcess']" label="状态" align="center">
        <template slot-scope="scope">
          <!--<span v-permission="['/coin/waitProcess']">-->
          <el-button size="small" type="primary" @click="showWaitProcess(scope.row)">
            操作
          </el-button>
        </template>
      </el-table-column>

    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="待处理记录" :visible.sync="processVisible">
      <el-form>
        <el-form-item label="收款地址" label-width="150px">
          <el-input v-model="process.Address" :disabled="true" />
        </el-form-item>
        <el-form-item label="转出时间" label-width="150px">
          <span>{{ process.created_at }}</span>
        </el-form-item>
        <el-form-item label="转出币种" label-width="150px">
          <el-input v-model="process.CoinName" :disabled="true" />
        </el-form-item>
        <el-form-item label="转出数量" label-width="150px">
          <el-input v-model="process.Money" :disabled="true" />
        </el-form-item>
        <el-form-item label="手续费" label-width="150px">
          <el-input v-model="process.Fee" :disabled="true" />
        </el-form-item>
        <el-form-item label="预计到账" label-width="150px">
          <el-input v-model="process.Real" :disabled="true" />
        </el-form-item>
        <!--<el-form-item v-if="process.Status === 0 || process.Status === 3" label="Google 验证码" label-width="70px">-->
        <!--<el-input v-model="process.verifycode" />-->
        <!--</el-form-item>-->
        <el-form-item label="区块返回的错误信息" label-width="150px">
          <el-input v-model="process.WithdrawInfo" type="textarea" autosize disabled />
        </el-form-item>
        <el-form-item label="备注" label-width="150px">
          <el-input v-model="process.Remark" type="textarea" disabled autosize />
        </el-form-item>
        <el-form-item label="审核备注" label-width="150px">
          <el-input v-model="process.auth_remark" />
        </el-form-item>
        <el-form-item label="HASH" label-width="150px">
          <span style="color: red">直接处理时填写</span>
          <el-input v-model="process.Hash" />
        </el-form-item>
      </el-form>

      <div v-if="process.Status == 0" slot="footer" class="dialog-footer">
        <el-button type="primary" :loading="processLoading" @click="handleProcess(1)">提交</el-button>
        <el-button type="primary" :loading="processLoading" @click="handleProcess(2)">驳回</el-button>
        <el-button type="primary" :loading="processLoading" @click="handleProcess(3)">直接处理</el-button>
      </div>

    </el-dialog>

  </div>
</template>

<script>
import { withdrawList, getProtocol, getWithdrawCoin, waitProcess } from '@/api/coin'
import waves from '@/directive/waves'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index'
// import FilenameOption from '../coin/components/FilenameOption'
// import AutoWidthOption from '../coin/components/AutoWidthOption'
// import BookTypeOption from '../coin/components/BookTypeOption'
export default {
  // , FilenameOption, AutoWidthOption, BookTypeOption
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
        Status: '0',
        page: 1,
        limit: 10,
        keywords: ''

      },
      protocolList: null,

      processVisible: false,

      process: {
        Hash: '',
        Address: '',
        RPCUser: '',
        AddTime: '',
        CoinName: '',
        Money: '',
        Fee: '',
        Real: '',
        verifycode: '',
        Remark: '',
        WithdrawInfo: '',
        Id: 0,
        created_at: '',
        auth_remark: ''
      },
      processLoading: false

    }
  },
  created() {
    this.getList()
    this.getProtocolList()
  },
  methods: {
    getList() {
      withdrawList(this.listQuery).then(res => {
        if (res.code === 20000) {
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
    // 待处理显示页面
    showWaitProcess(row) {
      getWithdrawCoin({ Id: row.Id }).then(res => {
        if (res.code === 20000) {
          this.process.Hash = res.data.Hash
          this.process.Address = res.data.Address
          this.process.RPCUser = res.data.RPCUser
          this.process.AddTime = res.data.AddTime
          this.process.CoinName = res.data.CoinName
          this.process.Money = res.data.Money
          this.process.Fee = res.data.Fee
          this.process.Real = res.data.Real
          this.process.verifycode = res.data.verifycode
          this.process.Remark = res.data.Remark
          this.process.Status = res.data.Status
          this.process.Id = res.data.Id
          this.process.WithdrawInfo = res.data.WithdrawInfo
          this.process.created_at = res.data.created_at
          this.process.auth_remark = res.data.auth_remark
        }
      })
      this.processVisible = true
    },

    // 待处理逻辑
    handleProcess(type) {
      console.log(this.process)
      console.log('待处理')
      const param = { id: this.process.Id, type: type, verifycode: this.process.verifycode, Hash: this.process.Hash, auth_remark: this.process.auth_remark }
      this.processLoading = true
      waitProcess(param)
        .then(res => {
          this.processLoading = false
          if (res.code === 20000) {
            this.$message({
              type: 'success',
              message: res.msg
            })
            this.processVisible = false
            this.getList()
          }
        })
        .catch(() => {
          this.processLoading = false
        })
    },
    handleDownload() {
      this.loading = true
        import('@/vendor/Export2Excel').then(excel => {
          const tHeader = ['序号', '用户名', '转出币种', '转出地址', '转出数量', '真实到账', 'Hash', '转出时间', '处理状态', '备注']
          const filterVal = ['Id', 'username', 'EnName', 'Address', 'Money', 'Real', 'Hash', 'created_at', 'Status', 'Remark']
          const list = this.list
          const data = this.formatJson(filterVal, list)
          excel.export_json_to_excel({
            header: tHeader,
            data,
            filename: this.filename,
            autoWidth: this.autoWidth,
            bookType: this.bookType
          })
          this.loading = false
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
            if (v[j] === '-1') {
              return (v[j] = '驳回')
            } else if (v[j] === '0') {
              return (v[j] = '待处理')
            } else if (v[j] === '1') {
              return (v[j] = '处理中')
            } else if (v[j] === '2') {
              return (v[j] = '已处理')
            } else if (v[j] === '3') {
              return (v[j] = '失败')
            } else if (v[j] === '4') {
              return (v[j] = '直接处理成功')
            }
          }
          return v[j]
        }),
      )
    }

  }
}
</script>

