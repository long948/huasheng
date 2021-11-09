<template>
  <div class="upload-container">
    <el-button
      :style="{background:color,borderColor:color}"
      icon="el-icon-upload"
      size="mini"
      type="primary"
      @click=" dialogVisible=true"
    >上传文件</el-button>

    <el-dialog
      custom-class="upload-dialog__body"
      width="450px"
      title="插入图片"
      :visible.sync="dialogVisible"
      append-to-body
    >

      <el-tabs v-model="activeName">
        <el-tab-pane label="本地上传" name="localhost">
          <div class="upload-content">
            <span class="text-muted">只能上传{{ ext }}文件，且大小不超过{{ size | renderSize }}，且宽高 {{ width ? width + 'px' : "不限" }} * {{ height ? height + 'px' : "不限" }}</span>
            <br>
            <div class="widget-upload" @dragenter="onDrag" @dragover="onDrag" @drop="onDrop">
              <input ref="upload" type="file" name="file" class="widget-upload__file" @change="onChange">
              <el-input
                v-model="fileName"
                class="widget-upload__text"
                size="medium"
                placeholder="文件拖拽到这里"
              />
              <el-button size="medium">选择文件</el-button>
            </div>
          </div>
        </el-tab-pane>
        <el-tab-pane label="远程地址获取" name="network">
          <div class="upload-content">
            <span class="text-muted">只能上传{{ ext }}文件，且大小不超过{{ size | renderSize }}，且宽高 {{ width ? width + 'px' : "不限" }} * {{ height ? height + 'px' : "不限" }}</span>
            <br>
            <div class="widget-upload">
              <el-input
                v-model="fileUrl"
                size="medium"
                placeholder="请输入文件所在网址"
              />
            </div>
          </div>
        </el-tab-pane>
      </el-tabs>

      <div style="text-align: right;padding-top: 40px;">
        <el-button size="medium" @click="onClose">取 消</el-button>
        <el-button size="medium" type="primary" :loading="uploadLoading" @click="handleSubmit">插入</el-button>
      </div>

    </el-dialog>

  </div>
</template>

<script>
import { qiuNiuUpToken, createFile } from '../../api/dateHandle'
import { renderSize } from '../../filters/index'
export default {
  name: 'Upload',
  props: {
    height: {
      type: Number
    },
    width: {
      type: Number
    },
    ext: {
      type: String
    },
    size: {
      type: Number
    },
    color: {
      type: String,
      default: '#1890ff'
    }
  },
  data() {
    return {
      activeName: 'localhost',
      file: null,
      fileName: '',
      filePath: '',
      filePathUrl: '',
      fileUrl: '',
      dialogVisible: false,
      uploadLoading: false
    }
  },
  computed: {},
  watch: {},
  methods: {
    onDrag(e) {
      e.stopPropagation()
      e.preventDefault()
    },
    onDrop(e) {
      e.stopPropagation()
      e.preventDefault()
      const data = e.dataTransfer.files
      // 获取文件对象
      if (data.length < 1) {
        return false // 检测是否有文件拖拽到页面
      }
      this.file = data[0]
      this.fileName = data[0].name
    },
    onClose() {
      this.onReset()
    },
    onChange(e) {
      const data = e.target.files
      // 获取文件对象
      if (data.length < 1) {
        return false // 检测是否有文件拖拽到页面
      }
      this.file = data[0]
      this.fileName = data[0].name
      e.target.value = ''
    },
    onReset() {
      this.activeName = 'localhost'
      this.file = null
      this.fileName = ''
      this.filePath = ''
      this.filePathUrl = ''
      this.fileUrl = ''
      this.dialogVisible = false
      this.uploadLoading = false
    },
    handleSubmit() {
      if (this.activeName === 'network') {
        if (!this.fileUrl) {
          this.$message.error('请输入文件网络地址')
          return
        }
        this.$emit('on-select', this.fileUrl, this.fileUrl)
        this.onReset()
        return
      }
      if (this.file === null) {
        this.$message.error('请先选择文件')
        return
      }
      // 检查文件
      const isVerify = this.beforeUpload(this.file)
      if (isVerify === false) {
        return
      }
      isVerify
        .then(() => {
          this.uploadLoading = true
          qiuNiuUpToken()
            .then(response => {
              const url = response.data.upload_url
              const domain = response.data.domain
              const formData = new FormData()
              formData.append('token', response.data.up_token)
              formData.append('file', this.file)
              createFile(url, formData)
                .then(response => {
                  this.uploadLoading = false
                  if (response.key || response.data.key) {
                    const filePath =
                        response.key || response.data.key
                    const _URL =
                        window.URL || window.webkitURL
                    const filePathUrl = _URL.createObjectURL(
                      this.file
                    )
                    const fileUrl = domain + '/' + filePath
                    this.$emit(
                      'on-select',
                      filePath,
                      filePathUrl,
                      fileUrl
                    )
                    this.onReset()
                    return
                  }
                  this.$message.error('上传出错')
                })
                .catch(err => {
                  this.uploadLoading = false
                  this.$message.error('上传出错')
                })
            })
            .catch(() => {
              this.uploadLoading = false
              this.$message.error('出现未知问题，刷新页面')
            })
        })
        .catch(() => {})
    },
    beforeUpload(file) {
      const name = file.name ? file.name : ''
      let ext = name
        ? name.substr(name.lastIndexOf('.') + 1, name.length)
        : true
        // 转成小写
      ext = ext.toLowerCase()
      let isExt = false
      // 如果有坚持文件后缀的配置
      if (this.ext) {
        isExt = this.ext.indexOf(ext) >= 0
        if (!isExt) {
          this.$message.error('文件只能为 ' + this.ext + ' 格式!')
          return false
        }
      }
      let isSize = false
      if (this.size) {
        const sizeStr = this.size
        isSize = sizeStr > 0 && file.size > sizeStr
        if (isSize) {
          this.$message.error(
            '上传文件不能超过 ' + renderSize(sizeStr) + '!'
          )
          return false
        }
      }
      const _this = this
      if (_this.width || _this.height) {
        return new Promise(function(resolve, reject) {
          const width = _this.width
          const height = _this.height
          const _URL = window.URL || window.webkitURL
          const img = new Image()
          img.src = _URL.createObjectURL(file)
          img.onload = function() {
            const valid =
                (!width || img.width === width) &&
                (!height || img.height === height)
            if (valid) {
              resolve()
            } else {
              let messageStr = ''
              if (width && !height) {
                messageStr = width
              }
              if (height && !width) {
                messageStr = height
              }
              if (height && width) {
                messageStr = width + '*' + height
              }
              _this.$message.error(
                '上传尺寸必须是 ' + messageStr + ' px!'
              )
              reject()
            }
          }
        })
      }
      return new Promise(function(resolve) {
        resolve()
      })
    }
  }
}
</script>

<style type="text/scss" lang="scss">
  .upload-dialog__body {
    .el-dialog__header {
      background-color: #f3f3f3;
      border-top-left-radius: 6px;
      border-top-right-radius: 6px;
    }
    .el-dialog__body {
      padding: 12px 20px !important;
    }
  }
  .upload-content {
    padding-top: 15px;
    .widget-upload {
      position: relative;
    }
    .widget-upload__text {
      width: 66.66%;
      padding-right: 15px;
      .el-input__inner {
        border: 1px solid #ccc;
        background-color: #eee;
      }
    }
    .widget-upload {
      padding-top: 5px;
      .widget-upload__file {
        position: absolute;
        opacity: 0;
        width: 85%;
        height: 100%;
        z-index: 10;
        cursor: pointer;
      }
    }
  }
</style>
