<template>
    <div class="user-container">
        <div class="body-container" v-if="userInfo">
            <div class="userinfo">
                <div class="avatar">
                    <img :src="userInfo.avatar" alt="">
                </div>
                <div class="info">
                    <div class="nickname">{{ userInfo.nickname }}</div>
                    <div class="username">{{ userInfo.username }}</div>
                </div>
            </div>
            <div class="action">
                <el-button type="primary" @click="hanldLogout">退出登录</el-button>
            </div>
        </div>
        <div class="loading" v-else v-loading="true" element-loading-text="正在获取用户信息..." />
    </div>
</template>

<script>
export default {
    props: {
        logOutCallback: {
            type: Function,
            default: () => {}
        }
    },
    data() {
        return {
            userInfo: null
        }
    },
    mounted() {
        this.getUserInfo()
    },
    methods: {
        hanldLogout() {
            const _this = this; 
            useVue.$useConfirm('确定要退出登录吗？', '温馨提示', 'warning').then(() => {
                useVue.$usePost('app/xbPlugins/admin/Server/logout').then(() => {
                    useVue.$useNotify('退出登录成功', 'success', '温馨提示')
                    _this.logOutCallback()
                    this.$emit('close')
                })
            })
        },
        getUserInfo() {
            useVue.$useGet('app/xbPlugins/admin/Server/userinfo').then((res) => {
                this.userInfo = res?.data ?? null;
            })
        }
    }
}
</script>

<style lang="scss" scoped>
.user-container {
    height: 230px;
    .body-container {
        height: 100%;
        padding: 0 30px;
        display: flex;
        justify-content: space-between;

        .userinfo {
            display: flex;
            align-items: center;

            .avatar {
                width: 50px;
                height: 50px;

                img {
                    width: 100%;
                    height: 100%;
                    border-radius: 50%;
                }
            }

            .info {
                margin-left: 10px;

                .nickname {
                    font-size: 16px;
                    color: #333;
                }

                .username {
                    font-size: 12px;
                    color: #666;
                }
            }
        }

        .action {
            display: flex;
            align-items: center;
        }
    }
    .loading {
        height: 100%;
    }
}
</style>