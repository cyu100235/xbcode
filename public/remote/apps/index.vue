<template>
    <div class="app-container">
        <div class="screen-container">
            <div class="category">
                <div class="item" :class="{ 'active': category.active === '' }" @click="hanldCategory('')">
                    全部
                </div>
                <div class="item" :class="{ 'active': category.active === item.id }" v-for="(item, index) in category.list"
                    :key="index" @click="hanldCategory(item.id)">
                    {{ item.title }}
                </div>
            </div>
            <!-- 搜索表单 -->
            <el-form :inline="true" class="search">
                <el-form-item>
                    <el-select placeholder="请选择应用类型" v-model="appsType.active">
                        <el-option label="全部" value=""></el-option>
                        <el-option :label="item.label" :value="item.value" v-for="(item, index) in appsType.list"
                            :key="index" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-select placeholder="请选择安装状态" v-model="installStatus.active">
                        <el-option label="全部" value=""></el-option>
                        <el-option :label="item.label" :value="item.value" v-for="(item, index) in installStatus.list"
                            :key="index" />
                    </el-select>
                </el-form-item>
                <el-form-item>
                    <el-input placeholder="请输入应用名称" v-model="keyword"></el-input>
                </el-form-item>
                <el-form-item>
                    <el-button type="primary" @click="search">
                        <template #icon>
                            <AppIcons icon="Search" :size="15" />
                        </template>
                        搜索
                    </el-button>
                </el-form-item>
            </el-form>
        </div>
        <div class="content-container">
            <div class="apps-content" v-if="datalist.length">
                <div class="content">
                    <div class="item" v-for="( item, index ) in  datalist " :key="index" @click="detail(item?.name)"
                        :title="item?.title">
                        <div class="logo-container">
                            <el-image class="logo" :src="item?.logo" />
                            <!-- <div class="platform">
                                <el-tag size="mini" type="success">微信小程序</el-tag>
                            </div> -->
                        </div>
                        <div class="app-title">{{ item?.title }}</div>
                        <div class="info">
                            <div class="download">
                                <AppIcons color="#909399" icon="Download" :size="14" />
                                <span class="text">{{ item?.down }}</span>
                            </div>
                            <div class="view">
                                <AppIcons color="#909399" icon="View" :size="14" />
                                <span class="text">{{ item?.view }}</span>
                            </div>
                            <div class="action">
                                <!-- 可购买 -->
                                <div class="money" v-if="!item?.install && !item?.update">
                                    ￥{{ item?.money }}
                                </div>
                                <!-- 有更新 -->
                                <el-button size="small" type="success" v-else-if="item.install && item?.update">
                                    有更新
                                </el-button>
                                <!-- 已安装 -->
                                <el-button size="small" type="info" v-else="item?.install && !item?.update">
                                    已安装
                                </el-button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="empty-content" v-else>
                <el-empty description="当前没有更多的应用" />
            </div>
        </div>
    </div>
</template>
  
