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
                <div class="item" v-for="(item, index) in datalist" :key="index" @click="detail(item?.name)">
                    <div class="logo-container">
                        <el-image class="logo"
                            src="https://fuss10.elemecdn.com/e/5d/4a731a90594a4af544c0c25941171jpeg.jpeg" />
                    </div>
                    <div class="action">
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

                .logo-container {
                    display: flex;
                    justify-content: center;
                    align-items: center;

                    .logo {
                        width: 80px;
                        height: 80px;
                        border-radius: 5px;
                    }
                }

                .action {
                    display: flex;
                    justify-content: center;
                    align-items: center;
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