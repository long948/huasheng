<template>
  <div class="app-container">
    <el-form :inline="true" class="demo-form-inline">
      <el-form-item label="系统维护：" label-width="320px">
        <!--<el-input v-model="qiniu.v"/>-->
        <el-radio v-if="qiniu.v==0" v-model="qiniu.v" label="1" border>开启系统维护</el-radio>
        <el-radio v-if="qiniu.v==1" v-model="qiniu.v" label="1" border>开启系统维护</el-radio>
        <el-radio v-model="qiniu.v" label="0" border>关闭系统维护</el-radio>
      </el-form-item>
      <el-form-item v-if="qiniu.v==1" label="维护说明：">
        <el-input v-model="qiniu.system_close_explanation" type="textarea" autosize style="width: 100%" />
      </el-form-item>
      <!--<el-form-item label="AK" label-width="70px">-->
      <!--<el-input v-model="qiniu.accesskey"/>-->
      <!--</el-form-item>-->
      <!--<el-form-item label="SK" label-width="70px">-->
      <!--<el-input v-model="qiniu.secretkey"/>-->
      <!--</el-form-item>-->

      <el-form-item label="">
        <el-button type="primary" @click="handleQiniu">确 定</el-button>
      </el-form-item>
      <el-alert
        title="注：请谨慎操作，开启系统维护后，用户将不能访问APP"
        type="error"
        style="width: 50%"
      />
    </el-form>
  </div>
</template>

<script>
import { systemList, SystemClose } from '@/api/config.js'
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
      systemList().then(res => {
        if (res.code === 20000) {
          this.qiniu.v = res.data.v
          this.qiniu.k = res.data.k
          this.qiniu.system_close_explanation = res.data.remark
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
      SystemClose(this.qiniu).then(res => {
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
