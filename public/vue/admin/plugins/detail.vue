<template>
    <div class="detail-container" v-if="detail">
        <div class="left">
            <div class="apps-info">
                <div class="banner">
                    <el-carousel indicator-position="outside" height="240px">
                        <el-carousel-item>
                            <el-image class="swiper-image" :src="detail?.logo" />
                        </el-carousel-item>
                    </el-carousel>
                </div>
                <div class="detail">
                    <div class="items title">
                        <div class="text">{{ detail?.title }}</div>
                    </div>
                    <div class="items">
                        <div class="label">发布时间：</div>
                        <div class="value">{{ detail?.create_at }}</div>
                    </div>
                    <div class="items">
                        <div class="label">所属分类：</div>
                        <div class="value">
                            <el-tag>{{ detail?.cate_title }}</el-tag>
                        </div>
                    </div>
                    <div class="items">
                        <div class="label">插件单价：</div>
                        <div class="value price">
                            {{ parseFloat(detail?.price) ? `￥${detail?.price}` : '免费' }}
                        </div>
                    </div>
                    <div class="items">
                        <div class="label">总售价格：</div>
                        <div class="value price">
                            {{ parseFloat(detail?.total) ? `￥${detail?.total}` : '免费' }}
                        </div>
                    </div>
                    <div class="items">
                        <div class="label">最新版本：</div>
                        <div class="value">{{ detail?.version }}</div>
                    </div>
                    <div class="items">
                        <div class="label">下载次数：</div>
                        <div class="value">{{ detail?.down }}次</div>
                    </div>
                    <div class="items">
                        <el-row class="apps-button">
                            <el-button type="warning" @click="hanldBuy" v-if="detail?.plugin_state === '10'">
                                <template #icon>
                                    <AppIcons icon="Lightning" />
                                </template>
                                购买
                            </el-button>
                            <el-button type="primary" @click="hanldInstall" v-if="detail?.plugin_state === '20'">
                                <template #icon>
                                    <AppIcons icon="Lightning" />
                                </template>
                                安装
                            </el-button>
                        </el-row>
                    </div>
                </div>
            </div>
            <div class="alert">
                <el-alert :closable="false" title="购买应用和当前域名、IP、云账户关联，不支持更换！" type="error">
                </el-alert>
            </div>
            <div class="content-box">
                <div class="title">
                    插件介绍
                </div>
                <div class="content" v-html="detail?.content">
                </div>
            </div>
        </div>
        <div class="right">
            <div class="apps-card">
                <div class="title">
                    该插件旗下依赖
                </div>
                <!-- 
                    TODO:完成依赖项插件展示、完成依赖项模态框跳转
                 -->
                <div class="content">
                    <div class="depend-list">
                        <div class="item" v-for="(item,index) in detail?.depend_list" :key=index>
                            <div class="info">
                                <el-image class="plugin-logo" :src="item.logo"></el-image>
                                <div class="plugin-title">{{ item.title }}</div>
                            </div>
                            <div class="plugin-price">
                                {{ parseFloat(item.price) ? item.price : '免费' }}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="apps-card">
                <div class="title">
                    最近更新
                </div>
                <div class="content">
                    <el-timeline v-if="detail?.update_log?.length">
                        <el-timeline-item icon="MoreFilled" type="primary" color="#0bbd87" v-for="(item, index) in detail?.update_log" :key="index">
                            <div class="version-info">
                                <div class="version-title">{{ item?.version }}</div>
                                <div class="version-time">{{ item?.create_at }}</div>
                            </div>
                            <div class="version-desc">{{ item?.content }}</div>
                        </el-timeline-item>
                    </el-timeline>
                </div>
            </div>
        </div>
    </div>
</template>
  
