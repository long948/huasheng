<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/product/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加交易币种
      </el-button>
    </div>
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="CoinName" align="center" label="币种" />
      <el-table-column label="求购" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsBuy == 1">可求购</span>
          <span v-else>不可求购</span>
        </template>
      </el-table-column>
      <el-table-column label="出售" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsSell == 1">可出售</span>
          <span v-else>不可出售</span>
        </template>
      </el-table-column>

      <el-table-column v-permission="['/product/edit']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/product/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row)">
                编辑
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑交易币种" :visible.sync="editProductVisible">
      <el-form>
        <el-form-item label="币种" label-width="100px">
          <el-select v-model="editForm.CoinId" style="width: 100px">
            <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="求购" label-width="100px">
          <el-select v-model="editForm.IsBuy" placeholder="允许购买">
            <el-option label="不可求购" :value="0" />
            <el-option label="可求购" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item label="可出售" label-width="100px">
          <el-select v-model="editForm.IsSell" placeholder="允许购买">
            <el-option label="不可出售" :value="0" />
            <el-option label="可出售" :value="1" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加交易币种" :visible.sync="addProductVisible">
      <el-form>
        <el-form-item label="币种" label-width="100px">
          <el-select v-model="addForm.CoinId" style="width: 100px">
            <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="求购" label-width="100px">
          <el-select v-model="addForm.IsBuy" placeholder="允许购买">
            <el-option label="不可求购" :value="0" />
            <el-option label="可求购" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item label="可出售" label-width="100px">
          <el-select v-model="addForm.IsSell" placeholder="允许购买">
            <el-option label="不可出售" :value="0" />
            <el-option label="可出售" :value="1" />
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
  ctccoin,
  AddCtcCoin,
  editCtcCoin
} from '@/api/setting'
import {
  coinList
} from '@/api/coin'
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
      editProductVisible: false,
      addProductVisible: false,
      listLoading: true,
      coinList: {},
      editForm: {
        Id: 0,
        CoinId: 0,
        IsBuy: 0,
        IsSell: 0
      },
      addForm: {
        CoinId: '',
        IsBuy: 0,
        IsSell: 0
      }
    }
  },
  created() {
    this.init()
    this.getCoinList()
  },
  methods: {
    init() {
      this.listLoading = true
      ctccoin({}).then((res) => {
        this.tableData = res.data
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    edit(row) {
      console.log(row)
      this.editForm.Id = row.Id
      this.editForm.CoinId = row.CoinId
      this.editForm.IsBuy = row.IsBuy
      this.editForm.IsSell = row.IsSell
      console.log(this.editForm)
      this.editProductVisible = true
    },
    handleEdit() {
      editCtcCoin(this.editForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '交易币种已更新',
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
      AddCtcCoin(this.addForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '交易币种已添加',
            type: 'success'
          })
          this.init()
          this.addProductVisible = false
        }
      })
    },
    getCoinList() {
      coinList().then(res => {
        this.coinList = res.data.data
        console.log(this.coinList)
      })
    }
  }
}
</script>
