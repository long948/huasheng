<template>
  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="商品名称">
        <el-input v-model="query.goods_name" placeholder="关键词" />
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
      <el-table-column type="index”" label="商品图" align="center" width="130">
        <template slot-scope="scope">
          <img v-image-preview :src="scope.row.original_img" width="60px">
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="商品名称"
        :show-overflow-tooltip="true"
        prop="goods_name"
        width="200px"
      />
      <el-table-column label="市场价" align="center">
        <template slot-scope="{ row }">
          {{ parseFloat(row.market_price) }}
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="活动口号"
        prop="act_name"
      />
      <el-table-column
        align="center"
        label="成团人数"
        prop="needer"
      />
      <el-table-column
        align="center"
        label="中奖人数"
        prop="stock_limit"
      />
      <el-table-column
        align="center"
        label="未中金额"
        prop="return_amount"
      />
      <el-table-column
        align="center"
        label="支付币种"
        prop="payCoin"
      />
      <el-table-column
        align="center"
        label="中奖获得币种"
        prop="getCoin"
      />
      <el-table-column label="商品状态" align="center">
        <template slot-scope="scope">
          <el-tag v-if="scope.row.goods_state == 0" type="warning">待上架</el-tag>
          <el-tag v-if="scope.row.goods_state == 1" type="success">已上架</el-tag>
          <el-tag v-if="scope.row.goods_state == 2" type="danger">已下架</el-tag>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="添加时间"
        prop="create_time"
        width="180"
      />
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

    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="65%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData" label-width="140px">
        <el-form-item label="商品名称：" prop="team_price">
          <el-input v-model.trim="formData.goods_name" disabled style="width: 30%" />
          <label style="margin-left: 78px">市场价格：</label>
          <el-input v-model.trim="formData.market_price" disabled auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="活动口号：" prop="act_name">
          <el-input v-model="formData.act_name" type="textarea" autosize auto-complete="off" style="width: 30%" />
          <label style="margin-left: 78px">支付币种：</label>
          <el-select v-model.trim="formData.coin_id" style="width: 30%">
            <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="拼购价格：" prop="team_price">
          <el-input-number v-model.trim="formData.team_price" label="描述文字" :min="0" style="width: 30%" />
          <label style="margin-left: 78px">成团人数：</label>
          <el-input-number v-model.trim="formData.needer" :min="1" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="中奖人数：" prop="stock_limit">
          <el-input-number v-model.trim="formData.stock_limit" :min="1" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 78px">未中返利：</label>
          <el-input-number v-model.trim="formData.return_amount" :min="0" auto-complete="off" style="width: 30%" />
          <span style="color: red;font-size: smaller">不包含本金</span>
        </el-form-item>
        <el-form-item label="中奖获得的币种：" prop="team_price">
          <el-select v-model="formData.luck_coin_id" style="width: 30%">
            <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
          </el-select>
          <label style="margin-left: 78px">获得金额：</label>
          <el-input-number v-model.trim="formData.luck_amount" :min="1" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="商品库存：" prop="store_count">
          <el-input v-model.trim="formData.store_count" auto-complete="off" style="width: 30%" @keyup.native="proving1" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" :loading="formLoading" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
    <!--    添加-->
    <el-dialog title="添加拼购商品" :visible.sync="selectDialogVisible" width="70%" top="5vh">
      <el-card class="filter-container" shadow="never">
        <el-form ref="dataForm" :model="form" label-width="140px">
          <el-form-item label="活动口号：" prop="act_name">
            <el-input v-model="form.act_name" type="textarea" autosize auto-complete="off" style="width: 30%" />
            <label style="margin-left: 78px">支付币种：</label>
            <el-select v-model.trim="form.coin_id" style="width: 30%">
              <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
            </el-select>
          </el-form-item>
          <el-form-item label="拼购价格：" prop="team_price">
            <el-input-number v-model.trim="form.team_price" label="描述文字" :min="0" style="width: 30%" />
            <label style="margin-left: 78px;color: #606266">成团人数：</label>
            <el-input-number v-model.trim="form.needer" :min="1" auto-complete="off" style="width: 30%" />
          </el-form-item>
          <el-form-item label="中奖人数：" prop="stock_limit">
            <el-input-number v-model.trim="form.stock_limit" :min="1" auto-complete="off" style="width: 30%" />
            <label style="margin-left: 78px;color: #606266">未中返利：</label>
            <el-input-number v-model.trim="form.return_amount" :min="0" auto-complete="off" style="width: 30%" />
            <span style="color: red;font-size: smaller">不包含本金</span>
          </el-form-item>
          <el-form-item label="中奖获得的币种：" prop="team_price">
            <el-select v-model="form.luck_coin_id" style="width: 30%">
              <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
            </el-select>
            <label style="margin-left: 78px;color: #606266">获得金额：</label>
            <el-input-number v-model.trim="form.luck_amount" :min="1" auto-complete="off" style="width: 30%" />
          </el-form-item>
          <el-form-item label="商品库存：" prop="store_count">
            <el-input v-model.trim="form.store_count" auto-complete="off" style="width: 30%" @keyup.native="proving1" />
          </el-form-item>
          <!--          <el-alert-->
          <!--            title="注：中奖获得的币种"-->
          <!--            type="error">-->
          <!--          </el-alert>-->
        </el-form>
      </el-card>
      <br><br>
      <span>选择商品</span>
      <el-card class="filter-container" shadow="never">
        <el-input v-model="goodslist.keyword" clearable style="width: 250px;margin-bottom: 20px" size="small" placeholder="商品名称关键词">
          <el-button slot="append" icon="el-icon-search" @click="handleSelectSearch()" />
        </el-input>
        <el-table ref="multipleTable" :data="goods" :header-cell-style="{background:'#eef1f6',color:'#909399'}" fixed border stripe @select="select" @row-click="rowClick" @selection-change="selectionChange">
          <el-table-column
            type="selection"
            width="55"
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
        <el-button size="small" @click="selectDialogVisible = false">取 消</el-button>
        <el-button size="small" type="primary" @click="handleSetting()">确 定</el-button>
      </div>
    </el-dialog>
  </div>

