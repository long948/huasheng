<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="用户协议" label-width="70px">
        <Tinymce ref="editor" v-model="content" :height="400" />
      </el-form-item>
      <el-button type="primary" @click="handleEdit">确 定</el-button>
    </el-form>
  </div>
</template>

<script>
import { MemberDoc, MemberDocEdit } from '@/api/bannerNotice.js'
import Tinymce from '@/components/Tinymce'

export default {
  components: { Tinymce },
  data() {
    return {
      content: ''
    }
  },
  created() {
    this.getContent()
  },
  methods: {
    getContent() {
      MemberDoc({}).then(res => {
        this.content = res.data.articlelist
      })
    },
    handleEdit() {
      MemberDocEdit({ content: this.content }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: '用户协议已修改'
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    }
  }
}
</script>

