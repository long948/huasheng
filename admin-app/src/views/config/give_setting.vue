<template>
  <div class="app-container">
    <el-form :inline="true" class="demo-form-inline">
      <el-form-item label="系统维护：" label-width="320px">
        <!--<el-input v-model="qiniu.v"/>-->
        <el-radio v-model="qiniu.v" label="0" border>赠送EB</el-radio>
        <el-radio v-model="qiniu.v" label="1" border>取消赠送EB</el-radio>
      </el-form-item>
      <el-form-item label="">
        <el-button type="primary" @click="handleQiniu">确 定</el-button>
      </el-form-item>
      <el-alert
        title="注：点击赠送EB后，将开启会员实名认证通过给上级赠送0.1EB，取消赠送EB将不给与上级0.1EB"
        type="error"
        style="width: 50%"
      />
    </el-form>
  </div>
</template>

<script>
import { GiveSettingList, GiveSetting } from '@/api/config.js'
export default {
  data() {
    return {
      listLoading: true,
      qiniu: {
        v: '',
        k: '',
        system_close_explanation: '',
        remark: ''
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      GiveSettingList().then(res => {
        if (res.code === 20000) {
          this.qiniu.v = res.data.v
          this.qiniu.k = res.data.k
          this.qiniu.give_setting = res.data.give_setting
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    handleQiniu() {
      this.listLoading = true
      GiveSetting(this.qiniu).then(res => {
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
