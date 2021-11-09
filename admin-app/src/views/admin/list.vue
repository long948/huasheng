<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />
        &nbsp;&nbsp;添加管理员
      </el-button>
      <el-button v-waves class="filter-item" type="primary" @click="guge()">
        <i class="el-icon-plus" />
        &nbsp;&nbsp;更换谷歌验证码秘钥
      </el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="ID" prop="id" sortable="custom" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Id }}</span>
        </template>
      </el-table-column>
      <el-table-column label="Name" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.Status == 0">启用</span>
          <span v-else>冻结</span>
        </template>
      </el-table-column>
      <!--      <el-table-column label="所属权限组" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.RuleGroupName }}</span>
        </template>
      </el-table-column> -->
      <el-table-column label="描述" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Introduction }}</span>
        </template>
      </el-table-column>
      <el-table-column label="创建时间" align="center">
        <template slot-scope="{ row }">
          {{ row.AddTime }}
        </template>
      </el-table-column>
      <el-table-column v-permission="['/admin/updateAdmin', '/admin/del']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/admin/updateAdmin']"><el-button size="small" type="primary" @click="edit(scope.row)">编辑</el-button></span>
            <span v-permission="['/admin/del']"><el-button size="small" type="danger" @click="confirmDelete(scope.row)">删除</el-button></span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />

    <el-dialog :title="title" :visible.sync="showPageVisible">
      <el-form>
        <el-form-item label="用户名" label-width="150px"><el-input v-model="adminUser.name" /></el-form-item>

        <el-form-item label="密码" label-width="150px"><el-input v-model="adminUser.password" /></el-form-item>

        <el-form-item label="个人介绍" label-width="150px"><el-input v-model="adminUser.introduction" type="textarea" /></el-form-item>

        <!-- <el-form-item label="是否可删除" label-width="150px">
          <el-radio-group v-model="adminUser.couldNotDel">
            <el-radio :label=0>否</el-radio>
            <el-radio :label=1>是</el-radio>
          </el-radio-group>
        </el-form-item> -->

        <el-form-item label="权限组" label-width="150px">
          <el-checkbox-group v-model="adminUser.ruleGroup"><el-checkbox v-for="(item, index) in rule" :key="index" :label="item.Name" /></el-checkbox-group>
        </el-form-item>

        <!-- <el-form-item label="头像" label-width="150px">
          <Upload v-model="adminUser.avatar"/>
        </el-form-item> -->
      </el-form>
      <div slot="footer" class="dialog-footer"><el-button type="primary" @click="handleAddEdit">确 定</el-button></div>
    </el-dialog>

    <el-dialog title="更换谷歌验证码秘钥" :visible.sync="show">
      <el-form>
        <el-form-item label="请输入验证码" label-width="150px"><el-input v-model="yzm" /></el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer"><el-button type="primary" @click="guge2()">确 定</el-button></div>
    </el-dialog>
  </div>
</template>

<script>
import { fetchList, delAdmin, ruleList, addAdmin, updateAdmin, getAdmin, adminGuge } from '@/api/admin'
import waves from '@/directive/waves'
import { parseTime } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import Upload from '@/components/Upload/SingleImage3'

export default {
  name: 'AdminList',
  components: { Pagination, Upload },
  directives: { waves, permission },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20
      },
      Guge: '',

      showPageVisible: false,
      adminUser: {
        id: 0,
        name: '',
        password: '',
        ruleGroup: [],
        introduction: '',
        avatar: '',
        couldNotDel: 1
      },
      title: '添加管理员',

      rule: null,
      show: false,
      yzm: ''
    }
  },
  created() {
    this.getList()
    this.ruleList()
  },
  methods: {
    confirmDelete(row) {
      this.$confirm('确认删除该管理员?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          delAdmin({ Id: row.Id }).then(res => {
            this.getList()
            this.$message({
              message: '管理员已删除',
              type: 'success'
            })
          })
        })
        .catch(() => {})
    },
    getList() {
      this.listLoading = true
      fetchList(this.listQuery).then(res => {
        this.list = res.data.list.List
        this.total = res.data.total
        this.Guge = res.data.list.Guge
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },

    add() {
      this.adminUser.id = false
      this.showPageVisible = true
    },

    // 提交数据
    handleAddEdit() {
      if (this.adminUser.id) {
        updateAdmin(this.adminUser).then(res => {
          if (res.code == 20000) {
            this.$message({
              type: 'success',
              message: res.msg
            })
            this.getList()
            this.showPageVisible = false
          }
        })
      } else {
        addAdmin(this.adminUser).then(res => {
          if (res.code == 20000) {
            this.$message({
              type: 'success',
              message: res.msg
            })
            this.getList()
            this.showPageVisible = false

            this.$confirm(res.data.Guge, '创建成功，该账号谷歌验证码秘钥为:', {
              confirmButtonText: '确定',
              cancelButtonText: '取消',
              type: 'warning'
            })
          }
        })
      }
    },

    // 获取权限组列表
    ruleList() {
      ruleList().then(res => {
        if (res.code == 20000) {
          this.rule = res.data
        }
      })
    },
    guge() {
      this.show = true
    },
    async guge2() {
      const res = await adminGuge({ yzm: this.yzm })
      console.log(res)
      if (res.code == 20000) {
        this.Guge = res.data
        this.$message({
          type: 'success',
          message: res.data
        })
      } else return

      this.$confirm(this.Guge, '当前谷歌验证码秘钥为，是否确认更换？', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      })
        .then(() => {
          adminGuge({ is: 1, yzm: this.yzm }).then(res => {
            if (res.code == 20000) {
              this.Guge = res.data
              this.$message({
                type: 'success',
                message: res.data
              })
              this.$confirm(res.data, '更换谷歌验证码秘钥成功，请牢记秘钥', {
                confirmButtonText: '确定',
                cancelButtonText: '取消',
                type: 'warning'
              })
                .then(() => {
                  return
                })
                .catch(() => {
                  return
                })
            }
          })
        })
        .catch(() => {
          return
        })
    },
    edit(row) {
      getAdmin({ id: row.Id }).then(res => {
        if (res.code == 20000) {
          this.adminUser.id = Number(row.Id)
          this.adminUser.name = res.data.Name
          this.adminUser.password = ''
          this.adminUser.ruleGroup = res.data.rouleName.split(',')
          this.adminUser.introduction = res.data.Introduction
          this.adminUser.avatar = res.data.Avatar
          this.adminUser.couldNotDel = Number(res.data.CouldNotDel)

          this.showPageVisible = true
        }
      })
    }
  }
}
</script>
