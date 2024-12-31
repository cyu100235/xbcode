<template>
    <div class="update-container">
        <el-steps class="step-container" direction="vertical" process-status="success" :active="stepData.step">
            <el-step v-for="(item, index) in stepData.list" :key="index">
                <template #title>
                    <div class="xb-title">
                        <div class="xb-icon">
                            <xbIcons :icon="item.icon" size="22" />
                        </div>
                        <div class="text">{{ item.title }}</div>
                    </div>
                </template>
            </el-step>
        </el-steps>
        <div class="content-container" v-if="plugin">
            <img :src="plugin.logo" class="logo" alt="">
            <div class="title">{{ plugin.title }} {{ plugin.version_name }}</div>
            <div class="loading">
                <div class="text" v-if="stepData.status && stepData.stepText">
                    {{ stepData.stepText }}
                </div>
                <div class="text" v-else>
                    {{ stepData.stepText ? `正在${stepData.stepText}...` : '安装错误' }}
                </div>
                <vxe-icon name="refresh" class="loading-icon" roll></vxe-icon>
            </div>
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            // 安装步骤数据
            stepData: {
                step: 0,
                status: false,
                stepText: '',
                list: [
                    {
                        step: 'download',
                        title: '下载插件',
                        icon: 'CloudDownloadOutlined',
                    },
                    {
                        step: 'unzip',
                        title: '解压插件',
                        icon: 'FileZipFilled',
                    },
                    {
                        step: 'database',
                        title: '安装数据',
                        icon: 'InteractionOutlined',
                    },
                    {
                        step: 'success',
                        title: '安装完成',
                        icon: 'SmileOutlined',
                    },
                ],
            },
            // 插件数据
            plugin: null
        }
    },
    methods: {
        pluginAction(step) {
            const _this = this;
            // 获取当前安装步骤
            const item = _this.stepData.list.find(item => item.step === step);
            // 设置安装步骤文字
            _this.stepData.stepText = item.title;
            // 设置当前安装步骤索引
            _this.stepData.step = _this.stepData.list.findIndex(item => item.step === step);
            // 发起安装步骤请求
            _this.$http.usePost(`backend/Plugins/install?step=${step}`, {
                name: _this.$route.query?.name,
                version_name: _this.$route.query?.version_name,
                version: _this.$route.query?.version,
            }).then((res) => {
                const next = res?.data?.next;
                if (next === '') {
                    // 安装完成
                    const stepIndex = _this.stepData.list.findIndex(item => item.step === 'success');
                    _this.stepData.step = stepIndex
                    setTimeout(() => {
                        _this.$emit("close");
                    }, 3000);
                    _this.stepData.stepText = res.msg;
                    _this.stepData.status = true;
                } else if (next) {
                    // 下一步
                    setTimeout(() => {
                        _this.pluginAction(next);
                    }, 1000);
                }
            }).catch(() => {
                setTimeout(() => {
                    _this.$emit("close");
                }, 3000);
            })
        },
        getPlugin() {
            const _this = this;
            _this.$http.useGet("backend/Plugins/detail", {
                name: _this.$route.query?.name,
                version_name: _this.$route.query?.version_name,
                version: _this.$route.query?.version,
            }).then((res) => {
                const plugin = res?.data ?? {};
                _this.plugin = plugin;
                _this.pluginAction('download')
            }).catch(() => {
                _this.$emit("close");
            })
        }
    },
    mounted() {
        this.getPlugin()
    },
};
</script>
  
<style lang="scss">
.update-container {
    display: flex;
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

        .xb-title {
            .xb-icon {
                text-align: center;
                line-height: 15px;
            }

            .text {
                font-size: 14px;
                font-weight: 700;
            }
        }
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
            .text{
                padding-top: 10px;
            }
        }
    }
}
</style>
  