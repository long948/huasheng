<template>
  <div style="display:inline-block;">
    <label class="radio-label" style="padding-left:0; color: #606266">导出记录: </label>
    <el-input v-model="filename" placeholder="文件名 (默认为订单)" style="width:180px;" prefix-icon="el-icon-document" />
  </div>
</template>

<script>
export default {
  props: {
    value: {
      type: String,
      default: ''
    }
  },
  computed: {
    filename: {
      get() {
        return this.value
      },
      set(val) {
        this.$emit('input', val)
      }
    }
  }
}
</script>
