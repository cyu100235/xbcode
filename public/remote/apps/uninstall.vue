<template>
    <div class="pages-container" v-if="detail">
        <el-steps class="step-container" direction="vertical" :active="stepData.step" process-status="success">
            <el-step v-for="(item, index) in stepData.list" :key="index" :title="item.title" />
        </el-steps>
        <div class="content-container">
            <img :src="detail.logo" class="logo" alt="">
            <div class="title">{{ detail.title }} {{ detail.version_name }}</div>
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
        app_name: {
            type: String,
            default: ''
        },
        version: {
            type: Number,
            default: 0
        }
    },
    data() {
        return {
            detail: null,
            stepData: {
                step: 0,
                stepText: '准备卸载',
                status: 'process',
                list: [
                    {
                        step: 'preparation',
                        title: '准备卸载',
                    },
                    {
                        step: 'deleteCode',
                        title: '卸载代码',
                    },
                    {
                        step: 'deleteSql',
                        title: '卸载数据',
                    },
                    {
                        step: 'uninstallData',
                        title: '云端处理',
                    },
                    {
                        step: 'success',
                        title: '卸载完成',
                    },
                ],
            },
        }
    },
    async mounted() {
        await this.getDetail().then(() => {
            setTimeout(() => {
                this.install('deleteCode')
            }, 1500);
        })
    },
    methods: {
        // 安装步骤
        install(step) {
            const index = this.stepData.list.findIndex(item => item.step === step);
            const item = this.stepData.list.find(item => item.step === step);
            this.stepData.stepText = item.title;
            this.stepData.step = index;
            // 发送安装请求
            const params = {
                app_name: this.app_name,
                version: this.version,
                step: step
            }
            this.$http.useGet('admin/Apps/uninstall', params).then((res) => {
                if (res?.data?.next !== '') {
                    this.install(res?.data?.next ?? '')
                } else {
                    this.$useNotify(res?.msg || "卸载失败", 'success', '温馨提示')
                    this.$emit("update:closeWin");
                    setTimeout(() => {
                        window.location.reload();
                    }, 2000);
                }
            }).catch(() => {
                this.$emit("update:closeWin");
            })
        },
        // 获取详情
        async getDetail() {
            const params = {
                app_name: this.app_name
            }
            await this.$http.useGet('admin/apps/detail', params).then(res => {
                this.detail = res?.data ?? {}
                return res
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    display: flex;
    width: 100%;
    height: 100%;
    overflow: hidden;

    .step-container {
        height: 100%;
        display: flex;
        justify-content: center;
        border-right: 1px solid #e5e5e5;
        overflow: hidden;
        box-sizing: border-box;
        padding: 20px 50px;
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