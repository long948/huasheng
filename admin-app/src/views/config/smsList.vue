<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="账户" label-width="100px">
        <el-input v-model="sms.account" />
      </el-form-item>
      <el-form-item label="密码" label-width="100px">
        <el-input v-model="sms.password" />
      </el-form-item>
      <el-form-item label="签名" label-width="100px">
        <el-input v-model="sms.signName" />
      </el-form-item>
      <el-form-item label="短信类型" label-width="100px">
        <el-select v-model="sms.mold" placeholder="请选择">
          <el-option v-for="(item,index) in options" :key="index" :label="item.title" :value="item.id" />
        </el-select>
      </el-form-item>
      <el-form-item label="短信位数" label-width="100px">
        <el-input v-model="sms.vaildCodeLength" />
      </el-form-item>
      <el-form-item label="短信有效期" label-width="100px">
        <el-input v-model="sms.timeOut" />
      </el-form-item>
      <el-form-item label="" label-width="100px">
        <el-button type="primary" @click="handleSms">确 定</el-button>
      </el-form-item>
    </el-form>
    <el-alert
      title="注：短信有效期，请填入数字。单位分钟"
      type="error"
    />
  </div>
</template>

<script>
import { smsList, updateAddSms, smstype } from '@/api/config.js'
// import waves from '@/directive/waves'
// import { parseTime } from '@/utils'
// import Pagination from '@/components/Pagination'
export default {
  data() {
    return {
      listLoading: true,
      sms: {
        account: '',
        password: '',
        signName: '',
        vaildCodeLength: '',
        timeOut: '',
        errorCount: '',
        mold: 1,
        id: 0
      },
      options: null
    }
  },
  created() {
    this.getList()
    this.getsms()
  },
  methods: {
    getList() {
      smsList().then(res => {
        if (res.code === 20000) {
          this.sms.id = res.data.Id
          this.sms.account = res.data.Account
          this.sms.password = res.data.Password
          this.sms.signName = res.data.SignName
          this.sms.vaildCodeLength = res.data.VaildCodeLength
          this.sms.timeOut = res.data.TimeOut
          this.sms.errorCount = res.data.ErrorCount
          this.sms.mold = Number(res.data.Mold)
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    getsms() {
      this.listLoading = true
      smstype().then(res => {
        if (res.code === 20000) {
          this.options = res.data
          this.listLoading = false
        }
      })
    },
    handleSms() {
      this.listLoading = true
      updateAddSms(this.sms).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          setTimeout(() => {
            this.listLoading = false
          })
          this.getList()
        } else {
          this.$message({
            type: 'error',
            message: res.msg
          })
        }
      })
    }
  }
}
</script>