<script>
export default {
    data() {
        return {
            paginate: {
                page: 1,
                limit: 20,
            },
            category: {
                active: '',
                list: []
            },
            installStatus: {
                active: '',
                list: []
            },
            appsType: {
                active: '',
                list: []
            },
            keyword: '',
            datalist: [],
        }
    },
    async mounted() {
        // 获取分类
        await this.getCategory()
        // 获取应用类型
        await this.getAppsType()
        // 获取安装状态
        await this.getInstallStatus()
        // 获取应用列表
        await this.getList()
    },
    methods: {
        detail(app_name) {
            this.$useRemote('remote/apps/detail', { app_name }, {
                title: '应用详情',
                customStyle: {
                    width: '70%',
                    height: '90vh',
                },
                beforeClose: (value, state, done) => {
                    this.paginate.page = 1
                    this.getList()
                    done()
                }
            })
        },
        // 点击分类
        hanldCategory(id) {
            this.category.active = id
            this.getList()
        },
        // 获取安装状态
        async getInstallStatus() {
            return await this.$http.useGet('admin/Apps/installStatus').then((res) => {
                this.installStatus.list = res?.data || []
            })
        },
        // 获取应用类型
        async getAppsType() {
            return await this.$http.useGet('admin/Apps/appType').then((res) => {
                this.appsType.list = res?.data || []
            })
        },
        // 获取应用分类
        async getCategory() {
            return await this.$http.useGet('admin/Apps/category').then((res) => {
                this.category.list = res?.data || []
            })
        },
        // 搜索
        search() {
            const loading = this.$useLoading('正在搜索...')
            this.getList().finally(() => {
                loading.close()
            })
        },
        // 获取应用列表
        async getList() {
            const params = {
                page: this.paginate.page,
                limit: this.paginate.limit,
                keyword: this.keyword,
                category: this.category.active,
                platform: this.appsType.active,
                install: this.installStatus.active,
            }
            return await this.$http.useGet('admin/Apps/index', params).then((res) => {
                this.datalist = res?.data?.data || []
                this.paginate.page = res?.data?.current_page || 1
                this.paginate.limit = res?.data?.per_page || 20
            })
        }
    },
};
</script>
  
<style lang="scss" scoped>
.app-container {
    height: 100%;

    .screen-container {
        display: flex;
        align-items: center;
        height: 60px;
        padding: 0 20px;
        background-color: #fff;

        .category {
            flex: 1;
            display: flex;

            .item {
                font-size: 14px;
                cursor: pointer;
                padding: 0 5px;

                &:hover {
                    color: var(--el-menu-active-color);
                }
            }

            .active {
                color: var(--el-menu-active-color);
            }
        }

        .search {
            .el-form-item {
                margin-bottom: 0 !important;
            }
        }
    }

    .content-container {
        height: calc(100% - 60px);
        background-color: #fff;
        margin-top: 15px;

        .apps-content {
            height: 100%;
            padding: 20px 30px;
            overflow-y: auto;
            overflow-x: hidden;
            box-sizing: border-box;

            .content {
                display: grid;
                grid-template-columns: repeat(24, 1fr);
                gap: 30px;

                .item {
                    grid-column: span 4;
                    box-shadow: var(--el-box-shadow-light);
                    transition: all .3s ease;
                    cursor: pointer;
                    border-radius: 5px;
                    overflow: hidden;

                    &:hover {
                        -webkit-transform: translateY(-2px) scale(1.02);
                        -moz-transform: translateY(-2px) scale(1.02);
                        -ms-transform: translateY(-2px) scale(1.02);
                        -o-transform: translateY(-2px) scale(1.02);
                        transform: translateY(-2px) scale(1.02);
                        -webkit-box-shadow: 0 14px 24px rgba(0, 0, 0, .2);
                        box-shadow: 0 14px 24px #0003;
                        z-index: 999;
                        border-radius: 6px
                    }

                    .logo-container {
                        position: relative;

                        .logo {
                            display: block;
                        }

                        .platform {
                            position: absolute;
                            left: 0;
                            top: 0;
                        }
                    }

                    .app-title {
                        padding: 10px 10px 0 10px;
                        overflow: hidden;
                        text-overflow: ellipsis;
                        white-space: nowrap;
                        font-size: 14px;
                        color: #555;
                    }

                    .info {
                        display: flex;
                        justify-content: space-between;
                        align-items: center;
                        padding: 15px;

                        .download {
                            color: #909399;
                            font-size: 14px;
                            display: flex;
                            align-items: center;

                            .text {
                                padding-left: 5px;
                            }
                        }

                        .view {
                            color: #909399;
                            font-size: 14px;
                            display: flex;
                            align-items: center;

                            .text {
                                padding-left: 5px;
                            }
                        }

                        .action {
                            .money {
                                color: red;
                                font-size: 14px;
                            }
                        }
                    }
                }
            }
        }

        .empty-content {
            height: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }
    }
}
</style>