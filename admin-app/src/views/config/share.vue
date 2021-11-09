<template>
  <div class="app-container">
    <el-form :inline="true" class="demo-form-inline">
      <el-form-item label="分享海报背景图A模板：" label-width="350px">
        <el-upload

          class="avatar-uploader"
          action="https://up-z2.qiniup.com"
          :show-file-list="false"
          :on-success="handleAvatarSuccess"
          :data="{ token: qiniu.Token }"
        >
          点击更换
          <img v-if="imageUrl" :src="imageUrl" class="avatar">
          <img v-else :src="app.posters_background_a" class="avatar">
          <!--<i v-else class="el-icon-plus avatar-uploader-icon" />-->
        </el-upload>
      </el-form-item>
      <el-form-item label="分享海报背景图B模板：" label-width="350px">
        <el-upload
          class="avatar-uploader"
          action="https://up-z2.qiniup.com"
          :show-file-list="false"
          :on-success="handleAvatarSuccess1"
          :data="{ token: qiniu.Token }"
        >
          点击更换
          <img v-if="imageUrls" :src="imageUrls" class="avatar">
          <img v-else :src="app.posters_background_b" class="avatar">
          <!--<i v-else class="el-icon-plus avatar-uploader-icon" />-->
        </el-upload>
      </el-form-item>
      <br>
      <el-button style="float: right" type="primary" @click="handleApp">确 定</el-button>
    </el-form>
  </div>
</template>

<script>
import { share, updateShare } from '@/api/config'
import {
  qiniuToken
} from '@/api/dateHandle'
export default {
  data() {
    return {
      listLoading: true,
      qiniu: {},
      imageUrl: '',
      imageUrls: '',
      app: {
        posters_background_a: '',
        posters_background_b: ''
      },
      options: null
    }
  },
  created() {
    this.share()
    this.qiniuGet()
  },
  methods: {
    share() {
      share().then(res => {
        if (res.code === 20000) {
          this.app = res.data
          console.log(this.app)
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    handleApp() {
      this.listLoading = true
      updateShare(this.app).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          setTimeout(() => {
            this.listLoading = false
          })
          this.share()
        } else {
          this.$message({
            type: 'error',
            message: res.msg
          })
        }
      })
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.app.posters_background_a = this.qiniu.Domain + res.key
    },
    handleAvatarSuccess1(res, file) {
      this.imageUrls = URL.createObjectURL(file.raw)
      this.app.posters_background_b = this.qiniu.Domain + res.key
    },
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    }
  }
}
</script>
<style>
  .avatar-uploader .el-upload {
    margin-left: 20px;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 280px;
    height: 350px;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }
  .avatar {
    width: 260px;
    height: 340px;

  }

  .el-table--border:after, .el-table--group:after, .el-table:before{
    z-index: 0;
  }
</style>