<script>
export default {
    props: {
        name: String,
        version: String | Number,
    },
    data() {
        return {
            loading: null,
            detail: null
        };
    },
    created() {
        this.getDetail()
    },
    methods: {
        // 执行步骤
        startInstall(params) {
            if (this.loading) {
                this.loading?.close()
            }
            const url = `admin/Plugins/install`
            this.$http.usePost(url, params).then((res) => {
                if (res?.data?.query) {
                    params = {
                        ...params,
                        ...res?.data?.query
                    }
                    this.loading = this.$useLoading(res?.msg || '正在安装插件', '温馨提示')
                    setTimeout(() => {
                        this.startInstall(params)
                    }, 800);
                } else {
                    setTimeout(() => {
                        window.location.reload()
                    }, 1500);
                    this.$useNotify(res?.msg || '安装成功', 'success', '温馨提示')
                    this.$emit("update:closeWin");
                }
            }).catch((err) => {
                console.log(err);
                this.$useNotify(res?.msg || '安装错误', 'success', '温馨提示')
            })
        },
        // 安装插件
        hanldInstall() {
            const params = {
                name: this?.detail?.name,
                version: this?.detail?.version
            }
            this.startInstall(params)
        },
        // 购买插件
        hanldBuy() {
            const params = {
                name: this?.detail?.name,
                version: this?.detail?.version
            }
            this.$http.useGet('admin/Plugins/order', params).then((res) => {
                this.$emit("update:closeWin");
                // 统一下订单
                this.$useRemote('/vue/admin/plugins/order', params, {
                    title: '购买插件',
                    customStyle: {
                        width: '45%',
                        height: '75vh',
                    },
                })
            }).catch((err) => {
                if (err?.code === 11000) {
                    this.$useNotify(err?.msg || "异常错误", 'error', '温馨提示')
                }
                this.$emit("update:closeWin");
            })
        },
        // 获取插件详情
        getDetail() {
            const params = {
                name: this.name,
                version: this.version
            }
            this.$http.useGet('admin/Plugins/detail', params).then(res => {
                console.log(res?.data);
                this.detail = res?.data ?? null
            }).catch(err => {
                this.$emit("update:closeWin");
                this.$useNotify(err?.msg ?? '异常错误', 'error', '温馨提示')
            })
        }
    },
};
</script>
  
<style lang="scss" scoped>
.detail-container {
    height: 100%;
    display: flex;
    flex-direction: row;
    overflow-y:auto;

    .left {
        flex: 1;
        display: flex;
        flex-direction: column;

        .apps-info {
            display: flex;
            flex-direction: row;
            padding: 10px;

            .banner {
                width: 50%;

                .swiper-image {
                    max-width: 280px;
                    max-height: 280px;
                    min-width: 90px;
                    min-height: 90px;
                    border-radius: 4px;
                }
            }

            .detail {
                width: 50%;
                padding-left: 10px;

                .items {
                    display: flex;
                    flex-direction: row;
                    align-items: center;
                    margin-bottom: 10px;

                    .label {
                        color: #909399;
                    }

                    .value {
                        color: #606266;
                    }

                    .price {
                        color: #f56c6c;
                        font-size: 16px;
                        font-weight: bolder;
                    }
                }

                .apps-button {
                    margin-top: 5px;
                }

                .title {
                    .text {
                        font-weight: bold;
                    }
                }
            }
        }

        .alert {
            border-top: 1px solid #F5F7FA;
            padding: 10px;
        }
        .content-box{
            display: flex;
            flex-direction: column;
            padding: 0 10px 10px 10px;
            .title{
                font-size: 16px;
                font-weight: bolder;
                padding: 8px 0;
                border-bottom: 1px solid #F5F7FA;
            }
            .content{
                overflow: hidden;
                white-space: pre-line;
                padding-top: 10px;
                p{
                    line-height: 20px;
                }
                img{
                    max-width: 100%;
                    object-fit: cover;
                }
            }
        }
    }

    .right {
        width: 250px;
        flex-shrink: 0;
        padding: 15px;
        flex-shrink: 0;

        .apps-card {
            .title {
                font-size: 14px;
                font-weight: bolder;
                padding: 10px 0;
                color: #333;
            }

            .content {
                padding: 15px 0;
                .depend-list{
                    display: flex;
                    flex-direction: column;
                    row-gap: 10px;
                    .item{
                        display: flex;
                        justify-content: space-between;
                        .info{
                            display: flex;
                            .plugin-logo{
                                height: 25px;
                                width: 25px;
                                border-radius: 4px;
                            }
                            .plugin-title{
                                display: flex;
                                align-items: center;
                                font-size: 12px;
                                color: #333;
                            }
                        }
                        .plugin-price{
                            color: #f56c6c;
                            font-weight: 700;
                            display: flex;
                            align-items: center;
                        }
                    }
                }

                .version-info {
                    display: flex;
                    justify-content: space-between;
                    .version-title {
                        font-size: 16px;
                        font-weight: bold;
                    }
                    .version-time {
                        font-size: 14px;
                        color: #606266;
                    }
                }
                .version-desc{
                    padding-top: 10px;
                    color:#606266;
                }
            }
        }
    }
}

.depend-info {
    display: flex;
    justify-content: space-between;

    .title {}

    .price {
        color: #f56c6c;
        font-weight: 700;
    }
}
</style>