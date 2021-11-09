<template>
  <div class="app-container">
    <div class="filter-container">
      <el-button v-waves v-permission="['/merchant/add']" class="filter-item" type="primary" @click="add()">
        <i class="el-icon-plus" />&nbsp;&nbsp;添加商户
      </el-button>
    </div>
    <el-table v-loading="listLoading" :data="tableData" style="width: 100%" border>
      <el-table-column prop="Id" label="ID" align="center" width="100" />
      <el-table-column prop="Name" align="center" label="名称" />
      <el-table-column prop="Weixin" align="center" label="微信" />
      <el-table-column prop="Alipay" align="center" label="支付宝" />
      <el-table-column prop="BankCard" align="center" label="银行卡号" />
      <el-table-column prop="Bank" align="center" label="开户行" />
      <!-- <el-table-column label="是否禁用" align="center">
        <template slot-scope="scope">
          <span v-if="scope.row.IsClose > 0">否</span>
          <span v-else>是</span>
        </template>
      </el-table-column> -->

      <el-table-column v-permission="['/merchant/edit']" label="操作" width="150" align="center">
        <template slot-scope="scope">
          <div>
            <span v-permission="['/merchant/edit']">
              <el-button size="small" type="primary" @click="edit(scope.row.Id)">
                编辑
              </el-button>
            </span>
          </div>
        </template>
      </el-table-column>
    </el-table>

    <el-dialog title="编辑商户" :visible.sync="editVisible">
      <el-form>
        <el-form-item label="商户名称" label-width="70px">
          <el-input v-model="editForm.Name" />
        </el-form-item>
        <el-form-item label="微信号" label-width="70px">
          <el-input v-model="editForm.WechatAccount" />
        </el-form-item>
        <el-form-item label="微信昵称" label-width="85px">
          <el-input v-model="editForm.WechatName" />
        </el-form-item>
        <el-form-item label="微信收款码" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="WechatEditSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="WechatQrCode" :src="WechatQrCode" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="支付宝号" label-width="85px">
          <el-input v-model="editForm.AlipayAccount" />
        </el-form-item>
        <el-form-item label="支付宝昵称" label-width="85px">
          <el-input v-model="editForm.AlipayName" />
        </el-form-item>
        <el-form-item label="支付宝收款码" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="AlipayEditSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="AlipayQrCode" :src="AlipayQrCode" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="银行卡号" label-width="85px">
          <el-input v-model="editForm.BankCard" type="number" />
        </el-form-item>
        <el-form-item label="开户行" label-width="85px">
          <el-input v-model="editForm.Bank" />
        </el-form-item>
        <el-form-item label="持卡人" label-width="85px">
          <el-input v-model="editForm.BankName" />
        </el-form-item>
        <el-form-item label="银行预留手机号" label-width="85px">
          <el-input v-model="editForm.BankPhone" type="number" />
        </el-form-item>
        <el-form-item label="是否禁用" label-width="85px">
          <el-select v-model="editForm.IsClose" placeholder="是否禁用">
            <el-option v-for="(item,index) in IsCloseList" :key="index" :label="item.name" :value="item.value" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleEdit">确 定</el-button>
      </div>
    </el-dialog>

    <el-dialog title="添加商户" :visible.sync="addVisible">
      <el-form>
        <el-form-item label="商户名称" label-width="70px">
          <el-input v-model="addForm.Name" />
        </el-form-item>
        <el-form-item label="微信号" label-width="70px">
          <el-input v-model="addForm.WechatAccount" />
        </el-form-item>
        <el-form-item label="微信昵称" label-width="85px">
          <el-input v-model="addForm.WechatName" />
        </el-form-item>
        <el-form-item label="微信收款码" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="WechatAddSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="WechatQrCode" :src="WechatQrCode" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="支付宝号" label-width="85px">
          <el-input v-model="addForm.AlipayAccount" />
        </el-form-item>
        <el-form-item label="支付宝昵称" label-width="85px">
          <el-input v-model="addForm.AlipayName" />
        </el-form-item>
        <el-form-item label="支付宝收款码" label-width="100px">
          <el-upload
            class="avatar-uploader"
            action="https://up-z2.qiniup.com"
            :show-file-list="false"
            :on-success="AlipayAddSuccess"
            :data="{ token: qiniu.Token }"
          >
            点击更换
            <img v-if="AlipayQrCode" :src="AlipayQrCode" class="avatar">
            <i v-else class="el-icon-plus avatar-uploader-icon" />
          </el-upload>
        </el-form-item>
        <el-form-item label="银行卡号" label-width="85px">
          <el-input v-model="addForm.BankCard" />
        </el-form-item>
        <el-form-item label="开户行" label-width="85px">
          <el-input v-model="addForm.Bank" />
        </el-form-item>
        <el-form-item label="持卡人" label-width="85px">
          <el-input v-model="addForm.BankName" />
        </el-form-item>
        <el-form-item label="银行预留手机号" label-width="85px">
          <el-input v-model="addForm.BankPhone" />
        </el-form-item>
        <el-form-item label="是否禁用" label-width="85px">
          <el-select v-model="addForm.IsClose" placeholder="是否禁用">
            <el-option v-for="(item,index) in IsCloseList" :key="index" :label="item.name" :value="item.value" />
          </el-select>
        </el-form-item>
      </el-form>
      <div slot="footer" class="dialog-footer">
        <el-button type="primary" @click="handleAdd">确 定</el-button>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import {
  list,
  detail,
  edit,
  add
} from '@/api/merchant'
import waves from '@/directive/waves'
import permission from '@/directive/permission/index.js'
import Upload from '@/components/Upload/SingleImage3'
import {
  dateHandle,
  qiniuToken
} from '@/api/dateHandle'

