<template>
    <div class="pages-container" v-if="user">
        <div class="main-container">
            <div class="user-container">
                <div class="user">
                    <el-avatar class="avatar" />
                    <div class="info">
                        <div class="nickname">楚羽幽</div>
                        <div class="balance">￥100.00</div>
                    </div>
                </div>
                <div class="recharge">
                    <el-button type="primary" @click="openRecharge()">充值</el-button>
                </div>
            </div>
            <div class="bill-container">
                <div class="title">收支明细</div>
                <div class="content">
                    <el-table :data="bill" height="450" border>
                        <el-table-column prop="trade_date" label="交易时间" width="160" />
                        <el-table-column prop="trade_type" label="交易类型" width="90">
                            <template #default="scope">
                                <el-tag type="warning" v-if="scope?.row?.bill_type === '10'">
                                    {{ scope?.row?.trade_type }}
                                </el-tag>
                                <el-tag type="success" v-if="scope?.row?.bill_type === '20'">
                                    {{ scope?.row?.trade_type }}
                                </el-tag>
                                <el-tag type="info" v-if="scope?.row?.bill_type === '30'">
                                    {{ scope?.row?.trade_type }}
                                </el-tag>
                            </template>
                        </el-table-column>
                        <el-table-column prop="money" label="交易金额" width="120">
                            <template #default="scope">
                                <div class="money">
                                    ￥{{ scope?.row?.money }}
                                </div>
                            </template>
                        </el-table-column>
                        <el-table-column prop="remarks" label="收支明目" />
                    </el-table>
                    <div class="paginate">
                        <el-pagination v-bind="paginateProps" v-model="paginate.page" @current-change="hanldChangePage" />
                    </div>
                </div>
            </div>
        </div>
        <div class="right-container">
            <div class="title">官方公告</div>
            <div class="content" v-if="notice.length">
                <div class="item" v-for="(item, index) in notice" :key="index" @click="openUrl(item?.url)">
                    <div class="label">文章名称文章名称文章名称文章名称文章名称文章名称文章名称文章名称文章名称</div>
                    <div class="value">2024-01-08</div>
                </div>
            </div>
            <div class="empty-container" v-else>
                <el-empty description="暂无公告" />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            user: null,
            paginate: {
                page: 1,
                limit: 20
            },
            paginateProps: {
                background: true,
                layout: 'prev, pager, next',
                total: 0,
                pageSize: 10,
            },
            bill: [],
            notice: [],
        }
    },
    async mounted() {
        await this.getUser().then(() => {
            this.getBillList()
            this.getNoticeList()
        })
    },
    methods: {
        // 打开充值
        openRecharge() {
            this.$useRemote('remote/cloud/recharge', {}, {
                title: '在线充值',
                customStyle: {
                    width: '500px',
                    height: '550px',
                },
                beforeClose: (value, state, done) => {
                    this.getUser()
                    done()
                }
            })
        },
        // 打开链接
        openUrl(url) {
            window.open(url)
        },
        // 分页改变
        hanldChangePage(value) {
            this.paginate.page = value
            this.getBillList()
        },
        // 获取公告列表
        getNoticeList() {
            this.$http.useGet('admin/Cloud/notice').then((res) => {
                this.notice = res?.data ?? []
            })
        },
        // 获取账单
        getBillList() {
            const params = {
                page: this.paginate.page,
                limit: this.paginate.limit,
            }
            this.$http.useGet('admin/Cloud/bill', params).then((res) => {
                this.bill = res?.data?.data ?? []
                this.paginateProps.total = res?.data?.total ?? 0
                this.paginateProps.pageSize = res?.data?.per_page ?? 10
                this.paginate.page = res?.data?.current_page ?? 1
                this.paginate.limit = res?.data?.per_page ?? 10
            })
        },
        // 获取用户信息
        async getUser() {
            return await this.$http.useGet('admin/Cloud/user').then((res) => {
                if (res?.data?.code === 12000) {
                    this.$emit("update:closeWin");
                    this.$emit("update:openWin", 'remote/cloud/login');
                    return
                }
                this.user = res?.data ?? {}
                return res
            }).catch((res) => {
                if (res?.code === 12000) {
                    this.$emit("update:closeWin");
                    this.$emit("update:openWin", 'remote/cloud/login');
                }
            })
        },
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    display: flex;
    padding: 0 20px;

    .main-container {
        flex: 1;
        margin-right: 20px;

        .user-container {
            background-color: #f5f5f5;
            border-radius: 10px;
            display: flex;
            justify-content: space-between;
            padding: 10px 15px;

            .user {
                display: flex;

                .avatar {
                    width: 40px;
                    height: 40px;
                }

                .info {
                    padding-left: 6px;
                    font-size: 14px;
                }
            }

            .recharge {
                display: flex;
                align-items: center;
            }
        }

        .bill-container {
            margin-top: 20px;

            .title {
                font-size: 16px;
                font-weight: 700;
            }

            .content {
                margin-top: 10px;
            }

            .paginate {
                margin-top: 10px;
            }
        }
    }

    .right-container {
        width: 350px;
        min-width: 350px;
        flex-shrink: 0;
        border: 1px solid #f1f1f1;

        .title {
            font-size: 16px;
            font-weight: 700;
            border-bottom: 1px solid #f1f1f1;
            padding: 10px;
        }

        .content {
            padding-bottom: 15px;

            .item {
                display: flex;
                justify-content: space-between;
                padding: 12px 15px 0 15px;
                cursor: pointer;

                &:hover {
                    color: var(--el-color-primary);
                }

                .label {
                    overflow: hidden;
                    text-overflow: ellipsis;
                    white-space: nowrap;
                    padding-right: 20px;
                }

                .value {
                    min-width: 80px;
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