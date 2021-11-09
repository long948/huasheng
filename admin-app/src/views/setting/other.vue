<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="注册送的矿机" label-width="160px">
        <el-input v-model="form.RegProductId" />
      </el-form-item>
      <el-form-item label="交易币种" label-width="160px">
        <el-input v-model="form.TradeCoinId" />
      </el-form-item>
      <el-form-item label="矿机收益最小提现数量" label-width="160px">
        <el-input v-model="form.MinWithdraw" />
      </el-form-item>
      <el-form-item label="矿机收益提现手续费(1为100%)" label-width="150px">
        <el-input v-model="form.WithdrawFee" />
      </el-form-item>
      <el-form-item label="最小购买数量" label-width="160px">
        <el-input v-model="form.MinPurchaseNumber" />
      </el-form-item>
      <el-form-item label="最大购买数量" label-width="160px">
        <el-input v-model="form.MaxPurchaseNumber" />
      </el-form-item>
      <el-form-item label="买单取消时间(分钟)" label-width="160px">
        <el-input v-model="form.TradeCancleTime" />
      </el-form-item>
      <el-form-item label="卖单确认时间(小时)" label-width="160px">
        <el-input v-model="form.AutoConfirm" />
      </el-form-item>
      <el-form-item label="交易币种价格" label-width="160px">
        <el-input v-model="form.TradeCoinPrice" />
      </el-form-item>
      <el-form-item label="空投奖励" label-width="160px">
        <el-input v-model="form.AuthReward" />
      </el-form-item>
      <el-form-item label="交易币种卖出" label-width="160px">
        <el-input v-model="form.TradeCoinSellPrice" />
      </el-form-item>
      <el-form-item label="每人限制购买矿机数量" label-width="160px">
        <el-input v-model="form.LimitNumber" />
      </el-form-item>
      <el-form-item label="" label-width="160px">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { other, editOther } from '@/api/setting.js'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
export default {
  data() {
    return {
      listLoading: true,
      list: [],
      form: {
        RegProductId: '',
        TradeCoinId: '',
        MinWithdraw: '',
        WithdrawFee: '',
        MinPurchaseNumber: '',
        MaxPurchaseNumber: '',
        TradeCancleTime: '',
        AuthReward: '',
        LimitNumber: ''
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      other({}).then(res => {
        this.form = res.data
      })
    },
    handleEdit() {
      editOther(this.form).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '设置已更新',
            type: 'success'
          })
        }
      })
    }
  }
}
</script>
