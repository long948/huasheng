<template>
  <div class="app-container">

    <div class="filter-container">
      <el-input v-model="listQuery.keywords" placeholder="关键字" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">
        搜索
      </el-button>
      <el-button v-waves class="filter-item" type="primary" @click="addNotice()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加通知消息
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
      <el-table-column label="标题" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.Title" />
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.AddTimeName" />
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

    <el-dialog title="添加通知消息" :visible.sync="noticeAddVisible">
      <el-form>
        <el-form-item label="标题" label-width="70px">
          <el-input v-model="addData.title" />
        </el-form-item>

        <el-form-item label="内容" label-width="70px">
          <Tinymce ref="editor" v-model="addData.content" :height="400" />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="编辑通知消息" :visible.sync="noticeUpdateVisible">
      <el-form>
        <el-form-item label="标题" label-width="70px">
          <el-input v-model="addData.title" />
        </el-form-item>

        <el-form-item label="内容" label-width="70px">
          <Tinymce ref="editor" v-model="addData.content" :height="400" />
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { noticeList, noticeUpdate, noticeDelete, noticeAdd, getNotice } from '@/api/bannerNotice.js'
import waves from '@/directive/waves'
// import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Tinymce from '@/components/Tinymce'

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
        title: '',
        content: ''
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
      noticeList(this.listQuery).then(res => {
        if (res.code === 20000) {
          this.list = res.data.data
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
    addNotice() {
      this.addData.title = ''
      this.addData.content = ''
      this.noticeAddVisible = true
    },

    // 提交公告数据
    handleAdd() {
      noticeAdd(this.addData).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.addData.title = ''
          this.addData.content = ''
          this.getList()
          this.noticeAddVisible = false
        }
      })
    },

    // 编辑公告页面弹窗
    showupdateNote(row) {
      this.id = row.Id
      getNotice(row.Id).then(res => {
        if (res.code === 20000) {
          this.addData.title = res.data.Title
          this.addData.content = res.data.Content
          setTimeout(() => {
            this.noticeUpdateVisible = true
          }, 0.3 * 1000)
        }
      })
    },

    // 编辑数据
    handleEdit() {
      const param = { title: this.addData.title, content: this.addData.content, id: this.id }
      noticeUpdate(param).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.noticeUpdateVisible = false
          this.getList()
        }
      })
    },

    // 删除公告
    confirmDelete(row) {
      this.$confirm('确认删除该分类?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        noticeDelete(row.Id).then(res => {
          this.getList()
          this.$message({
            message: '公告删除成功',
            type: 'success'
          })
        })
      }).catch(() => {

      })
    }

  }
}
</script>

