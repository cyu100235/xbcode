<template>
    <div class="pages-container">
        <div class="detail-container">
            <!-- 详情 -->
            <div class="detail">
                <div class="swiper">
                    <el-carousel indicator-position="outside" height="230px">
                        <el-carousel-item v-for="item in 4" :key="item">
                            <el-image class="priview" :src="item?.url" />
                        </el-carousel-item>
                    </el-carousel>
                </div>
                <div class="info">
                    <div class="item">
                        <div class="app-title">{{ detail?.title }}</div>
                    </div>
                    <div class="item">
                        <div class="label">应用发布：</div>
                        <div class="value">2023-11-28 17:12</div>
                    </div>
                    <div class="item">
                        <div class="label">销售价格：</div>
                        <div class="value money">￥{{ detail?.money }}</div>
                    </div>
                    <div class="item">
                        <div class="label">应用分类：</div>
                        <div class="value">
                            <el-tag>应用工具</el-tag>
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">适用平台：</div>
                        <div class="value">
                            <el-tag>应用工具</el-tag>
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">最新版本：</div>
                        <div class="value">
                            v1.5.3（1000）
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">本地版本：</div>
                        <div class="value">
                            v1.0.0（1000）
                        </div>
                    </div>
                    <div class="action">
                        <el-button type="primary" v-if="!detail?.buy" @click="buy()">
                            购买
                        </el-button>
                        <el-button type="primary" v-if="!detail?.install && detail?.buy" @click="install()">
                            安装
                        </el-button>
                        <el-button type="success" v-if="detail?.update" @click="update()">
                            更新
                        </el-button>
                        <el-button type="danger" v-if="detail?.install" @click="uninstall()">
                            卸载
                        </el-button>
                    </div>
                </div>
            </div>
            <div class="description">
                <el-alert title="购买应用将和当前域名、IP、云账户关联，不支持更换" type="error" :closable="false" />
            </div>
            <div class="user-container">
                <div class="user">
                    <el-avatar :src="user?.avatar" class="avatar" />
                    <div class="info">
                        <div class="nickname">{{ user?.nickname }}</div>
                        <div class="money">￥{{ user?.balance }}</div>
                    </div>
                </div>
                <div class="into">
                    <el-button type="primary" size="mini" @click="openUser()">用户中心</el-button>
                </div>
            </div>
            <div class="desc-container">
                <div class="title">应用介绍</div>
                <div class="content">
                    匿名聊天室和盲盒交友有异曲同工之处，小而美的氪金神器，越来越堵的单身青年都想通过扩列交到异性朋友，而假装聊天室恰好可以抓住这波机遇，常年躺赚的吸金小项目。
                </div>
            </div>
            <div class="content-container">
                <div class="title">应用内容</div>
                <div class="content" v-html="detail?.content" />
            </div>
        </div>
        <div class="log-container">
            <div class="title">更新日志</div>
            <div class="content">
                <div class="item" v-for="(item, index) in versionList" :key="index">
                    <div class="version-container">
                        <div class="version">{{ item.version_name }}（{{ item.version }}）</div>
                        <div class="time">{{ item.create_time }}</div>
                    </div>
                    <div class="value" v-html="item.content" />
                </div>
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
        }
    },
    data() {
        return {
            user: {},
            detail: {},
            versionList: [
                {
                    version_name: 'v1.0.0',
                    version: 1000,
                    create_time: '2024-01-11 16:27',
                    content: "- 优化心跳检测，对方不在线通知对方\n- 修复聊天页面表情不显示\n- 优化提现提示\n- 优化提现提示没有上传二维码，需要做跳转到上传页面\n- 优化微信环境内，如果是短信登录的话，没法进行第四方支付\n- 优化通知模版消息能否一键获取\n- 隐藏权限管理"
                },
                {
                    version_name: 'v1.0.0',
                    version: 1000,
                    create_time: '2024-01-11 16:27',
                    content: "- 优化心跳检测，对方不在线通知对方\n- 修复聊天页面表情不显示\n- 优化提现提示\n- 优化提现提示没有上传二维码，需要做跳转到上传页面\n- 优化微信环境内，如果是短信登录的话，没法进行第四方支付\n- 优化通知模版消息能否一键获取\n- 隐藏权限管理"
                },
                {
                    version_name: 'v1.0.0',
                    version: 1000,
                    create_time: '2024-01-11 16:27',
                    content: "- 优化心跳检测，对方不在线通知对方\n- 修复聊天页面表情不显示\n- 优化提现提示\n- 优化提现提示没有上传二维码，需要做跳转到上传页面\n- 优化微信环境内，如果是短信登录的话，没法进行第四方支付\n- 优化通知模版消息能否一键获取\n- 隐藏权限管理"
                },
                {
                    version_name: 'v1.0.0',
                    version: 1000,
                    create_time: '2024-01-11 16:27',
                    content: "- 优化心跳检测，对方不在线通知对方\n- 修复聊天页面表情不显示\n- 优化提现提示\n- 优化提现提示没有上传二维码，需要做跳转到上传页面\n- 优化微信环境内，如果是短信登录的话，没法进行第四方支付\n- 优化通知模版消息能否一键获取\n- 隐藏权限管理"
                },
                {
                    version_name: 'v1.0.0',
                    version: 1000,
                    create_time: '2024-01-11 16:27',
                    content: "- 优化心跳检测，对方不在线通知对方\n- 修复聊天页面表情不显示\n- 优化提现提示\n- 优化提现提示没有上传二维码，需要做跳转到上传页面\n- 优化微信环境内，如果是短信登录的话，没法进行第四方支付\n- 优化通知模版消息能否一键获取\n- 隐藏权限管理"
                },
            ],
        }
    },
    async mounted() {
        await this.getUser();
        await this.getDetail();
    },
    methods: {
        // 安装应用
        install() {
            this.$useConfirm('是否确定开始安装应用？', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.version ?? 0
                }
                this.$http.useGet('admin/apps/install', params).then(res => {
                    this.$useNotify('购买成功')
                })
            })
        },
        // 更新应用
        update() {
            this.$useConfirm('是否确定开始更新应用？', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.version ?? 0
                }
                this.$http.useGet('admin/apps/update', params).then(res => {
                    this.$useNotify('购买成功')
                })
            })
        },
        // 卸载应用
        uninstall() {
            this.$useConfirm('是否确定卸载该应用？卸载后数据将清空', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.version ?? 0
                }
                this.$http.useGet('admin/apps/uninstall', params).then(res => {
                    this.$useNotify('购买成功')
                })
            })
        },
        // 购买应用
        buy() {
            this.$useConfirm('是否确定购买该应用？', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.version ?? 0
                }
                this.$http.useGet('admin/apps/buy', params).then(res => {
                    this.$useNotify('购买成功')
                })
            })
        },
        // 打开用户中心
        openUser() {
            this.$useRemote('remote/cloud/user', {}, {
                title: '用户中心',
                customStyle: {
                    width: '55%',
                    height: '700px',
                },
            })
        },
        // 获取用户信息
        getUser() {
            this.$http.useGet('admin/Cloud/user').then((res) => {
                if (res?.data?.code === 12000) {
                    return
                }
                this.user = res?.data ?? {}
            })
        },
        // 获取详情
        getDetail() {
            const params = {
                app_name: this.app_name
            }
            this.$http.useGet('admin/apps/detail', params).then(res => {
                this.detail = res?.data ?? {}
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    display: flex;

    .detail-container {
        overflow: hidden;
        padding: 0 20px;
        border-right: 1px solid #e8e8e8;

        .detail {
            display: flex;

            .swiper {
                width: 320px;

                .priview {
                    width: 320px;
                    height: 320px;
                    border-radius: 3px;
                }
            }

            .info {
                display: flex;
                flex-direction: column;
                gap: 10px;
                padding-left: 15px;

                .item {
                    font-size: 14px;
                    display: flex;
                    .app-title{
                        font-size: 16px;
                        font-weight: 700;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                    }

                    .label {
                        color: #888;
                        display: flex;
                        align-items: center;
                    }
                    .value{
                        color: #666;
                    }

                    .money {
                        color: red;
                    }
                }
            }
        }

        .description {
            margin-top: 20px;
        }

        .user-container {
            display: flex;
            justify-content: space-between;
            background-color: #f0f0f0;
            padding: 10px 20px;
            border-radius: 8px;
            margin-top: 20px;

            .user {
                display: flex;

                .avatar {
                    width: 45px;
                    height: 45px;
                }

                .info {
                    padding-left: 10px;
                    display: flex;
                    flex-direction: column;
                    justify-content: center;
                }
            }

            .into {
                display: flex;
                align-items: center;
            }
        }

        .desc-container {
            margin-top: 20px;

            .title {
                font-size: 16px;
                font-weight: 700;
            }

            .content {
                padding-top: 10px;
                font-size: 14px;
                color: #666;
            }
        }

        .content-container {
            margin-top: 20px;

            .title {
                font-size: 16px;
                font-weight: 700;
            }

            .content {
                padding-top: 10px;
                font-size: 14px;
                color: #666;
                line-height: 26px;
                white-space: pre-wrap;
            }
        }
    }

    .log-container {
        width: 320px;
        min-width: 280px;
        flex-shrink: 0;
        padding: 0 15px;

        .title {
            font-size: 16px;
            font-weight: 700;
        }

        .content {
            .item {
                padding-top: 20px;

                .version-container {
                    display: flex;
                    justify-content: space-between;
                    font-weight: 700;
                    font-size: 14px;
                }

                .value {
                    color: #888;
                    white-space: pre-wrap;
                    padding-top: 10px;
                    font-size: 12px;
                    line-height: 24px;
                }
            }
        }
    }
}</style>