<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves class="filter-item" type="primary" v-permission="['/rule/group/add']" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加权限组
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
      <el-table-column label="组名" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Name }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsClose == 0">开启</span>
          <span v-else>关闭</span>
        </template>
      </el-table-column>
      <el-table-column label="操作" width="150" align="center" v-permission="['/rule/group/edit','/rule/group/del']">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/rule/group/edit']">
                <el-button
                  size="small"
                  type="primary"
                  @click="edit(scope.row.Id)"
                >
                  编辑
                </el-button>
            </span>
            <span v-permission="['/rule/group/del']">
              <el-button
                size="small"
                type="danger"
                @click="del(scope.row)"
              >
                删除
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
  </div>
</template>

<script>
import { getGroup, delGroup } from '@/api/rule'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'

export default {
  name: 'RuleGroup',
  directives: { waves, permission },
  data() {
    return {
      tableKey: 0,
      list: null,
      total: 0,
      listLoading: true,
      listQuery: {
        page: 1,
        limit: 20,
        importance: undefined,
        title: undefined,
        type: undefined,
        sort: '+id'
      },
    }
  },
  created() {
    this.getList()
  },
  methods: {
    edit(id){
      this.$router.push({ name: 'GroupEdit', params: { id: id }})
    },
    getList() {
      this.listLoading = true
      getGroup(this.listQuery).then(res => {
        this.list = res.data
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    add(){
      this.$router.push({ name: 'GroupAdd'})
    },
    del(row){
      this.$confirm('确认删除该权限组?', '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        delGroup({Id: row.Id}).then(res =>{
          this.$message({
            message: '已删除权限组',
            type: 'success'
          })
          this.getList()
        }).catch(()=>{
          
        })
      }).catch(()=>{

      })
    }
  }
}
</script>
