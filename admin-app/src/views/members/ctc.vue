<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.NickName" placeholder="昵称" clearable style="width: 200px;" class="filter-item" />
      <el-input v-model="listQuery.Phone" placeholder="手机号" clearable style="width: 200px;" class="filter-item" />
      <el-select v-model="listQuery.IsFrozenCTC" placeholder="是否冻结" clearable style="width: 150px" class="filter-item">
        <el-option label="未冻结" :value="0" />
        <el-option label="冻结" :value="1" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="昵称" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.NickName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="手机号" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="是否冻结CTC功能" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsFrozenCTC == 1">冻结</span>
          <span v-else>不冻结</span>
        </template>
      </el-table-column>
      <el-table-column label="是否启用个人手续费" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsOpenFee == 1">启动</span>
          <span v-else>不启动</span>
        </template>
      </el-table-column>
      <el-table-column label="出售手续费" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Fee }}</span>
        </template>
      </el-table-column>
      <el-table-column label="收款手续费" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.RecvFee }}</span>
        </template>
      </el-table-column>
      <el-table-column v-permission="['/members/ctcsetting']" label="操作" align="center">
        <template slot-scope="scope">
          <span v-permission="['/members/ctcsetting']">
            <el-button size="small" type="primary" @click="edit(scope.row)">编辑</el-button>
          </span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="编辑CTC会员" :visible.sync="editVisible">
      <el-form>
        <el-form-item label="是否冻结CTC功能" label-width="150px">
          <el-select v-model="editForm.IsFrozenCTC" style="width: 100px">
            <el-option label="不冻结" :value="0" />
            <el-option label="冻结" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item label="是否启用个人手续费" label-width="150px">
          <el-select v-model="editForm.IsOpenFee" style="width: 100px">
            <el-option label="不启用" :value="0" />
            <el-option label="启用" :value="1" />
          </el-select>
        </el-form-item>
        <el-form-item label="出售手续费(1为100%)" label-width="150px">
          <el-input v-model="editForm.Fee" placeholder="出售手续费" />
        </el-form-item>
        <el-form-item label="收款手续费(1为100%)" label-width="150px">
          <el-input v-model="editForm.RecvFee" placeholder="收款手续费" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  membersList,
  memberCtcSetting
} from '@/api/members'

import waves from '@/directive/waves'
import {
  parseTime
} from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import {
  dateHandle
} from '@/api/dateHandle'

export default {
  components: {
    Pagination
  },
  directives: {
    waves,
    permission
  },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      editVisible: false,
      listQuery: {
        page: 1,
        limit: 10,
        IsBan: 0
      },
      editForm: {
        Id: 0,
        IsFrozenCTC: 0,
        IsOpenFee: 0,
        Fee: 0,
        RecvFee: 0
      }
    }
  },
  created() {
    this.getList()
  },
  methods: {
    handleEdit() {
      memberCtcSetting(this.editForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '操作成功'
          })
          this.getList()
          this.editVisible = false
        }
      })
    },
    edit(row) {
      this.editForm.IsFrozenCTC = row.IsFrozenCTC
      this.editForm.IsOpenFee = row.IsOpenFee
      this.editForm.Fee = row.Fee
      this.editForm.RecvFee = row.RecvFee
      this.editForm.Id = row.Id
      this.editVisible = true
    },
    getList() {
      this.listLoading = true
      membersList(this.listQuery).then(res => {
        this.list = res.data.data
        this.total = res.data.total
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    }
  }
}
</script>
