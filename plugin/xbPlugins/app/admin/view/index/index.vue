<template>
    <div class="plugins-container">
        <div class="body-container">
            <div class="xb-header">
                <div class="xb-tabs">
                    <div class="item active" @click="hanldOpen('/app/xbPlugins/admin/Index/index')">
                        <xbIcons icon="CodeSandboxOutlined" />
                        <span class="text">插件市场</span>
                    </div>
                    <div class="item" @click="hanldOpen('/app/xbCode/admin/Plugins/index', { type: 'installed' })">
                        <xbIcons icon="Position" />
                        <span class="text">已安装插件</span>
                    </div>
                </div>
                <div class="user">
                    <el-button type="primary" @click="hanldUser">
                        <template #icon>
                            <xbIcons icon="UserOutlined" />
                        </template>
                        <span>{{ user ? user.nickname : '未登录' }}</span>
                    </el-button>
                </div>
            </div>
            <div class="xb-body">
                <div class="xb-plugin-filter">
                    <div class="item">
                        <div class="title">插件类型</div>
                        <div class="values">
                            <el-radio-group>
                                <el-radio-button label="all">全部</el-radio-button>
                                <el-radio-button label="free">基础</el-radio-button>
                                <el-radio-button label="charge">扩展</el-radio-button>
                            </el-radio-group>
                        </div>
                    </div>
                    <div class="item">
                        <div class="title">是否收费</div>
                        <div class="values">
                            <el-radio-group>
                                <el-radio-button label="all">全部</el-radio-button>
                                <el-radio-button label="free">免费</el-radio-button>
                                <el-radio-button label="charge">付费</el-radio-button>
                            </el-radio-group>
                        </div>
                    </div>
                </div>
                <div class="xb-plugin-content" v-if="datalist.length">
                    <div class="xb-content">
                        <div class="item" v-for="item in datalist" :key="item">
                            <!-- 信息区 -->
                            <div class="plugin-info">
                                <div class="logo">
                                    <el-image :src="item.logo"></el-image>
                                </div>
                                <div class="info">
                                    <div class="title-block">
                                        <div class="title">{{ item.title }}</div>
                                        <div class="price" v-if="item.local === '10'">￥500.00</div>
                                    </div>
                                    <div class="desc-block">
                                        {{ item.desc }}
                                    </div>
                                </div>
                            </div>
                            <div class="buttons">
                                <el-button type="primary" link v-if="item.buy_state === '10'" @click="hanldBuy(item)">
                                    <template #icon>
                                        <xbIcons icon="Sell" />
                                    </template>
                                    <span>购买</span>
                                </el-button>
                                <el-button type="primary" link v-if="item.install === '10'" @click="hanldInstall(item)">
                                    <template #icon>
                                        <xbIcons icon="Plus" />
                                    </template>
                                    <span>安装</span>
                                </el-button>
                                <el-button type="success" link v-if="item.install === '20' && item.update === '20'"
                                    @click="hanldUpdate(item)">
                                    <template #icon>
                                        <xbIcons icon="Position" />
                                    </template>
                                    <span>更新</span>
                                </el-button>
                                <el-button type="success" link v-if="item.install === '20' && item.state === '10'"
                                    @click="setPluginState(item, '20')">
                                    <template #icon>
                                        <xbIcons icon="Check" />
                                    </template>
                                    <span>启用</span>
                                </el-button>
                                <el-button type="warning" v-if="item.install === '20' && item.state === '20'"
                                    :disabled="item.is_system === '20'" @click="setPluginState(item, '10')" link>
                                    <template #icon>
                                        <xbIcons icon="Close" />
                                    </template>
                                    <span>禁用</span>
                                </el-button>
                                <el-button type="danger" :disabled="item.is_system === '20'"
                                    v-if="item.install === '20'" @click="hanldUninstall(item)" link>
                                    <template #icon>
                                        <xbIcons icon="Delete" />
                                    </template>
                                    <span>卸载</span>
                                </el-button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="empty" v-else>
                    <div class="loading" v-if="loadingObj.state" v-loading="loadingObj.state"
                        element-loading-text="正在加载中..." />
                    <el-empty description="暂无数据" v-else />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            user: null,
            loadingObj: {
                state: true,
                text: '正在加载中...',
            },
            datalist: [],
        }
    },
    mounted() {
        this.getPlugins()
        this.getUserInfo()
    },
    methods: {
        hanldOpen(path, query = {}) {
            this.$router.push({
                path,
                query
            })
        },
        hanldBuy(item) {
            useVue.$useRemote('app/xbPlugins/admin/Plugins/buy', {
                plugin: item,
                closeCallback: () => {
                    this.getPlugins()
                }
            }, {
                title: `购买「${item.title}」`,
                customStyle: {
                    width: '600px',
                },
            });
        },
        hanldInstall(item) {
            const _this = this;
            useVue.$useRemote('app/xbPlugins/admin/Plugins/install', {
                plugin: item,
                closeCallback: () => {
                    _this.getPlugins()
                }
            }, {
                title: `正在安装「${item.title}」`,
                customStyle: {
                    width: '600px',
                },
            });
        },
        hanldUpdate(item) {
            useVue.$useRemote('app/xbPlugins/admin/Plugins/update', {
                plugin: item,
                closeCallback: () => {
                    this.getPlugins()
                }
            }, {
                title: `正在更新「${item.title}」`,
                customStyle: {
                    width: '600px',
                },
            });
        },
        hanldUninstall(item) {
            useVue.$useConfirm(`是否确定要卸载「${item.title}」？`, '温馨提示', 'error').then(() => {
                useVue.$useRemote('app/xbPlugins/admin/Plugins/uninstall', {
                    plugin: item,
                    closeCallback: () => {
                        this.getPlugins()
                    }
                }, {
                    title: `正在卸载「${item.title}」`,
                    customStyle: {
                        width: '600px',
                    },
                });
            })
        },
        setPluginState(item, state) {
            const message = state === '20' ? '启用' : '禁用';
            const type = state === '20' ? 'success' : 'warning';
            useVue.$useConfirm(`是否确认要${message}「${item.title}」？`, '温馨提示', type).then(() => {
                useVue.$axios.put('app/xbPlugins/admin/Plugins/state', {
                    name: item.name,
                    state: state,
                }).then((res) => {
                    this.getPlugins()
                });
            })
        },
        hanldUser() {
            if (this.user) {
                this.userView()
            } else {
                this.loginView()
            }
        },
        getPlugins() {
            this.$xbcode.useGet('app/xbPlugins/admin/Index/plugins').then((res) => {
                this.datalist = res?.data ?? [];
            }).finally(() => {
                this.loadingObj.state = false;
            })
        },
        getUserInfo() {
            this.$xbcode.useGet('app/xbPlugins/admin/Server/userinfo').then((res) => {
                const userinfo = res?.data?.id ? res?.data : null;
                this.user = userinfo;
            }).catch(() => {
                this.user = null;
            })
        },
        userView() {            
            this.$xbcode.useRemoteModal('app/xbPlugins/admin/Server/user', {
                logOutCallback: () => {
                    _this.getUserInfo()
                }
            }, {
                title: '用户中心',
                customStyle: {
                    width: '400px',
                },
            });
        },
        loginView() {
            this.$xbcode.useRemoteModal('app/xbPlugins/admin/Server/login', {
            }, {},{
                title: '微信扫码登录',
                customStyle: {
                    width: '380px',
                },
            });
        },
    },
}
</script>