</template>

<script>
import { getGoodsList, Pg, PgSave } from '@/api/market'
import { coinList } from '@/api/coin'
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
        goods_id: [],
        needer: '',
        coin_id: '',
        luck_coin_id: '',
        stock_limit: '',
        return_amount: '',
        team_price: '',
        luck_amount: '',
        act_name: '',
        store_count: ''
      },
      goodstotal: 0,
      qiniu: {},
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
      formLoading: false,
      formVisible: false,
      formData: formJson,
      deleteLoading: false,
      listLoading: false,
      selectDialogVisible: false,
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
    if (this.$route.params.goodsName) {
      this.goodslist.keyword = this.$route.params.goodsName
      this.selectDialogVisible = true
      this.getGoodsList()
      this.getCoinList()
    }
    // 消息列表
    this.getList()
  },
  methods: {
    proving1() {
      this.formData.store_count = this.formData.store_count.replace(/[^\.\d]/g, '')
      this.formData.store_count = this.formData.store_count.replace('.', '')
    },
    select(selection, row) {
      // 清除 所有勾选项
      this.$refs.multipleTable.clearSelection()
      // 当表格数据都没有被勾选的时候 就返回
      // 主要用于将当前勾选的表格状态清除
      if (selection.length === 0) return
      this.$refs.multipleTable.toggleRowSelection(row, true)
    },
    // 表格的选中 可以获得当前选中的数据
    selectionChange(val) {
      // 将选中的数据存储起来
      this.selectData = val
    },
    // 表格某一行的单击事件
    rowClick(row, column) {
      const selectData = this.selectData
      if (selectData === undefined) {
        return
      }
      this.$refs.multipleTable.clearSelection()
      if (selectData.length === 1) {
        selectData.forEach(item => {
          // 判断 如果当前的一行被勾选, 再次点击的时候就会取消选中
          if (item === row) {
            this.$refs.multipleTable.toggleRowSelection(row, false)
          } else {
            this.$refs.multipleTable.toggleRowSelection(row, true)
          }
        })
      } else {
        this.$refs.multipleTable.toggleRowSelection(row, true)
      }
    },
    handleSelectSearch() {
      this.getGoodsList()
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
      Pg(this.query)
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
    getCoinList() {
      coinList().then(res => {
        this.coinList = res.data.data
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
      this.getCoinList()
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
    add() {
      this.goodslist.keyword = ''
      this.selectDialogVisible = true
      this.getGoodsList()
      this.getCoinList()
    },
    getGoodsList() {
      getGoodsList(this.goodslist).then(response => {
        this.goods = response.data.list
        this.goodstotal = response.data.total
      })
    },
    handleDialogSizeChange(val) {
      this.goodslist.page = 1
      this.goodslist.limit = val
      this.getGoodsList()
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
      if (this.selectData) {
        if (this.selectData.length > 1) {
          return this.$message.error('每次仅可设置单个拼购商品')
        } else if (this.selectData.length === 1) {
          this.form.goods_id = this.selectData[0].goods_id
        } else {
          this.form.goods_id = ''
        }
      }
      PgSave(this.form, this.formName).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          setTimeout(() => {
            this.listLoading = false
          })
          this.selectDialogVisible = false
          this.getList()
          this.form = {}
        } else {
          this.$message({
            type: 'error',
            message: res.msg
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
