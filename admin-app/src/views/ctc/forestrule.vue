<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="森林规则" label-width="70px">
        <Tinymce ref="editor" v-model="content" :height="400" />
      </el-form-item>
      <el-button type="primary" @click="handleEdit">确 定</el-button>
    </el-form>
  </div>
</template>

<script>
import { ForestRule, ForestRulEdit } from '@/api/trade.js'
import Tinymce from '@/components/Tinymce'

export default {
  components: { Tinymce },
  data() {
    return {
      content: '',
      remark: ''
    }
  },
  created() {
    this.getContent()
  },
  methods: {
    getContent() {
      ForestRule({}).then(res => {
        this.content = res.data.v
        this.remark = res.data.remark
      })
    },
    handleEdit() {
      ForestRulEdit({ content: this.content }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: '森林规则内容已修改'
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    }
  }
}
</script>

