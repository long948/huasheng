<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="交易规则" label-width="70px">
        <Tinymce ref="editor" v-model="content" :height="400" />
      </el-form-item>
      <el-button type="primary" @click="handleEdit">确 定</el-button>
    </el-form>
  </div>
</template>

<script>
import { TradeRules, TradeRulesEdit } from '@/api/trade.js'
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
      TradeRules({}).then(res => {
        this.content = res.data.ArticleDetails
      })
    },
    handleEdit() {
      TradeRulesEdit({ content: this.content }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: '交易规则内容已修改'
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    }
  }
}
</script>

