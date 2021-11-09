<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="关于我们" label-width="70px">
        <Tinymce ref="editor" v-model="content" :height="400" />
      </el-form-item>
      <el-button type="primary" @click="handleEdit">确 定</el-button>
    </el-form>
  </div>
</template>

<script>
import { AboutUs, AboutUsEdit } from '@/api/bannerNotice.js'
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
      AboutUs({}).then(res => {
        this.content = res.data.AboutUs
      })
    },
    handleEdit() {
      AboutUsEdit({ content: this.content }).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '关于我们内容已修改'
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    }
  }
}
</script>

