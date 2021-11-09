<template>
  <div class="app-container">

    <div class="filter-container">
      <el-input v-model="listQuery.keywords" placeholder="关键字" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">
        搜索
      </el-button>
      <el-button v-waves class="filter-item" type="primary" @click="addQa()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加问题
      </el-button>
    </div>

    <el-table
      :key="tableKey"
      v-loading="listLoading"
      :data="list"
      border
      fit
      highlight-current-row
      style="width: 100%;"
    >
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="问题" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.Question" />
        </template>
      </el-table-column>
      <el-table-column label="回答" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.Answer" />
        </template>
      </el-table-column>

      <el-table-column v-permission="['/bannerNotice/noticeUpdate','/bannerNotice/noticeDelete']" label="操作" width="200" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/bannerNotice/noticeUpdate']">
              <el-button size="small" type="primary" @click="showupdateNote(scope.row)">编辑</el-button>
            </span>
            <span v-permission="['/bannerNotice/noticeDelete']">
              <el-button size="small" type="primary" @click="confirmDelete(scope.row)">删除</el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="添加问题" :visible.sync="noticeAddVisible">
      <el-form>
        <el-form-item label="问题" label-width="70px">
          <el-input v-model="addData.Question" />
        </el-form-item>
        <el-form-item label="回答" label-width="70px">
          <el-input v-model="addData.Answer" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="编辑公告" :visible.sync="noticeUpdateVisible">
      <el-form>
        <el-form-item label="问题" label-width="70px">
          <el-input v-model="addData.Question" />
        </el-form-item>

        <el-form-item label="回答" label-width="70px">
          <el-input v-model="addData.Answer" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { qaList, qaAdd, qaDelete, getQa, qaUpdate } from '@/api/bannerNotice.js'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Tinymce from '@/components/Tinymce'
import { dateHandle } from '@/api/dateHandle'

export default {
  components: { Pagination, Tinymce },
  directives: { waves, permission },
  data() {
    return {
      tableKey: 0,
      list: null,
      listLoading: true,
      total: 0,
      listQuery: {
        page: 1,
        limit: 10,
        keywords: ''
      },

      noticeAddVisible: false,
      addData: {
        Question: '',
        Answer: ''
      },

      noticeUpdateVisible: false,
      id: 0

    }
  },
  created() {
    this.getList()
  },
  methods: {
    getList() {
      this.listLoading = true
      qaList(this.listQuery).then(res => {
        if (res.code == 20000) {
          this.list = res.data.list
          this.total = res.data.total
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    // 添加公告页面
    addQa() {
      this.addData.Question = ''
      this.addData.Answer = ''
      this.noticeAddVisible = true
    },
    // 提交公告数据
    handleAdd() {
      qaAdd(this.addData).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.addData.Question = ''
          this.addData.Answer = ''
          this.getList()
          this.noticeAddVisible = false
        }
      })
    },

    // 编辑公告页面弹窗
    showupdateNote(row, index) {
      this.id = row.Id
      getQa(row.Id).then(res => {
        if (res.code == 20000) {
          this.addData.Question = res.data.Question
          this.addData.Answer = res.data.Answer
          setTimeout(() => {
            this.noticeUpdateVisible = true
          }, 0.3 * 1000)
        }
      })
    },

    // 编辑数据
    handleEdit() {
      const param = { Question: this.addData.Question, Answer: this.addData.Answer, id: this.id }
      qaUpdate(param).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.noticeUpdateVisible = false
          this.getList()
        }
      })
    },

    // 删除快讯
    confirmDelete(row) {
      this.$confirm('确认删除该常见问题?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        qaDelete(row.Id).then(res => {
          this.getList()
          this.$message({
            message: '此常见问题已删除',
            type: 'success'
          })
        })
      }).catch(() => {

      })
    },
    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    }

  }
}
</script>

