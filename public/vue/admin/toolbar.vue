<template>
    <div class="toolbar-container">
        <view class="toolbar">
            <el-tooltip v-bind="item.props" v-for="(item, index) in toolbar" :key="index">
                <div class="item" @click="item.hanlder">
                    <el-badge :is-dot="item.hot">
                        <AppIcons :icon="item.icon" color="#666" />
                    </el-badge>
                </div>
            </el-tooltip>
        </view>
    </div>
</template>

<script>
export default {
    data() {
        return {
            toolbar: [
                {
                    props: {
                        placement: 'bottom',
                        effect: 'dark',
                        content: '控制台主页'
                    },
                    hot: false,
                    icon: 'House',
                    hanlder: this.homes
                },
                {
                    props: {
                        placement: 'bottom',
                        effect: 'dark',
                        content: '清除缓存'
                    },
                    hot: false,
                    icon: 'Brush',
                    hanlder: this.clear
                },
                {
                    props: {
                        placement: 'bottom',
                        effect: 'dark',
                        content: '系统重启'
                    },
                    icon: 'IceCream',
                    hanlder: this.restart
                },
                {
                    props: {
                        placement: 'bottom',
                        effect: 'dark',
                        content: '版本更新'
                    },
                    hot: true,
                    icon: 'Monitor',
                    hanlder: this.updated
                },
                // {
                //     props: {
                //         placement: 'bottom',
                //         effect: 'dark',
                //         content: '主题设置'
                //     },
                //     hot: true,
                //     icon: 'Setting',
                //     hanlder: this.settings
                // },
            ],
        }
    },
    mounted() {
    },
    methods: {
        // 控制台主页
        homes() {
            this.$router.push({ path: '/' })
        },
        // 清除缓存
        clear() {
            this.$router.push({ path: '/admin/Index/clear' })
        },
        // 检查系统重启
        checkRestart(loadingBar, count = 5) {
            if (count <= 0) {
                loadingBar.close()
                this.$useNotify('系统重启失败', 'error', '温馨提示')
                return
            }
            const params = {
                state: 'checked'
            }
            this.$http.usePost('admin/Index/restart', params).then(res => {
                loadingBar.close()
                this.$useNotify('系统重启成功', 'success', '温馨提示')
            }).catch(() => {
                count--
                setTimeout(() => {
                    this.checkRestart(loadingBar, count)
                }, 1000);
            })
        },
        // 弹出确认框
        restart() {
            const _this = this
            _this.$useConfirm('是否确定重启系统？', '温馨提示', 'warning').then(() => {
                // 进度条
                const loading = _this.$useLoading('正在重启系统，请稍后...');
                const params = {
                    state: 'restart'
                }
                _this.$http.usePost('admin/Index/restart', params).then(res => {
                    loading.close()
                    _this.$useNotify(res?.msg ?? '系统重启异常', 'success', '温馨提示')
                }).catch(err => {
                    setTimeout(() => {
                        _this.checkRestart(loading)
                    }, 1000);
                })
            })
        },
        // 版本更新
        updated() {
            this.$router.push({ path: '/admin/Updated/index' })
        },
        // 主题设置
        settings() {
            console.log('主题设置');
        },
    },
}
</script>

<style lang="scss" scoped>
.toolbar-container {
    height: 100%;
    display: flex;
    justify-content: flex-end;

    .toolbar {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: 100%;

        .item {
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100%;
            padding: 0 15px;
            cursor: pointer;
            transition: background-color 0.3s;

            &:hover {
                background-color: #f5f5f5;
            }
        }
    }
}
</style>