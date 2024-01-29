<template>
    <div class="pages-container">
        <div class="detail-container">
            <!-- 详情 -->
            <div class="detail">
                <div class="swiper">
                    <el-carousel indicator-position="outside" height="230px">
                        <el-carousel-item v-for="item in detail?.priview" :key="item">
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
                        <div class="value">{{ detail?.publish_time }}</div>
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
                            {{ detail?.new_version_name ? detail?.new_version_name : '未发布' }}
                            {{ detail?.new_version ? `（${detail?.new_version}）` : '' }}
                        </div>
                    </div>
                    <div class="item">
                        <div class="label">本地版本：</div>
                        <div class="value">
                            {{ detail?.client_version_name ? detail?.client_version_name : '未安装' }}
                            {{ detail?.client_version ? `（${detail?.client_version}）` : '' }}
                        </div>
                    </div>
                    <div class="action">
                        <el-button type="warning" v-if="!detail?.buy" @click="buy()">
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
                <div class="content" v-html="detail?.desc" />
            </div>
            <div class="content-container">
                <div class="title">应用内容</div>
                <div class="content" v-html="detail?.content" />
            </div>
        </div>
        <div class="log-container">
            <div class="title">更新日志</div>
            <div class="content" v-if="detail?.versions?.length">
                <div class="item" v-for="(item, index) in detail?.versions" :key="index">
                    <div class="version-container">
                        <div class="version">{{ item.version_name }}（{{ item.version }}）</div>
                        <div class="time">{{ item.publish_time }}</div>
                    </div>
                    <div class="value" v-html="item.content" />
                </div>
            </div>
            <div class="empty-container" v-else>
                <el-empty description="暂无更新日志" />
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
                    version: this.detail?.new_version ?? 0
                }
                this.$emit("update:closeWin");
                this.$useRemote('remote/apps/install', params, {
                    title: '应用安装',
                    customStyle: {
                        width: '1000px',
                        height: '700px',
                    },
                })
            })
        },
        // 更新应用
        update() {
            this.$useConfirm('是否确定开始更新应用？', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.new_version ?? 0
                }
                this.$emit("update:closeWin");
                this.$useRemote('remote/apps/update', params, {
                    title: '应用更新',
                    customStyle: {
                        width: '1000px',
                        height: '700px',
                    },
                })
            })
        },
        // 卸载应用
        uninstall() {
            this.$useConfirm('是否确定卸载该应用？卸载后数据将清空', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.new_version ?? 0
                }
                this.$emit("update:closeWin");
                this.$useRemote('remote/apps/uninstall', params, {
                    title: '应用卸载',
                    customStyle: {
                        width: '1000px',
                        height: '700px',
                    },
                })
            })
        },
        // 购买应用
        buy() {
            this.$useConfirm('是否确定购买该应用？', '温馨提示', 'warning').then(() => {
                const params = {
                    app_name: this.app_name,
                    version: this.detail?.new_version ?? 0
                }
                this.$http.useGet('admin/apps/buy', params).then(res => {
                    this.getUser()
                    this.getDetail()
                    this.$useNotify(res?.msg || "购买成功", 'success', '温馨提示')
                })
            })
        },
        // 打开用户中心
        openUser() {
            this.$useRemote('remote/cloud/user', {}, {
                title: '用户中心',
                customStyle: {
                    width: '1000px',
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
        flex: 1;
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

                    .app-title {
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

                    .value {
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

        .empty-container {
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }
}
</style>