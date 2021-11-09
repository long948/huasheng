<template>
  <div class="app-container">
    <el-form label-width="500px">
      <el-form-item label="最低限额">
        <el-input v-model="form.MinMoney" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="最高限额">
        <el-input v-model="form.MaxMoney" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="个人挂单数量上限">
        <el-input v-model="form.MaxSellOrder" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="个人出售单数量上限">
        <el-input v-model="form.MaxTrade" type="number" style="width: 30%" />
      </el-form-item>
      <!--<el-form-item label="卖单手续费(1为100%)">-->
      <!--<el-input v-model="form.SellFee" type="number" style="width: 30%" />-->
      <!--</el-form-item>-->
      <!--<el-form-item label="收款手续费(1为100%)">-->
      <!--<el-input v-model="form.RecvFee" type="number" style="width: 30%" />-->
      <!--</el-form-item>-->
      <el-form-item label="超时取消时间(秒)">
        <el-input v-model="form.CancleTime" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="超时冻结时间(秒)">
        <el-input v-model="form.FrozenTime" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="是否支持银行卡">
        <el-select v-model="form.IsBank" style="width: 160px" class="filter-item">
          <el-option label="支持" :value="1" />
          <el-option label="不支持" :value="0" />
        </el-select>
      </el-form-item>
      <el-form-item label="是否支持微信支付">
        <el-select v-model="form.IsWechat" style="width: 160px" class="filter-item">
          <el-option label="支持" :value="1" />
          <el-option label="不支持" :value="0" />
        </el-select>
      </el-form-item>
      <el-form-item label="是否支持支付宝">
        <el-select v-model="form.IsAlipay" style="width: 160px" class="filter-item">
          <el-option label="支持" :value="1" />
          <el-option label="不支持" :value="0" />
        </el-select>
      </el-form-item>
      <el-form-item label="交易开始时间">
        <el-input v-model="form.start_time" type="number" style="width: 30%" />
      </el-form-item>
      <el-form-item label="交易结束时间">
        <el-input v-model="form.end_time" type="number" style="width: 30%" />
      </el-form-item>
      <el-alert
        title="注：交易开始/结束时间（0表示凌晨12点，6表示早上6点，13表示下午1点，22表示晚上10点）以此类推。"
        type="error"
      />
      <el-form-item label="">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { ctc, editCTC } from '@/api/setting.js'
// // import waves from '@/directive/waves'
// import { parseTime } from '@/utils'
// import Pagination from '@/components/Pagination'
export default {
  data() {
    return {
      listLoading: true,
      list: [],
      form: {

      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      ctc({}).then(res => {
        this.form = res.data
      })
    },
    handleEdit() {
      editCTC(this.form).then(res => {
        if (res.code === 20000) {
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
