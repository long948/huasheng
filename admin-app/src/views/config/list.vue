<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="域名" label-width="70px">
        <el-input v-model="qiniu.domain"/>
      </el-form-item>
      <el-form-item label="Bucket" label-width="70px">
        <el-input v-model="qiniu.bucket"/>
      </el-form-item>
      <el-form-item label="AK" label-width="70px">
        <el-input v-model="qiniu.accesskey"/>
      </el-form-item>
      <el-form-item label="SK" label-width="70px">
        <el-input v-model="qiniu.secretkey"/>
      </el-form-item>
      
      <el-form-item label="" label-width="70px">
        <el-button type="primary" @click="handleQiniu">确 定</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { list,updateAddQiniu } from '@/api/config.js'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
export default {
  data() {
    return {
      listLoading: true,
      qiniu:{
        domain:'',
        bucket:'',
        accesskey:'',
        secretkey:'',
        id:0,
      },
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList(){
      list().then(res => {
        if (res.code == 20000) {
          this.qiniu.id = res.data.Id
          this.qiniu.domain = res.data.Domain
          this.qiniu.bucket = res.data.Bucket
          this.qiniu.accesskey = res.data.AccessKey
          this.qiniu.secretkey = res.data.SecretKey
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    handleQiniu(){
      this.listLoading = true
      updateAddQiniu(this.qiniu).then(res=> {
          if(res.code == 20000){
            this.$message({
              type: 'success',
              message: res.msg
            })
            setTimeout(() => {
              this.listLoading = false
            })
            this.getList()
          }else{
            this.$message({
              type: 'error',
              message: res.msg
            })
          }
      })
    },
  }
}
</script>
