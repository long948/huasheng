<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      style="width: 100%;"
    >
      <el-table-column
        label="配置名称"
        prop="remark"
        width="400px"
        align="center"
      />
      <el-table-column
        label="值/内容"
        prop="v"
        :show-overflow-tooltip="true"
        align="center"
      />
      <el-table-column
        label="操作"
        align="center"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">配置
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="50%"
      top="20vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item v-if="formData.k==='activity_order_time_out' || formData.k==='activity_time_out'" :label="formData.remark" prop="v" label-width="180px">
          <el-input v-model="formData.v" autosize controls-position="right" style="width: 60%">
            <template slot="append">秒</template>
          </el-input>
          <span style="color: red">*</span>
        </el-form-item>
        <el-form-item v-if="formData.k==='shop_rule'" :label="formData.remark" label-width="150px">
          <Tinymce ref="editor" v-model="formData.v" :height="400" />
        </el-form-item>
        <!--        拼购推荐奖励-->
        <template v-if="formData.k==='shop_reward_rule'">
          <el-row>
            <el-col :span="24">
              <el-table size="mini" :data="rule" border style="width: 100%" highlight-current-row>
                <el-table-column label="代数" width="150" align="center" type="index" />
                <el-table-column v-for="(item) in master_user.columns" align="center" :label="item.label" :prop="item.prop" :width="item.width">
                  <template slot-scope="scope">
                    <span v-if="scope.row.isSet">
                      <el-input v-model="sel[item.prop]" size="mini" placeholder="请输入内容" />
                    </span>
                    <span v-else>{{ scope.row[item.prop] }}</span>
                  </template>
                </el-table-column>
                <el-table-column label="操作" width="">
                  <template slot-scope="scope">
                    <span class="el-tag el-tag--success el-tag--mini" style="cursor: pointer;" @click.stop="saveRow(scope.row,scope.$index)">
                      确定
                    </span>
                    <span class="el-tag el-tag--primary el-tag--mini" style="cursor: pointer;" @click="editRow(scope.row,scope.$index)">
                      编辑
                    </span>
                    <span class="el-tag el-tag--danger el-tag--mini" style="cursor: pointer;" @click="deleteRow(scope.$index,rule,scope.row)">
                      删除
                    </span>
                  </template>
                </el-table-column>
              </el-table>
            </el-col>
            <el-col :span="24">
              <div class="el-table-add-row" style="width: 99.2%;" @click="add()"><span>+ 添加</span></div>
            </el-col>
          </el-row>
        </template>
      </el-form>
      <div slot="footer" class="dialog-footer" align="center">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  getOrderSetting,
  updateOrderSetting
} from '@/api/market'
import Tinymce from '@/components/Tinymce'
const formJson = {
  k: '',
  v: '',
  remark: ''
}
export default {
  components: {
    Tinymce
  },
  data() {
    return {
      sel: null, // 选中行
      master_user: {
        columns: [
          //   {
          //   prop: 'id',
          //   label: '代数',
          //   width: 150,
          // },
          {
            prop: 'val',
            label: '奖励比例（1表示100%）',
            width: 250
          }
        ],
        data: []
      },
      query: {
        page: 1,
        limit: 30
      },
      listPop: [],
      rule: [],
      list: [],
      total: 0,
      loading: true,
      index: null,
      formName: null,
      formMap: {
        edit: '配置'
      },
      rules: {},
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false
    }
  },
  mounted() {},
  created() {
    // 加载表格数据
    this.getList()
  },
  methods: {
    add() {
      const strList = JSON.stringify(this.rule)
      this.listPop = JSON.parse(strList).pop() // 获取res.data数组的最后一位
      console.log(this.listPop.id++)
      for (const i of this.rule) {
        if (i.isSet) return this.$message.warning('请先保存当前编辑项')
      }
      const j = {
        'id': this.listPop.id++,
        'val': '',
        'isSet': true
      }
      this.rule.push(j)
      this.sel = JSON.parse(JSON.stringify(j))
    },
    saveRow(row, index) { // 保存
      const data = JSON.parse(JSON.stringify(this.sel))
      for (const k in data) {
        row[k] = data[k] // 将sel里面的value赋值给这一行
      }
      row.isSet = false
    },
    editRow(row) { // 编辑
      for (const i of this.rule) {
        if (i.isSet) return this.$message.warning('请先保存当前编辑项')
      }
      this.sel = row
      row.isSet = true
    },
    deleteRow(index, rows, row) { // 删除
      // console.log(row)
      const List = JSON.stringify(this.rule)
      this.last = JSON.parse(List).pop() // 获取res.data数组的最后一位
      console.log(this.last.id)
      if (row.id < this.last.id) return this.$message.warning('请按层级以此往上删除')
      if (rows.length <= 1) return this.$message.warning('无法删除')
      rows.splice(index, 1)
    },
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
        page: 1,
        limit: 30
      }
      this.getList()
    },
    getList() {
      this.loading = true
      getOrderSetting(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.data || []
          this.rule = response.data.rule || []
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.total = 0
        })
    },
    // 刷新表单
    resetForm() {
      if (this.$refs['dataForm']) {
        // 清空验证信息表单
        this.$refs['dataForm'].clearValidate()
        // 刷新表单
        this.$refs['dataForm'].resetFields()
      }
    },
    // 隐藏表单
    hideForm() {
      // 更改值
      this.formVisible = !this.formVisible
      this.formData = JSON.parse(JSON.stringify(formJson))
      // 清空表单
      this.$refs['dataForm'].resetFields()
      return true
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      if (row !== null) {
        this.formData = Object.assign({}, row)
      }
      this.formName = 'add'
      this.formRules = this.addRules
      if (index !== null) {
        this.index = index
        this.formName = 'edit'
      }
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          var data = Object.assign({}, this.formData)
          var k = data.k
          if (data.k === 'shop_reward_rule') { data = this.rule }
          for (const i of this.rule) {
            if (i.isSet) {
              this.formLoading = false
              return this.$message.warning('请先保存当前编辑项')
            }
          }
          updateOrderSetting({ data: data, k: k }).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            // 刷新表单
            this.getList()
            this.resetForm()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    }
  }
}
</script>

<style type="text/scss" lang="scss">
  .el-table-add-row {
    margin-top: 10px;
    width: 100%;
    height: 34px;
    border: 1px dashed #c1c1cd;
    border-radius: 3px;
    cursor: pointer;
    justify-content: center;
    display: flex;
    line-height: 34px;
  }
</style>
