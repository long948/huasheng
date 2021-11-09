<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/product/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加产品
      </el-button>
    </div>
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="Name" align="center" label="矿机名称" />
      <el-table-column prop="Price" align="center" label="价格(USDT)" />
      <el-table-column prop="Period" align="center" label="有效期" />
      <el-table-column prop="Ratio" align="center" label="日利" />
      <el-table-column label="允许购买" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsSell == 1">允许</span>
          <span v-else>不允许</span>
        </template>
      </el-table-column>
      <el-table-column label="是否有效" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsValid == 1">有效</span>
          <span v-else>无效</span>
        </template>
      </el-table-column>
      <el-table-column label="是否退本" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsReturn == 1">退</span>
          <span v-else>不退</span>
        </template>
      </el-table-column>

      <el-table-column v-permission="['/product/edit']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/product/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row.Id)">
                编辑
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑矿机" :visible.sync="editProductVisible">
      <el-form>
        <el-form-item label="矿机名称" label-width="100px">
          <el-input v-model="editForm.Name" />
        </el-form-item>
        <el-form-item label="价格(USDT)" label-width="100px">
          <el-input v-model="editForm.Price" type="number" />
        </el-form-item>
        <el-form-item label="有效期" label-width="100px">
          <el-input v-model="editForm.Period" type="number" />
        </el-form-item>
        <el-form-item label="日利" label-width="100px">
          <el-input v-model="editForm.Ratio" type="number" />
        </el-form-item>
        <el-form-item label="允许购买" label-width="100px">
          <el-select v-model="editForm.IsSell" placeholder="允许购买">
            <el-option label="允许" :value="1" />
            <el-option label="不允许" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item label="是否有效" label-width="100px">
          <el-select v-model="editForm.IsValid" placeholder="是否有效">
            <el-option label="有效" :value="1" />
            <el-option label="无效" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item label="是否退本" label-width="100px">
          <el-select v-model="editForm.IsReturn" placeholder="是否退本">
            <el-option label="退" :value="1" />
            <el-option label="不退" :value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加矿机" :visible.sync="addProductVisible">
      <el-form>
        <el-form-item label="矿机名称" label-width="100px">
          <el-input v-model="addForm.Name" />
        </el-form-item>
        <el-form-item label="价格(USDT)" label-width="100px">
          <el-input v-model="addForm.Price" type="number" />
        </el-form-item>
        <el-form-item label="有效期" label-width="100px">
          <el-input v-model="addForm.Period" type="number" />
        </el-form-item>
        <el-form-item label="日利" label-width="100px">
          <el-input v-model="addForm.Ratio" type="number" />
        </el-form-item>
        <el-form-item label="允许购买" label-width="100px">
          <el-select v-model="addForm.IsSell" placeholder="允许购买">
            <el-option label="允许" :value="1" />
            <el-option label="不允许" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item label="是否有效" label-width="100px">
          <el-select v-model="addForm.IsValid" placeholder="是否有效">
            <el-option label="有效" :value="1" />
            <el-option label="无效" :value="0" />
          </el-select>
        </el-form-item>
        <el-form-item label="是否退本" label-width="100px">
          <el-select v-model="addForm.IsReturn" placeholder="是否退本">
            <el-option label="退" :value="1" />
            <el-option label="不退" :value="0" />
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
import {
  list,
  detail,
  edit,
  add
} from '@/api/product'
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
        Price: '',
        Period: '',
        Ratio: '',
        IsSell: '',
        IsReturn: '',
        IsValid: ''
      },
      addForm: {
        Name: '',
        Price: '',
        Period: '',
        Ratio: '',
        IsSell: '',
        IsReturn: ''
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
