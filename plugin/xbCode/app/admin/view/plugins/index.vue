<template>
    <div class="plugins-container">
        <div class="body-container">
            <div class="xb-header">
                <div class="xb-tabs">
                    <div class="item" :class="{ active: item.name === tabs.active }" v-for="item in tabs.list"
                        :key="item.name" @click="handleTabChange(item.name)">
                        <XbIcons :icon="item.icon" />
                        <span class="text">{{ item.label }}</span>
                    </div>
                </div>
                <div class="user">
                    <el-button type="primary" @click="hanldImport">
                        <template #icon>
                            <XbIcons icon="Plus" />
                        </template>
                        <span>导入插件</span>
                    </el-button>
                    <el-button type="warning" @click="hanldeRefresh">
                        <template #icon>
                            <XbIcons icon="Refresh" />
                        </template>
                        <span>刷新缓存</span>
                    </el-button>
                </div>
            </div>
            <div class="xb-body">
                <div class="xb-plugin-content" v-if="datalist.length">
                    <el-row :gutter="20" class="plugin-grid">
                        <el-col :xs="24" :sm="12" :md="8" :lg="6" :xl="6" v-for="item in datalist" :key="item.name">
                            <el-card class="plugin-card" shadow="hover">
                                <!-- 插件封面 -->
                                <div class="plugin-cover">
                                    <el-image :src="item.preview" fit="cover" class="cover-image">
                                        <template #error>
                                            <div class="image-slot">
                                                <XbIcons icon="Picture" :size="48" />
                                            </div>
                                        </template>
                                    </el-image>
                                    <div class="plugin-mask">
                                        <div class="mask-item">
                                            <XbIcons icon="User" :size="16" />
                                            <span>{{ item.author }}</span>
                                        </div>
                                        <div class="mask-item">
                                            <XbIcons icon="Stopwatch" :size="16" />
                                            <span>{{ item.name }}</span>
                                        </div>
                                        <div class="mask-item">
                                            <XbIcons icon="PriceTag" :size="16" />
                                            <span>{{ item.version }}</span>
                                        </div>
                                    </div>
                                    <div class="plugin-type-badge"
                                        :class="item.type === 'app' ? 'type-app' : 'type-plugin'">
                                        <span>{{ item.type === 'app' ? '应用' : '插件' }}</span>
                                    </div>
                                    <div class="plugin-home-mask" v-if="item?.home_url">
                                        <el-tooltip effect="dark" content="打开主页">
                                            <div class="home" @click="hanldHome(item)">
                                                <XbIcons icon="Monitor" :size="16" />
                                            </div>
                                        </el-tooltip>
                                    </div>
                                </div>

                                <!-- 插件信息 -->
                                <div class="plugin-info">
                                    <h3 class="plugin-title" :title="item.title">
                                        {{ item.title }}
                                    </h3>
                                    <p class="plugin-desc" :title="item.desc">
                                        {{ item.desc || '暂无描述' }}
                                    </p>
                                </div>

                                <!-- 操作按钮 -->
                                <div class="plugin-actions">
                                    <!-- 未安装状态 -->
                                    <el-button type="primary" size="small" v-if="item.install === '10'"
                                        @click="hanldInstall(item)" class="action-btn">
                                        <XbIcons icon="Plus" />
                                        <span>安装</span>
                                    </el-button>

                                    <!-- 已安装状态 -->
                                    <template v-if="item.install === '20'">
                                        <!-- 启用/禁用 -->
                                        <el-button type="success" size="small" :disabled="item.is_system === '20'"
                                            v-if="item.state === '10'" @click="setPluginState(item, '20')"
                                            class="action-btn">
                                            <XbIcons icon="Check" />
                                            <span>启用</span>
                                        </el-button>
                                        <el-button type="warning" size="small" :disabled="item.is_system === '20'"
                                            v-if="item.state === '20'" @click="setPluginState(item, '10')"
                                            class="action-btn">
                                            <XbIcons icon="Close" />
                                            <span>禁用</span>
                                        </el-button>

                                        <!-- 配置 -->
                                        <el-button type="info" size="small" v-if="item.has_config === '20'"
                                            @click="hanldConfig(item)" class="action-btn">
                                            <XbIcons icon="Setting" />
                                            <span>配置</span>
                                        </el-button>

                                        <!-- 卸载 -->
                                        <el-button type="danger" size="small" :disabled="item.is_system === '20'"
                                            @click="hanldUninstall(item)" class="action-btn">
                                            <XbIcons icon="Delete" />
                                            <span>卸载</span>
                                        </el-button>
                                    </template>

                                    <!-- 删除（仅未安装的非系统插件） -->
                                    <el-button type="danger" size="small"
                                        v-if="item.is_system === '10' && item.install === '10'" @click="hanldDel(item)"
                                        class="action-btn">
                                        <XbIcons icon="Delete" />
                                        <span>删除</span>
                                    </el-button>

                                    <!-- 说明 -->
                                    <el-button type="primary" size="small" plain v-if="item.has_readme === '20'"
                                        @click="hanldReadme(item)" class="action-btn">
                                        <XbIcons icon="Document" />
                                        <span>说明</span>
                                    </el-button>
                                </div>
                            </el-card>
                        </el-col>
                    </el-row>
                </div>
                <div class="empty" v-else>
                    <div class="loading" v-if="loadingObj.state" v-loading="loadingObj.state"
                        element-loading-text="正在加载中..."></div>
                    <el-empty description="当前没有更多插件" v-else />
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
                active: 'installed',
                list: [
                    {
                        icon: 'CompassOutlined',
                        name: 'installed',
                        label: '已安装插件',
                    },
                    {
                        icon: 'CodeSandboxOutlined',
                        name: 'plugins',
                        label: '未安装插件',
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
        this.tabs.active = this.$route.query.type || 'installed'
        this.getPlugins()
    },
    methods: {
        // 处理刷新缓存
        hanldeRefresh() {
            this.$xbcode.useConfirm(`是否确认要刷新插件缓存？`, 'warning').then(() => {
                const url = 'app/xbCode/admin/Plugins/refresh'
                this.$xbcode.$http.get(url).then((res) => {
                    this.getPlugins()
                }).catch((err) => {
                    this.$xbcode.useNotify(err.response?.data?.msg ?? err.message, 'error')
                });
            })
        },
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
            this.$xbcode.useConfirm('安装完成后，请重启服务，部分配置方可生效', 'success', `是否安装「${item.title}」插件？`).then(() => {
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
            this.$xbcode.useConfirm(`是否确定卸载「${item.title}」插件？`, 'error').then(() => {
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
            this.$xbcode.useConfirm(`是否确认要删除插件「${item.title}」？请谨慎该操作`, 'error').then(() => {
                this.$xbcode.useDelete('app/xbCode/admin/Plugins/del', {
                    name: item.name,
                }).then((res) => {
                    this.getPlugins()
                }).catch((err) => {
                    this.$xbcode.useNotify(err.message, 'error')
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
                title: `${item.title} — 面板配置`,
                customStyle: {
                    width: '45vw',
                    height: '60vh',
                },
            });
        },
        // 插件说明
        hanldReadme(item) {
            this.$xbcode.useRemoteModal('app/xbCode/admin/Plugins/readme', {
                name: item.name,
            }, {
                onRefresh: () => {
                    // 刷新数据
                    this.getPlugins();
                },
            }, {
                title: `${item.title}-插件说明`,
                customStyle: {
                    width: '60vw',
                    height: '75vh',
                },
            });
        },
        // 打开应用主页
        hanldHome(item) {
            var homeUrl = item?.home_url || ''
            if (!homeUrl) {
                return;
            }
            if (!homeUrl.includes('http')) {
                homeUrl = `${window.location.origin}${homeUrl}`
            }
            window.open(homeUrl)
        },
        // 设置插件状态
        setPluginState(item, state) {
            const message = state === '20' ? '启用' : '禁用';
            const type = state === '20' ? 'success' : 'warning';
            this.$xbcode.useConfirm(`是否确认要${message}「${item.title}」？`, type).then(() => {
                const url = 'app/xbCode/admin/Plugins/state'
                const data = {
                    name: item.name,
                    value: state,
                }
                this.$xbcode.usePut(url, data).then((res) => {
                    this.getPlugins()
                }).catch((err) => {
                    this.$xbcode.useNotify(err.message, 'error')
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

        .xb-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #e8e8e8;
            background-color: #fff;

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
                        background-color: #f6faff;
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
                overflow-x: hidden;
                padding: 10px 0;
                box-sizing: border-box;

                .plugin-grid {
                    .el-col {
                        margin-bottom: 15px;

                        .plugin-card {
                            height: 100%;
                            transition: all 0.3s ease;
                            border-radius: 4px;
                            overflow: hidden;

                            &:hover {
                                transform: translateY(-4px);
                                box-shadow: 0 4px 16px rgba(0, 0, 0, 0.12);
                            }

                            :deep(.el-card__body) {
                                padding: 0;
                                display: flex;
                                flex-direction: column;
                                height: 100%;
                            }

                            .plugin-cover {
                                width: 100%;
                                background-color: #f0f2f5;
                                overflow: hidden;
                                position: relative;

                                .cover-image {
                                    width: 100%;
                                    height: 100%;
                                    display: block;
                                }

                                .image-slot {
                                    width: 100%;
                                    height: 100%;
                                    display: flex;
                                    justify-content: center;
                                    align-items: center;
                                    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                                    color: #fff;
                                    font-size: 48px;
                                }

                                .plugin-mask {
                                    position: absolute;
                                    bottom: 0;
                                    left: 0;
                                    right: 0;
                                    width: 100%;
                                    background: rgba(0, 0, 0, 0.5);
                                    display: flex;
                                    align-items: center;
                                    justify-content: space-between;
                                    color: #f1f1f1;
                                    font-size: 14px;

                                    .mask-item {
                                        display: flex;
                                        align-items: center;
                                        gap: 4px;
                                        padding: 4px 8px;

                                        span {
                                            line-height: 30px;
                                        }
                                    }
                                }

                                .plugin-type-badge {
                                    position: absolute;
                                    top: 10px;
                                    left: 10px;
                                    padding: 4px 12px;
                                    border-radius: 4px;
                                    font-size: 12px;
                                    font-weight: 500;
                                    backdrop-filter: blur(4px);
                                    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.15);

                                    &.type-app {
                                        background: rgba(103, 194, 58, 0.9);
                                        color: #fff;
                                    }

                                    &.type-plugin {
                                        background: rgba(64, 158, 255, 0.9);
                                        color: #fff;
                                    }

                                    span {
                                        line-height: 1;
                                    }
                                }

                                .plugin-home-mask {
                                    position: absolute;
                                    top: 0;
                                    left: 0;
                                    right: 0;
                                    width: 100%;
                                    display: flex;
                                    align-items: center;
                                    justify-content: flex-end;
                                    color: #f1f1f1;
                                    font-size: 14px;

                                    .home {
                                        display: flex;
                                        align-items: center;
                                        gap: 4px;
                                        padding: 6px 10px;
                                        cursor: pointer;
                                        color: #fff;
                                        background-color: #626aef;
                                    }
                                }
                            }

                            .plugin-info {
                                padding: 15px 0;
                                display: flex;
                                flex-direction: column;
                                gap: 12px;

                                .plugin-title {
                                    margin: 0;
                                    font-size: 18px;
                                    font-weight: 600;
                                    color: #303133;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    white-space: nowrap;
                                    line-height: 1.4;
                                }

                                .plugin-desc {
                                    margin: 0;
                                    font-size: 14px;
                                    color: #606266;
                                    line-height: 1.6;
                                    overflow: hidden;
                                    text-overflow: ellipsis;
                                    display: -webkit-box;
                                    -webkit-line-clamp: 2;
                                    line-clamp: 2;
                                    -webkit-box-orient: vertical;
                                }
                            }

                            .plugin-actions {
                                padding-top: 15px;
                                border-top: 1px solid #e4e7ed;
                                display: flex;
                                flex-wrap: wrap;
                                gap: 8px;
                                align-items: center;

                                .action-btn {
                                    display: inline-flex;
                                    align-items: center;
                                    gap: 4px;
                                    padding: 8px 12px;
                                    font-size: 13px;

                                    span {
                                        line-height: 1;
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }

        // 移动端小屏: < 576px, 封面高度 8rem
        @media screen and (max-width: 575px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 8rem;
                        }
                    }
                }
            }
        }

        // 移动端大屏: 576px - 767px, 封面高度 9rem
        @media screen and (min-width: 576px) and (max-width: 767px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 9rem;
                        }
                    }
                }
            }
        }

        // 平板竖屏: 768px - 991px, 封面高度 10rem
        @media screen and (min-width: 768px) and (max-width: 991px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 10rem;
                        }
                    }
                }
            }
        }

        // 平板横屏/小桌面: 992px - 1199px, 封面高度 11rem
        @media screen and (min-width: 992px) and (max-width: 1199px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 11rem;
                        }
                    }
                }
            }
        }

        // 中等桌面: 1200px - 1399px, 封面高度 12rem
        @media screen and (min-width: 1200px) and (max-width: 1399px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 12rem;
                        }
                    }
                }
            }
        }

        // 大屏桌面: 1400px - 1599px, 封面高度 13rem
        @media screen and (min-width: 1400px) and (max-width: 1599px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 13rem;
                        }

                        .plugin-info {
                            padding: 15px 0;

                            .plugin-title {
                                font-size: 16px;
                            }

                            .plugin-desc {
                                font-size: 13px;
                            }
                        }
                    }
                }
            }
        }

        // 超大屏: 1600px - 1919px, 封面高度 14rem
        @media screen and (min-width: 1600px) and (max-width: 1919px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 14rem;
                        }

                        .plugin-info {
                            padding: 14px;

                            .plugin-title {
                                font-size: 17px;
                            }

                            .plugin-desc {
                                font-size: 14px;
                            }
                        }
                    }
                }
            }
        }

        // Full HD: 1920px - 2559px, 封面高度 16rem
        @media screen and (min-width: 1920px) and (max-width: 2559px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 16rem;
                        }

                        .plugin-info {
                            padding: 14px;

                            .plugin-title {
                                font-size: 18px;
                            }

                            .plugin-desc {
                                font-size: 14px;
                            }
                        }
                    }
                }
            }
        }

        // 2K屏及以上: >= 2560px, 封面高度 18rem
        @media screen and (min-width: 2560px) {
            .xb-plugin-content {
                .plugin-grid {
                    .plugin-card {
                        .plugin-cover {
                            height: 18rem;
                        }

                        .plugin-info {
                            padding: 16px;

                            .plugin-title {
                                font-size: 18px;
                            }

                            .plugin-desc {
                                font-size: 15px;
                            }
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
