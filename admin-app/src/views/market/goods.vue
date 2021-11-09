<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="商品名称">
        <el-input v-model="query.goods_name" placeholder="关键词" />
      </el-form-item>
      <el-form-item class="query-form-item" label="状态">
        <el-select v-model="query.goods_state" placeholder="是否上架">
          <el-option label="全部" value="" />
          <el-option label="待上架" value="0" />
          <el-option label="已上架" value="1" />
          <el-option label="已下架" value="2" />
        </el-select>
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      border
      style="width: 100%;"
    >
      <el-table-column type="index”" label="商品图" align="center" width="150">
        <template slot-scope="scope">
          <img v-image-preview :src="scope.row.original_img" width="50px">
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="商品名称"
        prop="goods_name"
        :show-overflow-tooltip="true"
      />
      <el-table-column
        align="center"
        label="市场价"
        prop="market_price"
      />
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.goods_state == 0" type="warning">待上架</el-tag>
          <el-tag v-if="scope.row.goods_state == 1" type="success">已上架</el-tag>
          <el-tag v-else-if="scope.row.goods_state == 2" type="danger">已下架</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="添加时间"
        prop="on_time"
      />
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
          </el-button>
          <el-button v-if="scope.row.goods_state == 2 || scope.row.goods_state == 0" type="warning" size="small" @click.native="upShelf(scope.row)">上架
          </el-button>
          <el-button v-if="scope.row.goods_state == 1" type="danger" size="small" @click.native="downShelf(scope.row)">下架
          </el-button>
        </template>
      </el-table-column>
    </el-table>
    <pagination
      v-show="total > 0"
      :total="total"
      :page.sync="query.page"
      :limit.sync="query.limit"
      @pagination="getList"
    />
    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="65%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="商品名称:" prop="goods_name" label-width="150px">
          <el-input v-model="formData.goods_name" auto-complete="off" style="width: 50%" type="textarea" autosize />
        </el-form-item>
        <el-form-item label="市场价:" prop="market_price" label-width="150px">
          <el-input-number v-model="formData.market_price" :min="0" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="商品主图" label-width="150px">
          <div>
            <upload
              ext="jpg,png,jpeg,wmv,avi,3gp,rm,rmvb,mp4"
              @on-select="onSelectMainImg"
            />
          </div>
          <div v-if="formData.original_img" class="upload-img">
            <div
              style="
              display: inline-block;
              margin-left: 2px;
              position: relative;
              max-width: 200px;
              max-height: 200px;
            "
            >
              <img :src="formData.original_img" style="width: 100%; height: 100%">
            </div>
          </div>
        </el-form-item>
        <el-form-item label="商品轮播图" prop="carousel_img" label-width="150px">
          <div>
            <upload ext="jpg,png,jpeg" @on-select="onSelectImgList" />
          </div>
          <div
            v-if="formData.ImgList && formData.ImgList.length > 0"
            class="upload-img"
          >
            <div
              v-for="(item, index) in formData.ImgList"
              :key="index"
              style="
              display: inline-block;
              margin-left: 2px;
              position: relative;
              max-width: 200px;
              max-height: 200px;
            "
            >
              <img :src="item" style="width: 100%; height: 100%">
              <span
                style="
                position: absolute;
                right: 5px;
                top: 0;
                cursor: pointer;
                size: 500px;
                color: #ff0000;
              "
                @click="onDeleteImgList(index)"
              >×</span>
            </div>
          </div>
        </el-form-item>
        <el-form-item v-if="formVisible" label="详情">
          <tinymce
            v-model="formData.goods_content"
            style="display: inline-block; width: 100%"
            :height="500"
          />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import { downShelf, goodsList, goodsSave, upShelf } from '@/api/market'
import Upload from '@/components/File/Upload.vue'
import Tinymce from '../../components/Tinymce3/index.vue'
import Pagination from '@/components/Pagination'

