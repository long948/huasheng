<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves class="filter-item" type="primary" @click="addView = true">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加
      </el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="手机号" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Logo" align="center">
        <template slot-scope="scope">
          <img :src="qiniu.Domain+scope.row.Logo" style="width:40px;height:40px;algin:center;" class="avatar">
        </template>
      </el-table-column>
      <el-table-column label="名字" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Name }}</span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog title="添加僵尸号" :visible.sync="addView">
      <el-form>
        <el-form-item label="手机号" label-width="70px">
          <el-input v-model="addForm.Phone" type="number" />
        </el-form-item>
        <el-form-item label="Logo" label-width="70px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            <img v-if="imageUrl" :src="imageUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="名字" label-width="70px">
          <el-input v-model="addForm.Name" />
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
  fake, fakeAdd
} from '@/api/members'

import waves from '@/directive/waves'
import {
  parseTime
} from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import {
  dateHandle,
  qiniuToken
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
      addView: false,
      listQuery: {
        page: 1,
        limit: 10
      },
      addForm: {
        Phone: '',
        Logo: '',
        Name: ''
      },
      qiniu: {},
      imageUrl: ''
    }
  },
  created() {
    this.getList()
    qiniuToken().then(res => {
      this.qiniu = res.data
    })
  },
  methods: {
    getList() {
      this.listLoading = true
      fake(this.listQuery).then(res => {
        this.list = res.data.list
        this.total = res.data.total

        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    handleAdd() {
      console.log(this.addForm)
      fakeAdd(this.addForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '新增成功',
            type: 'success'
          })
          this.getList()
          this.addView = false
          this.addForm = {}
        }
      })
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.addForm.Logo = res.key
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
</style>
