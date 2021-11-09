<template>
  <div class="app-container">

    <div class="filter-container">
      <el-button v-waves class="filter-item" type="primary" @click="btnAdd()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加币种
      </el-button>
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
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="协议" align="center">
        <template slot-scope="scope">
          <span v-for="(item,index) in protocolList" v-if="item.id == scope.row.Protocol" :key="index">{{ item.name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="名称" align="center">
        <template slot-scope="scope">
          {{ scope.row.Name }}
        </template>
      </el-table-column>
      <el-table-column label="英文简称" align="center">
        <template slot-scope="scope">
          {{ scope.row.EnName }}
        </template>
      </el-table-column>
      <el-table-column label="英文全称" align="center">
        <template slot-scope="scope">
          {{ scope.row.FullName }}
        </template>
      </el-table-column>
      <el-table-column label="Logo" align="center">
        <template slot-scope="scope">
          <img :src="scope.row.Logo" width="60px">
        </template>
      </el-table-column>
      <el-table-column label="是否允许提现" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsWithDraw == 0">否</span>
          <span v-if="scope.row.IsWithDraw == 1">是</span>
        </template>
      </el-table-column>
      <el-table-column label="钱包服务器地址" align="center">
        <template slot-scope="scope">
          {{ scope.row.RPCServer }}
        </template>
      </el-table-column>
      <el-table-column label="钱包账户" align="center">
        <template slot-scope="scope">
          {{ scope.row.RPCUser }}
        </template>
      </el-table-column>
      <el-table-column label="钱包密码" align="center">
        <template slot-scope="scope">
          {{ scope.row.RPCPassword }}
        </template>
      </el-table-column>

      <el-table-column v-permission="['/coin/coinUpdate']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/coin/coinUpdate']">
              <el-button size="small" type="primary" @click="iconUpdateHandle(scope.row)">编辑</el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="title" :visible.sync="CoinAddVisible">
      <el-form>
        <el-form-item label="协议类型" label-width="130px">
          <el-select v-model="coin.Protocol" placeholder="请选择">
            <el-option v-for="(item,index) in protocolList" :key="index" :label="item.name" :value="item.id" />
          </el-select>
        </el-form-item>
        <el-form-item label="币种中文名称" label-width="130px">
          <el-input v-model="coin.Name" />
        </el-form-item>
        <el-form-item label="币种英文名称" label-width="130px">
          <el-input v-model="coin.EnName" />
        </el-form-item>
        <el-form-item label="英文全称" label-width="130px">
          <el-input v-model="coin.FullName" />
        </el-form-item>
        <el-form-item label="当前市场价格" label-width="130px">
          <el-input v-model="coin.Price" />
        </el-form-item>
        <el-form-item label="简介" label-width="130px">
          <el-input v-model="coin.Description" type="textarea" />
        </el-form-item>
        <el-form-item label="发币时间" class="postInfo-container-item" label-width="130px">
          <el-date-picker v-model="coin.PublishTime" type="datetime" format="yyyy-MM-dd HH:mm:ss" placeholder="请选择发币时间" />
        </el-form-item>
        <el-form-item label="发行数量" label-width="130px">
          <el-input v-model="coin.PublishNum" />
        </el-form-item>
        <el-form-item label="流通数量" label-width="130px">
          <el-input v-model="coin.CirculationNum" />
        </el-form-item>
        <el-form-item label="众筹价格" label-width="130px">
          <el-input v-model="coin.CrowdPrice" />
        </el-form-item>
        <el-form-item label="白皮书地址" label-width="130px">
          <el-input v-model="coin.WhitePaper" />
        </el-form-item>
        <el-form-item label="官网" label-width="130px">
          <el-input v-model="coin.WebUrl" />
        </el-form-item>
        <el-form-item label="区块浏览器地址" label-width="130px">
          <el-input v-model="coin.Browser" />
        </el-form-item>
        <el-form-item label="提现手续费" label-width="130px">
          <el-input v-model="coin.WithDrawFee" />
        </el-form-item>
        <el-form-item label="最低提现手续费" label-width="130px">
          <el-input v-model="coin.MinWithDrawFee" />
        </el-form-item>
        <el-form-item label="是否可提现" label-width="130px">
          <el-radio v-model="coin.IsWithDraw" label="1">是</el-radio>
          <el-radio v-model="coin.IsWithDraw" label="0">否</el-radio>
        </el-form-item>
        <el-form-item label="是否可充值" label-width="130px">
          <el-radio v-model="coin.IsRecharge" label="1">是</el-radio>
          <el-radio v-model="coin.IsRecharge" label="0">否</el-radio>
        </el-form-item>
        <el-form-item label="是否自动处理" label-width="130px">
          <el-radio v-model="coin.IsAutoWithDraw" label="1">是</el-radio>
          <el-radio v-model="coin.IsAutoWithDraw" label="0">否</el-radio>
        </el-form-item>
        <el-form-item label="状态，是否启用" label-width="130px">
          <el-radio v-model="coin.Status" label="1">是</el-radio>
          <el-radio v-model="coin.Status" label="0">否</el-radio>
        </el-form-item>
        <el-form-item label="钱包服务器" label-width="130px">
          <el-input v-model="coin.RPCServer" />
        </el-form-item>
        <el-form-item label="钱包账号" label-width="130px">
          <el-input v-model="coin.RPCUser" />
        </el-form-item>
        <el-form-item label="钱包密码" label-width="130px">
          <el-input v-model="coin.RPCPassword" />
        </el-form-item>
        <el-form-item label="最低提现数量" label-width="130px">
          <el-input v-model="coin.MinWithDraw" />
        </el-form-item>
        <el-form-item label="最低充值数量" label-width="130px">
          <el-input v-model="coin.MinRecharge" />
        </el-form-item>
        <el-form-item label="最大提现数量" label-width="130px">
          <el-input v-model="coin.MaxWithDraw" />
        </el-form-item>
        <el-form-item label="币种保留几位小数" label-width="130px">
          <el-input v-model="coin.Fixed" />
        </el-form-item>
        <el-form-item label="充值文字描述" label-width="130px">
          <el-input v-model="coin.RechargeInfo" type="textarea" />
        </el-form-item>
        <el-form-item label="提现文字描述" label-width="130px">
          <el-input v-model="coin.WithDrawInfo" type="textarea" />
        </el-form-item>
        <el-form-item label="网络确认次数" label-width="130px">
          <el-input v-model="coin.Confirms" />
        </el-form-item>
        <el-form-item label="钱包链接" label-width="130px">
          <el-input v-model="coin.WalletUrl" />
        </el-form-item>
        <el-form-item label="手机钱包地址" label-width="130px">
          <el-input v-model="coin.MobileWalletUrl" />
        </el-form-item>
        <el-form-item label="前端排序" label-width="130px">
          <el-input v-model="coin.Sort" />
        </el-form-item>
        <el-form-item label="主地址" label-width="130px">
          <el-input v-model="coin.MainAddress" />
        </el-form-item>
        <el-form-item label="合约地址" label-width="130px">
          <el-input v-model="coin.Ext" />
        </el-form-item>
        <el-form-item label="保留长度" label-width="130px">
          <el-input v-model="coin.Decimals" />
        </el-form-item>
        <el-form-item label="Logo" label-width="130px">
          <Upload v-model="coin.Logo" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { coinList, coinAdd, getProtocol, getCoin, coinUpdate } from '@/api/coin.js'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Upload from '@/components/Upload/SingleImage3'

export default {
  components: { Pagination, Upload },
  directives: { waves, permission },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      total: 0,
      listQuery: {
        page: 1,
        limit: 10
      },

      coin: {
        Id: 0,
        Name: null,
        EnName: null,
        FullName: null,
        Logo: null,
        Price: null,
        Description: null,
        PublishTime: null,
        PublishNum: null,
        CirculationNum: null,
        CrowdPrice: null,
        WhitePaper: null,
        WebUrl: null,
        Browser: null,
        WithDrawFee: null,
        MinWithDrawFee: null,
        IsWithDraw: '0',
        IsRecharge: '0',
        IsAutoWithDraw: '0',
        Status: '0',
        RPCServer: null,
        RPCUser: null,
        RPCPassword: null,
        MinWithDraw: null,
        MinRecharge: null,
        MaxWithDraw: null,
        Fixed: 2,
        RechargeInfo: null,
        WithDrawInfo: null,
        Confirms: 2,
        WalletUrl: null,
        MobileWalletUrl: null,
        Protocol: 1,
        Sort: 10,
        MainAddress: null,
        Ext: null,
        Decimals: 2,
        Logo: ''
      },

      CoinAddVisible: false,
      protocolList: null,

      title: ''

    }
  },
  created() {
    this.getList()
    this.getProtocolList()
  },
  methods: {
    getList() {
      coinList(this.listQuery).then(res => {
        if (res.code == 20000) {
          this.list = res.data.data
          this.total = res.data.total
          this.listLoading = false
        }
      })
      this.listLoading = true
    },

    // 显示添加币种界面
    btnAdd() {
      this.title = '添加币种'
      this.coin.Id = 0
      this.coin.Name = ''
      this.coin.EnName = ''
      this.coin.FullName = ''
      this.coin.Logo = ''
      this.coin.Price = ''
      this.coin.Description = ''
      this.coin.PublishTime = ''
      this.coin.PublishNum = ''
      this.coin.CirculationNum = ''
      this.coin.CrowdPrice = ''
      this.coin.WhitePaper = ''
      this.coin.WebUrl = ''
      this.coin.Browser = ''
      this.coin.WithDrawFee = ''
      this.coin.MinWithDrawFee = ''
      this.coin.IsWithDraw = '0'
      this.coin.IsRecharge = '0'
      this.coin.IsAutoWithDraw = '0'
      this.coin.Status = '0'
      this.coin.RPCServer = ''
      this.coin.RPCUser = ''
      this.coin.RPCPassword = ''
      this.coin.MinWithDraw = ''
      this.coin.MinRecharge = ''
      this.coin.MaxWithDraw = ''
      this.coin.Fixed = 2
      this.coin.RechargeInfo = ''
      this.coin.WithDrawInfo = ''
      this.coin.Confirms = 2
      this.coin.WalletUrl = ''
      this.coin.MobileWalletUrl = ''
      this.coin.Protocol = 1
      this.coin.Sort = 10
      this.coin.MainAddress = ''
      this.coin.Ext = ''
      this.coin.Decimals = 2
      this.CoinAddVisible = true
    },

    // 获取币种类型
    getProtocolList() {
      getProtocol().then(res => {
        if (res.code == 20000) {
          this.protocolList = res.data
        }
      })
    },

    // 修改币种
    iconUpdateHandle(row) {
      this.title = '修改币种'
      this.coin.Id = row.Id
      getCoin(row.Id).then(res => {
        if (res.code == 20000) {
          this.coin.Name = res.data.Name
          this.coin.EnName = res.data.EnName
          this.coin.FullName = res.data.FullName
          this.coin.Logo = res.data.Logo ? res.data.Logo : ''
          this.coin.Price = res.data.Price
          this.coin.Description = res.data.Description
          this.coin.PublishTime = res.data.PublishTime
          this.coin.PublishNum = res.data.PublishNum
          this.coin.CirculationNum = res.data.CirculationNum
          this.coin.CrowdPrice = res.data.CrowdPrice
          this.coin.WhitePaper = res.data.WhitePaper
          this.coin.WebUrl = res.data.WebUrl
          this.coin.Browser = res.data.Browser
          this.coin.WithDrawFee = res.data.WithDrawFee
          this.coin.MinWithDrawFee = res.data.MinWithDrawFee ? res.data.MinWithDrawFee : '0'
          this.coin.IsWithDraw = res.data.IsWithDraw ? res.data.IsWithDraw : '0'
          this.coin.IsRecharge = res.data.IsRecharge ? res.data.IsRecharge : '0'
          this.coin.IsAutoWithDraw = res.data.IsAutoWithDraw ? res.data.IsAutoWithDraw : '0'
          this.coin.Status = res.data.Status
          this.coin.RPCServer = res.data.RPCServer
          this.coin.RPCUser = res.data.RPCUser
          this.coin.RPCPassword = res.data.RPCPassword
          this.coin.MinWithDraw = res.data.MinWithDraw
          this.coin.MinRecharge = res.data.MinRecharge
          this.coin.MaxWithDraw = res.data.MaxWithDraw
          this.coin.Fixed = res.data.Fixed
          this.coin.RechargeInfo = res.data.RechargeInfo
          this.coin.WithDrawInfo = res.data.WithDrawInfo
          this.coin.Confirms = res.data.Confirms
          this.coin.WalletUrl = res.data.WalletUrl
          this.coin.MobileWalletUrl = res.data.MobileWalletUrl
          this.coin.Protocol = res.data.Protocol ? Number(res.data.Protocol) : ''
          this.coin.Sort = res.data.Sort
          this.coin.MainAddress = res.data.MainAddress
          this.coin.Ext = res.data.Ext
          this.coin.Decimals = res.data.Decimals
          this.CoinAddVisible = true
        }
      })
    },

    // 添加修改币种
    handleAdd() {
      console.log(this.coin)
      if (this.coin.Id) {
        coinUpdate(this.coin).then(res => {
          if (res.code == 20000) {
            this.$message({
              message: '更新成功',
              type: 'success'
            })
            this.getList()
            this.CoinAddVisible = false
          }
        })
      } else {
        coinAdd(this.coin).then(res => {
          if (res.code == 20000) {
            this.$message({
              message: '新增成功',
              type: 'success'
            })
            this.getList()
            this.CoinAddVisible = false
          }
        })
      }
    }

  }
}
</script>

