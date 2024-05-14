<template>
    <div class="pages-container" v-if="detail">
        <div class="order-info">
            <div class="item">
                <span class="label">订单标题：</span>
                <span class="value">{{ detail?.title }}</span>
            </div>
            <div class="item">
                <span class="label">订单编号：</span>
                <span class="value">{{ detail?.order_no }}</span>
            </div>
            <div class="item">
                <span class="label">授权类型：</span>
                <span class="value">{{ detail?.order_no }}</span>
            </div>
            <div class="item">
                <span class="label">订单价格：</span>
                <span class="value price">￥{{ detail?.total }}</span>
            </div>
        </div>
        <div class="content">
            <div class="pay-type">
                <div class="pay-list">
                    <div class="item active">
                        <el-image class="logo" src="/static/image/alipay.png" />
                    </div>
                    <div class="item">
                        <el-image class="logo" src="/static/image/wechat.png" />
                    </div>
                    <div class="item">
                        <el-image class="logo" src="/static/image/wechat.png" />
                    </div>
                </div>
                <div class="other-list">
                    <div class="item">
                        <el-button class="btn" type="primary" plain>
                            不支持七天无理由退款
                        </el-button>
                    </div>
                    <div class="item">
                        <el-button class="btn" type="primary" plain>
                            授权协议 & 授权说明
                        </el-button>
                    </div>
                </div>
            </div>
            <div class="pay-qrcode">
                <el-image class="logo" src="/static/image/qrcode.png" />
                <div class="tip">
                    <AppIcons icon="FullScreen" :size="40" />
                    <div class="qrcode">
                        <div class="item">请使用支付宝扫一扫</div>
                        <div class="item">扫描二维码支付</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            detail: null
        }
    },
    mounted() {
        this.getData()
    },
    methods: {
        async getData() {
            const params = {
                name: this.$attrs?.name,
                version: this.$attrs?.version
            }
            const url = 'admin/Plugins/order'
            const data = await this.$http.useGet(url, params).catch((err) => {
                return err
            })
            if (data?.code === 11000) {
                this.$emit("update:closeWin");
                this.$useRemote('/vue/admin/cloud/login', {}, {
                    title: '云服务登录',
                    customStyle: {
                        width: '420px',
                        height: '450px',
                    },
                })
                return
            }
            if (data?.code !== 200) {
                this.$emit("update:closeWin");
                return
            }
            this.detail = data?.data
        }
    },
}
</script>

<style lang="scss" scoped>
.pages-container {
    height: 100%;

    .order-info {
        display: flex;
        flex-direction: column;
        row-gap: 8px;
        padding: 20px;

        .item {
            font-size: 14px;

            .label {}

            .label {}

            .price {
                color: #f56c6c;
                font-weight: 700;
            }
        }
    }

    .content {
        display: flex;
        column-gap: 20px;
        padding: 20px;
        border-top: 1px solid #f1f1f1;

        .pay-type {
            flex: 1;

            .pay-list {
                display: flex;
                flex-direction: column;
                row-gap: 20px;

                .item {
                    height: 50px;
                    border-radius: 6px;
                    border: 2px solid #ddd;
                    padding: 6px;
                    display: flex;
                    align-items: center;
                    cursor: pointer;

                    &:hover {
                        border: 2px solid #409eff;
                    }
                }

                .active {
                    border: 2px solid #409eff;
                }
            }

            .other-list {
                display: flex;
                flex-direction: column;
                padding-top: 15px;

                .item {
                    padding: 6px 0;

                    .btn {
                        width: 100%;
                    }
                }
            }
        }

        .pay-qrcode {
            flex: 1;

            .logo {
                width: 100%;
            }

            .tip {
                display: flex;
                align-items: center;
                column-gap: 10px;
                margin-top: 1px;
                background-color: #409eff;
                padding: 10px 20px;
                border-radius: 4px;
                color: #fff;
                user-select: none;

                .qrcode {
                    display: flex;
                    flex-direction: column;
                    row-gap: 5px;

                    .item {}
                }
            }
        }
    }
}</style>