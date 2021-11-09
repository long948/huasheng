<template>
  <div class="app-container">

    <div class="filter-container">
      <el-input v-model="listQuery.keywords" placeholder="关键字" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">
        搜索
      </el-button>
      <el-button v-waves class="filter-item" type="primary" @click="addNews()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加快讯
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
      <el-table-column label="图片" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.Imgs" />
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center">
        <template slot-scope="scope">
          {{ _date(scope.row.AddTime) }}
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

    <el-dialog title="添加公告" :visible.sync="noticeAddVisible">
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

    <el-dialog title="编辑公告" :visible.sync="noticeUpdateVisible">
      <el-form>
        <el-form-item label="标题" label-width="70px">
          <el-input v-model="addData.title" />
        </el-form-item>

        <el-form-item label="内容" label-width="70px">
          <Tinymce ref="editor" v-model="addData.content" :height="400" />
        </el-form-item>

        <el-form-item label="图片" label-width="100px">
          <el-upload
            action="https://up-z2.qiniup.com"
            list-type="picture-card"
            :on-preview="handlePictureCardPreview"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
            :file-list="ImgList"
            :on-remove="handleRemove"
          >
            <i class="el-icon-plus" />
          </el-upload>
          <el-dialog :visible.sync="dialogVisible" size="tiny">
            <img width="100%" :src="dialogImageUrl" alt="">
          </el-dialog>
        </el-form-item>

      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import { newsList, newsUpdate, newsDelete, newsAdd, getNews } from '@/api/bannerNotice.js'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Tinymce from '@/components/Tinymce2'
import { dateHandle, qiniuToken } from '@/api/dateHandle'
import md5 from 'js-md5'

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
      qiniu: {},
      noticeUpdateVisible: false,
      id: 0,
      dialogImageUrl: '',
      dialogVisible: false,
      Imgs: [],
      ImgList: [{ name: 'food.jpg', url: 'https://xxx.cdn.com/xxx.jpg' }],
      ImgsMap: null
    }
  },
  created() {
    this.getList()
    this.qiniuGet()
    this.ImgsMap = new Map()
  },
  methods: {
    getList() {
      this.listLoading = true
      newsList(this.listQuery).then(res => {
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
    handleRemove(file, fileList) {
      console.log(file)
    },
    handlePictureCardPreview(file) {
      this.dialogImageUrl = file.url
      this.dialogVisible = true
    },
    handleAvatarSuccess(res, file) {
      this.ImgsMap.set(md5(file.url), res.hash)
      console.log(this.ImgsMap)
    },
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
      })
    },
    // 添加公告页面
    addNews() {
      this.addData.title = ''
      this.addData.content = ''
      this.noticeAddVisible = true
    },

    // 提交公告数据
    handleAdd() {
      newsAdd(this.addData).then(res => {
        if (res.code == 20000) {
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
    showupdateNote(row, index) {
      this.id = row.Id
      getNews(row.Id).then(res => {
        if (res.code == 20000) {
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
      newsUpdate(param).then(res => {
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
      this.$confirm('确认删除该快讯?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        newsDelete(row.Id).then(res => {
          this.getList()
          this.$message({
            message: '此快讯已删除',
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

