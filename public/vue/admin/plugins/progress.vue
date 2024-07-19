<template>
  <div class="update-container" v-if="detail">
    <el-steps class="step-container" :active="stepData.step" process-status="success">
      <el-step v-for="(item, index) in stepData.list" :key="index" :title="item.title" />
    </el-steps>
    <div class="content-container" v-if="pageData">
      <img :src="pageData.logo" class="logo" alt="">
      <div class="title">{{ pageData.title }} {{ pageData.version_name }}</div>
      <div class="loading">
        <vxe-icon name="refresh" class="loading-icon" roll></vxe-icon>
        <div>{{ stepData.stepText ? `正在${stepData.stepText}...` : '出现异常错误' }}</div>
      </div>
    </div>
  </div>
</template>
    
<script>
export default {
  props: {
    name: String | undefined,
    version: String | undefined,
    type: String
  },
  data() {
    return {
      stepData: {
        step: 'preparation',
        lock: false,
        updateText: '',
        list: [
          {
            title: '准备更新',
            step: 'preparation',
          },
          {
            title: '下载更新包',
            step: 'download',
          },
          {
            title: '备份代码',
            step: 'backCode',
          },
          {
            title: '备份数据库',
            step: 'backSql',
          },
          {
            title: '解压更新包',
            step: 'unzip',
          },
          {
            title: '更新数据',
            step: 'updateData',
          },
          {
            title: '更新成功',
            step: 'success',
          },
        ],
      },
      detail: null,
    }
  },
  methods: {
    // 更新进行中
    hanldStepUpdate(step) {
      const _this = this;
      const version = _this.updated.version;
      // 设置开始更新与文字
      const item = _this.stepData.list.find(item => item.step === step);
      _this.stepData.updateText = `正在${item.title}...`;
      // 设置当前步骤
      _this.stepData.step = step
      // 发送更新请求
      _this.$http.usePost(`admin/Updated/updateCheck?step=${step}&version=${version}`).then((res) => {
        if (res?.data?.next === '') {
          _this.stepData.step = 'success';
          _this.stepData.updateText = res.msg;
          setTimeout(() => {
            _this.$routerApp.push({ path: '/' });
          }, 2500);
        } else if (res?.data?.next) {
          _this.hanldStepUpdate(res.data.next);
        }
      }).catch((err) => {
        _this.stepData.step = 'preparation';
        _this.stepData.lock = false;
        if (err?.message?.includes('timeout')) {
          _this.$useNotify("更新失败，网络超时", 'error', '温馨提示', {
            'onClose': () => {
              window.location.reload();
            }
          })
          return;
        }
      });
    },
    // 获取插件详情
    getDetail() {
      const params = {
        name: this.name,
        version: this.version
      }
      this.$http.useGet('admin/Plugins/detail', params).then(res => {
        this.detail = res?.data ?? null
      }).catch(err => {
        this.$emit("update:closeWin");
        this.$useNotify(err?.msg ?? '异常错误', 'error', '温馨提示')
      })
    }
  },
  mounted() {
    this.getDetail()
  },
}
</script>
    
<style lang="scss">.update-container {
  display: flex;
  flex-direction: column;
  width: 100%;
  height: 100%;
  overflow: hidden;

  .step-container {
      display: flex;
      justify-content: flex-start;
      align-items: flex-start;
      padding: 20px;
      border-right: 1px solid #e5e5e5;
      height: 100%;
      overflow-y: hidden;
      overflow-x: hidden;
      box-sizing: border-box;
  }

  .content-container {
      flex: 1;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;

      .logo {
          width: 90px;
          height: 90px;
          border-radius: 10px;
      }

      .title {
          font-size: 20px;
          font-weight: 700;
          margin-top: 10px;
      }

      .loading {
          display: flex;
          justify-content: center;
          align-items: center;
          flex-direction: column;
          gap: 10px;

          .loading-icon {
              font-size: 22px;
          }
      }
  }
}
</style>
    