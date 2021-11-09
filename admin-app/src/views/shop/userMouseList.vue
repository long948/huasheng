<template>

  <div class="app-container">
    <el-form :inline="true" :model="query" class="query-form" size="mini">
      <el-form-item class="query-form-item" label="用户名称">
        <el-input v-model="query.NickName" placeholder="用户名称" />
      </el-form-item>
      <el-form-item class="query-form-item" label="用户电话">
        <el-input v-model="query.Phone" placeholder="用户电话" />
      </el-form-item>
      <el-form-item>
        <el-button-group>
          <el-button type="primary" icon="el-icon-refresh" @click="onReset" />
          <el-button type="primary" icon="search" @click="onSubmit">查询</el-button>
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
        label="用户手机号"
        prop="Phone"
      />
      <el-table-column
        align="center"
        label="老鼠名称"
        prop="nickname"
      />
      <el-table-column label="是否需要疗伤" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_healing == 0">否</span>
          <span v-else>是</span>
        </template>
      </el-table-column>
      <el-table-column label="是否正在疗伤" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_in_healing == 0">否</span>
          <span v-else>是</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="疗伤时间"
        prop="healing_time"
      />
      <el-table-column
        align="center"
        label="疗伤恢复时间"
        prop="healing_time_ltt"
      />
      <el-table-column
        align="center"
        label="当前偷取次数"
        prop="frequency"
      />
      <el-table-column
        align="center"
        label="最多偷取次数"
        prop="max_frequency"
      />

      <!--<el-table-column label="是否属于体验" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.is_experience == 0">否</span>-->
      <!--<span v-else>是</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_disable == 0">否</span>
          <span v-else>是</span>
        </template>
      </el-table-column>
      <el-table-column label="是否删除" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.is_delete == 0">否</span>
          <span v-else>是</span>
        </template>
      </el-table-column>
      <el-table-column
        align="center"
        label="创建时间"
        prop="create_time"
      />
      <el-table-column
        align="center"
        label="操作"
      >
        <template slot-scope="scope">
          <el-button v-if="scope.row.is_delete==0" type="danger" size="small" @click.native="handleDel(scope.$index, scope.row)">删除
          </el-button>
          <el-button v-if="scope.row.is_delete==1" type="info" size="small">已删除
          </el-button>
          <!--<el-button type="text" size="small" @click.native="Disable(scope.$index, scope.row)">禁用-->
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
  </div>

</template>

<script>
import { user_sapling_package, user_sapling_packageDel
} from '@/api/members'
// import Tinymce from '../../components/Tinymce/index.vue'
const formJson = {
  id: '',
  NickeName: '',
  Phone: '',
  sapling_share_reward_id: '',
  reward: '',
  create_time: ''
}
export default {
  // components: {
  //   Tinymce
  // },

  data() {
    return {
      query: {
        id: '',
        page: 1,
        limit: 20
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
      user_sapling_package(this.query)
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
    // 删除
    handleDel(index, row) {
      if (row.id) {
        this.$confirm('确认删除该记录吗?', '提示', {
          type: 'warning'
        })
          .then(() => {
            const para = { id: row.id }
            this.deleteLoading = true
            user_sapling_packageDel(para)
              .then(response => {
                this.deleteLoading = false
                this.$message.success('操作成功')
                // 刷新数据
                this.list.splice(index, 1)
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
    }
    // // 禁用
    // Disable(index, row) {
    //   if (row.id) {
    //     this.$confirm('确认禁用该用户拥有的机器人吗?', '提示', {
    //       type: 'warning'
    //     })
    //       .then(() => {
    //         const para = { id: row.id }
    //         this.deleteLoading = true
    //         ConvertProductDel(para)
    //           .then(response => {
    //             this.deleteLoading = false
    //             this.$message.success('操作成功')
    //             // 刷新数据
    //             this.list.splice(index, 1)
    //           })
    //           .catch(() => {
    //             this.deleteLoading = false
    //           })
    //       })
    //       .catch(() => {
    //         this.$message.info('取消删除')
    //       })
    //   }
    // }
  }
}
</script>

<style type="text/scss" lang="scss">
</style>
