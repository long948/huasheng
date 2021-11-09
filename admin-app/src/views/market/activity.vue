<template>
  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="活动标题">
        <el-input v-model="query.title" placeholder="关键词" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" style="height: 28px" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
          <el-button type="primary" @click="add()">新增</el-button>
        </el-button-group>
      </el-form-item>
    </el-form>
    <el-table
      v-loading="loading"
      :data="list"
      border
      style="width: 100%;"
    >
      <el-table-column
        align="center"
        label="活动标题"
        prop="title"
      />
      <el-table-column
        align="center"
        label="开始时间"
        prop="begin_time"
      />
      <el-table-column
        align="center"
        label="结束时间"
        prop="end_time"
      />
      <el-table-column
        align="center"
        label="状态"
        prop="status"
      />
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.status == 1" type="success">进行中</el-tag>
          <el-tag v-if="scope.row.status == 2" type="warning">未开始</el-tag>
          <el-tag v-if="scope.row.status == 3" type="info">已结束</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
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

    <!--编辑-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="90%"
      top="5vh"
    >
      <el-card class="filter-container" shadow="never">
        <el-form ref="dataForm" :model="formData" label-width="120px">
          <el-form-item label="活动标题：" prop="team_price">
            <el-input v-model="formData.title" placeholder="请输入活动标题" style="width: 30%" type="textarea" autosize />
          </el-form-item>
          <el-form-item label="活动开始时间" prop="begin_time">
            <el-date-picker
              v-model="formData.begin_time"
              style="width: 30%"
              type="datetime"
              placeholder="请选择活动开始时间"
              value-format="yyyy-MM-dd HH:mm:ss"
              :picker-options="startTime"
              @blur="checkTime()"
            />
          </el-form-item>
          <el-form-item label="活动结束时间" prop="end_time">
            <el-date-picker
              v-model="formData.end_time"
              style="width: 30%"
              type="datetime"
              placeholder="请选择活动结束时间"
              value-format="yyyy-MM-dd HH:mm:ss"
              :picker-options="endTime"
              @blur="checkTime()"
            />
          </el-form-item>
        </el-form>
      </el-card>
      <el-card>
        <span>活动商品列表</span>
        <el-button type="danger" style="float: right" @click.native="delGoods()">删除</el-button>
        <el-button style="float: right; margin-bottom: 15px" @click="innerVisible = true">添加商品</el-button>
      </el-card>
      <el-card class="filter-container" shadow="never">
        <el-table
          ref="delFormRef"
          :header-cell-style="{background:'#eef1f6',color:'#909399'}"
          :data="pgGoods"
          border
          style="width: 100%"
          :row-key="handleReserve"
          @selection-change="delSelectionChange"
        >
          <el-table-column
            :selectable="handleDisable"
            type="selection"
            reserve-selection="reserve-selection"
            width="55"
            @click="drawer = true"
          />
          <el-table-column type="index”" label="商品图" align="center" width="150">
            <template slot-scope="scope">
              <img :src="scope.row.original_img" width="50px">
            </template>
          </el-table-column>
          <el-table-column label="商品名称" align="center" :show-overflow-tooltip="true">
            <template slot-scope="scope">{{ scope.row.goods_name }}</template>
          </el-table-column>
          <el-table-column label="市场价" width="160" align="center">
            <template slot-scope="scope">{{ scope.row.market_price }}</template>
          </el-table-column>
          <el-table-column label="拼购价" width="160" align="center">
            <template slot-scope="scope">{{ scope.row.team_price }}</template>
          </el-table-column>
          <el-table-column label="成团人数" width="160" align="center">
            <template slot-scope="scope">{{ scope.row.needer }}</template>
          </el-table-column>
          <el-table-column label="中奖人数" align="center">
            <template slot-scope="scope">{{ scope.row.stock_limit }}</template>
          </el-table-column>
          <el-table-column label="未中返利" align="center">
            <template slot-scope="scope">{{ scope.row.return_amount }}</template>
          </el-table-column>
          <el-table-column label="支付币种" align="center">
            <template slot-scope="scope">{{ scope.row.payCoin }}</template>
          </el-table-column>
          <el-table-column label="奖励币种" align="center">
            <template slot-scope="scope">{{ scope.row.getCoin }}</template>
          </el-table-column>
          <el-table-column label="商品状态" align="center">
            <template slot-scope="scope">
              <el-tag v-if="scope.row.goods_state == 0" type="warning">待上架</el-tag>
              <el-tag v-if="scope.row.goods_state == 1" type="success">已上架</el-tag>
              <el-tag v-if="scope.row.goods_state == 2" type="danger">已下架</el-tag>
            </template>
          </el-table-column>
          <el-table-column label="状态" align="center">
            <template slot-scope="scope">
              <span v-if="scope.row.is_found" style="color: green">正在拼团</span>
            </template>
          </el-table-column>
        </el-table>
      </el-card>
      <!--      <div slot="footer" class="dialog-footer">-->
      <!--        <el-button @click.native="hideForm">取消</el-button>-->
      <!--        <el-button type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>-->
      <!--      </div>-->
      <el-dialog
        width="80%"
        title="选择商品"
        :before-close="hideForm1"
        :visible.sync="innerVisible"
        append-to-body
      >
        <el-card class="filter-container" shadow="never">
          <el-input v-model="pgform.keyword" clearable style="width: 250px;margin-bottom: 20px" size="small" placeholder="商品名称关键词">
            <el-button slot="append" icon="el-icon-search" @click="SearchpgGoods()" />
          </el-input>
          <el-table
            ref="editFormRef"
            :header-cell-style="{background:'#eef1f6',color:'#909399'}"
            :data="goodsLists"
            border
            style="width: 100%"
            :row-key="handleReserve"
            @selection-change="PgGoodsList"
          >
            <el-table-column
              :selectable="choice"
              type="selection"
              reserve-selection="reserve-selection"
              width="55"
              @click="drawer = true"
            />
            <el-table-column type="index”" label="商品图" align="center" width="150">
              <template slot-scope="scope">
                <img :src="scope.row.original_img" width="50px">
              </template>
            </el-table-column>
            <el-table-column label="商品名称" align="center" :show-overflow-tooltip="true" width="200">
              <template slot-scope="scope">{{ scope.row.goods_name }}</template>
            </el-table-column>
            <el-table-column label="市场价" width="150" align="center">
              <template slot-scope="scope">{{ scope.row.market_price }}</template>
            </el-table-column>
            <el-table-column label="拼购价" width="150" align="center">
              <template slot-scope="scope">{{ scope.row.team_price }}</template>
            </el-table-column>
            <el-table-column label="成团人数" width="150" align="center">
              <template slot-scope="scope">{{ scope.row.needer }}</template>
            </el-table-column>
            <el-table-column label="中奖人数" align="center">
              <template slot-scope="scope">{{ scope.row.stock_limit }}</template>
            </el-table-column>
            <el-table-column label="未中返利" align="center">
              <template slot-scope="scope">{{ scope.row.return_amount }}</template>
            </el-table-column>
            <el-table-column label="支付币种" align="center">
              <template slot-scope="scope">{{ scope.row.payCoin }}</template>
            </el-table-column>
            <el-table-column label="奖励币种" align="center">
              <template slot-scope="scope">{{ scope.row.getCoin }}</template>
            </el-table-column>
            <el-table-column label="商品状态" align="center">
              <template slot-scope="scope">
                <el-tag v-if="scope.row.goods_state == 0" type="warning">待上架</el-tag>
                <el-tag v-if="scope.row.goods_state == 1" type="success">已上架</el-tag>
                <el-tag v-if="scope.row.goods_state == 2" type="danger">已下架</el-tag>
              </template>
            </el-table-column>
          </el-table>
        </el-card>
        <div slot="footer" class="dialog-footer">
          <el-button @click.native="hideForm1()">取消</el-button>
          <el-button type="primary" :loading="formLoading" @click.native="addGoods()">提交</el-button>
        </div>
      </el-dialog>
    </el-dialog>
    <!--    添加-->
    <el-dialog title="添加拼购商品" :visible.sync="selectDialogVisible" :before-close="hideForm2" width="70%" top="5vh">
      <el-card class="filter-container" shadow="never">
        <el-form ref="dataForm" :model="form" label-width="120px">
          <el-form-item label="活动标题：" prop="team_price">
            <el-input v-model="form.title" placeholder="请输入活动标题" style="width: 30%" type="textarea" autosize />
          </el-form-item>
          <el-form-item label="活动开始时间" prop="begin_time">
            <el-date-picker
              v-model="form.begin_time"
              style="width: 30%"
              type="datetime"
              placeholder="请选择活动开始时间"
              value-format="yyyy-MM-dd HH:mm:ss"
              :picker-options="startTime"
              @blur="checkTime()"
            />
          </el-form-item>
          <el-form-item label="活动结束时间" prop="end_time">
            <el-date-picker
              v-model="form.end_time"
              style="width: 30%"
              type="datetime"
              placeholder="请选择活动结束时间"
              value-format="yyyy-MM-dd HH:mm:ss"
              :picker-options="endTime"
              @blur="checkTime()"
            />
          </el-form-item>
        </el-form>
      </el-card>
      <br><br>
      <span>选择活动商品</span>
      <el-card class="filter-container" shadow="never">
        <el-input v-model="goodslist.keyword" clearable style="width: 250px;margin-bottom: 20px" size="small" placeholder="商品名称关键词">
          <el-button slot="append" icon="el-icon-search" @click="handleSelectSearch()" />
        </el-input>
        <el-table
          ref="multipleTable"
          :header-cell-style="{background:'#eef1f6',color:'#909399'}"
          :data="goods"
          border
          style="width: 100%"
          :row-key="handleReserve"
          @selection-change="handleSelectionChange"
        >
          <el-table-column
            :selectable="choice"
            type="selection"
            reserve-selection="reserve-selection"
            width="55"
            @click="drawer = true"
          />
          <el-table-column type="index”" label="商品图" align="center" width="150">
            <template slot-scope="scope">
              <img :src="scope.row.original_img" width="50px">
            </template>
          </el-table-column>
          <el-table-column label="商品名称" align="center" :show-overflow-tooltip="true">
            <template slot-scope="scope">{{ scope.row.goods_name }}</template>
          </el-table-column>
          <el-table-column label="市场价" width="160" align="center">
            <template slot-scope="scope">{{ scope.row.market_price }}</template>
          </el-table-column>
          <el-table-column label="拼购价" width="150" align="center">
            <template slot-scope="scope">{{ scope.row.team_price }}</template>
          </el-table-column>
          <el-table-column label="成团人数" width="150" align="center">
            <template slot-scope="scope">{{ scope.row.needer }}</template>
          </el-table-column>
          <el-table-column label="中奖人数" align="center">
            <template slot-scope="scope">{{ scope.row.stock_limit }}</template>
          </el-table-column>
          <el-table-column label="未中返利" align="center">
            <template slot-scope="scope">{{ scope.row.return_amount }}</template>
          </el-table-column>
          <el-table-column label="支付币种" align="center">
            <template slot-scope="scope">{{ scope.row.payCoin }}</template>
          </el-table-column>
          <el-table-column label="奖励币种" align="center">
            <template slot-scope="scope">{{ scope.row.getCoin }}</template>
          </el-table-column>
          <el-table-column label="商品状态" align="center">
            <template slot-scope="scope">
              <el-tag v-if="scope.row.goods_state == 0" type="warning">待上架</el-tag>
              <el-tag v-if="scope.row.goods_state == 1" type="success">已上架</el-tag>
              <el-tag v-if="scope.row.goods_state == 2" type="danger">已下架</el-tag>
            </template>
          </el-table-column>
        </el-table>
        <div class="pagination-container">
          <el-pagination
            background
            layout="total, sizes,prev, pager, next,jumper"
            :page-size="goodslist.limit"
            :page-sizes="[5,10,15]"
            :current-page.sync="goodslist.page"
            :total="goodstotal"
            @size-change="handleDialogSizeChange"
            @current-change="getGoodsList"
          />
        </div>
      </el-card>
      <div slot="footer">
        <el-button size="small" @click="hideForm2()">取 消</el-button>
        <el-button v-if="this.is_exist===1" size="small" type="primary" @click="handleSetting()">确 定</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import {
  activity,
  activityAdd,
  activityGoodsAdd,
  activityGoodsDel,
  checkGoods,
  checkPgGoods,
  checkTime,
  PgSave
} from '@/api/market'
import Pagination from '@/components/Pagination'

