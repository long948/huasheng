<!--<template>-->
  <!--<div class="app-container">-->
    <!--<el-tabs v-model="activeName" @tab-click="handleClick">-->
      <!--<el-tab-pane label="会员统计" name="members">-->
        <!--<div style="margin-left: 450px;">-->
          <!--<table class="table" width="60%">-->
            <!--<caption>-->
              <!--<h2>会员统计</h2>-->
            <!--</caption>-->
            <!--<tr>-->
              <!--<td>今日已认证人数</td>-->
              <!--<td>{{ Members.authenticated }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>今日未认证人数</td>-->
              <!--<td>{{ Members.unauthorized }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>已认证会员总量</td>-->
              <!--<td>{{ Members.authenticatedall }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>未认证会员总量</td>-->
              <!--<td>{{ Members.unauthorizedall }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>今日注册会员数</td>-->
              <!--<td>{{ Members.today }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>昨日注册会员数</td>-->
              <!--<td>{{ Members.lastday }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>该月注册会员数</td>-->
              <!--<td>{{ Members.tomonth }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>上月注册会员数</td>-->
              <!--<td>{{ Members.lastmonth }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>今年注册会员数</td>-->
              <!--<td>{{ Members.toyear }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>去年注册会员数</td>-->
              <!--<td>{{ Members.lastyear }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>总的注册会员数</td>-->
              <!--<td>{{ Members.all }}</td>-->
            <!--</tr>-->
          <!--</table>-->
        <!--</div>-->
      <!--</el-tab-pane>-->
      <!--<el-tab-pane label="管理员赠送树苗统计" name="h5Coin">-->
        <!--<div v-for="(item, index) in coinList" :key="index" style="padding: 50px;">-->
          <!--<br><br>-->
          <!--<div v-for="(a, i) in h5CoinList" :key="i" style="margin-top: -90px">-->
            <!--<div v-if="item.id === i">-->
              <!--<div v-if="a.miner_user_sapling">-->
                <!--<table class="table" width="50%" style="margin-left: 350px;">-->
                  <!--<caption>-->
                    <!--<h2>{{ item.nickname }}赠送统计</h2>-->
                  <!--</caption>-->
                  <!--<tr v-if="a.miner_user_sapling.today">-->
                    <!--<td>今日赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.today.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>今日赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.lastday">-->
                    <!--<td>昨天赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.lastday.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>昨天赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.tomonth">-->
                    <!--<td>该月赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.tomonth.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>该月赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.lastmonth">-->
                    <!--<td>上月赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.lastmonth.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>上月赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.toyear">-->
                    <!--<td>今年赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.toyear.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>今年赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.lastyear">-->
                    <!--<td>去年赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.lastyear.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>去年赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                  <!--<tr v-if="a.miner_user_sapling.all">-->
                    <!--<td>累计赠送数量</td>-->
                    <!--<td>{{ a.miner_user_sapling.all.Total }}</td>-->
                  <!--</tr>-->
                  <!--<tr v-else>-->
                    <!--<td>累计赠送数量</td>-->
                    <!--<td>0</td>-->
                  <!--</tr>-->
                <!--</table>-->
              <!--</div>-->
            <!--</div>-->
          <!--</div>-->
          <!--<br><br>-->
        <!--</div>-->
      <!--</el-tab-pane>-->
      <!--<el-tab-pane label="管理员充值EB统计" name="Admin">-->
        <!--<div style="margin-left: 450px;">-->
          <!--<table class="table" width="60%">-->
            <!--<caption>-->
              <!--<h2>管理员充值EB统计</h2>-->
            <!--</caption>-->
            <!--<tr>-->
              <!--<td>今日充值总额</td>-->
              <!--<td>{{ Admin.today }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>昨天充值总额</td>-->
              <!--<td>{{ Admin.lastday }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>本月充值EB总额</td>-->
              <!--<td>{{ Admin.tomonth }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>上月充值EB总额</td>-->
              <!--<td>{{ Admin.lastmonth }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>今年充值EB总额</td>-->
              <!--<td>{{ Admin.toyear }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>去年充值EB总额</td>-->
              <!--<td>{{ Admin.lastyear }}</td>-->
            <!--</tr>-->
            <!--<tr>-->
              <!--<td>充值总额</td>-->
              <!--<td>{{ Admin.all }}</td>-->
            <!--</tr>-->
          <!--</table>-->
        <!--</div>-->
      <!--</el-tab-pane>-->
    <!--</el-tabs>-->
  <!--</div>-->
<!--</template>-->

<!--<script>-->
<!--import {-->
  <!--statistics-->
<!--} from '@/api/user.js'-->
<!--import waves from '@/directive/waves'-->
<!--import permission from '@/directive/permission/index.js'-->

<!--export default {-->
  <!--directives: {-->
    <!--waves,-->
    <!--permission-->
  <!--},-->
  <!--data() {-->
    <!--return {-->
      <!--h5CoinList: {},-->
      <!--IA_count: {},-->
      <!--IA_sum: {},-->
      <!--coinList: {},-->
      <!--Machine: {},-->
      <!--activeName: 'members',-->
      <!--IALockStoreRewardLog: {},-->
      <!--IAMemberExtend: {},-->
      <!--order_money: {},-->
      <!--Members: {},-->
      <!--Admin: {},-->
      <!--Comment: {},-->
      <!--Record: {}-->
    <!--}-->
  <!--},-->
  <!--created() {-->
    <!--this.handleClick({-->
      <!--name: 'members'-->
    <!--})-->
  <!--},-->
  <!--methods: {-->
    <!--handleClick(t) {-->
      <!--if (t.name === 'h5Coin') {-->
        <!--this.getList(t.name).then(res => {-->
          <!--this.h5CoinList = res.List-->
          <!--this.coinList = res.CoinList-->
        <!--})-->
      <!--} else if (t.name === 'order') {-->
        <!--this.getList(t.name).then(res => {-->
          <!--this.IALockStoreRewardLog = res.IALockStoreRewardLog-->
          <!--this.IAMemberExtend = res.IAMemberExtend-->
        <!--})-->
      <!--} else if (t.name === 'members') {-->
        <!--this.getList(t.name).then(res => {-->
          <!--this.Members = res-->
          <!--console.log(res)-->
        <!--})-->
      <!--} else if (t.name === 'Admin') {-->
        <!--this.getList(t.name).then(res => {-->
          <!--this.Admin = res-->
        <!--})-->
      <!--}-->
    <!--},-->

    <!--async getList(type) {-->
      <!--const res = await statistics({-->
        <!--type: type-->
      <!--})-->
      <!--return res.data-->
    <!--}-->
  <!--}-->
<!--}-->
<!--</script>-->
<!--<style>-->
  <!--.title {-->
    <!--font-size: 25px;-->
    <!--color: blue;-->
  <!--}-->

  <!--.content {-->
    <!--margin-bottom: 20px;-->
    <!--font-size: 18px;-->
  <!--}-->

  <!--.table {-->
    <!--border-collapse: collapse;-->
    <!--margin: 0 auto;-->
    <!--text-align: center;-->
    <!--float: left;-->
    <!--table-layout: fixed;-->
  <!--}-->

  <!--.table td,-->
  <!--.table th {-->
    <!--border: 1px solid #cad9ea;-->
    <!--color: #666;-->
    <!--height: 30px;-->
  <!--}-->

  <!--.table thead th {-->
    <!--background-color: #CCE8EB;-->
    <!--width: 100px;-->
  <!--}-->

  <!--.table tr:nth-child(odd) {-->
    <!--background: #fff;-->
  <!--}-->

  <!--.table tr:nth-child(even) {-->
    <!--background: #F5FAFA;-->
  <!--}-->
<!--</style>-->
