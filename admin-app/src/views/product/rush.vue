<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/rush/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加产品
      </el-button>
    </div>
    <!-- 展示序号、预约时间、公布时间、可抢矿机名称、定金比例 -->
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column label="预约时间" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.PlanStartTime }} - {{ scope.row.PlanEndTime }}</span>
        </template>
      </el-table-column>
      <el-table-column prop="PromulgateTime" align="center" label="公布时间" />
      <el-table-column prop="ProductName" align="center" label="可抢矿机名称" />
      <el-table-column prop="Ratio" align="center" label="定金比例" />

      <el-table-column v-permission="['/rush/edit']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/rush/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row.Id)">
                编辑
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑活动" :visible.sync="editProductVisible">
      <el-form>
        <el-form-item label="开始时间" label-width="100px">
          <el-time-picker
            v-model="editForm.PlanStartTime"
            arrow-control
            placeholder="开始时间"
          />
        </el-form-item>
        <el-form-item label="结束时间" label-width="100px">
          <el-time-picker
            v-model="editForm.PlanEndTime"
            arrow-control
            placeholder="结束时间"
          />
        </el-form-item>
        <el-form-item label="公布时间" label-width="100px">
          <el-time-picker
            v-model="editForm.PromulgateTime"
            arrow-control
            placeholder="公布时间"
          />
        </el-form-item>
        <el-form-item label="产品" label-width="100px">
          <el-select v-model="editForm.ProductId" placeholder="产品">
            <el-option v-for="(item,i) in products" :key="i" :label="item.Name" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="定金比例(1为100%)" label-width="80px">
          <el-input v-model="editForm.Ratio" type="number" placeholder="定金比例" />
        </el-form-item>
        <el-form-item label="是否禁用" label-width="100px">
          <el-select v-model="editForm.IsClose" placeholder="是否禁用">
            <el-option label="禁用" :value="1" />
            <el-option label="不禁用" :value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加活动" :visible.sync="addProductVisible">
      <el-form>
        <el-form-item label="开始时间" label-width="100px">
          <el-time-picker
            v-model="addForm.PlanStartTime"
            arrow-control
            placeholder="开始时间"
          />
        </el-form-item>
        <el-form-item label="结束时间" label-width="100px">
          <el-time-picker
            v-model="addForm.PlanEndTime"
            arrow-control
            placeholder="结束时间"
          />
        </el-form-item>
        <el-form-item label="公布时间" label-width="100px">
          <el-time-picker
            v-model="addForm.PromulgateTime"
            arrow-control
            placeholder="公布时间"
          />
        </el-form-item>
        <el-form-item label="产品" label-width="100px">
          <el-select v-model="addForm.ProductId" placeholder="产品">
            <el-option v-for="(item,i) in products" :key="i" :label="item.Name" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="定金比例(1为100%)" label-width="80px">
          <el-input v-model="addForm.Ratio" type="number" placeholder="定金比例" />
        </el-form-item>
        <el-form-item label="是否禁用" label-width="100px">
          <el-select v-model="addForm.IsClose" placeholder="是否禁用">
            <el-option label="禁用" :value="1" />
            <el-option label="不禁用" :value="0" />
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
} from '@/api/rush'
import {
  list as ProductList
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
      products: [],
      editProductVisible: false,
      addProductVisible: false,
      listLoading: true,
      editForm: {
        PlanStartTime: '',
        PlanEndTime: '',
        PromulgateTime: '',
        ProductId: '',
        Ratio: '',
        IsClose: 0
      },
      addForm: {
        PlanStartTime: '',
        PlanEndTime: '',
        PromulgateTime: '',
        ProductId: '',
        Ratio: '',
        IsClose: 0
      }
    }
  },
  created() {
    this.init()
    ProductList({}).then(res => {
      this.products = res.data
    })
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
        this.editForm.PlanStartTime = new Date(res.data.PlanStartTime)
        this.editForm.PlanEndTime = new Date(res.data.PlanEndTime)
        this.editForm.PromulgateTime = new Date(res.data.PromulgateTime)
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
            message: '抢购已添加',
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
