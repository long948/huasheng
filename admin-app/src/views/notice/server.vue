<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="QQ" label-width="70px">
        <el-input v-model="form.QQ" />
      </el-form-item>
      <el-form-item label="微信" label-width="70px">
        <el-input v-model="form.Wechat" />
      </el-form-item>
      <el-form-item label="" label-width="70px">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </el-form-item>
    </el-form>

  </div>
</template>

<script>
import { server, editServer } from '@/api/setting.js'
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
        TradeCancleTime: ''
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      server({}).then(res => {
        this.form = res.data
      })
    },
    handleEdit() {
      editServer(this.form).then(res => {
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
