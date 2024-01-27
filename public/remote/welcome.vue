<template>
    <div class="pages-container">
        <div class="tools-container">
            <div class="screen">
                <el-input placeholder="请输入内容" v-model="input" clearable></el-input>
            </div>
            <div class="tools">
            </div>
        </div>
        <div class="xb-project">
            <div class="project-container">
                <div class="project-title">
                    <div class="title">项目管理</div>
                    <div class="action">
                        <el-button type="warning" @click="openWin('/Projects/desc')">项目独立</el-button>
                        <el-button type="primary" @click="openWin('/Projects/add')">新建项目</el-button>
                    </div>
                </div>
                <div class="project-content">
                    <div class="project-grid" v-if="projects.list.length > 0">
                        <div class="item" v-for="(item, index) in projects.list" :key="index">
                            <div class="info" @click="openUrl(item?.id)">
                                <div class="icon">
                                    <el-image style="width: 100%; height: 200px;border-radius: 3px;" :src="item?.logo" />
                                </div>
                                <div class="title">
                                    {{ item.title }}
                                </div>
                            </div>
                            <div class="btns">
                                <el-button type="primary" size="small" @click="openWin('/Projects/edit', { id: item?.id })">
                                    编辑
                                </el-button>
                                <el-button type="danger" size="small" @click="openDel(item?.id)">
                                    删除
                                </el-button>
                            </div>
                        </div>
                    </div>
                    <div class="empty-container" v-else>
                        <el-empty description="当前没有更多的项目" />
                    </div>
                </div>
                <!-- 分页 -->
                <div class="xb-paginate">
                    <el-pagination layout="prev, pager, next" v-model:current-page="projects.paginate.page"
                        :total="projects.paginate.total" :page-size="projects.paginate.limit"
                        @current-change="hanldChangePage" small background />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            projects: {
                list: [],
                paginate: {
                    page: 1,
                    limit: 20,
                    total: 1000,
                },
            },
        }
    },
    async mounted() {
        await this.getList()
    },
    methods: {
        openDel(id) {
            this.$useConfirm('是否确定删除该项目？', '温馨提示', 'error').then(() => {
                const params = {
                    id
                }
                this.$http.useDelete('/admin/Projects/del', params).then((res) => {
                    this.$useNotifySuccess(res?.msg ?? '删除成功')
                })
            })
        },
        getList() {
            this.$http.useGet('/admin/Projects/index').then((res) => {
                this.projects.list = res?.data?.data ?? []
                this.projects.paginate.page = res?.data?.current_page ?? []
            })
        },
        openUrl(id) {
            const params = {
                id
            }
            this.$http.useGet('/admin/Projects/login', params).then((res) => {
                if (!res?.data?.url) {
                    this.$useNotifyError('获取地址失败')
                    return;
                }
                window.open(res?.data?.url)
            })
        },
        openWin(url, params = {}) {
            this.$routerApp.push({
                path: url,
                query: {
                    ...params,
                    isBack: '1'
                }
            })
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    height: 100%;
    display: flex;
    flex-direction: column;

    .tools-container {
        height: 60px;
        display: flex;
        align-items: center;
        background-color: #fff;
        justify-content: space-between;
        padding: 0 15px;

        .screen {}

        .tools {}
    }

    .xb-project {
        padding-top: 15px;
        flex: 1;
        overflow: hidden;

        .project-container {
            height: 100%;
            display: flex;
            flex-direction: column;

            .project-title {
                height: 60px;
                display: flex;
                justify-content: space-between;
                align-items: center;
                padding: 0 15px;
                background-color: #fff;

                .title {
                    font-size: 18px;
                    font-weight: 700;
                }

                .action {}
            }

            .project-content {
                flex: 1;
                overflow-y: auto;
                overflow-x: hidden;
                padding-top: 15px;

                .project-grid {
                    display: grid;
                    grid-template-columns: repeat(20, minmax(0, 1fr));
                    gap: 20px;

                    .item {
                        grid-column: span 4/span 4;
                        background-color: #fff;
                        display: flex;
                        flex-direction: column;
                        align-items: center;
                        padding: 15px;
                        border-radius: 5px;
                        overflow: hidden;
                        cursor: pointer;
                        transition: all .3s ease;

                        .icon {
                            width: 100%;
                        }

                        .desc {
                            color: #666;
                            font-size: 12px;
                            min-height: 34px;
                            /* 固定宽度 */
                            width: 100%;
                            /* 将溢出的部分隐藏 */
                            overflow: hidden;
                            /* 把盒子作为弹性盒子显示 */
                            display: -webkit-box;
                            /* 让子元素垂直排列 */
                            -webkit-box-orient: vertical;
                            /* 设置元素显示的行数 */
                            -webkit-line-clamp: 2;
                        }

                        .title {
                            color: rgba(0, 0, 0, .85);
                            margin: 10px 0px;
                            font-weight: bold;
                        }

                        .btns {
                            margin: 10px 0px;
                        }
                    }

                    .item:hover {
                        -webkit-transform: translateY(-4px) scale(1.02);
                        -moz-transform: translateY(-4px) scale(1.02);
                        -ms-transform: translateY(-4px) scale(1.02);
                        -o-transform: translateY(-4px) scale(1.02);
                        transform: translateY(-4px) scale(1.02);
                        z-index: 999;
                        border-radius: 6px;
                    }
                }
                .empty-container{
                    height: 100%;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    background-color: #fff;
                }
            }

            .xb-paginate {
                height: 50px;
                display: flex;
                justify-content: flex-end;
            }
        }
    }
}
</style>