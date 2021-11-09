<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/rule/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加权限
      </el-button>
      <!--<el-button v-waves v-permission="['/config/ClearCache']" class="filter-item" type="primary" @click="handleEdit1()">-->
      <!--清除系统缓存-->
      <!--</el-button>-->
    </div>
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="CName" label="权限名称" />
      <el-table-column prop="Rule" label="路由规则" />
      <el-table-column label="是否需要写日志" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsLog > 0">是</span>
          <span v-else>否</span>
        </template>
      </el-table-column>

      <el-table-column v-permission="['/rule/edit', '/rule/del']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/rule/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row)">
                编辑
              </el-button>
            </span>
            <span v-permission="['/rule/del']">
              <el-button size="small" type="danger" @click="confirmDelete(scope.row)">
                删除
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑权限" :visible.sync="editRuleVisible">
      <el-form>
        <el-form-item label="权限名称" label-width="70px">
          <el-input v-model="editFrom.Name" />
        </el-form-item>
        <el-form-item label="路由" label-width="70px">
          <el-input v-model="editFrom.Rule" />
        </el-form-item>
        <el-form-item label="上级菜单">
          <el-select v-model="editFrom.Parent" filterable placeholder="上级权限">
            <el-option label="无" value="0" />
            <el-option v-for="(item,index) in tableData" :key="index" :label="item.CName" :value="item.Id" />
          </el-select>
        </el-form-item>

        <el-form-item label="是否需要写日志">
          <el-select v-model="editFrom.IsLog" placeholder="是否需要写日志">
            <el-option label="不需要" :value="0" />
            <el-option label="需要" :value="1" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加权限" :visible.sync="addRuleVisible">
      <el-form>
        <el-form-item label="权限名称" label-width="70px">
          <el-input v-model="addFrom.Name" />
        </el-form-item>
        <el-form-item label="路由" label-width="70px">
          <el-input v-model="addFrom.Rule" />
        </el-form-item>
        <el-form-item label="上级菜单">
          <el-select v-model="addFrom.Parent" filterable placeholder="上级权限">
            <el-option label="无" value="0" />
            <el-option v-for="(item,index) in tableData" :key="index" :label="item.CName" :value="item.Id" />
          </el-select>
        </el-form-item>

        <el-form-item label="是否需要写日志">
          <el-select v-model="addFrom.IsLog" placeholder="是否需要写日志">
            <el-option label="不需要" :value="0" />
            <el-option label="需要" :value="1" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import '@/assets/css/global.css'
import '@/assets/css/base.css'
import {
  getRule,
  editRule,
  addRule,
  delRule
} from '@/api/rule'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'

export default {
  name: 'RuleList',
  directives: {
    waves,
    permission
  },
  data() {
    return {
      tableData: [],
      multipleSelection: [],
      editRuleVisible: false,
      addRuleVisible: false,
      listLoading: true,
      editFrom: {
        Name: '',
        Rule: '',
        Id: null,
        Parent: '',
        IsLog: ''
      },
      addFrom: {
        Name: '',
        Rule: '',
        Parent: ''
      },
      IsLog: {
        0: '不需要',
        1: '需要'
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    confirmDelete(row) {
      this.$confirm('确认删除该权限?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delRule({
          Id: row.Id
        }).then(res => {
          this.init()
          this.$message({
            message: '权限已删除',
            type: 'success'
          })
        })
      }).catch(() => {

      })
    },
    selectItem(val) {
      this.multipleSelection = val
    },
    edit(row) {
      this.editFrom.Name = row.Name
      this.editFrom.Rule = row.Rule
      this.editFrom.Id = row.Id
      this.editFrom.Parent = row.ParentId
      this.editFrom.IsLog = row.IsLog
      this.editRuleVisible = true
    },
    handleEdit() {
      this.editRuleVisible = false
      editRule(this.editFrom).then(res => {
        this.init()
        this.$message({
          message: '权限规则已修改',
          type: 'success'
        })
      })
    },
    // handleEdit1() {
    //   this.editRuleVisible = false
    //   ClearCache().then(res => {
    //     this.init()
    //     this.$message({
    //       message: '系统缓存已清除',
    //       type: 'success'
    //     })
    //   })
    // },
    add() {
      this.addRuleVisible = true
    },
    handleAdd() {
      addRule(this.addFrom).then(res => {
        this.$message({
          message: '权限已添加',
          type: 'success'
        })
        this.init()
      })
      this.addRuleVisible = false
    },
    init() {
      this.listLoading = true
      getRule({}).then((res) => {
        this.tableData = res.data
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    }
  }
}
</script>
