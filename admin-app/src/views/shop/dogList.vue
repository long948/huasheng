<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <!--<el-button type="primary" @click.native="handleForm(null,null)">新增</el-button>-->
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
        label="小狗名称"
        prop="nickname"
      />
      <el-table-column
        align="center"
        label="等级"
        prop="level"
      />
      <el-table-column
        align="center"
        label="售价"
        prop="price"
      />
      <el-table-column
        align="center"
        label="支付币种"
        prop="EnName"
      />
      <el-table-column
        align="center"
        label="最小防御成功比例"
        prop="min_defense"
      />
      <el-table-column
        align="center"
        label="最大防御成功比例"
        prop="max_defense"
      />
      <el-table-column
        align="center"
        label="最多防御次数"
        prop="max_defense_count"
      />
      <el-table-column label="站岗后是否需要休息" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_defense_interval == 1">需要</span>
          <span v-else-if="scope.row.is_defense_interval == 0">不需要</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="站岗后的休息时间（分钟）"
        prop="defense_interval_time"
      />
      <el-table-column
        align="center"
        label="站岗有效时长(分钟)"
        prop="stand_guard_time_ltt"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="最大持有数量(只限制购买，赠送不限制)"-->
      <!--prop="max_hold"-->
      <!--width="110px"-->
      <!--/>-->
      <!--<el-table-column label="是否属于体验" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.is_experience == 0">否</span>-->
      <!--<span v-else-if="scope.row.is_experience == 1">是</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_disable == 0" style="color: green">否</span>
          <span v-else-if="scope.row.is_disable == 1" style="color:red;">是</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="更新时间"
        prop="update_time"
      />
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">编辑
          </el-button>
          <!--<el-button v-if="scope.row.is_delete==0" type="text" size="small" @click.native="handleDel(scope.$index, scope.row)">删除-->
          <!--</el-button>-->
        </template>
      </el-table-column>
    </el-table>

    <el-pagination
      :page-size="query.limit"
      layout="prev, pager, next"
      :total="total"
      @current-change="handleCurrentChange"
    />

    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="60%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData">
        <el-form-item label="小狗名称:" prop="nickname" label-width="150px">
          <el-input v-model="formData.nickname" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 80px">等级：</label>
          <el-input v-model="formData.level" disabled auto-complete="off" style="width: 30%" />
        </el-form-item>

        <el-form-item label="售价:" prop="price" label-width="150px">
          <el-input v-model="formData.price" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 24px">最多防御次数：</label>
          <el-input v-model="formData.max_defense_count" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="说明:" prop="explanation" label-width="150px">
          <el-input v-model="formData.explanation" type="textarea" autosize placeholder="请输入说明" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 110px">是否禁用</label>
          <el-radio-group v-model="formData.is_disable">
            <el-radio :label="1">是</el-radio>
            <el-radio :label="0">否</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="站岗后是否需要休息:" prop="is_defense_interval" label-width="150px">
          <el-radio-group v-model="formData.is_defense_interval">
            <el-radio :label="1">需要</el-radio>
            <el-radio :label="0">不需要</el-radio>
          </el-radio-group>
          <label v-if="formData.is_defense_interval==1" style="margin-left: 64px"> 站岗后的休息时间（分钟）：</label>
          <el-input v-if="formData.is_defense_interval==1" v-model="formData.defense_interval_time" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="防御成功比例:" prop="min_defense" label-width="150px">
          <el-input v-model="formData.min_defense" auto-complete="off" style="width: 30%" />
          <label> &nbsp;&nbsp; 至 &nbsp;&nbsp; </label>
          <el-input v-model="formData.max_defense" auto-complete="off" style="width: 30%" />
          <span style="color: red">(1表示100%)</span>
        </el-form-item>
        <el-form-item label="站岗有效时长(分钟):" prop="defense_interval_time" label-width="150px">
          <el-input v-model="formData.stand_guard_time_ltt" auto-complete="off" style="width: 30%" />
          <!--<label style="margin-left: 25px">最大持有数量：</label>-->
          <!--<el-input v-model="formData.max_hold" auto-complete="off" style="width: 30%" />-->
          <!--<span style="color: red">(只限制购买，赠送不限制)</span>-->
          <label style="margin-left: 52px">支付币种：</label>
          <el-select v-model="formData.coin_id" style="width: 30%">
            <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
          </el-select>
        </el-form-item>
        <el-form-item label="创建时间:" prop="create_time" label-width="150px">
          <el-input v-model="formData.create_time" disabled auto-complete="off" style="width: 30%" />
          <label style="margin-left: 52px">更新时间：</label>
          <el-input v-model="formData.update_time" disabled auto-complete="off" style="width: 30%" />
        </el-form-item>
        <!--<el-form-item label-width="150px" label="是否属于体验:" prop="is_experience">-->
        <!--<el-radio-group v-model="formData.is_experience">-->
        <!--<el-radio :label="0">否</el-radio>-->
        <!--<el-radio :label="1">是</el-radio>-->
        <!--</el-radio-group>-->
        <!--</el-form-item>-->
        <el-form-item label="图标:" label-width="150px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="formData.iconUrl" :src="formData.iconUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>&nbsp;&nbsp;&nbsp;&nbsp;
          <a target="_blank" :href="formData.iconUrl"> <span style="color: red">点击查看图标</span></a>
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
import {
  DogList, saplingPackageDel, DogListSave
} from '@/api/miner'
import {
  qiniuToken
} from '@/api/dateHandle'
const formJson = {
  id: '',
  nickname: '',
  icon: '',
  iconUrl: '',
  level: '',
  price: '',
  min_defense: '',
  max_defense: '',
  max_defense_count: '',
  is_defense_interval: '',
  defense_interval_time: '',
  stand_guard_time_ltt: '',
  max_hold: '',
  explanation: '',
  sort: '',
  create_time: '',
  is_experience: '',
  is_disable: '',
  is_delete: '',
  coin_id: ''

}
export default {

  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 20
      },
      coinList: [],
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
    this.qiniuGet()
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
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    handleAvatarSuccess(res, file) {
      this.formData.icon = res.key
      this.formData.iconUrl = this.qiniu.Domain + res.key
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
      DogList(this.query)
        .then(response => {
          this.loading = false
          this.list = response.data.lists
          this.coinList = response.data.coinList || []
        })
        .catch(() => {
          this.loading = false
          this.data = []
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
    handleDel(index, row) {
      if (row.id) {
        this.$confirm('确认删除该记录吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            saplingPackageDel(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                // 刷新数据
                this.getList()
              })
              .catch(() => {
                this.deleteLoading = false
              })
          })
          .catch(() => {
            this.$message.info('取消删除')
          })
      }
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          DogListSave(data, this.formName).then(response => {
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
