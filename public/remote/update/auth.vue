<template>
    <div class="empower-container">
        <div class="title-container">
            <div class="title">系统授权信息</div>
            <div class="more">
                <el-button type="info" @click="openDeveloper()">开发者应用</el-button>
                <el-button type="warning" @click="openRecharge()">在线充值</el-button>
                <el-button type="success" @click="openCloud()">云服务</el-button>
            </div>
        </div>
        <div class="empower-box">
            <div class="empower" v-if="detail?.title">
                <el-divider>系统授权信息</el-divider>
                <div class="item">
                    <div class="label">
                        系统版本
                    </div>
                    <div class="value">{{ detail?.system_info?.version_name || '版本错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        站点名称
                    </div>
                    <div class="value">{{ detail?.title || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        站点域名
                    </div>
                    <div class="value">{{ detail?.domain || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        服务地址
                    </div>
                    <div class="value">{{ detail?.ip || '授权错误' }}</div>
                </div>
                <div class="item">
                    <div class="label">
                        授权版本
                    </div>
                    <div class="value">
                        <el-tag type="success" v-if="detail?.is_auth == '20'">商业版</el-tag>
                        <el-tag type="info" v-else>免费版</el-tag>
                    </div>
                </div>
            </div>
            <el-empty v-else description="未获取授权版本，或未登录云服务" />
        </div>
    </div>
</template>

<script >
export default {
    data() {
        return {
            detail: {
                title: '',
                ip: '',
                domain: '',
                logo: '',
                is_auth: 0
            }
        }
    },
    async mounted() {
        await this.getDetail();
    },
    methods: {
        // 打开云服务
        openCloud() {
            this.$useRemote('remote/cloud/user', {}, {
                title: '云服务中心',
                customStyle: {
                    width: '70%',
                    height: '90vh',
                },
                beforeClose: (value, state, done) => {
                    done()
                }
            })
        },
        // 打开充值
        openRecharge() {
            this.$useRemote('remote/cloud/recharge', {}, {
                title: '在线充值',
                customStyle: {
                    width: '500px',
                    height: '550px',
                },
                beforeClose: (value, state, done) => {
                    done()
                }
            })
        },
        // 打开开发者应用页面
        openDeveloper() {
            this.$useRemote('remote/developer/index', {}, {
                title: '开发者应用',
                customStyle: {
                    width: '70%',
                    height: '90vh',
                },
                beforeClose: (value, state, done) => {
                    done()
                }
            })
        },
        // 获取授权信息
        getDetail() {
            const _this = this;
            _this.$http.usePut("admin/Update/auth").then((res) => {
                _this.detail = res?.data || {};
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.empower-container {
    background: #fff;
    height: 100%;
    display: flex;
    flex-direction: column;

    .title-container {
        height: 50px;
        border-bottom: 1px solid #e5e5e5;
        display: flex;
        justify-content: space-between;
        padding: 0 20px;

        .title {
            display: flex;
            align-items: center;
            font-size: 16px;
            font-weight: 700;
        }

        .more {
            display: flex;
            align-items: center;
        }
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
</style>