export default {
  name: 'MerchantList',
  directives: {
    waves,
    permission
  },
  components: {
    Upload
  },
  data() {
    return {
      tableData: [],
      planList: [],
      editVisible: false,
      addVisible: false,
      addProductVisible: false,
      listLoading: true,
      IsCloseList: [
        {
          name: '不禁用',
          value: 0
        },
        {
          name: '禁用',
          value: 1
        }
      ],
      editForm: {
        Id: 0,
        Name: '',
        WechatAccount: '',
        WechatName: '',
        AlipayAccount: '',
        AlipayName: '',
        BankCard: '',
        Bank: '',
        BankName: '',
        BankPhone: '',
        WechatQrCode: '',
        AlipayQrCode: '',
        IsClose: 0
      },
      addForm: {
        Name: '',
        WechatAccount: '',
        WechatName: '',
        AlipayAccount: '',
        AlipayName: '',
        BankCard: '',
        Bank: '',
        BankName: '',
        BankPhone: '',
        WechatQrCode: '',
        AlipayQrCode: '',
        IsClose: 0
      },
      WechatQrCode: '',
      AlipayQrCode: '',
      qiniu: {}
    }
  },
  created() {
    this.init()
    qiniuToken().then(res => {
      this.qiniu = res.data
      console.log(res.data)
    })
  },
  methods: {
    WechatEditSuccess(res, file) {
      this.WechatQrCode = URL.createObjectURL(file.raw)
      this.editForm.WechatQrCode = res.key
      console.log(this.editForm)
    },
    AlipayEditSuccess(res, file) {
      this.AlipayQrCode = URL.createObjectURL(file.raw)
      this.editForm.AlipayQrCode = res.key
      console.log(this.editForm)
    },
    WechatAddSuccess(res, file) {
      this.WechatQrCode = URL.createObjectURL(file.raw)
      this.addForm.WechatQrCode = res.key
      console.log(this.addForm)
    },
    AlipayAddSuccess(res, file) {
      this.AlipayQrCode = URL.createObjectURL(file.raw)
      this.addForm.AlipayQrCode = res.key
      console.log(this.addForm)
    },
    init() {
      this.listLoading = true
      list({}).then((res) => {
        this.tableData = res.data
        setTimeout(() => {
          this.listLoading = false
        }, 0.3 * 1000)
      })
    },
    edit(id) {
      detail({ Id: id }).then(res => {
        this.editForm = res.data
        this.editForm.Id = id
        this.WechatQrCode = this.qiniu.Domain + res.data.WechatQrCode
        this.AlipayQrCode = this.qiniu.Domain + res.data.AlipayQrCode
        this.editVisible = true
      })
    },
    handleEdit() {
      edit(this.editForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '产品已更新',
            type: 'success'
          })
          this.init()
          this.editVisible = false
        }
      })
    },
    add() {
      this.WechatQrCode = ''
      this.AlipayQrCode = ''
      this.addVisible = true
    },
    handleAdd() {
      add(this.addForm).then(res => {
        if (res.code == 20000) {
          this.$message({
            message: '产品已添加',
            type: 'success'
          })
          this.init()
          this.addVisible = false
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
</style>
