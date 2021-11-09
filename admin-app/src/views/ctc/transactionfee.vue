<template>
  <div class="app-container">
    <el-form>
      <el-form-item label="交易手续费" label-width="120px">
        <!--<Tinymce ref="editor" v-model="content" :height="400" />-->
        <el-input v-model="content" placeholder="1为100%" style="width: 30%">
          <template slot="append">%</template>
        </el-input>
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </el-form-item>

    </el-form>
  </div>
</template>

<script>
import { TransactionFee, TransactionEdit } from '@/api/trade.js'
// import Tinymce from '@/components/Tinymce'

export default {
  // components: { Tinymce },
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
      TransactionFee({}).then(res => {
        this.content = res.data.v
      })
    },
    handleEdit() {
      TransactionEdit({ content: this.content }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: '交易手续费已修改'
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    }
  }
}
</script>

