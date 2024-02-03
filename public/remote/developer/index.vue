<template>
    <div class="pages-container">
        <el-card>
            <div slot="header">
                <span>开发者模式</span>
            </div>
            <div class="developer-mode">
                <el-radio-group v-model="developer_mode" @change="hanldMode">
                    <el-radio label="10">用户模式</el-radio>
                    <el-radio label="20">开发者模式</el-radio>
                </el-radio-group>
            </div>
        </el-card>
        <div class="content-container" v-if="datalist.length">
            <div class="content">
                <div class="item" v-for="(item, index) in datalist" :key="index">
                    <div class="logo-container">
                        <el-image class="logo"
                            src="https://fuss10.elemecdn.com/e/5d/4a731a90594a4af544c0c25941171jpeg.jpeg" />
                    </div>
                    <div class="title">{{ item.title }}</div>
                    <div class="action">
                        <el-tooltip effect="dark" content="安装测试" placement="bottom">
                            <AppIcons class="icon" icon="InfoFilled" :size="22" @click="install(item?.name)" />
                        </el-tooltip>
                        <el-tooltip effect="dark" content="更新测试" placement="bottom">
                            <AppIcons class="icon" icon="WarningFilled" :size="22" @click="update(item?.name)" />
                        </el-tooltip>
                        <el-tooltip effect="dark" content="发布应用" placement="bottom">
                            <AppIcons class="icon" icon="UploadFilled" :size="22" @click="publish(item?.name)" />
                        </el-tooltip>
                    </div>
                </div>
            </div>
        </div>
        <div class="empty-container" v-else>
            <el-empty description="当前没有更多应用" />
        </div>
    </div>
</template>

<script >
export default {
    data() {
        return {
            developer_mode: '10',
            datalist: []
        }
    },
    async mounted() {
        await this.getDeveloperMode()
        await this.getList()
    },
    methods: {
        // 应用发布
        publish(app_name) {
            this.$useRemote('remote/developer/publish', { app_name }, {
                title: '更新测试',
                customStyle: {
                    width: '70%',
                    height: '90vh',
                },
                beforeClose: (value, state, done) => {
                    this.getList()
                    done()
                }
            })
        },
        // 安装测试
        install(app_name) {
            const params = {
                app_name
            }
            const loading = this.$useLoading('正在进行数据安装测试...')
            this.$http.useGet('admin/developer/install',params).then(res => {
                this.$useNotify(res?.msg || "网络错误", 'success', '温馨提示')
            }).finally(() => {
                loading.close()
            })
        },
        // 更新测试
        update(app_name) {
            const params = {
                app_name
            }
            const loading = this.$useLoading('正在进行数据更新测试...')
            this.$http.useGet('admin/developer/update',params).then(res => {
                this.$useNotify(res?.msg || "网络错误", 'success', '温馨提示')
            }).finally(() => {
                loading.close()
            })
        },
        // 切换模式
        hanldMode(value) {
            const params = {
                developerMode: value
            }
            this.$http.usePost('admin/developer/getDeveloperMode', params).then(res => {
                this.$useNotify(res?.msg || "网络错误", 'success', '温馨提示')
            })
        },
        // 获取开发者模式
        getDeveloperMode() {
            this.$http.useGet('admin/developer/getDeveloperMode').then(res => {
                this.developer_mode = res?.data?.developerMode ?? '10'
            })
        },
        // 获取应用数据
        getList() {
            this.$http.useGet('admin/developer/index').then(res => {
                this.datalist = res?.data ?? []
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;

    .content-container {
        flex: 1;

        .content {
            display: grid;
            grid-template-columns: repeat(24, 1fr);
            overflow-y: auto;
            overflow-x: hidden;
            gap: 15px;
            padding: 15px;

            .item {
                grid-column: span 3;
                display: flex;
                flex-direction: column;
                justify-content: center;
                padding-top: 15px;
                position: relative;

                .logo-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;

                    .logo {
                        width: 100%;
                        height: 100px;
                    }
                }

                .title {
                    position: absolute;
                    bottom: 28px;
                    left: 0;
                    right: 0;
                    background: rgba(#000000, .4);
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    color: #fff;
                    padding: 3px 6px;
                    font-size: 12px;
                }

                .action {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    padding-top: 6px;

                    .icon {
                        cursor: pointer;
                    }
                }
            }
        }
    }

    .empty-container {
        flex: 1;
        display: flex;
        justify-content: center;
        align-items: center;
    }
}
</style>