const formJson = {
  goods_id: '',
  goods_name: '',
  original_img: '',
  market_price: '',
  ImgList: [],
  goods_content: ''
}
export default {
  name: 'GoodsList',
  components: { Upload, Tinymce, Pagination },
  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 20,
        goods_state: '0'
      },
      list: [],
      total: 0,
      loading: true,
      index: null,
      isEdit: null,
      formName: null,
      formMap: {
        add: '新增',
        edit: '编辑'
      },
      qiniu: {},
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false,
      langList: []
    }
  },
  mounted() {
  },
  created() {
    // 将参数拷贝进查询对象
    const query = this.$route.query
    this.query = Object.assign(this.query, query)
    this.query.limit = parseInt(this.query.limit)
    // 消息列表
    this.getList()
  },
  methods: {
    onReset() {
      this.$router.push({
        path: ''
      })
      this.query = {
        id: '',
        page: 1,
        limit: 20
      }
      this.getList()
    },
    onSelectMainImg(filePath, filePathUrl, fileUrl) {
      this.formData.original_img = fileUrl
    },
    onSelectImgList(filePath, filePathUrl, fileUrl) {
      this.formData.ImgList.push(fileUrl)
    },
    onDeleteImgList(index) {
      this.formData.ImgList.splice(index, 1)
    },
    onSubmit() {
      this.query.page = 1
      this.$router.push({
        path: '',
        query: this.query
      })
      this.getList()
    },
    handleCurrentChange(val) {
      this.query.page = val
      this.getList()
    },
    getList() {
      this.loading = true
      goodsList(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.list || []
          this.total = response.data.total || 0
        })
        .catch(() => {
          this.loading = false
          this.list = []
          this.total = 0
          this.roles = []
        })
    },
    // 刷新表单
    resetForm() {
      if (this.$refs['dataForm']) {
        // 清空验证信息表单
        this.$refs['dataForm'].clearValidate()
        // 刷新表单
        this.$refs['dataForm'].resetFields()
      }
    },
    // 隐藏表单
    hideForm() {
      // 更改值
      this.formVisible = !this.formVisible
      // 清空表单
      this.$refs['dataForm'].resetFields()
      this.index = null
      return true
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      this.formData = JSON.parse(JSON.stringify(formJson))
      if (row !== null) {
        this.formData = JSON.parse(JSON.stringify(row))
      }
      this.formName = 'add'
      this.isEdit = 0
      if (index !== null) {
        this.isEdit = 1
        this.index = index
        this.formName = 'edit'
      }
    },
    // 上架
    upShelf(row) {
      var goodsId = row.goods_id
      upShelf({ goodsId: goodsId }).then(res => {
        if (res.info === 1) {
          this.$confirm('请先将该商品添加到拼购商品中在上架', '提示', {
            confirmButtonText: '前往添加',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            var goodsName = row.goods_name
            this.$router.push({ name: 'PgGoods', params: { goodsName }})
          }).catch(() => {})
        }
        if (res.code === 20000) {
          this.$message({
            message: '上架成功',
            type: 'success'
          })
          this.getList()
        }
      })
    },
    // 下架商品
    downShelf(row) {
      var goodsId = row.goods_id
      var msg = '确定下架该商品？下架后默认将该商品从所有活动中删除。'
      this.$confirm(msg, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        downShelf({ goodsId: goodsId }).then(res => {
          if (res.code === 20000) {
            this.getList()
            this.$message({
              type: 'success',
              message: '操作成功!'
            })
          }
        })
      }).catch(() => {})
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          goodsSave(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            if (this.formName === 'add') {
              // 向头部添加数据
              if (response.data && response.data.id) {
                const upData = Object.assign(data, response.data)
                this.list.unshift(upData)
              }
            } else {
              const upData = Object.assign(data, response.data)
              this.list.splice(this.index, 1, upData)
            }
            // 刷新表单
            this.getList()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>

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