const formJson = {
  activity_id: '',
  needer: '',
  coin_id: '',
  luck_coin_id: '',
  stock_limit: '',
  return_amount: '',
  team_price: ''
}
export default {
  components: { Pagination },
  data() {
    return {
      coinList: {},
      query: {
        id: '',
        page: 1,
        limit: 20
      },
      goodslist: {
        page: 1,
        limit: 5,
        keyword: ''
      },
      form: {
        tableList: [],
        title: '',
        begin_time: '',
        end_time: ''
      },
      pgform: {
        keyword: '',
        addList: '',
        page: 1,
        limit: 20
      },
      startTime: {
        disabledDate: (time) => {
          return time.getTime() < Date.now() - 8.64e7
        }
      },
      endTime: {
        disabledDate: (time) => {
          return time.getTime() < Date.now() - 8.64e7
        }
      },
      goodsLists: [],
      pgGoods: [],
      pgtotal: 0,
      is_exist: '',
      goodstotal: 0,
      list: [],
      goods: [],
      total: 0,
      loading: true,
      index: null,
      isEdit: null,
      formName: null,
      formMap: {
        add: '新增',
        edit: '编辑'
      },
      delForm: {
        id: '',
        list: ''
      },
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false,
      listLoading: false,
      selectDialogVisible: false,
      innerVisible: false,
      langList: [],
      currentRow: null
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
    handleReserve(row) {
      return row.activity_id
    },
    handleSelectionChange(val) {
      this.selectIds = val.map(item => {
        return item
      })
      this.form.tableList = this.selectIds
    },
    handleDisable(row, index) {
      // 函数需要一个返回值,true为可选,false为不可选择
      if (row.is_found === '1') {
        return false
      } else {
        return true
      }
    },
    choice(row) {
      // 函数需要一个返回值,true为可选,false为不可选择
      if (row.goods_state === 2 || row.goods_state === 0) {
        return false
      } else {
        return true
      }
    },
    delSelectionChange(val) {
      this.selectIds = val.map(item => {
        return item
      })
      this.delForm.list = this.selectIds
    },
    handleSelectSearch() {
      this.getGoodsList()
    },
    SearchpgGoods() {
      this.getPgGoodsList(this.formData.id)
    },
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
    handleClose() {
      this.$refs.editFormRef.resetFields()
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
      activity(this.query)
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
    hideForm1() {
      // 更改值
      this.innerVisible = !this.innerVisible
      this.$refs.editFormRef.clearSelection()
      this.table = false
      return true
    },
    hideForm2() {
      // 更改值
      this.selectDialogVisible = !this.selectDialogVisible
      this.toggleSelection()
      return true
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      this.getPgGoodsList(row.id)
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
    toggleSelection(rows) {
      this.$refs.multipleTable.clearSelection()
      this.$refs.multipleTable.clearSelection()
      this.table = false
    },
    add() {
      this.selectDialogVisible = true
      this.getGoodsList()
    },
    getGoodsList() {
      checkGoods(this.goodslist).then(response => {
        this.goods = response.data.list
        this.goodstotal = response.data.total
      })
    },
    handleDialogSizeChange(val) {
      this.goodslist.page = 1
      this.goodslist.limit = val
      this.getGoodsList()
    },
    getPgGoodsList(id) {
      checkPgGoods({ id: id, keyword: this.pgform.keyword }).then(response => {
        this.pgGoods = response.data.list
        this.pgtotal = response.data.total
        this.goodsLists = response.data.goodsLists
      })
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          let data = Object.assign({}, this.formData)
          PgSave(data, this.formName).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            data = Object.assign(data, response.data)
            this.list.splice(this.index, 1, data)
            // 刷新表单
            this.resetForm()
            this.getList()
          })
            .catch(() => {
              this.formLoading = false
            })
        }
      })
    },
    handleSetting() {
      this.listLoading = true
      this.formName = 'add'
      activityAdd(this.form, this.formName).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          setTimeout(() => {
            this.goodslist.keyword = ''
            this.form.tableList = ''
            this.form.title = ''
            this.form.begin_time = ''
            this.form.end_time = ''
            this.toggleSelection()
            this.listLoading = false
          })
          this.selectDialogVisible = false
          this.getList()
        } else {
          this.$message({
            type: 'error',
            message: res.msg
          })
        }
      })
    },
    checkTime() {
      this.listLoading = true
      checkTime({ starTime: this.form.begin_time, endTime: this.form.end_time }).then(res => {
        this.is_exist = res.is_exist
        if (this.is_exist === 2) return this.$message.error('所选日期内已存在活动')
        return
      })
    },
    PgGoodsList(val) {
      this.selectIds = val.map(item => {
        return item
      })
      this.pgform.addList = this.selectIds
    },
    // 在活动中添加商品
    addGoods(row) {
      if (this.pgform.addList.length < 1) return this.$message.error('请选择商品')
      this.$confirm('确定将所选商品添加到该活动中?', '提示', {
        type: 'warning'
      })
        .then(() => {
          const para = { id: this.formData.id, list: this.pgform.addList }
          activityGoodsAdd(para)
            .then(response => {
              this.$message.success('操作成功')
              // 刷新数据
              this.innerVisible = false
              this.$refs.editFormRef.clearSelection()
              this.table = false
              this.getPgGoodsList(this.formData.id)
            })
            .catch(() => {
            })
        })
        .catch(() => {
          this.$message.info('取消')
        })
    },
    // 删除活动中的商品
    delGoods() {
      if (this.delForm.list.length < 1) return this.$message.error('请选择商品')
      this.$confirm('确定将所选商品从该活动中删除?', '提示', {
        type: 'warning'
      })
        .then(() => {
          const para = { id: this.formData.id, list: this.delForm.list }
          activityGoodsDel(para)
            .then(response => {
              this.$message.success('操作成功')
              // 刷新数据
              this.innerVisible = false
              this.$refs.delFormRef.clearSelection()
              this.table = false
              this.getPgGoodsList(this.formData.id)
            })
            .catch(() => {
            })
        })
        .catch(() => {
          this.$message.info('取消')
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
    width: 230px;
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
    width: 220px;
    height: 205px;
    display: block;
  }

  .el-table--border:after, .el-table--group:after, .el-table:before{
    z-index: 0;
  }
</style>
