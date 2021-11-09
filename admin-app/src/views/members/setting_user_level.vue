<template>
  <div class="app-container">
    <el-form ref="form" :rules="rules" :model="form" status-icon label-width="300px" class="demo-ruleForm">
      <el-form-item>
        <div v-for="(item,index) in form" :key="index" style="margin-left: 2px;position: relative;">
          <b style="color: #FF0000">等级{{ index+1 }}：</b>
          <span>等级名称：</span>          <el-input v-model="item.name" style="width: 10%" label="等级名称" />
          <span>邀请人数：</span>          <el-input v-model="item.invite" style="width: 10%" :min="0" label="邀请人数" />
          <span>购买数量：</span>          <el-input v-model="item.buy" style="width: 10%" :min="0" label="购买数量" />
          <span>手续费：</span>            <el-input v-model="item.rate" style="width: 13%" :min="0" label="手续费"> <template slot="append">%</template></el-input>

          &nbsp;
        </div>
      </el-form-item>
      <el-form-item label="">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </el-form-item>
    </el-form>
  </div>
</template>

<script>
import { SettingUserLevel, EditUserLevel } from '@/api/members.js'
export default {
  data() {
    return {
      listLoading: true,
      list: [],
      form: {
      },
      rules: {
        name: [
          { required: true, message: '请输入等级名称', trigger: 'blur' },
          { min: 3, max: 5, message: '长度在 3 到 5 个字符', trigger: 'blur' }
        ]
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      SettingUserLevel({}).then(res => {
        this.form = res.data
        this.rules = this.addRules
      })
    },
    handleEdit() {
      EditUserLevel(this.form).then(res => {
        if (res.code === 20000) {
          this.$message({
            message: '设置已更新',
            type: 'success'
          })
        }
      })
    }
  }
}
</script>
