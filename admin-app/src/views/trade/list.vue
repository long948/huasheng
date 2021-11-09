<template>
  <div class="app-container">
    <div class="filter-container">
      <el-select v-model="listQuery.State" placeholder="订单状态" clearable style="width: 150px" class="filter-item">
        <el-option label="全部" :value="0" />
        <el-option label="待付款" :value="1" />
        <el-option label="已付款" :value="2" />
        <el-option label="已完成" :value="3" />
        <el-option label="已取消" :value="4" />
      </el-select>
      <el-select v-model="listQuery.Type" placeholder="订单类型" clearable style="width: 150px" class="filter-item">
        <el-option label="用户买" :value="1" />
        <el-option label="用户卖" :value="2" />
      </el-select>
      <span style="font-size:14px;vertical-align:middle;color:#606266;">下单时间: </span>&nbsp;
      <datePicker v-model="listQuery.AddTime" class="filter-item" />
      <el-input v-model="listQuery.Merchant" placeholder="商户名" style="width: 200px;" class="filter-item" clearable />
      <el-input v-model="listQuery.MemberName" placeholder="用户昵称" style="width: 200px;" class="filter-item" clearable />
      <el-input v-model="listQuery.Phone" placeholder="用户手机号" style="width: 200px;" class="filter-item" clearable />
      <el-input v-model="listQuery.OrderNumber" placeholder="订单号" style="width: 200px;" class="filter-item" clearable />
      <el-input v-model="listQuery.RemarkCode" placeholder="备注码" style="width: 200px;" class="filter-item" clearable />

      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
    </div>
    <div>
      <p>累计购买: {{ buy }}</p>
      <p>累计卖出: {{ sell }}</p>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column prop="Id" align="center" label="ID" />
      <el-table-column prop="Phone" align="center" label="用户手机号" />
      <el-table-column prop="NickName" align="center" label="用户昵称" />
      <el-table-column prop="MerchantName" align="center" label="商户名" />
      <el-table-column align="center" label="类型">
        <template slot-scope="scope">
          <span v-if="scope.row.Type == 1">用户买</span>
          <span v-if="scope.row.Type == 2">用户卖</span>
        </template>
      </el-table-column>
      <el-table-column prop="Number" align="center" label="订单数量" />
      <el-table-column align="center" label="收款方式">
        <template slot-scope="scope">
          <span v-if="scope.row.PayMethod == 1">银行卡</span>
          <span v-if="scope.row.PayMethod == 2">支付宝</span>
          <span v-if="scope.row.PayMethod == 3">微信</span>
        </template>
      </el-table-column>
      <el-table-column prop="Account" align="center" label="收款账号" />
      <el-table-column prop="PayPrice" align="center" label="订单金额" />
      <el-table-column prop="RemarkCode" align="center" label="备注码" />
      <el-table-column prop="OrderNumber" align="center" label="订单号" />
      <el-table-column label="下单时间" align="center">
        <template slot-scope="{ row }">
          {{ _date(row.AddTime) }}
        </template>
      </el-table-column>
      <el-table-column label="付款时间" align="center">
        <template slot-scope="{ row }">
          <span v-if="row.PayTime > 0">
            {{ _date(row.PayTime) }}
          </span>
        </template>
      </el-table-column>
      <el-table-column label="完成时间/取消时间" align="center">
        <template slot-scope="{ row }">
          <span v-if="row.FinishTime > 0">
            {{ _date(row.FinishTime) }}
          </span>
        </template>
      </el-table-column>
      <el-table-column align="center" label="订单状态">
        <template slot-scope="scope">
          <span v-if="scope.row.State == 1">待付款</span>
          <span v-if="scope.row.State == 2">已付款</span>
          <span v-if="scope.row.State == 3">已完成</span>
          <span v-if="scope.row.State == 4">已取消</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" align="center" width="300">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/members/subList']">
              <el-button v-if="scope.row.Type == 1 && scope.row.State == 2" size="small" type="primary" @click="confirm(scope.row.Id)">
                确认收款
              </el-button>
              <el-button v-if="scope.row.Type == 2 && scope.row.State == 1" size="small" type="primary" @click="pay(scope.row.Id)">
                我已支付
              </el-button>
              <el-button v-if="scope.row.Type == 2 && scope.row.State == 1" size="small" type="primary" @click="cancle(scope.row.Id)">
                取消订单
              </el-button>
              <el-button size="small" type="primary" @click="detail(scope.row.Id)">
                详情
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="详情" :visible.sync="detailView">
      <p><span>下单时间 : &nbsp;{{ detailData.AddTime }}</span></p>
      <p><span>商户名字 : &nbsp;{{ detailData.MerchantName }}</span></p>
      <p><span>交易数量 : &nbsp;{{ detailData.Number }} {{ detailData.CoinName }}</span></p>
      <p><span>交易类型 : &nbsp;
        <span v-if="detailData.Type == 1">用户买</span>
        <span v-if="detailData.Type == 2">用户卖</span>
      </span></p>
      <p><span>订单号 : &nbsp;{{ detailData.OrderNumber }}</span></p>
      <p><span>支付价格 : &nbsp;{{ detailData.PayPrice }}元</span></p>
      <p><span>支付时间 : &nbsp;{{ detailData.PayTime }}</span></p>
      <p><span>备注码 : &nbsp;{{ detailData.RemarkCode }}</span></p>
      <p><span>订单状态 : &nbsp;
        <span v-if="detailData.State == 1">待付款</span>
        <span v-if="detailData.State == 2">已付款</span>
        <span v-if="detailData.State == 3">已完成</span>
        <span v-if="detailData.State == 4">已取消</span>
      </span></p>
      <p><span>支付方式 : &nbsp;
        <span v-if="detailData.PayMethod == 1">银行卡</span>
        <span v-if="detailData.PayMethod == 2">支付宝</span>
        <span v-if="detailData.PayMethod == 3">微信</span>
      </span></p>
      <p><span>完成时间 : &nbsp;{{ detailData.FinishTime }}</span></p>
      <p v-if="detailData.Type == 2">
        <span v-if="detailData.PayMethod ==1">卡号 : &nbsp;{{ detailData.Pay }}</span>
        <span v-else>收款码 : &nbsp;<img :src="detailData.Pay"></span>
      </p>
      <p v-if="detailData.Type == 2">
        <span v-if="detailData.PayMethod == 1">开户行 : &nbsp;{{ detailData.Bank }}</span>
      </p>
      <p v-if="detailData.Type == 2">
        <span>收款人 : &nbsp;{{ detailData.RecName }}</span>
      </p>
    </el-dialog>
  </div>
</template>

<script>
import {
  list,
  confirm,
  pay,
  cancle,
  detail
} from '@/api/trade'

import waves from '@/directive/waves'
import {
  parseTime
} from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import datePicker from '@/components/Upload/datePicker'
import {
  dateHandle
} from '@/api/dateHandle'

export default {
  components: {
    Pagination,
    datePicker
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
      detailView: false,
      detailData: {

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
      list(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total
        this.buy = res.data.buy
        this.sell = res.data.sell
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    confirm(id) {
      confirm({ Id: id }).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '已确认收款'
          })
          this.getList()
        }
      })
    },
    pay(id) {
      pay({ Id: id }).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '已确认支付'
          })
          this.getList()
        }
      })
    },
    cancle(id) {
      cancle({ Id: id }).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '已取消订单'
          })
          this.getList()
        }
      })
    },
    detail(id) {
      detail({ Id: id }).then(res => {
        if (res.code == 20000) {
          this.detailData = res.data
          this.detailView = true
        }
      })
    }
  }
}
</script>
