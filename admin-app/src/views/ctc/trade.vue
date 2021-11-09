<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.sPhone" placeholder="买家用户手机号" style="width: 200px;" class="filter-item" />
      <el-input v-model="listQuery.Phone" placeholder="卖家用户手机号" style="width: 200px;" class="filter-item" />
      <el-input v-model="listQuery.OrderSn" placeholder="订单号" style="width: 200px;" class="filter-item" clearable />
      <el-input v-model="listQuery.RemarkCode" placeholder="备注码" style="width: 200px;" class="filter-item" clearable />
      <el-button-group>
        <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
        <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="filter">搜索</el-button>
      </el-button-group>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <!--<el-table-column prop="Id" align="center" label="ID" />-->
      <el-table-column prop="OrderSn" align="center" label="订单编号" width="110px" />
      <el-table-column
        label="买家昵称/手机号"
        align="center"
        width="130px"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.BuyMember }}</span>
          <br>
          <span>{{ scope.row.BuyPhone }}</span>
        </template>
      </el-table-column>
      <el-table-column
        label="卖家昵称/手机号"
        align="center"
        width="130px"
      >
        <template slot-scope="scope">
          <span>{{ scope.row.SellMember }}</span>
          <br>
          <span>{{ scope.row.SellPhone }}</span>
        </template>
      </el-table-column>
      <!--<el-table-column prop="Number" align="center" label="交易数量" />-->
      <el-table-column label="数量(PT)" align="center" width="100px">
        <template slot-scope="{ row }">
          {{ parseFloat(row.Number) }}
        </template>
      </el-table-column>
      <el-table-column label="交易单价(USDT)" align="center" width="130px">
        <template slot-scope="{ row }">
          {{ parseFloat(row.Price) }}
        </template>
      </el-table-column>
      <!--<el-table-column prop="Price" align="center" label="交易单价" />-->
      <!--<el-table-column prop="Fee" align="center" label="手续费" />-->
      <el-table-column label="手续费(PT)" align="center" width="110px">
        <template slot-scope="{ row }">
          {{ parseFloat(row.Fee) }}
        </template>
      </el-table-column>
      <el-table-column align="center" label="订单状态" width="110px">
        <template slot-scope="scope">
          <span v-if="scope.row.State == 0">待付款</span>
          <span v-if="scope.row.State == 1">待确认</span>
          <span v-if="scope.row.State == 2">完成</span>
          <span v-if="scope.row.State == 3">取消</span>
          <span v-if="scope.row.State == 4">自动取消</span>
          <span v-if="scope.row.State == 5">冻结</span>
        </template>
      </el-table-column>
      <el-table-column prop="RemarkCode" align="center" label="备注码" width="100px" />
      <el-table-column label="接单时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.AddTime) }}
        </template>
      </el-table-column>

      <el-table-column label="付款时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.PayTime) }}
        </template>
      </el-table-column>
      <el-table-column label="结束时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.FinishTime) }}
        </template>
      </el-table-column>

      <el-table-column label="操作" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.State == 0" v-permission="['/ctc/cancle']">
            <el-button size="small" type="danger" @click="cancle(scope.row.Id)">撤销</el-button>
          </span>
          <span v-permission="['/ctc/cancle']">
            <el-button size="small" type="primary" @click.native="handleForm(scope.$index, scope.row)">查看</el-button>
          </span>
        </template>
      </el-table-column>
    </el-table>

    <div style="float: right">
      <label style="color: red">交易手续费：</label>
      <input v-model="ctc_tx_fee" style="border: none" disabled="true">
    </div>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <!--表单-->
    <el-dialog
      title="付款截图"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="40%"
      top="20vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item v-if="formData.ImgList!=''" prop="">
          <div v-for="(item, index) in formData.ImgListUrl" :key="index" style="display: inline-block;margin-left: 2px;position: relative;max-width: 200px;max-height: 200px;">
            <el-popover
              placement="right"
              title=""
              width="500px"
              trigger="hover"
            >
              <img :src="item" style="width: 450px;height: 780px">
              <img slot="reference" :src="item" :alt="item" style="display: inline-block;margin-left: 2px;position: relative;max-width: 200px;max-height: 200px;">
            </el-popover>
          </div>
        </el-form-item>
        <el-form-item v-if="formData.ImgList==''" prop="">
          <span>暂无截图！</span>
        </el-form-item>
      </el-form>
    </el-dialog>
  </div>
</template>

<script>
import {
  ctclist,
  ctccancle
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
const formJson = {
  Imgs: ''
}
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
      ImgListUrl: [],
      formData: formJson,
      listQuery: {
        page: 1,
        limit: 10,
        State: '',
        Type: '',
        AddTime: [],
        Merchant: '',
        MemberName: '',
        Phone: '',
        OrderNumber: '',
        RemarkCode: ''
      },
      srcList: [

      ],
      formVisible: false
    }
  },
  created() {
    this.getList()
  },
  methods: {
    cancle(Id) {
      this.$confirm('确定撤销这笔交易?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        ctccancle({ Id: Id }).then(res => {
          this.$message({
            message: '交易已终止',
            type: 'success'
          })
          this.getList()
        })
      }).catch(() => {})
    },
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
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
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      if (row !== null) {
        this.formData = Object.assign({}, row)
      }
      this.formName = 'add'
      if (index !== null) {
        this.index = index
        this.formName = 'edit'
      }
    },
    // 隐藏表单
    hideForm() {
      // 更改值
      this.formVisible = !this.formVisible
      this.$refs['dataForm'].resetFields()
      return true
    },
    filter() {
      this.listQuery.page = 1
      this.getList()
    },
    getList() {
      this.listLoading = true
      ctclist(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total
        this.buy = res.data.buy
        this.sell = res.data.sell
        this.ctc_tx_fee = res.data.ctc_tx_fee
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    }
  }
}
</script>
