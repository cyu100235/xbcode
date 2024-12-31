<template>
    <div class="authorize-container">
        <div class="body">
            <div class="title">系统授权信息</div>
            <div class="empower-box">
                <div class="empower" v-if="authorize?.system_name">
                    <div class="logo-box">
                        <div class="system-name">
                            {{ authorize?.system_name || '授权错误' }}
                            {{ authorize?.system_version || '版本错误' }}
                        </div>
                    </div>
                    <el-divider>系统授权信息</el-divider>
                    <div class="item">
                        <div class="label">
                            站点域名
                        </div>
                        <div class="value">{{ authorize?.domain || '授权错误' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">
                            服务地址
                        </div>
                        <div class="value">{{ authorize?.server_ip || '授权错误' }}</div>
                    </div>
                    <div class="item">
                        <div class="label">
                            使用授权
                        </div>
                        <div class="value">
                            <el-tag :type="authorize.expire_state ? 'success' :'error'">
                                {{ authorize?.expire_time || '未授权' }}
                            </el-tag>
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">
                            更新授权
                        </div>
                        <div class="value">
                            <el-tag type="warning">
                                {{ authorize?.update_time || '未授权' }}
                            </el-tag>
                        </div>
                    </div>
                </div>
                <el-empty v-else description="当前未授权" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            authorize: null,
        }
    },
    mounted() {
        this.getAuthorize();
    },
    methods: {
        getAuthorize() {
            this.$usePost('backend/Server/authorize').then((res) => {
                this.authorize = res?.data ?? null;
            });
        }
    }
}
</script>

<style lang="scss" scoped>
.authorize-container {
    height: 100%;
    padding: 15px;
    .body {
        background: #fff;
        height: 100%;
        display: flex;
        flex-direction: column;
        .title {
            height: 50px;
            border-bottom: 1px solid #e5e5e5;
            display: flex;
            align-items: center;
            padding: 0 20px;
            font-size: 16px;
            font-weight: 700;
        }

        .empower-box {
            display: flex;
            justify-content: center;
            height: calc(100% - 50px);

            .empower {
                width: 500px;
                padding: 30px 20px;
                border-radius: 10px;
                margin-top: 50px;

                .logo-box {
                    text-align: center;

                    .logo {
                        width: 100px;
                        height: 100px;
                        border-radius: 10px;
                        margin: 0 auto;
                    }

                    .system-name {
                        font-weight: 700;
                        font-size: 28px;
                        padding-top: 10px;
                    }
                }

                .item {
                    padding: 15px 0;
                    display: flex;
                    justify-content: center;
                    font-size: 14px;

                    .label {
                        width: 200px;
                        text-align: center;
                    }

                    .value {
                        width: 200px;
                    }
                }
            }
        }
    }
}
</style>