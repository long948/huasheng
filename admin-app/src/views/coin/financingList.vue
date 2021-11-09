<template>
  <div class="app-container">
    <div class="filter-container">
      按会员Id快速查询
      <el-input v-model="listQuery.keywords" placeholder="关键字" style="width: 200px;" class="filter-item" @keyup.enter.native="handleFilter" />
      按变动类型
      <el-select v-model="listQuery.Mold" placeholder="变动类型" clearable style="width: 150px" class="filter-item">
        <el-option v-for="(item, index) in moldList" :key="index" :label="item.title" :value="item.id" />
      </el-select>
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">快捷搜索</el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="会员Id" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.MemberId }}</span>
        </template>
      </el-table-column>
      <el-table-column label="会员手机号" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="币种Id--名称" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.CoinId }} -- {{ scope.row.CoinName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="资金类型" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.MoldTitle }}</span>
        </template>
      </el-table-column>
      <el-table-column label="变动金额" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.Money > 0" style="color: green;">{{ parseFloat(scope.row.Money) }}</span>
          <span v-else style="color: red;">{{ parseFloat(scope.row.Money) }}</span>
        </template>
      </el-table-column>
      <el-table-column label="变动后余额" align="center">
        <template slot-scope="scope">
          <span>{{ parseFloat(scope.row.Balance) }}</span>
        </template>
      </el-table-column>
      <el-table-column label="备注" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Remark }}</span>
        </template>
      </el-table-column>
      <el-table-column label="变动时间" align="center">
        <template slot-scope="scope">
          <span>{{ _date(scope.row.AddTime) }}</span>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
  </div>
</template>

<script>
import { financingList,financingMoldList } from '@/api/coin';
import waves from '@/directive/waves';
import { parseTime } from '@/utils';
import Pagination from '@/components/Pagination';
import permission from '@/directive/permission/index.js';
import { dateHandle } from '@/api/dateHandle';

export default {
  components: { Pagination },
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
        keywords: ''
      },
      moldList:{}
    };
  },
  created() {
    this.getList();
    this.getMoldList();
  },

  methods: {
    getMoldList(){
      financingMoldList().then (res=>{
        this.moldList = res.data;
      });
    },
    getList() {
      this.listLoading = true;
      financingList(this.listQuery).then(res => {
        this.list = res.data.data;
        this.total = res.data.total;
        setTimeout(() => {
          this.listLoading = false;
        }, 0.3 * 1000);
      });
    },

    _date(t) {
      return dateHandle('Y-m-d H:i:s', t);
    },
    sum(i) {
      this.allNumber += i;
    }
  }
};
</script>
