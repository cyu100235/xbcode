<template>
  <div class="update-version-container" v-if="updated.client_version">
    <div class="version-info">
      <div class="xbase-version">
        <el-image class="system-logo" :src="system_info?.web_logo ? system_info?.web_logo : '/image/logo.png'" />
        <div class="system-name">
          {{ system_info?.web_name }}
        </div>
        <div class="system-version">
          版本 {{ updated.client_version_name }}（{{ updated.client_version }}）
        </div>
      </div>
      <div class="xbase-empty">
        当前已经是最新版本
      </div>
    </div>
  </div>
</template>
  
<script>
export default {
  data() {
    return {
      updated: {
        title: "",
        version_name: "",
        version: "",
        client_version_name: "",
        client_version: "",
        content: "",
      },
      system_info: {},
    }
  },
  async mounted() {
    await this.getSystemInfo()
    await this.getDetail()
  },
  methods: {
    getSystemInfo() {
      this.$http.useGet("admin/Index/info").then((res) => {
          this.system_info = res.data ?? {};
        })
    },
    getDetail() {
      this.$http.useGet("admin/Update/getUpdate").then((res) => {
          this.$emit('update:openWin', 'remote/update/update')
        }).catch((err) => {
          this.updated.client_version = err?.data?.client_version
          this.updated.client_version_name = err?.data?.client_version_name
        })
    },
  },
}
</script>
  
<style lang="scss" scoped>
.update-version-container {
  height: 100%;
  background: #fff;

  .version-info {
    height: 100%;
    display: flex;
    flex-direction: column;
    align-items: center;
    justify-content: center;

    .xbase-version {
      display: flex;
      flex-direction: column;
      align-items: center;
      justify-content: center;

      .system-logo {
        width: 120px;
        height: 120px;
        border-radius: 10px;
      }

      .system-name {
        padding-top: 20px;
        font-weight: 700;
        font-size: 26px;
      }

      .system-version {
        padding-top: 10px;
        font-size: 14px;
      }
    }
    .xbase-empty{
      margin-top: 10px;
      font-size: 14px;
    }
  }
}
</style>