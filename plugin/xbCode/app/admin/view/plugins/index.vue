<template>
    <div class="plugins-container">
        <div class="body-container">
            <div class="xb-header">
                <div class="xb-tabs">
                    <div class="item" :class="{ active: item.name === tabs.active }" v-for="item in tabs.list"
                        :key="item.name" @click="handleTabChange(item.name)">
                        <xbIcons :icon="item.icon" />
                        <span class="text">{{ item.label }}</span>
                    </div>
                </div>
                <div class="user">
                    <el-button type="primary" @click="hanldImport">
                        <template #icon>
                            <xbIcons icon="Plus" />
                        </template>
                        <span>导入插件</span>
                    </el-button>
                </div>
            </div>
            <div class="xb-body">
                <div class="xb-plugin-content" v-if="datalist.length">
                    <div class="xb-content">
                        <div class="item" v-for="item in datalist" :key="item">
                            <!-- 信息区 -->
                            <div class="plugin-info">
                                <div class="logo">
                                    <el-image :src="item.preview"></el-image>
                                </div>
                                <div class="info">
                                    <div class="title-block">
                                        <div class="title">{{ item.title }}</div>
                                    </div>
                                    <div class="desc-block">
                                        {{ item.desc }}
                                    </div>
                                </div>
                            </div>
                            <div class="buttons">
                                <el-button type="primary" link v-if="item.install === '10'" @click="hanldInstall(item)">
                                    <template #icon>
                                        <xbIcons icon="Plus" />
                                    </template>
                                    <span>安装</span>
                                </el-button>
                                <el-button type="info" link v-if="item.install === '20' && item.has_config === '20'"
                                    @click="hanldConfig(item)">
                                    <template #icon>
                                        <xbIcons icon="Setting" />
                                    </template>
                                    <span>配置</span>
                                </el-button>
                                <el-button type="success" :disabled="item.is_system === '20'" v-if="item.install === '20' && item.state === '10'"
                                    @click="setPluginState(item, '20')" link>
                                    <template #icon>
                                        <xbIcons icon="Check" />
                                    </template>
                                    <span>启用</span>
                                </el-button>
                                <el-button type="warning" :disabled="item.is_system === '20'" v-if="item.install === '20' && item.state === '20'"
                                    @click="setPluginState(item, '10')" link>
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
                                <el-button type="danger" v-if="item.is_system === '10' && item.install === '10'"
                                    @click="hanldDel(item)" link>
                                    <template #icon>
                                        <xbIcons icon="Delete" />
                                    </template>
                                    <span>删除</span>
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
            tabs: {
                active: 'plugins',
                list: [
                    {
                        icon: 'CodeSandboxOutlined',
                        name: 'plugins',
                        label: '未安装插件',
                    },
                    {
                        icon: 'CompassOutlined',
                        name: 'installed',
                        label: '已安装插件',
                    },
                ],
            },
            loadingObj: {
                state: true,
                text: '正在加载中...',
            },
            datalist: [],
        }
    },
    mounted() {
        this.tabs.active = this.$route.query.type || 'plugins'
        this.getPlugins()
    },
    methods: {
        // 处理tab切换
        handleTabChange(name) {
            this.$router.push({ query: { type: name } })
        },
        // 导入插件
        hanldImport() {
            this.$xbcode.useRemoteModal('app/xbCode/admin/Plugins/import', {}, {
                onRefresh: () => {
                    // 刷新数据
                    this.getPlugins();
                },
            }, {
                title: '导入插件',
                customStyle: {
                    width: '35vw',
                    height: '45vh',
                },
            })
        },
        // 安装插件
        hanldInstall(item) {
            this.$xbcode.useConfirm(`是否确定安装「${item.title}」插件？`, '温馨提示', 'error').then(() => {
                this.$xbcode.useRemoteModal('app/xbCode/admin/Plugins/install', {}, {
                    plugin: item,
                    onRefresh: () => {
                        // 刷新数据
                        this.getPlugins();
                    },
                }, {
                    title: `正在安装「${item.title}」`,
                    customStyle: {
                        width: '35vw',
                        height: '50vh',
                    },
                });
            })
        },
        // 卸载插件
        hanldUninstall(item) {
            this.$xbcode.useConfirm(`是否确定卸载「${item.title}」插件？`, '温馨提示', 'error').then(() => {
                this.$xbcode.useRemoteModal('app/xbCode/admin/Plugins/uninstall', {}, {
                    plugin: item,
                    onRefresh: () => {
                        // 刷新数据
                        this.getPlugins();
                    },
                }, {
                    title: `正在卸载「${item.title}」`,
                    customStyle: {
                        width: '35vw',
                        height: '50vh',
                    },
                });
            })
        },
        // 删除插件
        hanldDel(item) {
            this.$xbcode.useConfirm(`是否确认要删除插件「${item.title}」？`, '温馨提示', 'error').then(() => {
                this.$xbcode.useDelete('app/xbCode/admin/Plugins/del', {
                    name: item.name,
                }).then((res) => {
                    this.getPlugins()
                });
            })
        },
        // 插件配置
        hanldConfig(item) {
            this.$xbcode.useRemoteModal('app/xbCode/admin/Plugins/config', {
                name: item.name,
            }, {
                onRefresh: () => {
                    // 刷新数据
                    this.getPlugins();
                },
            }, {
                title: `${item.title}-配置管理`,
                customStyle: {
                    width: '35vw',
                    height: '50vh',
                },
            });
        },
        // 设置插件状态
        setPluginState(item, state) {
            const message = state === '20' ? '启用' : '禁用';
            const type = state === '20' ? 'success' : 'warning';
            this.$xbcode.useConfirm(`是否确认要${message}「${item.title}」？`, '温馨提示', type).then(() => {
                this.$xbcode.usePut('app/xbCode/admin/Plugins/state', {
                    name: item.name,
                    value: state,
                }).then((res) => {
                    this.getPlugins()
                });
            })
        },
        // 获取插件列表
        getPlugins() {
            const params = {
                type: this.tabs.active,
                _act: 'query'
            }
            this.$xbcode.useGet('app/xbCode/admin/Plugins/index', params).then((res) => {
                this.datalist = res?.data ?? [];
            }).finally(() => {
                this.loadingObj.state = false;
            })
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

                    &:hover,
                    &.active {
                        color: var(--el-color-primary);
                        background-color: #f9f9f9;
                    }
                }
            }

            .user {
                display: flex;
                gap: 10px;
                padding-right: 20px;
            }
        }

        .xb-body {
            flex: 1;
            height: 100%;
            overflow: hidden;
            display: flex;
            flex-direction: column;

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
                            height: 120px;
                            display: flex;
                            align-items: center;
                            gap: 20px;
                            padding: 0 15px;

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