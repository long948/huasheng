<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/level/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加等级
      </el-button>
    </div>
    <!-- 展示名称、奖励比例、9代内业绩、伞下指定等级的用户需达成数量 -->
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="Name" align="center" label="等级名称" />
      <el-table-column prop="Ratio" align="center" label="奖励比例" />
      <el-table-column prop="Achievement" align="center" label="9代内业绩" />
      <el-table-column prop="LowNumber" align="center" label="伞下用户数量" />
      <el-table-column prop="LowLevel" align="center" label="达成指定等级" />
      <el-table-column v-permission="['/level/edit']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/level/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row.Id)">
                编辑
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑等级" :visible.sync="editProductVisible">
      <el-form>
        <el-form-item label="等级名称" label-width="100px">
          <el-input v-model="editForm.Name" />
        </el-form-item>
        <el-form-item label="等级排序" label-width="100px">
          <el-input v-model="editForm.Level" />
        </el-form-item>
        <el-form-item label="奖励比例" label-width="100px">
          <el-input v-model="editForm.Ratio" type="number" />
        </el-form-item>
        <el-form-item label="9代内业绩" label-width="100px">
          <el-input v-model="editForm.Achievement" type="number" />
        </el-form-item>
        <el-form-item label="伞下用户数量" label-width="100px">
          <el-input v-model="editForm.LowNumber" type="number" />
        </el-form-item>
        <el-form-item label="达成指定等级" label-width="100px">
          <el-input v-model="editForm.LowLevel" type="number" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加等级" :visible.sync="addProductVisible">
      <el-form>
        <el-form-item label="等级名称" label-width="100px">
          <el-input v-model="addForm.Name" />
        </el-form-item>
        <el-form-item label="等级排序" label-width="100px">
          <el-input v-model="addForm.Level" />
        </el-form-item>
        <el-form-item label="奖励比例" label-width="100px">
          <el-input v-model="addForm.Ratio" type="number" />
        </el-form-item>
        <el-form-item label="9代内业绩" label-width="100px">
          <el-input v-model="addForm.Achievement" type="number" />
        </el-form-item>
        <el-form-item label="伞下用户数量" label-width="100px">
          <el-input v-model="addForm.LowNumber" type="number" />
        </el-form-item>
        <el-form-item label="达成指定等级" label-width="100px">
          <el-input v-model="addForm.LowLevel" type="number" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  list,
  detail,
  edit,
  add
} from '@/api/level'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'

export default {
  name: 'ProductList',
  directives: {
    waves,
    permission
  },
  data() {
    return {
      tableData: [],
      planList: [],
      editProductVisible: false,
      addProductVisible: false,
      listLoading: true,
      editForm: {
        Id: 0,
        Name: '',
        Level: '',
        Ratio: '',
        Achievement: '',
        LowNumber: '',
        LowLevel: ''
      },
      addForm: {
        Name: '',
        Level: '',
        Ratio: '',
        Achievement: '',
        LowNumber: '',
        LowLevel: ''
      }
    }
  },
  created() {
    this.init()
  },
  methods: {
    init() {
      this.listLoading = true
      list({}).then((res) => {
        this.tableData = res.data
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    edit(id) {
      detail({ Id: id }).then(res => {
        this.editForm = res.data
        this.editProductVisible = true
      })
      this.editForm.Id = id
    },
    handleEdit() {
      edit(this.editForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '矿机已更新',
            type: 'success'
          })
          this.init()
          this.editProductVisible = false
        }
      })
    },
    add() {
      this.addProductVisible = true
    },
    handleAdd() {
      add(this.addForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '产品已添加',
            type: 'success'
          })
          this.init()
          this.addProductVisible = false
        }
      })
    }
  }
}
</script>
