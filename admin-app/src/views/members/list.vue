<template>
  <div class="app-container">
    <div class="filter-container">
      <el-input v-model="listQuery.NickName" placeholder="昵称" style="width: 200px;" class="filter-item" />
      <el-input v-model="listQuery.Phone" placeholder="手机号" style="width: 200px;" class="filter-item" />
      <!--<el-select v-model="listQuery.Level" placeholder="社区等级" clearable style="width: 150px" class="filter-item">-->
      <!--<el-option v-for="(item, index) in levels" :key="index" :label="item.Name" :value="item.Level" />-->
      <!--</el-select>-->
      <el-input
        v-model="listQuery.IdCard"
        placeholder="身份证号"
        style="width: 200px;"
        class="filter-item"
      />
      <!--<el-select v-model="listQuery.IsBan" placeholder="是否锁定">-->
      <!--<el-option label="全部" value="0" />-->
      <!--<el-option label="未锁定" value="0" />-->
      <!--<el-option label="被锁定" value="1" />-->
      <!--</el-select>-->
      <el-select v-model="listQuery.IsBan" placeholder="是否锁定" clearable style="width: 150px" class="filter-item">
        <el-option v-for="(item, index) in {0:'未锁定',1:'被锁定',100:'全部'}" :key="index" :label="item" :value="index" />
      </el-select>
      <el-button class="filter-item" type="primary" icon="el-icon-refresh" @click="onReset" />
      <el-button v-waves class="filter-item" type="primary" icon="el-icon-search" @click="getList">搜索</el-button>
    </div>
    <el-table :key="tableKey" v-loading="listLoading" type="selection" element-loading-text="拼命加载中(长时间未加载出来请按F5刷新页面)" :data="list" border fit highlight-current-row style="width: 100%;">
      <el-table-column label="昵称" align="center" width="110">
        <template slot-scope="scope">
          <span>{{ scope.row.NickName }}</span>
        </template>
      </el-table-column>
      <el-table-column label="状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsBan == 0" style="color: green;">启用</span>
          <span v-else style="color: red;">禁用</span>
        </template>
      </el-table-column>
      <el-table-column label="手机号" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.Phone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="上级ID" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.ParentId }}</span>
        </template>
      </el-table-column>
      <!--<el-table-column label="上级姓名" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span>{{ scope.row.ParentName }}</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column label="上级手机号" align="center">
        <template slot-scope="scope">
          <span>{{ scope.row.ParentPhone }}</span>
        </template>
      </el-table-column>
      <el-table-column label="上级状态" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.ParentStatus == 0" style="color: green;">启用</span>
          <span v-else style="color: red;">禁用</span>
        </template>
      </el-table-column>
      <!--<el-table-column label="团队" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--<span v-if="scope.row.TeamDisable == 0" style="color: green;">启用</span>-->
      <!--<span v-else style="color: red;">禁用</span>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <!--<el-table-column label="邀请获得的树苗收益" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--{{ scope.row.invite_amount }}-->
      <!--</template>-->
      <!--</el-table-column>-->
      <!--<el-table-column label="邀请获得的算力" align="center">-->
      <!--<template slot-scope="scope">-->
      <!--{{ scope.row.invite_computing_power }}-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column label="有效直推" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.effective_people_count"> {{ scope.row.effective_people_count }}</span>
          <span v-else> 0</span>
        </template>
      </el-table-column>
      <el-table-column label="团队总算力" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.total_computing_power"> {{ scope.row.total_computing_power }}</span>
          <span v-else> 0</span>

        </template>
      </el-table-column>
      <!--<el-table-column-->
      <!--prop="SumMoney"-->
      <!--label="管理员累计充值"-->
      <!--align="center"-->
      <!--sortable-->
      <!--width="145"-->
      <!--/>-->
      <el-table-column
        prop="Money"
        label="PT余额"
        align="center"
        sortable
        width="110"
      />
      <!--<el-table-column-->
      <!--align="center"-->
      <!--label="赠送树苗"-->
      <!--&gt;-->
      <!--<template slot-scope="scope">-->
      <!--<el-button type="primary" size="small" @click.native="handleForm1(scope.$index, scope.row)">赠送树苗-->
      <!--</el-button>-->
      <!--</template>-->
      <!--</el-table-column>-->
      <el-table-column
        v-permission="['/members/membersStatus', '/members/subList', '/members/holdCoin']"
        label="操作"
        width="320"
        align="center"
      >
        <template slot-scope="scope">

          <div>
            <el-button type="primary" size="small" @click.native="handleForm(scope.$index, scope.row)">详情
            </el-button>
            <span v-permission="['/members/membersStatus']">
              <el-button
                size="small"
                :class="[scope.row.Status == 1 ? 'el-button--danger' : 'el-button--primary']"
                @click="getSetStatus(scope.row,0)"
              >
                <span v-if="scope.row.IsBan == 0">禁用</span>
                <span v-else>启用</span>
              </el-button>
            </span>
            <span v-permission="['/members/subList']">
              <el-button size="small" type="primary" @click="front_sublist(scope.row)">下级</el-button>
            </span>
            <span v-permission="['/members/holdCoin']">
              <el-button size="small" type="primary" @click="holdcoin(scope.row)">持币</el-button>
            </span>
            <br>
            <br>
            <!--<span v-permission="['/members/holdCoin']">-->
            <!--<el-button size="small" type="primary" @click="holdGrade(scope.$index,scope.row)">等级</el-button>-->
            <!--</span>-->
            <span v-permission="['/members/bill']">
              <el-button size="small" type="primary" @click="bill(scope.row.Id)">资金记录</el-button>
            </span>
            <!--<span v-permission="['/members/team_disable']">-->
            <!--<el-button v-if="scope.row.TeamDisable==0" size="small" type="primary" @click="TeamDisable(scope.row.Id, 1)">团队禁用</el-button>-->
            <!--<el-button v-if="scope.row.TeamDisable==1" size="small" type="primary" @click="TeamDisable(scope.row.Id, 2)">取消团队禁用</el-button>-->
            <!--</span>-->
            <span v-if="scope.row.isPartner == 1" v-permission="['/members/Partner']">
              <el-button size="small" type="primary" @click="city(scope.row.Id, 2)">取消合伙人</el-button>
            </span>
            <span v-if="scope.row.isPartner == 0 && scope.row.IsBan == 0" v-permission="['/members/Partner']">
              <el-button size="small" type="primary" @click="city(scope.row.Id, 1)">设为合伙人</el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>
    <pagination v-show="total > 0" :total="total" :page.sync="listQuery.page" :limit.sync="listQuery.limit" @pagination="getList" />
    <!--表单-->
    <el-dialog
      :title="formMap[formName]"
      :visible.sync="formVisible"
      :before-close="hideForm"
      width="60%"
      top="5vh"
    >
      <el-form ref="dataForm" :model="formData" label-width="150px">
        <el-form-item label="会员名称" prop="NickName">
          <el-input v-model="formData.NickName" disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 60px">会员手机号</label>
          <el-input v-model="formData.Phone" auto-complete="off" style="width: 30%" />
          <span style="color: red; font-size: 3px">谨慎修改手机号</span>
        </el-form-item>
        <el-form-item label="交易等级" prop="Level" label-width="150px">
          <el-input v-if="formData.Level==0" value="E1" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-if="formData.Level==1" value="E2" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-if="formData.Level==2" value="E3" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-if="formData.Level==3" value="E4" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-if="formData.Level==4" value="E5" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-if="formData.Level==5" value="E6" disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 89px">邀请码</label>
          <el-input v-model="formData.InviteCode" disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="注册时间" prop="RegTime" label-width="150px">
          <el-input v-model="formData.RegTime" disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 89px">注册IP</label>
          <el-input v-model="formData.RegIp" disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="有效直推人数" prop="effective_people_count" label-width="150px">
          <el-input v-if="formData.effective_people_count" v-model="formData.effective_people_count" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-else disabled="true" auto-complete="off" style="width: 30%" value="0" />
          <label style="margin-left: 59px">团队总算力</label>
          <el-input v-if="formData.total_computing_power" v-model="formData.total_computing_power" disabled="true" auto-complete="off" style="width: 30%" />
          <el-input v-else disabled="true" auto-complete="off" style="width: 30%" value="0" />
        </el-form-item>
        <el-form-item label="出售手续费" prop="Fee" label-width="150px">
          <el-input v-model="formData.Fee" disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 60px">收款手续费</label>
          <el-input v-model="formData.RecvFee" disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="邀请获得的树苗收益" prop="invite_amount" label-width="150px">
          <el-input v-model="formData.invite_amount" disabled="true" auto-complete="off" style="width: 30%" />
          <label style="margin-left: 31px">邀请获得的算力</label>
          <el-input v-model="formData.invite_computing_power" disabled="true" auto-complete="off" style="width: 30%" />
        </el-form-item>
        <el-form-item label="收款方式" prop="paytype">
          <el-radio-group v-model="formData.paytype">
            <el-radio disabled="true" :label="1">微信</el-radio>
            <el-radio disabled="true" :label="2">支付宝</el-radio>
            <el-radio disabled="true" :label="3">USDT地址</el-radio>
          </el-radio-group>
          <label style="margin-left: 89px">是否绑定银行卡</label>
          <el-radio-group v-model="formData.paytype">
            <el-radio disabled="true" :label="0">否</el-radio>
            <el-radio disabled="true" :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否绑定微信" prop="IsBindWx">
          <el-radio-group v-model="formData.IsBindWx">
            <el-radio disabled="true" :label="0">否</el-radio>
            <el-radio disabled="true" :label="1">是</el-radio>
          </el-radio-group>
          <label style="margin-left: 250px">是否绑定支付宝</label>
          <el-radio-group v-model="formData.IsBindAlipay">
            <el-radio disabled="true" :label="0">否</el-radio>
            <el-radio disabled="true" :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="是否禁用" prop="IsBan">
          <el-radio-group v-model="formData.IsBan">
            <el-radio disabled="true" :label="0">否</el-radio>
            <el-radio disabled="true" :label="1">是</el-radio>
          </el-radio-group>
          <label style="margin-left: 265px">是否实名认证</label>
          <el-radio-group v-model="formData.IsAuth">
            <el-radio disabled="true" :label="0">否</el-radio>
            <el-radio disabled="true" :label="1">是</el-radio>
          </el-radio-group>
        </el-form-item>
        <el-form-item label="收款码" label-width="150px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="handleAvatarSuccess"
            :data="{ token: qiniu.Token }"
          >
            <span style="color: red">点击更换</span>
            <img v-if="imageUrl" :src="imageUrl" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer" align="center">
        <el-button @click.native="hideForm">取消</el-button>
        <el-button type="primary" @click.native="formSubmit()">提交</el-button>
      </div>
    </el-dialog>
    <el-dialog title="我的下级会员" :visible.sync="subListVisible">
      <el-table :key="tableKey" v-loading="listLoading" :data="sublist" border fit highlight-current-row style="width: 100%;">
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
        <el-table-column label="昵称" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.NickName }}</span>
          </template>
        </el-table-column>
        <el-table-column label="状态" align="center">
          <template slot-scope="scope">
            <span v-if="scope.row.IsBan == 1">禁用</span>
            <span v-else>正常</span>
          </template>
        </el-table-column>
        <el-table-column label="注册时间" align="center">
          <template slot-scope="{ row }">
            {{ _date(row.RegTime) }}
          </template>
        </el-table-column>
      </el-table>
      <pagination
        v-show="total > 0"
        :total="subtotal"
        :page.sync="sublistQuery.page"
        :limit.sync="sublistQuery.limit"
        @pagination="asyc_subList"
      />
    </el-dialog>

    <el-dialog title="会员持币信息" :visible.sync="coinVisible">
      <el-select v-model="addCoinParams" placeholder="添加持币信息" clearable style="width: 150px" class="filter-item">
        <el-option v-for="(item, index) in coinList" :key="index" :label="item.EnName" :value="item.Id" />
      </el-select>
      <el-button size="small" type="primary" @click="addCoinHandle()">添加一个持币信息</el-button>
      <el-table :key="tableKey" v-loading="listLoading" :data="holdCoinlist" border fit highlight-current-row style="width: 100%;">
        <el-table-column label="币种ID" prop="id" sortable="custom" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.CoinId }}</span>
          </template>
        </el-table-column>
        <el-table-column label="币种名称" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Name }}</span>
          </template>
        </el-table-column>
        <el-table-column label="余额" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Money }}</span>
          </template>
        </el-table-column>
        <el-table-column label="锁定余额" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.LockMoney }}</span>
          </template>
        </el-table-column>
        <el-table-column label="冻结余额" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Forzen }}</span>
          </template>
        </el-table-column>
        <el-table-column label="地址" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Address }}</span>
          </template>
        </el-table-column>

        <el-table-column
          v-permission="['/members/memberCoinUpdate', '/members/memberCoinLockMoney']"
          label="修改操作"
          width="200"
          align="center"
        >
          <template slot-scope="scope">
            <div>
              <span v-permission="['/members/memberCoinUpdate']">
                <el-button size="small" type="primary" @click="coinMoney(scope.row)">余额</el-button>
              </span>
              <span v-if="scope.row.CoinName !== 'IA'" v-permission="['/members/memberCoinLockMoney']">
                <el-button size="small" type="primary" @click="coinLockMoney(scope.row)">锁定</el-button>
              </span>
            </div>
          </template>
        </el-table-column>
      </el-table>
      <pagination
        v-show="total > 0"
        :total="subtotal"
        :page.sync="sublistQuery.page"
        :limit.sync="sublistQuery.limit"
        @pagination="asyc_subList(mid)"
      />
    </el-dialog>

    <el-dialog title="修改金额" :visible.sync="moneyVisible">
      <el-form>
        <el-form-item :label="oldmoneyname" label-width="100px">
          <el-input v-model="oldmoney" :placeholder="oldmoney" disabled="true" />
        </el-form-item>

        <el-form-item :label="newmoneyname" label-width="100px">
          <el-input v-model="newmoney" :placeholder="newmoney" />
        </el-form-item>
        <el-alert title="注：直接输入数字表示在相应的金额上面相加，如果是减直接输入减(-)。如：100则加上100,-100则在原来的金额上面-100" type="error" />
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="修改备注" :visible.sync="showRemark_show">
      <el-form>
        <el-form-item label="输入交易备注码" label-width="150px">
          <el-input v-model="showRemark_item.Remark" placeholder="请输入交易备注码" />
        </el-form-item>
        <el-alert title="注：该交易备注码在发起转账时调用" type="error" />
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="remarkHandle()">确 定</el-button>
      </div>
    </el-dialog>

    <!--<el-dialog title="修改信息" :visible.sync="gradeVisible">-->
    <!--<el-form>-->
    <!--<el-form-item label="等级">-->
    <!--<el-radio-group v-model="level">-->
    <!--<el-radio :label="0">普通会员</el-radio>-->
    <!--<el-radio :label="1">1星</el-radio>-->
    <!--<el-radio :label="2">2星</el-radio>-->
    <!--<el-radio :label="3">3星</el-radio>-->
    <!--<el-radio :label="4">4星</el-radio>-->
    <!--</el-radio-group>-->
    <!--</el-form-item>-->
    <!--</el-form>-->
    <!--<div slot="footer" class="dialog-footer">-->
    <!--<el-button type="primary" @click="handleGrade">确 定</el-button>-->
    <!--</div>-->
    <!--</el-dialog>-->

    <el-dialog title="资金明细" :visible.sync="BillView">
      <!--<div>-->
      <!--<p>总数USDT: </p>-->
      <!--&nbsp;&nbsp;&nbsp;<span>静态奖励: {{ Bill.sum.static }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>邀请奖励: {{ Bill.sum.invite }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>团队奖励: {{ Bill.sum.team }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>空投奖励: {{ Bill.sum.air }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--</div>-->
      <!--<div>-->
      <!--<p>USDT: </p>-->
      <!--&nbsp;&nbsp;&nbsp;<span>静态奖励: {{ Bill.sum.staticBalance }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>邀请奖励: {{ Bill.sum.inviteBalance }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>团队奖励: {{ Bill.sum.teamBalance }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>空投奖励: {{ Bill.sum.airBalance }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--</div>-->
      <!--<div>-->
      <!--<p>OKD: </p>-->
      <!--&nbsp;&nbsp;&nbsp;<span>静态奖励: {{ Bill.sum.staticLock }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>邀请奖励: {{ Bill.sum.inviteLock }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>团队奖励: {{ Bill.sum.teamLock }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--<span>空投奖励: {{ Bill.sum.airLock }}</span>&nbsp;&nbsp;&nbsp;-->
      <!--</div>-->
      <el-table :key="tableKey" v-loading="listLoading" :data="Bill.list" border fit highlight-current-row style="width: 100%;">
        <el-table-column label="ID" prop="id" sortable="custom" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Id }}</span>
          </template>
        </el-table-column>
        <el-table-column label="币种名称" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.CoinName }}</span>
          </template>
        </el-table-column>
        <el-table-column label="变动类型" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.MoldTitle }}</span>
          </template>
        </el-table-column>
        <el-table-column label="变动金额" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Money }}</span>
          </template>
        </el-table-column>
        <el-table-column label="余额" align="center">
          <template slot-scope="scope">
            <span>{{ scope.row.Balance }}</span>
          </template>
        </el-table-column>
        <!--<el-table-column label="类型" align="center">-->
        <!--<template slot-scope="scope">-->
        <!--<span v-if="scope.row.Type == 1">静态奖励</span>-->
        <!--<span v-if="scope.row.Type == 2">邀请奖励</span>-->
        <!--<span v-if="scope.row.Type == 3">团队奖励</span>-->
        <!--<span v-if="scope.row.Type == 4">空投奖励</span>-->
        <!--</template>-->
        <!--</el-table-column>-->
      </el-table>
      <pagination
        v-show="total > 0"
        :total="Bill.total"
        :page.sync="BillQuery.page"
        :limit.sync="BillQuery.limit"
        @pagination="get_bill"
      />
    </el-dialog>
    <el-dialog
      :title="formMap1[formName]"
      :visible.sync="formVisible1"
      :before-close="hideForm1"
      width="30%"
      top="30vh"
    >
      <el-form ref="dataForm" :model="tree" label-width="110px">
        <el-form-item label="赠送花田：" label-width="90px">
          <el-select v-model="tree.sapling_id" placeholder="花田名称" style="width: 35%">
            <el-option v-for="item in SaplingList" :key="item.id" :label="item.nickname" :value="item.nickname" />
          </el-select>
          &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;   <label>赠送数量：</label>
          <el-input v-model="tree.Numbers" style="width: 33%" placeholder="请选择赠送数量" :min="1" :step="1" :max="10000" type="number" disabled="true" />
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer" align="center">
        <el-button @click.native="hideForm1">取消</el-button>
        <el-button type="primary" @click.native="givesapling()">提交</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  addCoin,
  getCoinId,
  GiveSaplings,
  holdCoin,
  memberBill,
  memberCoinLockMoney,
  memberCoinUpdate,
  memberRemark,
  MembersEdit,
  membersLevel,
  membersList,
  membersStatus,
  Partner,
  subList,
  TeamDisable
} from '@/api/members'
import { coinList } from '@/api/coin'
import waves from '@/directive/waves'
// import {
//   parseTime
// } from '@/utils'
import Pagination from '@/components/Pagination'
import permission from '@/directive/permission/index.js'
import { dateHandle, qiniuToken } from '@/api/dateHandle'

// import {
//   list as LevelList
// } from '@/api/level'
const formJson = {
  Id: '',
  Title: '',
  Thumb: '',
  ThumbUrl: '',
  ConvertCoin: '',
  CoinEnName: '',
  SurplusNumber: '',
  Images: [],
  ImagesUrl: [],
  Describe: '',
  ConvertType: 2,
  ConvertCoinId: '',
  Number: '',
  TaskNumber: 0,
  EndTime: '',
  min: '',
  max: '',
  PresentationType: 1,
  ProductType: 1,
  Integral: '',
  is_dsj: 1,
  TeamDisable: '',
  sapling_id: '',
  Numbers: ''

}
export default {
  name: 'List',
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
      listQuery: {
        page: 1,
        limit: 10,
        IsBan: '',
        IdCard: ''
      },
      coinList: {},
      multipleSelection: [],
      subListVisible: false,
      mid: 0,
      sublist: null,
      subtotal: 0,
      sublistQuery: {
        page: 1,
        limit: 10
      },
      index: null,
      isEdit: null,
      formName: null,
      formMap: {
        add: '修改',
        edit: '修改'
      },
      formMap1: {
        add: '修改',
        edit: '赠送花田'
      },
      formLoading: false,
      formVisible: false,
      formVisible1: false,
      formData: formJson,
      coinVisible: false,
      holdCoinlist: null,
      holdCointotal: 0,
      coinQuery: {
        page: 1,
        limit: 10
      },
      qiniu: {},
      imageUrl: '',
      SaplingList: '',
      moneyVisible: false,
      oldmoneyname: '余额',
      newmoneyname: '修改余额',
      oldmoney: 0,
      newmoney: 0,
      mcid: 0,
      flag: 1,
      addCoinParams: '',
      showRemark_show: false,
      showRemark_item: {},
      levels: [],
      level: 1,
      levelId: '',
      gradeVisible: false,
      Bill: {
        list: [],
        total: 0,
        sum: {
          team: 0,
          invite: 0,
          air: 0,
          static: 0
        }
      },
      tree: {
        Id: '',
        sapling_id: '',
        Numbers: 1
      },
      BillQuery: {
        page: 1,
        limit: 10,
        Id: 0
      },
      BillView: false
    }
  },
  created() {
    this.getCoinList()
    this.getList()
    this.qiniuGet()
    // LevelList({}).then(res => {
    //   this.levels = res.data
    // })
  },
  methods: {
    get_bill(query) {
      console.log(query)
      this.bill(this.BillMid)
    },
    bill(id) {
      this.BillMid = id
      this.BillQuery.Id = id
      memberBill(this.BillQuery).then(res => {
        if (res.code === 20000) {
          this.Bill.list = res.data.list
          this.Bill.total = res.data.total
          // this.Bill.sum = res.data.sum
          this.BillView = true
        }
      })
    },
    qiniuGet() {
      qiniuToken().then(res => {
        this.qiniu = res.data
        console.log(res.data)
      })
    },
    remarkHandle() {
      memberRemark({
        Id: this.showRemark_item.Id,
        Remark: this.showRemark_item.Remark
      }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.getList()
          this.showRemark_show = false
        }
      })
    },
    holdGrade(index, row) {
      this.level = row.SettingLevel
      this.levelId = row.Id
      this.gradeVisible = true
    },
    handleGrade() {
      const data = {
        id: this.levelId,
        Level: this.level
      }
      membersLevel(data).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.gradeVisible = false
          this.getList()
        }
      })
    },
    showRemark(item) {
      this.showRemark_item = item
      this.showRemark_show = true
    },
    getCoinList() {
      coinList().then(res => {
        this.coinList = res.data.data
        console.log(this.coinList)
      })
    },

    _date(t) {
      return dateHandle('Y-m-d H:i:s', t)
    },
    getList() {
      this.listLoading = true
      membersList(this.listQuery).then(response => {
        this.list = response.data.list || []
        this.total = response.data.total || 0
        this.SaplingList = response.data.SaplingList
        this.listLoading = false
        setTimeout(() => {
          this.listLoading = false
        }, 0.9 * 1000)
      })
    },
    getSetStatus(row) {
      this.listLoading = true
      membersStatus({
        id: row.Id
      }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.getList()
          this.listLoading = false
        }
      })
    },
    onReset() {
      this.$router.push({
        path: ''
      })
      this.listQuery = {
        IsBan: '',
        page: 1,
        limit: 20
      }
      this.getList()
    },
    // 我的下级
    front_sublist(row) {
      this.subListVisible = true
      this.mid = row.Id
      this.asyc_subList(this.mid)
    },
    asyc_subList(mid) {
      this.listLoading = true
      this.sublistQuery.ParentId = this.mid
      subList(this.sublistQuery).then(res => {
        this.sublist = res.data.data
        this.subtotal = res.data.total
        if (res.code === 20000) {
          this.listLoading = false
        }
      })
    },

    // 我的持币
    holdcoin(row) {
      this.coinVisible = true
      this.mid = row.Id
      this.asyc_holdCoin(this.mid)
    },
    asyc_holdCoin(mid) {
      this.listLoading = true
      holdCoin(mid).then(res => {
        this.holdCoinlist = res.data.data
        this.holdCointotal = res.data.total
        if (res.code === 20000) {
          this.listLoading = false
        }
      })
    },

    // 修改余额
    coinMoney(row) {
      this.oldmoneyname = '余额'
      this.newmoneyname = '修改余额'
      this.moneyVisible = true
      this.newmoney = 1
      this.asyc_memberCoinUpdate(row)
      this.mcid = row.Id
      this.flag = 1
    },

    // 修改锁定余额
    coinLockMoney(row) {
      this.oldmoneyname = '锁定余额'
      this.newmoneyname = '修改锁定余额'
      this.moneyVisible = true
      this.newmoney = 1
      this.asyc_memberCoinUpdate(row)
      this.mcid = row.Id
      this.flag = 2
    },

    // 修改冻结余额
    coinForzenMoney(row) {
      this.oldmoneyname = '冻结余额'
      this.newmoneyname = '修改冻结余额'
      this.moneyVisible = true
      this.newmoney = 1
      this.asyc_memberCoinUpdate(row)
      this.mcid = row.Id
      this.flag = 3
    },

    // 获取我的币中的一条记录，用于更新页面
    asyc_memberCoinUpdate(row) {
      getCoinId(row.Id).then(res => {
        if (res.code === 20000) {
          this.oldmoney = res.data.Money
        }
      })
    },
    // 显示表单
    handleForm(index, row) {
      this.formVisible = true
      this.imageUrl = this.qiniu.Domain + row.qrcode
      this.formData = JSON.parse(JSON.stringify(formJson))
      if (row !== null) {
        this.formData = JSON.parse(JSON.stringify(row))
      }
      this.formName = 'add'
      this.formRules = this.addRules
      this.isEdit = 0
      if (index !== null) {
        this.isEdit = 1
        this.index = index
        this.formName = 'edit'
      }
    },
    handleForm1(index, row) {
      this.formVisible1 = true
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
    handleChange(value) {
      console.log(value)
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
    // 隐藏表单
    hideForm1() {
      // 更改值
      this.formVisible1 = !this.formVisible1
      // 清空表单
      this.$refs['dataForm'].resetFields()
      this.index = null
      return true
    },
    handleAvatarSuccess(res, file) {
      this.imageUrl = URL.createObjectURL(file.raw)
      this.formData.qrcode = res.key
    },
    formSubmit() {
      this.$refs['dataForm'].validate(valid => {
        if (valid) {
          this.formLoading = true
          const data = Object.assign({}, this.formData)
          MembersEdit({
            Id: this.formData.Id,
            qrcode: this.formData.qrcode,
            Phone: this.formData.Phone
          }).then(response => {
            this.formLoading = false
            this.$message.success('操作成功')
            this.formVisible = false
            const upData = Object.assign(data, response.data)
            this.list.splice(this.index, 1, upData)
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
    // 赠送花田
    givesapling() {
      GiveSaplings({
        sapling_id: this.tree.sapling_id,
        Numbers: this.tree.Numbers,
        Id: this.formData.Id
      }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.formLoading = false
          this.formVisible1 = false
          this.tree.sapling_id = undefined
          this.getList
        }
      })
    },

    // 提交数据
    handleEdit() {
      const param = {
        mcid: this.mcid,
        money: this.newmoney
      }
      if (this.flag === 1) {
        memberCoinUpdate(param).then(res => {
          console.log(res)
          if (res.code === 20000) {
            this.$message({
              type: 'success',
              message: res.msg
            })
            this.asyc_holdCoin(this.mid)
            this.moneyVisible = false
          }
        })
      }
      if (this.flag === 2) {
        memberCoinLockMoney(param).then(res => {
          console.log(res)
          if (res.code === 20000) {
            this.$message({
              type: 'success',
              message: res.msg
            })
            this.asyc_holdCoin(this.mid)
            this.moneyVisible = false
          }
        })
      }
      // if (this.flag === 3) {
      // }
    },
    city(id, type) {
      var msg = '确定设置为合伙人？'
      if (type === 2) msg = '确定取消合伙人?'
      this.$confirm(msg, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        Partner({ Id: id }).then(res => {
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
    TeamDisable(id, type) {
      var msg = '是否将该用户及其团队中成员禁用？'
      if (type === 2) msg = '是否取消该用户及其团队中成员禁用？'
      this.$confirm(msg, '提示', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        TeamDisable({ Id: id, Type: type }).then(res => {
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
    handleSelectionChange(val) {
      this.multipleSelection = val
    },
    addCoinHandle() {
      addCoin({
        MemberId: this.mid,
        CoinId: this.addCoinParams
      }).then(res => {
        if (res.code === 20000) {
          this.$message({
            type: 'success',
            message: res.msg
          })
          this.asyc_holdCoin(this.mid)
        }
      })
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

  .el-table--border:after, .el-table--group:after, .el-table:before{
    z-index: 0;
  }
</style>