<style lang="scss" scoped>
.plugins-container {
    height: 100%;
    box-sizing: border-box;
    overflow: hidden;

    .body-container {
        height: 100%;
        display: flex;
        flex-direction: column;
        background-color: #fff;

        .xb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 0 10px;
            border-bottom: 1px solid #e8e8e8;

            .xb-tabs {
                display: flex;
                gap: 6px;

                .item {
                    display: flex;
                    align-items: center;
                    gap: 6px;
                    padding: 20px 10px;
                    cursor: pointer;
                    font-size: 14px;
                    user-select: none;

                    &:hover {
                        color: var(--el-color-primary);
                        background-color: #f9f9f9;
                    }

                    &.active {
                        color: var(--el-color-primary);
                    }
                }
            }

            .user {
                display: flex;
                gap: 10px;
            }
        }

        .xb-body {
            flex: 1;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;

            .xb-plugin-filter {
                display: flex;
                flex-direction: column;
                border-bottom: 1px solid #e8e8e8;

                .item {
                    display: flex;
                    gap: 20px;
                    padding: 10px 20px;

                    .title {
                        font-size: 14px;
                        line-height: 32px;
                    }

                    .values {
                        flex: 1;
                        display: flex;
                        gap: 10px;
                    }
                }
            }

            .xb-plugin-content {
                flex: 1;
                height: 100%;
                overflow-y: auto;
                background-color: #fdfdfd;

                .xb-content {
                    display: grid;
                    gap: 20px;
                    padding: 10px;
                    grid-template-columns: repeat(4, 1fr);
                    box-sizing: border-box;

                    .item {
                        display: flex;
                        flex-direction: column;
                        border: 1px solid #e8e8e8;
                        border-radius: 4px;
                        background-color: #fff;

                        &:hover {
                            box-shadow: 1px 2px 10px rgba(0, 0, 0, .1);
                            transition: all .3s;
                        }

                        .plugin-info {
                            display: flex;
                            gap: 20px;
                            padding: 30px 15px;

                            .logo {
                                width: 100px;
                                height: 70px;

                                img {
                                    width: 100%;
                                    height: 100%;
                                    object-fit: cover;
                                    border-radius: 4px;
                                }
                            }

                            .info {
                                flex: 1;

                                .title-block {
                                    display: flex;
                                    justify-content: space-between;

                                    .title {
                                        font-size: 20px;
                                        font-weight: bold;
                                    }

                                    .price {
                                        font-size: 20px;
                                        color: var(--el-color-danger);
                                    }
                                }

                                .desc-block {
                                    padding-top: 10px;
                                    font-size: 14px;
                                    color: #666;
                                }
                            }
                        }

                        .buttons {
                            display: flex;
                            align-items: center;
                            gap: 20px;
                            padding: 10px;
                            border-top: 1px solid #e8e8e8;
                        }
                    }
                }
            }
        }
    }

    .empty {
        height: 100%;
        display: flex;
        justify-content: center;
        align-items: center;
        background-color: #fdfdfd;

        .loading {
            width: 100%;
            height: 100%;
        }
    }
}
</style>