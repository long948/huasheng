<template>
  <div class="app-container">

    <div class="filter-container">
      <el-button v-waves class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加微信群
      </el-button>
    </div>

    <el-table :key="tableKey" v-loading="listLoading" :data="wechatList" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="群名" align="center">
        <template slot-scope="scope">
          <span v-html="scope.row.Name" />
        </template>
      </el-table-column>
      <el-table-column label="图片" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.QrCode">
            <img v-image-preview :src="qiniu.Domain + scope.row.QrCode" width="50px">
          </span>
        </template>
      </el-table-column>
      <el-table-column label="是否开启" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsOpen == 1">开启</span>
          <span v-if="scope.row.IsOpen == 0">关闭</span>
        </template>
      </el-table-column>

      <el-table-column v-permission="['/bannerNotice/editWechat']" label="操作" width="200" align="center">
        <template slot-scope="scope">
          <span v-permission="['/bannerNotice/editWechat']">
            <el-button size="small" type="primary" @click="edit(scope.row)">编辑</el-button>
          </span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total>0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="添加微信群" :visible.sync="show">
      <el-form>
        <el-form-item label="群名" label-width="70px">
          <el-input v-model="addForm.Name" />
        </el-form-item>
        <el-form-item label="二维码" label-width="70px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="imageUrl" :src="imageUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="是否开启" label-width="100px">
          <el-select v-model="addForm.IsOpen" placeholder="是否开启">
            <el-option label="开启" :value="1" />
            <el-option label="关闭" :value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd()">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="修改微信群" :visible.sync="editShow">
      <el-form>
        <el-form-item label="群名" label-width="70px">
          <el-input v-model="editForm.Name" />
        </el-form-item>
        <el-form-item label="二维码" label-width="70px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="QrCodeEditSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="imageUrl" :src="imageUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="是否开启" label-width="100px">
          <el-select v-model="editForm.IsOpen" placeholder="是否开启">
            <el-option label="开启" :value="1" />
            <el-option label="关闭" :value="0" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit()">确 定</el-button>
      </div>
    </el-dialog>

  </div>
</template>

<script>
import {
  WechatList,
  AddWechat,
  EditWechat
} from '@/api/bannerNotice.js'
import waves from '@/directive/waves'
import {
  parseTime
} from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Upload from '@/components/Upload/SingleImage3'
import {
  dateHandle,
  qiniuToken
} from '@/api/dateHandle'
export default {
  components: {
    Pagination,
    Upload
  },
  directives: {
    waves,
    permission
  },
  data() {
    return {
      tableKey: 0,
      wechatList: null,
      listLoading: true,
      total: 0,
      listQuery: {
        page: 1,
        limit: 10,
        keywords: ''
      },
      qiniu: {},
      imageUrl: '',
      addForm: {

      },
      editForm: {
        Id: 0,
        QrCode: '',
        Name: '',
        IsOpen: 0
      },
      show: false,
      editShow: false
    }
  },
  created() {
    this.qiniuGet()
    this.getList()
  },
  methods: {
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.addForm.QrCode = res.key
    },
    QrCodeEditSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.editForm.QrCode = res.key
    },
    getList() {
      this.listLoading = true
      WechatList(this.listQuery).then(res => {
        if (res.code == 20000) {
          this.wechatList = res.data
          setTimeout(() => {
            this.listLoading = false
          }, 0.3 * 1000)
        } else {
          this.listLoading = true
        }
      })
    },
    edit(row) {
      this.editForm.Id = row.Id
      this.editForm.Name = row.Name
      this.editForm.QrCode = row.QrCode
      this.editForm.IsOpen = row.IsOpen
      this.imageUrl = this.qiniu.Domain + row.QrCode
      this.editShow = true
    },
    add() {
      this.imageUrl = ''
      this.addForm = {}
      this.show = true
    },
    handleAdd() {
      AddWechat(this.addForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '微信群已添加'
          })
          this.getList()
          this.show = false
        }
      })
    },
    handleEdit() {
      EditWechat(this.editForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            type: 'success',
            message: '微信群已修改'
          })
          this.editShow = false
          this.getList()
        }
      })
    }

  }
}
</script>
<style>
  .avatar-uploader .el-upload {
    margin-left: 20px;
    border: 1px dashed #d9d9d9;
    border-radius: 6px;
    cursor: pointer;
    position: relative;
    overflow: hidden;
    width: 200px;
    height: 250px;
  }

  .avatar-uploader .el-upload:hover {
    border-color: #409eff;
  }

  .avatar-uploader-icon {
    font-size: 28px;
    color: #8c939d;
    width: 198px;
    height: 198px;
    line-height: 178px;
    text-align: center;
  }

  .avatar {
    width: 190px;
    height: 190px;
    display: block;
  }

  .el-table--border:after, .el-table--group:after, .el-table:before{
    z-index: 0;
  }
</style>
